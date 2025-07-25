<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Démarre la session
session_start();

// 3) Récupère l’utilisateur courant (objet Personne)
if (isset($_SESSION['user'])) {
    $current_user = unserialize($_SESSION['user']);
} else {
    // aucun utilisateur connecté
    $current_user = null;
}

if (
    basename($_SERVER['PHP_SELF']) === basename(__FILE__)
    && $current_user === null
) {
    $url_page_index = '../index.php';
    exit('
      <div class="alert alert-danger text-center m-4" role="alert">
        <strong>Accès interdit !</strong> Vous devez être connecté pour accéder à cette page.
        <a href="' . htmlspecialchars($url_page_index, ENT_QUOTES) . '" class="alert-link">Retourner à l’accueil</a>.
      </div>
    ');
}

if (! $current_user instanceof Personne || ! $current_user->is_admin()) {
    exit('
      <p style="color:red; text-align:center; margin:20px;">
        Vous n\'avez pas le droit d\'accéder à cette page.
        <a href="../index.php">Retour à l’accueil</a>
      </p>
    ');
}
?>


//dernière correction