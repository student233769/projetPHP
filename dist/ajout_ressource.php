<?php
require_once __DIR__ . '/class/Personne.php';
require_once __DIR__ . '/class/Ressource.php';
require_once __DIR__ . '/class/Cours.php';
require_once __DIR__ . '/base_de_donnee/recup_info.php'; 

session_start();

$actual_user = isset($_SESSION['user']) && is_string($_SESSION['user'])
    ? unserialize($_SESSION['user'])
    : null;


if (
    basename($_SERVER['PHP_SELF']) === basename(__FILE__)
    && $actual_user === null
) {
    $url_page_index = './index.php';
    exit('
      <div class="alert alert-danger text-center m-4" role="alert">
        <strong>Accès interdit!</strong> Vous devez être connecté pour accéder à cette page.
        <a href="' . htmlspecialchars($url_page_index, ENT_QUOTES) . '" class="alert-link">
          Retourner à la page d\'accueil
        </a>.
      </div>
    '); 
}
$listeCoursDispo = getAllCours();


$message_ajout = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_ressource'])) {
    $titre = htmlspecialchars($_POST['titre'] ?? '', ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars($_POST['type'] ?? '', ENT_QUOTES, 'UTF-8');
    $coursId_form = filter_input(INPUT_POST, 'coursId', FILTER_VALIDATE_INT); 
    $cheminRelatif = null;

    $types_autorises = ['URL', 'PDF', 'JPG', 'PNG'];
    $file_upload_success = true; 
    if ($titre && $type && $coursId_form !== false && in_array($type, $types_autorises) && $actual_user) {
        
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
                $upload_dir = __DIR__ . '/uploads/resources/'; 
                
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        $message_ajout = '<div class="alert alert-danger" role="alert">Erreur: Impossible de créer le répertoire de téléversement.</div>';
                        $file_upload_success = false;
                    }
                }
                
                if ($file_upload_success && !is_writable($upload_dir)) {
                    $message_ajout = '<div class="alert alert-danger" role="alert">Erreur: Le répertoire de téléversement n\'est pas inscriptible.</div>';
                    $file_upload_success = false;
                }

                if ($file_upload_success) {
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
                            $message_ajout = '<div class="alert alert-danger" role="alert">Erreur lors du déplacement du fichier téléversé. Vérifiez les permissions.</div>';
                            $file_upload_success = false;
                        }
                    } else {
                        $message_ajout = '<div class="alert alert-danger" role="alert">Type de fichier non autorisé ou ne correspond pas au type sélectionné.</div>';
                        $file_upload_success = false;
                    }
                }
            } else {
                 $message_ajout = '<div class="alert alert-danger" role="alert">Erreur lors du téléversement ou aucun fichier sélectionné. Code d\'erreur : ' . ($_FILES['resource_file']['error'] ?? 'N/A') . '</div>';
                 $file_upload_success = false;
            }
        }

        if ($file_upload_success && $cheminRelatif) {
            if ($actual_user instanceof Personne && ajouterRessource($titre, $type, $cheminRelatif, $coursId_form, $actual_user->getMatricule())) {
                $message_ajout = '<div class="alert alert-success" role="alert">Ressource ajoutée avec succès ! Elle est en attente de validation.</div>';
            } else {
                $message_ajout = '<div class="alert alert-danger" role="alert">Une erreur est survenue lors de l\'enregistrement dans la base de données.</div>';
            }
        }

    } else {
        $message_ajout = '<div class="alert alert-warning" role="alert">Veuillez remplir tous les champs du formulaire correctement et être connecté.</div>';
    }
}
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter une Ressource</title>
    <script src="http://localhost:5173/src/js/main.js" type="module"></script>
    <script src="http://localhost:5173/@vite/client" type="module"></script>
    <style>
      .hidden-form-field {
        display: none;
      }
    </style>
  </head>
  <body>
    <?php if (!empty($message_ajout)) echo $message_ajout; ?>
    
    <main>
      <div class="navbar navbar-expand-lg navbar-dark bg-dark p-3 w-100">
        <div class="container-fluid d-flex flex-column flex-lg-row align-items-center justify-content-between">
            <?php 
            include './page_builder/header.php'; 
            ?>
        </div>
      </div>

      <div class="container mt-4">
        <h2>Ajouter une nouvelle ressource</h2>
        <form action="ajout_ressource.php" method="POST" id="resourceForm" enctype="multipart/form-data">  
            <input type="hidden" name="ajouter_ressource" value="1">        
            
            <div class="mb-3">
                <label for="titre" class="form-label">Titre de la ressource</label>
                <input type="text" class="form-control" id="titre" name="titre" required value="<?= htmlspecialchars($_POST['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            
            <div class="mb-3">
                <label for="type" class="form-label">Type de ressource</label>
                <select class="form-select" id="type" name="type" required>
                    <option selected disabled value="">Choisir un type...</option>
                    <?php 
                    $selectedType = $_POST['type'] ?? '';
                    ?>
                    <option value="URL" <?= ($selectedType === 'URL') ? 'selected' : '' ?>>Lien Web (URL)</option>
                    <option value="PDF" <?= ($selectedType === 'PDF') ? 'selected' : '' ?>>Document PDF</option>
                    <option value="JPG" <?= ($selectedType === 'JPG') ? 'selected' : '' ?>>Image JPG</option>
                    <option value="PNG" <?= ($selectedType === 'PNG') ? 'selected' : '' ?>>Image PNG</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="coursId" class="form-label">Cours</label>
                <select class="form-select" id="coursId" name="coursId" required>
                    <option selected disabled value="">Choisir un cours...</option>
                    <?php 
                    $selectedCoursId = $_POST['coursId'] ?? '';
                    foreach($listeCoursDispo as $coursItem): 
                    ?>
                        <option value="<?= htmlspecialchars($coursItem->getId(), ENT_QUOTES) ?>" 
                            <?= ($selectedCoursId == $coursItem->getId()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($coursItem->getTitre(), ENT_QUOTES) ?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>

            <div class="mb-3 hidden-form-field" id="urlInputContainer">
                <label for="cheminRelatif" class="form-label">Lien (URL)</label>
                <input type="url" class="form-control" id="cheminRelatif" name="cheminRelatif" placeholder="https://example.com/ressource" value="<?= htmlspecialchars($_POST['cheminRelatif'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="mb-3 hidden-form-field" id="fileInputContainer">
                <label for="resource_file" class="form-label">Fichier</label>
                <input type="file" class="form-control" id="resource_file" name="resource_file">
            </div>

            <button type="submit" class="btn btn-primary">Soumettre la ressource</button>
        </form>
      </div>
    </main>

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
              fileInput.accept = '';

              if (selectedType === 'URL') {
                  urlInputContainer.classList.remove('hidden-form-field');
                  urlInput.required = true;
              } else if (selectedType === 'PDF' || selectedType === 'JPG' || selectedType === 'PNG') {
                  fileInputContainer.classList.remove('hidden-form-field');
                  fileInput.required = true;
                  fileInput.accept = '.' + selectedType.toLowerCase() + (selectedType === 'JPG' ? ',.jpeg' : ''); 
              }
          }
          typeSelect.addEventListener('change', toggleInputs);
          toggleInputs(); 
      });
    </script>
  </body>
</html>