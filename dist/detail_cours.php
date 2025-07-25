<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/class/Personne.php';
require_once __DIR__ . '/class/Ressource.php';
require_once __DIR__ . '/base_de_donnee/recup_info.php';



$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
session_start();

$actual_user = isset($_SESSION['user']) ? unserialize($_SESSION['user']) : null;

// if ($actual_user === null) {
//     exit('Accès interdit. Vous devez être connecté.');
// }

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    exit("ID de cours non valide ou manquant.");
}


$message_ajout = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_ressource'])) {

    $titre = htmlspecialchars($_POST['titre'] ?? '', ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($_POST['type'] ?? '', ENT_QUOTES, 'UTF-8');
    $coursId_form = filter_input(INPUT_POST, 'coursId', FILTER_VALIDATE_INT);
    $cheminRelatif = null;

    $types_autorises = ['URL', 'PDF', 'JPG', 'PNG'];
    $file_upload_success = true;

    if ($titre && $type && $coursId_form && in_array($type, $types_autorises)) {
        
        if ($type === 'URL') {
            $url = filter_input(INPUT_POST, 'cheminRelatif', FILTER_VALIDATE_URL);
            if ($url) {
                $cheminRelatif = $url;
            } else {
                $message_ajout = '<div class="alert alert-danger" role="alert">L\'URL fournie n\'est pas valide.</div>';
                $file_upload_success = false;
            }
        } 
        else {
            if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === UPLOAD_ERR_OK) {
                $file_info = $_FILES['resource_file'];
                $upload_dir = __DIR__ . '/../uploads/resources/'; 
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0775, true);
                }
                

                $original_name = basename($file_info['name']);
                $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                $nom_sans_extension = pathinfo($original_name, PATHINFO_FILENAME);

                $nom_nettoye = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nom_sans_extension);

                $timestamp = time();
                $nom_fichier_final = $nom_nettoye . '_' . $timestamp . '.' . $extension;

                $destination = $upload_dir . $nom_fichier_final;

            
                $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];
                if (in_array($extension, $allowed_extensions) && strtolower($type) === $extension) {
                    if (move_uploaded_file($file_info['tmp_name'], $destination)) {
                        $cheminRelatif = 'uploads/resources/' . $nom_fichier_final;
                    } else {
                        $message_ajout = '<div class="alert alert-danger" role="alert">Erreur lors du déplacement du fichier téléversé.</div>';
                        $file_upload_success = false;
                    }
                } else {
                    $message_ajout = '<div class="alert alert-danger" role="alert">Type de fichier non autorisé ou ne correspond pas à la sélection.</div>';
                    $file_upload_success = false;
                }
            } else {
                 $message_ajout = '<div class="alert alert-danger" role="alert">Erreur lors du téléversement ou aucun fichier sélectionné.</div>';
                 $file_upload_success = false;
            }
        }

        if ($file_upload_success && $cheminRelatif) {
            if (ajouterRessource($titre, $type, $cheminRelatif, $coursId_form, $actual_user->getMatricule())) {
                $message_ajout = '<div class="alert alert-success" role="alert">Ressource ajoutée avec succès ! Elle est en attente de validation.</div>';
            } else {
                $message_ajout = '<div class="alert alert-danger" role="alert">Une erreur est survenue lors de l\'enregistrement dans la base de données.</div>';
            }
        }

    } else {
        $message_ajout = '<div class="alert alert-warning" role="alert">Veuillez remplir tous les champs du formulaire correctement.</div>';
    }


}


$list_ressources = getRessourcesValideesPourCours($id);

if (isset($_GET['mark_as_read']) && isset($_SESSION['user'])) {
    $ressource_id_a_marquer = filter_input(INPUT_GET, 'mark_as_read', FILTER_VALIDATE_INT);
    $actual_user_for_mark = unserialize($_SESSION['user']);

    if ($ressource_id_a_marquer && $actual_user_for_mark) {
        affecterCommeLue($actual_user_for_mark->getMatricule(), $ressource_id_a_marquer);
        // Rediriger pour nettoyer l'URL
        header("Location: detail_cours.php?id=$id");
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détail du Cours</title>
    <script src="http://localhost:5173/src/js/main.js" type="module"></script>
    <script src="http://localhost:5173/@vite/client" type="module"></script>
    <style>
      .hidden-form-field {
        display: none;
      }
    </style>
  </head>
  <body>
    <main>

      <div class="navbar navbar-expand-lg navbar-dark bg-dark p-3 w-100">
        <div class="container-fluid d-flex flex-column flex-lg-row align-items-center justify-content-between">
            <?php include './page_builder/header.php'; ?>
        </div>
      </div>

      <div class="container mt-5">

        <?php if (!empty($message_ajout)) echo $message_ajout; ?>

        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Ressources disponibles</h2>
            <?php if ($actual_user->getMatricule() != null): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResourceModal">
              + Ajouter une ressource
            </button>
            <?php endif; ?>
        </div>
        <hr>

        <div class="row" id="quotes-container">
            <?php if(count($list_ressources) > 0): ?>
                <?php foreach($list_ressources as $ressource): ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4">  
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($ressource->getTitre()); ?></h5>
                            <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($ressource->getType()); ?></p>
                            <p class="card-text">
                                <?php if($actual_user && ressourceEstLue($ressource->getId(), $actual_user->getMatricule())): ?>
                                    <span class="badge bg-success">Lue</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Non lue</span>
                                <?php endif; ?>
                            </p>
                            
                            <?php if ($actual_user->getMatricule() != null):
                                $read_url = "detail_cours.php?id=$id&mark_as_read=" . $ressource->getId();
                            ?>
                                <a href="<?php echo $read_url; ?>"
                                   class="btn btn-info mt-auto"
                                   onclick="window.open('<?php echo htmlspecialchars($ressource->getCheminRelatif(), ENT_QUOTES); ?>', '_blank'); window.location.href='<?php echo $read_url; ?>'; return false;">
                                    Voir la ressource
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer text-muted">
                            Ajouté par <?php echo htmlspecialchars($ressource->getAuteurPrenom() . ' ' . $ressource->getAuteurNom()); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach;  ?>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    Aucune ressource n'a encore été validée pour ce cours. Soyez le premier à en ajouter une !
                </div>
            <?php endif; ?>
        </div>
      </div>
    </main>

    <div class="modal fade" id="addResourceModal" tabindex="-1" aria-labelledby="addResourceModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addResourceModalLabel">Ajouter une nouvelle ressource</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            
          </div>
          <div class="modal-body">
            <form action="detail_cours.php?id=<?php echo htmlspecialchars($id, ENT_QUOTES); ?>" method="POST" id="resourceForm" enctype="multipart/form-data">
                <input type="hidden" name="coursId" value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>">
                <input type="hidden" name="ajouter_ressource" value="1">
                
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre de la ressource</label>
                    <input type="text" class="form-control" id="titre" name="titre" required>
                </div>
                
                <div class="mb-3">
                    <label for="type" class="form-label">Type de ressource</label>
                    <select class="form-select" id="type" name="type" required>
                        <option selected disabled value="">Choisir un type...</option>
                        <option value="URL">Lien Web (URL)</option>
                        <option value="PDF">Document PDF</option>
                        <option value="JPG">Image JPG</option>
                        <option value="PNG">Image PNG</option>
                    </select>
                </div>

                <div class="mb-3 hidden-form-field" id="urlInputContainer">
                    <label for="cheminRelatif" class="form-label">Lien (URL)</label>
                    <input type="url" class="form-control" id="cheminRelatif" name="cheminRelatif" placeholder="https://example.com/ressource">
                </div>

                <div class="mb-3 hidden-form-field" id="fileInputContainer">
                    <label for="resource_file" class="form-label">Fichier</label>
                    <input type="file" class="form-control" id="resource_file" name="resource_file">
                </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" form="resourceForm" class="btn btn-primary">Soumettre la ressource</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
          const typeSelect = document.getElementById('type');
          const urlInputContainer = document.getElementById('urlInputContainer');
          const fileInputContainer = document.getElementById('fileInputContainer');
          const urlInput = document.getElementById('cheminRelatif');
          const fileInput = document.getElementById('resource_file');

          function toggleInputs() {
              const selectedType = typeSelect.value;
              
              urlInputContainer.classList.add('hidden-form-field');
              fileInputContainer.classList.add('hidden-form-field');
              urlInput.required = false;
              fileInput.required = false;

              if (selectedType === 'URL') {
                  urlInputContainer.classList.remove('hidden-form-field');
                  urlInput.required = true;
              } else if (selectedType === 'PDF' || selectedType === 'JPG' || selectedType === 'PNG') {
                  fileInputContainer.classList.remove('hidden-form-field');
                  fileInput.required = true;
                  fileInput.accept = '.' + selectedType.toLowerCase();
              }
          }

          typeSelect.addEventListener('change', toggleInputs);
          toggleInputs(); 
      });
    </script>
  </body>
</html>