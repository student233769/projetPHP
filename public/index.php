<!-- lancer xampp -->
<!-- npm run dev -->


<?php
echo "coucou";


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
        
  </body>
</html>


