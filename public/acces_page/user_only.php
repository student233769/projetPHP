<?php

if( basename($_SERVER['PHP_SELF']) === basename(__FILE__) ){
    $url_page_index = '../index.php';
    exit('
    <div class="alert alert-danger" role="alert" style="text-align: center; margin: 20px;">
        <strong>Accès interdit!</strong> Vous n\'avez pas le droit d\'accéder à cette page. 
        <a href="'.htmlspecialchars($url_page_index, ENT_QUOTES).'" class="alert-link">Retourner à la page d\'accueil</a>.
    </div>
    ');
}


if(!isset($_SESSION['user'])) {
    exit('
        <div class="alert alert-danger text-center" role="alert" style="max-width: 600px; margin: 50px auto;">
            <strong>Accès interdit !</strong> Veuillez vous connecter pour accéder à cette page.
            <br>
            <a href="../connect.php" class="btn btn-primary mt-3">Cliquez ici pour vous connecter</a>
        </div>
    ');
}
?>