<!-- lancer xampp -->
<!-- npm run dev -->


<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/class/Personne.php';
require_once __DIR__ . '/class/Ressource.php';
require_once __DIR__ . '/../base_de_donnee/recup_info.php';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

$liste_cours = getCoursAvecRessourcesValidees();

session_write_close();
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
        <div class="container mt-3">
            <h1>Cours disponible</h1>
            <div class="row" id="quotes-container">
                <?php if(count($liste_cours) > 0): ?>
                    <?php foreach($liste_cours as $cours): ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-4">  
                        <div class="card">
                            <div class="card-header">
                                <h3><?php echo htmlspecialchars($cours->getTitre()) ?></h4>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $cours->getSection() ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($cours->getBloc()) ?></p>
                                <p class="text-muted">nombre de ressource :<?php echo count(getRessourcesValideesPourCours($cours->getId())) ?></p>
                            </div>
                            <div class="card-footer">
                                <a href="detail_cours.php?id=<?= urlencode($cours->getId()) ?>" class="btn btn-primary">
                                    voir les ressources
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach;  ?>
                <?php else: ?>
                    <p>Aucun cours disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
                            

    </main>

    </body>
</html>


