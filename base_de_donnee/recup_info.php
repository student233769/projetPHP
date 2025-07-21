<?php
require_once 'bd_connection.php';
require_once __DIR__ . '/../public/class/Personne.php';
require_once __DIR__ .  '/../public/class/Cours.php';
require_once __DIR__ .  '/../public/class/Ressource.php';

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

function getCoursNonVideValider(){
        $pdo = getConnexion();
        $sql = "
        SELECT 
            c.id, 
            c.titre, 
            c.bloc, 
            c.section, 
            MAX(r.dateAjout) AS derniereRessource
        FROM 
            Cours c
        INNER JOIN 
            Ressources r 
              ON c.id = r.cours_id
             AND r.etat = 'VALIDE'
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

function ressourceEstLue($ressourceId, $personneId){
    $pdo = getConnexion();
    $sql = "
    SELECT EXISTS (    
            SELECT 1
                FROM LectureRessource r
                WHERE r.ressource_id = ? AND r.personne_id = ?
        )
    ";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $ressourceId,
        $personneId
    ]);

}


function affecterCommeLue($personne_id, $ressource_id){
    $pdo = getConnexion();
    $sql = "
        INSERT INTO Ressources (personne_id, ressource_id)
        VALUES (?, ?)
    ";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $personne_id,
        $ressource_id
    ]);
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


function ajouterRessource($titre, $type, $cheminRelatif, $coursId,$matricule) {
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
        $matricule
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
    $actual_user = unserialize($_SESSION['user']);

    $matricule = $actual_user->getMatricule();

    $pdo = getConnexion();
    $sql = "
        SELECT r.*, p.nom AS auteurNom, p.prenom AS auteurPrenom
        FROM Ressources r
        JOIN Personne p ON r.personne_id = p.matricule
        WHERE r.personne_id = ?
        ORDER BY r.dateAjout DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$matricule]);
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


function getRessourcesEnAttenteAvecAuteur() {
    $pdo = getConnexion();
    $sql = "
        SELECT r.*, p.nom AS auteurNom, p.prenom AS auteurPrenom, c.titre AS coursTitre
        FROM Ressources r
        JOIN Personne p ON r.personne_id = p.matricule
        JOIN Cours c ON r.cours_id = c.id
        WHERE r.etat = 'EN_ATTENTE'
        ORDER BY r.dateAjout ASC
    ";
    $stmt = $pdo->query($sql);
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
        $ressource->setCoursTitre($row['coursTitre']);
        $ressourcesList[] = $ressource;
    }
    return $ressourcesList;
}
?>