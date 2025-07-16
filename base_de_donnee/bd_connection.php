<?php

$dsn = 'mysql:host=localhost;dbname=projetTest;charset=utf8mb4';
$dbUser = 'testeur';
$dbPass = 'test123';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    // Configurer les attributs PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gestion de l'erreur de connexion
    echo "<p style='color:red;'>Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}
?>