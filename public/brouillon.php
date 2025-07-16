<main>
        <!-- NAVBAR -->
        <div class="navbar navbar-expand-lg navbar-dark bg-dark p-3">
            <div class="container-fluid d-flex flex-column flex-lg-row align-items-center justify-content-between">
                
                <!-- USER SECTION -->
                <?php include './includes/user_section.php';?>

                <!-- NAVIGATE SECTION -->
                <div class="navbar-nav d-flex flex-column flex-lg-row gap-3 text-center text-lg-end">
                    <div class="nav-item mb-3 mb-lg-0">
                        <a class="nav-link text-light" href="index.php">Retour à la page principale</a>
                    </div>
                </div>

            </div>
        </div>

                
        <!-- MAIN CONTENT -->
        <div>
            <?php if($message): ?>
                <p style="color: <?php echo (strpos($message, 'succès') !== false ? 'green' : 'red'); ?>;">
                    <?php echo htmlentities($message); ?>
                </p>
            <?php endif; ?>

            <?php if($current_user->is_guest()): ?>
                <p>Veuillez-vous connecter.</p>
                <form method="POST">
                    <div class="mb-3">
                        <label for="matricule" class="form-label">Matricule :</label>
                        <input type="text" name="matricule" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe :</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Connexion</button>
                </form>
            <?php endif; ?>
        </div>
    </main>



    <?php
require_once __DIR__.'../class/Personne.php';
require_once __DIR__.'../../base_de_donnee/fonction.php';
session_start();


$message = '';
$current_user = new Personne();

// CHECK IF USER EXIST
if( isset($_SESSION['user']) ){
    $current_user = unserialize($_SESSION['user']);
    
    if( !$current_user->is_guest() ){
        $message = "Vous êtes déjà connecté.";
    }
}

// CHECK IF USER CAN CONNECT VIA POST METHOD
if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
    $matricule = $_POST['matricule'] ?? null;
    $password = $_POST['password'] ?? null;

    if( !empty($matricule) && !empty($password) ){
        $current_user = connection_profile_user($matricule, $password);

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