<?php
require_once '../../base_de_donnee/recup_info.php';
require_once '../class/Personne.php';
require_once '../class/Ressource.php';
require_once '../class/Cours.php';
include '../acces_page/admin_only.php';


 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ressource_id']) && isset($_POST['action'])) {
    $ressourceId = (int)$_POST['ressource_id'];
    $action = $_POST['action'];
    
    if ($action === 'valider') {
        if (validerRessource($ressourceId, 'VALIDE')) {
            $message = '<div class="alert alert-success" role="alert">Ressource validée avec succès !</div>';
        } else {
            $message = '<div class="alert alert-danger" role="alert">Erreur lors de la validation de la ressource.</div>';
        }
    } elseif ($action === 'rejeter') {
        if (validerRessource($ressourceId, 'REJETE')) {
            $message = '<div class="alert alert-warning" role="alert">Ressource rejetée avec succès !</div>';
        } else {
            $message = '<div class="alert alert-danger" role="alert">Erreur lors du rejet de la ressource.</div>';
        }
    }
}

$ressourcesEnAttente = getRessourcesEnAttenteAvecAuteur();

?>
<script src="http://localhost:5173/src/js/main.js" type="module"></script>
<script src="http://localhost:5173/@vite/client" type="module"></script>
<body>
    <div class="container">
        <h1 class="mb-4 text-center ">Validation des Ressources en Attente</h1>

        <?php echo $message;?>

        <?php if (empty($ressourcesEnAttente)): ?>
            <div class="alert alert-info text-center" role="alert">
                Aucune ressource en attente de validation pour le moment.
            </div>
        <?php else: ?>
            <div class="row row-cols-sm-1 row-cols-md-12 row-cols-lg-3 g-4">
                <?php foreach ($ressourcesEnAttente as $ressource): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($ressource->getTitre()); ?> <small class="text-white-50">(ID: <?php echo $ressource->getId(); ?>)</small></h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text mb-1"><strong>Type:</strong> <?php echo htmlspecialchars($ressource->getType()); ?></p>
                                <p class="card-text mb-1"><strong>Cours:</strong> <?php echo htmlspecialchars($ressource->getCoursTitre()); ?></p>
                                <p class="card-text mb-1"><strong>Auteur:</strong> <?php echo htmlspecialchars($ressource->getAuteurPrenom() . ' ' . $ressource->getAuteurNom()); ?></p>
                                <p class="card-text mb-0"><strong>Ajouté le:</strong> <?php echo $ressource->formatDateAjout(); ?></p>
                            </div>
                            <div class="card-footer" >
                                <div class="btn-group mt-3">
                                    <?php
                                        $cheminRelatif = $ressource->getCheminRelatif();
                                        $typeRessource = $ressource->getType();

                                        if (!empty($cheminRelatif)) {
                                            if ($typeRessource == 'URL') {
                                                echo '<a href="' . htmlspecialchars($cheminRelatif) . '" target="_blank" class="btn btn-warning btn mr-4">Consulter le lien</a>';
                                            } else {
                                                echo '<a href="' . htmlspecialchars($cheminRelatif) . '" download class="btn btn-warning btn mr-4">Télécharger le fichier</a>';
                                            }
                                        } else {
                                            echo '<button type="button" class="btn btn-secondary btn-sm" disabled>Pas de ressource</button>';
                                        }
                                    ?>
                                    <form method="POST" class="d-inline-block">
                                        <input type="hidden" name="ressource_id" value="<?php echo $ressource->getId(); ?>">
                                        <button type="submit" name="action" value="valider" class="btn btn-success btn">Valider</button>
                                    </form>
                                    <form method="POST" class="d-inline-block">
                                        <input type="hidden" name="ressource_id" value="<?php echo $ressource->getId(); ?>">
                                        <button type="submit" name="action" value="rejeter" class="btn btn-danger btn">Rejeter</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>