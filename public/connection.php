<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__.'/class/Personne.php';
require_once __DIR__.'/base_de_donnee/recup_info.php';

session_start();

///////// ATTENTION MISE EN PROD A FAIRE ICI 

$message = '';
$current_user = new Personne();

// CHECK IF USER EXIST
if( isset($_SESSION['user']) ){
    $current_user = unserialize($_SESSION['user']);
    
    if($current_user->is_admin() ){
        $message = "Vous êtes déjà connecté.";
    }else{
        $message = "n'est pas un admin.";
    }
}

if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
    $matricule = $_POST['matricule'] ?? null;
    $password = $_POST['password'] ?? null;

    if( !empty($matricule) && !empty($password) ){
        $current_user = login($matricule, $password);

        if( $current_user === null ){
            $current_user = new Personne();
            $message = "Matricule ou mot de passe incorrect.";
        }else{
            $_SESSION['user'] = serialize($current_user);
            header("Location: index.php");
            exit;
        }

    }else{
        $message = "Veuillez remplir tous les champs.";
    }
}


echo $message;

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page de connexion</title>
    <script src="http://localhost:5173/src/js/main.js" type="module"></script>
    <script src="http://localhost:5173/@vite/client" type="module"></script>
</head>

<body>
            <div>
            <?php if($message): ?>
                <p style="color: <?php echo (strpos($message, 'succès') !== false ? 'green' : 'red'); ?>;">
                    <?php echo htmlentities($message); ?>
                </p>
            <?php endif; ?>

            <?php if(!$current_user->is_admin()): ?>
                <p>Veuillez-vous connecter.</p>
                <form method="POST">
                    <div class="mb-3">
                        <label for="matricule" class="form-label">Matricule :</label>
                        <input type="text" name="matricule" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe :</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Connexion</button>
                </form>
            <?php endif; ?>
        </div>
</body>
</html>