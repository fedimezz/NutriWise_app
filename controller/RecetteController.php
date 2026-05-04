<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/Recette.php';
require_once __DIR__ . '/../model/Aliment.php';

class RecetteController {
    private $db;
    private $recette;
    private $aliment;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->recette = new Recette($this->db);
        $this->aliment = new Aliment($this->db);
    }

    public function index($area = 'front') {
        $keyword = $_GET['search'] ?? '';
        if($area == 'back') {
            if(!empty($keyword)) {
                $stmt = $this->recette->search($keyword);
            } else {
                $stmt = $this->recette->readAllWithAliments();
            }
            $recettes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            require_once __DIR__ . '/../view/backoffice_recettes_index.php';
        } else {
            if(!empty($keyword)) {
                $stmt = $this->recette->search($keyword);
                $recettes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $stmt = $this->recette->readPublished();
                $recettes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            require_once __DIR__ . '/../view/frontoffice_recettes_index.php';
        }
    }

    public function create() {
        $aliments = $this->aliment->getAllForSelect();
        require_once __DIR__ . '/../view/backoffice_recettes_create.php';
    }
    
    public function createUser() {
        $aliments = $this->aliment->getAllForSelect();
        require_once __DIR__ . '/../view/frontoffice_recettes_create.php';
    }

    public function store() {
        session_start();
        $this->recette->title = $_POST['title'];
        $this->recette->description = $_POST['description'];
        $this->recette->instructions = $_POST['instructions'];
        $this->recette->duree = $_POST['duree'];
        $this->recette->difficulte = $_POST['difficulte'];
        $this->recette->saison = $_POST['saison'];
        $this->recette->statut = $_POST['statut'];
        $this->recette->score_durabilite = $_POST['score_durabilite'];
        
        // Gestion de la date de publication
        if($_POST['statut'] == 'Programmée' && !empty($_POST['date_publication'])) {
            $this->recette->date_publication = str_replace('T', ' ', $_POST['date_publication']) . ':00';
        } else {
            $this->recette->date_publication = null;
        }
        
        if(isset($_SESSION['user_id'])) {
            $this->recette->user_id = $_SESSION['user_id'];
        }

        $result = $this->recette->create();
        if($result['success']) {
            $area = $_POST['area'] ?? 'back';
            if($area == 'front') {
                header("Location: index.php?controller=recette&action=index&area=front&success=created");
            } else {
                header("Location: index.php?controller=recette&action=index&area=back&success=created");
            }
        } else {
            $_SESSION['errors'] = $result['errors'];
            $area = $_POST['area'] ?? 'back';
            if($area == 'front') {
                header("Location: index.php?controller=recette&action=createUser&area=front");
            } else {
                header("Location: index.php?controller=recette&action=create&area=back");
            }
        }
    }

    public function edit($id) {
        $this->recette->id = $id;
        $recette = $this->recette->readOne();
        $aliments = $this->aliment->getAllForSelect();
        $selected_aliments = $recette['aliments_ids'] ? explode(',', $recette['aliments_ids']) : [];
        require_once __DIR__ . '/../view/backoffice_recettes_edit.php';
    }

    public function update($id) {
        session_start();
        $this->recette->id = $id;
        $this->recette->title = $_POST['title'];
        $this->recette->description = $_POST['description'];
        $this->recette->instructions = $_POST['instructions'];
        $this->recette->duree = $_POST['duree'];
        $this->recette->difficulte = $_POST['difficulte'];
        $this->recette->saison = $_POST['saison'];
        $this->recette->statut = $_POST['statut'];
        $this->recette->score_durabilite = $_POST['score_durabilite'];
        
        // Gestion de la date de publication
        if($_POST['statut'] == 'Programmée' && !empty($_POST['date_publication'])) {
            $this->recette->date_publication = str_replace('T', ' ', $_POST['date_publication']) . ':00';
        } else {
            $this->recette->date_publication = null;
        }

        $result = $this->recette->update();
        if($result['success']) {
            header("Location: index.php?controller=recette&action=index&area=back&success=updated");
        } else {
            $_SESSION['errors'] = $result['errors'];
            header("Location: index.php?controller=recette&action=edit&id=" . $id . "&area=back");
        }
    }

    public function delete($id) {
        $this->recette->id = $id;
        $this->recette->delete();
        header("Location: index.php?controller=recette&action=index&area=back&success=deleted");
    }

    public function show($id) {
        $this->recette->id = $id;
        $recette = $this->recette->readOne();
        require_once __DIR__ . '/../view/frontoffice_recettes_show.php';
    }
}
?>