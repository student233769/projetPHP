<!-- lancer xampp -->
<!-- npm run dev -->


<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/class/Personne.php';
require_once __DIR__ . '/class/Ressource.php';
require_once __DIR__ . '/../base_de_donnee/recup_info.php';

if (isset($_GET['action']) && $_GET['action'] === 'logout'){
    session_unset();
    session_destroy();
    header("Location: index.php");
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

        <!-- OPTION DE TRI -->
        <form method="POST" class="mb-4">

            <label for="sort_by" class="form-label">Trier par :</label>
            <select name="sort_by" id="sort_by" class="form-select" onchange="/*this.form.submit()*/">
                <option value="date_sort">Date (du plus récent au plus ancien)</option>
                <option value="like_sort">Nombre de likes (du plus au moins)</option>
                <option value="follow_sort">personne follow</option>
            </select>

        </form>

        <div class="container mt-5">
            <h1>Cours disponible</h1>
            <div class="row" id="quotes-container">
                <?php if(count($liste_cours) > 0): ?>
                    <?php foreach($liste_cours as $cours): ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-4">  
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($cours->getTitre()) ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($cours->getBloc()) ?></p>
                                <p class="card-footer text-muted"><?php echo $cours->getSection() ?></p>
                                <p class="card-footer text-muted"><?php echo $cours->getId() ?></p>
                                <p class="card-footer text-muted">nombre de ressource :<?php echo count(getRessourcesValideesPourCours($cours->getId())) ?></p>
                                <a href="detail_cours.php?id=<?= urlencode($cours->getId()) ?>" class="btn btn-primary">
                                    consulter
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


