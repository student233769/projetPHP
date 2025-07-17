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

$avatar = $_SESSION['user']['avatar_path'] ?? 'default.png';
$imgSrc = $baseUrl . '/profile_pict/' . rawurlencode($avatar);
?>

<!-- USER SECTION -->
<div class="d-flex align-items-center mb-3 mb-lg-0 text-center text-lg-start">

<img 
  src="<?= htmlspecialchars($imgSrc, ENT_QUOTES) ?>" 
  alt="Avatar de <?= htmlspecialchars($_SESSION['user']['prenom'] ?? 'utilisateur', ENT_QUOTES) ?>"
  loading="lazy"
/>
    <div>
        <p class="text-light mb-0"><?php echo $actual_user->getNom(); ?></p>
        <p class="matricule mb-0"><?php echo $actual_user->getPrenom(); ?></p>

    </div>
</div>
