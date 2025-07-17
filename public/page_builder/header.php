<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$publicDir = __DIR__;
$docRoot   = realpath($_SERVER['DOCUMENT_ROOT']);
$baseUrl = str_replace('\\','/', substr($publicDir, strlen($docRoot)));
if ($baseUrl === false || $baseUrl === '') {
    $baseUrl = '';
}

$avatar = $_SESSION['user']['avatar'] ?? 'buste.jpg';
$imgSrc = $baseUrl . '/../profile_pict/' . $avatar;
$actual_user = null;
session_start();



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
