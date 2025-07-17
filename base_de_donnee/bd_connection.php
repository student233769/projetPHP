<?php

function getConnexion() {
    $dsn = 'mysql:host=localhost;dbname=projet_PHP_2_sess;charset=utf8mb4';
    $dbUser = 'root';
    $dbPass = '';
    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur de connexion à la base de données : " . 
             htmlspecialchars($e->getMessage()) . "</p>";
        exit();
    }
}
?>
