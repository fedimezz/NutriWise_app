<?php
// controllers/AlimentController.php
require_once 'models/AlimentModel.php';

if (!class_exists('AlimentController')) { // Sécurité supplémentaire
    class AlimentController {
        private $model;

        public function __construct() {
            $this->model = new AlimentModel();
        }

        // Liste pour l'administration (Back-office)
        public function listAliments() {
            if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
                header("Location: index.php?page=home");
                exit();
            }
            $alimentsList = $this->model->getAllAliments();
            require_once 'views/back/aliments.php';
        }

        // Liste pour les utilisateurs (Front-office)
        public function frontList() {
            if(!isset($_SESSION['user_id'])) {
                header("Location: index.php?page=login&error=Connectez-vous");
                exit();
            }
            $aliments = $this->model->getAllAliments();
            // Note: Assurez-vous que getAllCategories() existe dans votre modèle
            $categories = method_exists($this->model, 'getAllCategories') ? $this->model->getAllCategories() : [];
            require_once 'views/front/aliments.php';
        }

        public function details() {
            $id = $_GET['id'] ?? null;
            if (!$id) { header("Location: index.php?page=aliments"); exit(); }
            $aliment = $this->model->getAlimentById($id);
            require_once 'views/front/aliment_details.php';
        }

        public function addAliment() {
            if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
                header("Location: index.php?page=home");
                exit();
            }
            
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $success = $this->model->addAliment(
                    $_POST['nom'],
                    $_POST['category_id'], // Correspond à l'ID de catégorie
                    floatval($_POST['calories']),
                    floatval($_POST['proteins']),
                    floatval($_POST['glucides']),
                    floatval($_POST['lipids']),
                    floatval($_POST['eco_score'])
                );
                if($success) {
                    header("Location: index.php?page=admin_aliments&success=Ajouté");
                    exit;
                }
            }
            require_once 'views/back/add_aliment.php';
        }

        public function deleteAliment() {
            if(isset($_GET['id']) && $_SESSION['user_role'] == 'admin') {
                $this->model->deleteAliment(intval($_GET['id']));
            }
            header("Location: index.php?page=admin_aliments");
            exit;
        }

        public function editAliment() {
            // Logique de modification similaire à addAliment...
            // Assurez-vous d'appeler $this->model->updateAliment avec les bons paramètres
        }
    }
}