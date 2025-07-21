<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/class/Personne.php';
require_once __DIR__ . '/class/Ressource.php';
require_once __DIR__ . '/../base_de_donnee/recup_info.php';


session_start();

$actual_user = isset($_SESSION['user'])
    ? unserialize($_SESSION['user'])
    : null;

if (
    basename($_SERVER['PHP_SELF']) === basename(__FILE__)
    && $actual_user === null
) {
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
            <h1>Votre historique de post</h1>
            <div class="row" id="quotes-container">
                <?php if(count($liste_cours_poster_utilisateur) > 0): ?>
                    <table class="table">
                      <thead >
                        <tr>
                          <th scope="col">Titre</th>
                          <th scope="col">Type</th>
                          <th scope="col">Date upload</th>
                          <th scope="col">status</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php foreach($liste_cours_poster_utilisateur as $ressources): ?>
                        <tr>
                          <th><?php echo $ressources->getTitre() ?></th>
                          <th><?php echo $ressources->getType() ?></th>
                          <td><?php echo $ressources->getDateAjout()->format('d-m-Y H:i') ?></td>
                          <td><?php echo $ressources->getEtat() ?></td>
                        </tr>
                    <?php endforeach;  ?>
                      </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucun ressource disponible pour le moment.</p>
                <?php endif; ?>    
            </div>
        </main>
    </body>
</html>


