<?php
session_start();

require_once 'bd_connection.php';
function login($matricule, $motdepasse) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM Personne WHERE matricule = ? AND mdp = ?");
    $stmt->execute([$matricule, $motdepasse]);
    

    if ($stmt->rowCount()>0){
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $user = new Personne(
                $user['matricule'],
                $user['mdp'],//ici correction de password a mdp
                $user['nom'],
                $user['prenom'],
                $user['avatar'],
                $user['role']
            );
            return $user;
    }else{
        return null;
    }

}

function getCoursAvecRessourcesValidees() {
    $pdo = getConnexion();
    $sql = "
        SELECT 
            c.*, 
            MAX(r.dateAjout) AS derniereRessource
        FROM 
            Cours c
        LEFT JOIN 
            Ressources r 
            ON c.id = r.cours_id AND r.etat = 'VALIDE'
        GROUP BY 
            c.id
        ORDER BY 
            derniereRessource DESC
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function getRessourcesValideesPourCours($coursId) {
    $pdo = getConnexion();
    $sql = "
        SELECT * FROM Ressources
        WHERE cours_id = ? AND etat = 'VALIDE'
        ORDER BY dateAjout DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$coursId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $_SESSION['user']['matricule']
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
        SELECT * FROM Ressources
        WHERE personne_id = ?
        ORDER BY dateAjout DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user']['matricule']]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

