<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once __DIR__ . '/../class/Personne.php';
require_once __DIR__ . '/../class/Ressource.php';
require_once __DIR__ . '/../../base_de_donnee/recup_info.php';

//session_start();

if (
    basename($_SERVER['PHP_SELF']) === basename(__FILE__)
    && ! isset($_SESSION['user'])
) {
    $url_page_index = '../index.php';
    exit('
      <div class="alert alert-danger text-center m-4" role="alert">
        <strong>Accès interdit !</strong>
        Vous devez être connecté pour accéder à cette page.
        <a href="' . htmlspecialchars($url_page_index, ENT_QUOTES) . '" class="alert-link">
          Retourner à la page d\'accueil
        </a>.
      </div>
    ');
}

$actual_user = isset($_SESSION['user'])
    ? unserialize($_SESSION['user'])
    : new Personne();



if( basename($_SERVER['PHP_SELF']) === basename(__FILE__) ){
    $url_page_index = '../index.php';
    exit('
    <div class="alert alert-danger" role="alert" style="text-align: center; margin: 20px;">
        <strong>Accès interdit!</strong> Vous n\'avez pas le droit d\'accéder à cette page. 
        <a href="'.htmlspecialchars($url_page_index, ENT_QUOTES).'" class="alert-link">Retourner à la page d\'accueil</a>.
    </div>
    ');
}




$publicDir = __DIR__;
$docRoot   = realpath($_SERVER['DOCUMENT_ROOT']);
$baseUrl = str_replace('\\','/', substr($publicDir, strlen($docRoot)));
if ($baseUrl === false || $baseUrl === '') {
    $baseUrl = '';
}

$avatar = $_SESSION['user']['avatar'] ?? 'buste.jpg';
$imgSrc = $baseUrl . '/../profile_pict/' . $avatar;
$actual_user = null;




if (isset($_SESSION['user'])) {
    $actual_user = unserialize($_SESSION['user']);
} else {
    $actual_user = new Personne();
}

?>

<!-- USER SECTION -->
<div class="d-flex align-items-center mb-3 mb-lg-0 text-center text-lg-start">

<img src="<?php echo $imgSrc; ?>" alt="Avatar" width="50" height="50" class="rounded-circle me-3" loading="lazy">

    <div>
        <p class="text-light mb-0"><?php echo $actual_user->getNom(); ?></p>
        <p class="matricule mb-0"><?php echo $actual_user->getPrenom(); ?></p>

    </div>
</div>
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