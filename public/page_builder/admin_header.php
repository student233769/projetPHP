<?php
if( basename($_SERVER['PHP_SELF']) === basename(__FILE__) ){
    include '../includes/no_access.php';
}

    // CHECK IF THE USER IS NOT AN ADMIN
    if(!$current_user->is_admin()){
        exit("<p style='color:red;'>Vous n'avez pas le droit d'accéder à cette page: <a href=\"../index.php\">Cliquez ici</a></p>");
    }
?>