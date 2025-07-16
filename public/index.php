<!-- lancer xampp -->
<!-- npm run dev -->


<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/class/Personne.php';

$actual_user = null;
session_start();



if (isset($_SESSION['user'])) {
    $actual_user = unserialize($_SESSION['user']);
} else {
    $actual_user = new Personne();
}

/*if (isset($_GET['action']) && $_GET['action'] === 'logout'){
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}*/

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
      <div class="navbar navbar-expand-lg navbar-dark bg-dark p-3 w-100">
            <div class="container-fluid d-flex flex-column flex-lg-row align-items-center justify-content-between">
                
                <!-- USER SECTION -->
                <?php include './page_builder/header.php'; ?>


                <!-- NAVIGATION SECTION -->
                <div class="navbar-nav d-flex flex-column flex-lg-row gap-3 text-center text-lg-end">
                    <?php if(!$actual_user->is_admin()): ?>
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
  </body>
</html>


