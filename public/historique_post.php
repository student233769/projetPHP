<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/class/Personne.php';
require_once __DIR__ . '/class/Ressource.php';
require_once __DIR__ . '/../base_de_donnee/recup_info.php';


// 1) on démarre la session tout de suite
session_start();

// 2) on détermine si l'utilisateur est logué
$actual_user = isset($_SESSION['user'])
    ? unserialize($_SESSION['user'])
    : null;

// 3) si accès direct *ET* pas d'utilisateur connecté → blocage
if (
    basename($_SERVER['PHP_SELF']) === basename(__FILE__)
    && $actual_user === null
) {
    // redirige vers l'index ou affiche un message
    $url_page_index = './index.php';
    exit('
      <div class="alert alert-danger text-center m-4" role="alert">
        <strong>Accès interdit!</strong> Vous devez être connecté pour accéder à cette page.
        <a href="' . htmlspecialchars($url_page_index, ENT_QUOTES) . '" class="alert-link">
          Retourner à la page d\'accueil
        </a>.
      </div>
    '); 
}


$liste_cours_poster_utilisateur = getRessourcesUtilisateurConnecte();
// echo 'j ai recuperer '. count($liste_cours_poster_utilisateur);
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap w/ Vite</title>
    <script src="http://localhost:5173/src/js/main.js" type="module"></script>
    <script src="http://localhost:5173/@vite/client" type="module"></script>
  </head>
  <body>
    <main>

      <div class="navbar navbar-expand-lg navbar-dark bg-dark p-3 w-100">
            <div class="container-fluid d-flex flex-column flex-lg-row align-items-center justify-content-between">
                
                <!-- USER SECTION -->
                <?php include './page_builder/header.php'; ?>



            </div>
        </div>

        <div class="container mt-5">
            <h1>Cours disponible</h1>
            <div class="row" id="quotes-container">
                <?php if(count($liste_cours_poster_utilisateur) > 0): ?>
                    <?php foreach($liste_cours_poster_utilisateur as $ressources): ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-4">  
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $ressources->getTitre() ?></h5>
                                <p class="card-text"><?php echo $ressources->getType() ?></p>
                                <p class="card-footer text-muted"><?php echo $ressources->getPersonneId() ?></p>
                                <!-- <p class="card-footer text-muted"><?php echo $ressources->getEstDejaLue() ?></p> -->
                                <p class="card-footer text-muted"><?php echo $ressources->getCheminRelatif() ?></p>
                                <p class="card-footer text-muted"><?php echo $ressources->getAuteurNom() ?></p>
                                <p class="card-footer text-muted"><?php echo $ressources->getAuteurPrenom() ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach;  ?>
                <?php else: ?>
                    <p>Aucun ressource disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
                            

    </main>

    </body>
</html>


