<!-- En dev Vite injectera ici @vite/client et ton main.js -->
<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Passe $production à false en local ou en dev
$production = false; //--------------------------------TRUER POUR EN PROD --------------------------------

if ($production) {
  // On lit le manifest généré par Vite (après un npm run build)
  $manifestPath = './manifest.json'; 
  $manifestJson = json_decode(file_get_contents($manifestPath), true);

  // Récupère le nom de fichier JS et son CSS associé
  $mainJs = $manifestJson['src/js/main.js']['file'];
  $mainCss = $manifestJson['src/js/main.js']['css'][0] ?? null;
  ?>
  <!-- En production, on sert les bundles hachés -->
  <script type="module" src="<?php echo htmlspecialchars($mainJs); ?>"></script>
  <?php if ($mainCss): ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($mainCss); ?>">
  <?php endif; ?>
<?php
} else {
  // En développement, on utilise le serveur Vite pour le hot‑reload
  ?>
  <script type="module" src="http://localhost:5173/@vite/client"></script>
  <script type="module" src="http://localhost:5173/src/js/main.js"></script>
  <?php
}
?>

<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once __DIR__ . '/../class/Personne.php';
require_once __DIR__ . '/../class/Ressource.php';
require_once __DIR__ . '/../base_de_donnee/recup_info.php';

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


$baseProjectUrl = $_SERVER['REQUEST_SCHEME']
                . '://' 
                . $_SERVER['HTTP_HOST']
                . '/projet_php_repase';


// 4. Choix de l’avatar (nom de fichier)
$avatarFilename = $actual_user->getAvatar() ?: 'buste.jpg';

// 5. URL complète de l’image
$imgSrc = $baseProjectUrl 
        . '/public/profile_pict/' 
        . rawurlencode($avatarFilename);

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

?>

<!-- USER SECTION -->
<div class="d-flex align-items-center mb-3 mb-lg-0 text-center text-lg-start">

<img src="<?php echo $imgSrc; ?>" alt="Avatar" width="50" height="50" class="rounded-circle me-3" loading="lazy">

    <div>
        <p class="text-light mb-0"><?php echo $actual_user->getNom(); ?></p>
        <p class="text-light matricule mb-0"><?php echo $actual_user->getPrenom(); ?></p>

    </div>
</div>
<div class="navbar-nav d-flex flex-column flex-lg-row gap-3 text-center text-lg-end">
    <?php if($actual_user->getMatricule() == null): ?>
        <a class="nav-link text-light" href="./connection.php">Se connecter</a>
    <?php else: ?>
        <a class="nav-link text-light" href="http://localhost/projet_php_repase/public/index.php">Acceuil</a>
        <a class="nav-link text-light" href="http://localhost/projet_php_repase/public/index.php?action=logout">Se déconnecter</a>
        <?php if(!$actual_user->getMatricule() == null): ?>
            <a class="nav-link text-light" href="http://localhost/projet_php_repase/public/ajout_ressource.php">Ajouter une ressource</a>
        <?php endif; ?>

        <?php if($actual_user->is_admin()): ?>
            <a class="nav-link text-light" href="./admin/admin_index.php">Page Admin</a>
        <?php endif; ?>
    <?php endif; ?>
</div>