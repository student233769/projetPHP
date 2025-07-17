<?php
require_once 'bd_connection.php';
require_once '../public/class/Personne.php';
require_once '../public/class/Cours.php';
require_once '../public/class/Ressource.php';

function login($matricule, $motdepasse) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM Personne WHERE matricule = ? AND mdp = ?");
    $stmt->execute([$matricule, $motdepasse]);
    

    if ($stmt->rowCount()>0){
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $personne = new Personne(
            $user['matricule'],
            $user['mdp'],
            $user['nom'],
            $user['prenom'],
            $user['avatar'],
            $user['administrateur']
        );
        return $personne;
    }else{
        return null;
    }

}

function getCoursAvecRessourcesValidees() {
    $pdo = getConnexion();
    $sql = "
        SELECT 
            c.id, c.titre, c.bloc, c.section, 
            MAX(r.dateAjout) AS derniereRessource
        FROM 
            Cours c
        LEFT JOIN 
            Ressources r 
            ON c.id = r.cours_id AND r.etat = 'VALIDE'
        GROUP BY 
            c.id, c.titre, c.bloc, c.section
        ORDER BY 
            derniereRessource DESC
    ";
    $stmt = $pdo->query($sql);
    $coursList = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $coursList[] = new Cours(
            $row['titre'],
            $row['bloc'],
            $row['section'],
            $row['id']
        );
    }
    return $coursList;
}

function getRessourcesValideesPourCours($coursId) {
    $pdo = getConnexion();
    $sql = "
        SELECT r.*, p.nom AS auteurNom, p.prenom AS auteurPrenom
        FROM Ressources r
        JOIN Personne p ON r.personne_id = p.matricule
        WHERE r.cours_id = ? AND r.etat = 'VALIDE'
        ORDER BY r.dateAjout DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$coursId]);
    $ressourcesList = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ressource = new Ressource(
            $row['titre'],
            $row['type'],
            $row['cours_id'],
            $row['personne_id'],
            $row['etat'],
            $row['cheminRelatif'],
            $row['dateValidationAjout'],
            $row['dateAjout'],
            $row['id']
        );
        $ressource->setAuteurNom($row['auteurNom']);
        $ressource->setAuteurPrenom($row['auteurPrenom']);
        $ressourcesList[] = $ressource;
    }
    return $ressourcesList;
}

function ajouterRessource($titre, $type, $cheminRelatif, $coursId) {
    if (!isset($_SESSION['user'])) return false;

    $pdo = getConnexion();
    $sql = "
        INSERT INTO Ressources (titre, type, cheminRelatif, cours_id, personne_id)
        VALUES (?, ?, ?, ?, ?)
    ";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $titre,
        $type,
        $cheminRelatif,
        $coursId,
        $_SESSION['user']->getMatricule()
    ]);
}

function validerRessource($ressourceId, $etat) {
    if (!in_array($etat, ['VALIDE', 'REJETE'])) return false;

    $pdo = getConnexion();
    $sql = "
        UPDATE Ressources
        SET etat = ?, dateValidationAjout = NOW()
        WHERE id = ?
    ";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$etat, $ressourceId]);
}

function getRessourcesUtilisateurConnecte() {
    if (!isset($_SESSION['user'])) return [];

    $pdo = getConnexion();
    $sql = "
        SELECT r.*, p.nom AS auteurNom, p.prenom AS auteurPrenom
        FROM Ressources r
        JOIN Personne p ON r.personne_id = p.matricule
        WHERE r.personne_id = ?
        ORDER BY r.dateAjout DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user']->getMatricule()]);
    $ressourcesList = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ressource = new Ressource(
            $row['titre'],
            $row['type'],
            $row['cours_id'],
            $row['personne_id'],
            $row['etat'],
            $row['cheminRelatif'],
            $row['dateValidationAjout'],
            $row['dateAjout'],
            $row['id']
        );
        $ressource->setAuteurNom($row['auteurNom']);
        $ressource->setAuteurPrenom($row['auteurPrenom']);
        $ressourcesList[] = $ressource;
    }
    return $ressourcesList;
}

?>