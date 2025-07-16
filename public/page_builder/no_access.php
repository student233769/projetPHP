<?php

// GET THE ROOT PROJECT FOLDER DYNAMICALLY
$project_folder = explode('/', $_SERVER['PHP_SELF'])[1];

// DEFINE THE BASE URL TO ACCESS THE ENTIRE PROJECT
$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $project_folder;

// DEFINE THE URL PATH TO THE PUBLIC FOLDER
$url_to_public_folder = $base_url.'/public/';

exit('
    <div class="alert alert-danger" role="alert" style="text-align: center; margin: 20px;">
        <strong>Accès interdit!</strong> Vous n\'avez pas le droit d\'accéder à cette page. 
        <a href="'.$url_to_public_folder.'index.php.'.'" class="alert-link">Retourner à la page d\'accueil</a>.
    </div>
    ');

?>