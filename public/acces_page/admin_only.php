<?php
session_start();

// 2) on détermine si l'utilisateur est logué
$actual_user = isset($_SESSION['user'])
    ? unserialize($_SESSION['user'])
    : null;

if( basename($_SERVER['PHP_SELF']) === basename(__FILE__) ){
    $url_page_index = '../index.php';
    exit('
    <div class="alert alert-danger" role="alert" style="text-align: center; margin: 20px;">
        <strong>Accès interdit!</strong> Vous n\'avez pas le droit d\'accéder à cette page. 
        <a href="'.htmlspecialchars($url_page_index, ENT_QUOTES).'" class="alert-link">Retourner à la page d\'accueil</a>.
    </div>
    ');
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//  include '../class/Personne.php';

$url_page_index = '../index.php';

//CHECK IF THE USER IS NOT AN ADMIN
if($actual_user === null){
    exit("<p style='color:red;'>Vous n'avez pas le droit d'accéder à cette page: <a href=\"$url_page_index\">Cliquez ici</a></p>");
}
else if(!$actual_user->is_admin()){
    exit("<p style='color:red;'>Vous n'avez pas le droit d'accéder à cette page: <a href=\"$url_page_index\">Cliquez ici</a></p>");
}
?>