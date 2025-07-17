<?php 
  echo "DEBUG URL → ", htmlspecialchars($url_to_public_folder . $actual_user->getAvatar());
  exit;
?>
<?php if (!empty($_SESSION['user']['avatar_path'])): ?>
    <img 
        src="/ViteExo3Chap2/public/profile_pict/<?= 
            htmlspecialchars($_SESSION['user']['avatar_path'], ENT_QUOTES) 
        ?>" 
        alt="Photo de profil de <?= htmlspecialchars($_SESSION['user']['prenom'], ENT_QUOTES) ?>"
    >