<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/class/Personne.php';
require_once __DIR__ . '/class/Ressource.php';
require_once __DIR__ . '/../base_de_donnee/recup_info.php';

$actual_user = null;
session_start();



if (isset($_SESSION['user'])) {
    $actual_user = unserialize($_SESSION['user']);
} else {
    $actual_user = new Personne();
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
echo $id;

$list_ressources = getRessourcesValideesPourCours($id);
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


                <!-- NAVIGATION SECTION -->
                <div class="navbar-nav d-flex flex-column flex-lg-row gap-3 text-center text-lg-end">
                    <?php if($actual_user->getMatricule() == null): ?>
                        <a class="nav-link text-light" href="./connection.php">Se connecter</a>
                    <?php else: ?>
                        <a class="nav-link text-light" href="?action=logout">Se déconnecter</a>
                        <?php if($actual_user->is_admin()): ?>
                            <a class="nav-link text-light" href="./admin/admin_index.php">Page Admin</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="container mt-5">
            <h1>Cours disponible</h1>
            <div class="row" id="quotes-container">
                <?php if(count($list_ressources) > 0): ?>
                    <?php foreach($list_ressources as $ressources): ?>
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


