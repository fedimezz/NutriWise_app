<?php
class Recette {
    private $conn;
    private $table_name = "recettes";

    public $id;
    public $title;
    public $description;
    public $instructions;
    public $duree;
    public $difficulte;
    public $saison;
    public $statut;
    public $date_publication;
    public $score_durabilite;
    public $user_id; // Pour permettre aux users de créer des recettes

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Récupérer les recettes avec les aliments associés
    public function readAllWithAliments() {
        $query = "SELECT r.*, 
                         GROUP_CONCAT(a.nom SEPARATOR ', ') as aliments_list
                  FROM " . $this->table_name . " r
                  LEFT JOIN recette_aliments ra ON r.id = ra.recette_id
                  LEFT JOIN aliments a ON ra.aliment_id = a.id
                  GROUP BY r.id
                  ORDER BY r.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function search($keyword) {
        $keyword = '%' . $keyword . '%';
        $query = "SELECT r.*, 
                         GROUP_CONCAT(a.nom SEPARATOR ', ') as aliments_list
                  FROM " . $this->table_name . " r
                  LEFT JOIN recette_aliments ra ON r.id = ra.recette_id
                  LEFT JOIN aliments a ON ra.aliment_id = a.id
                  WHERE r.title LIKE :keyword OR r.description LIKE :keyword
                  GROUP BY r.id
                  ORDER BY r.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":keyword", $keyword);
        $stmt->execute();
        return $stmt;
    }

    public function readPublished() {
        $query = "SELECT r.*, 
                         GROUP_CONCAT(a.nom SEPARATOR ', ') as aliments_list
                  FROM " . $this->table_name . " r
                  LEFT JOIN recette_aliments ra ON r.id = ra.recette_id
                  LEFT JOIN aliments a ON ra.aliment_id = a.id
                  WHERE r.statut = 'Programmée'
                  AND r.date_publication IS NOT NULL
                  AND r.date_publication <= NOW()
                  GROUP BY r.id
                  ORDER BY r.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT r.*, 
                         GROUP_CONCAT(ra.aliment_id) as aliments_ids,
                         GROUP_CONCAT(a.nom SEPARATOR ', ') as aliments_list
                  FROM " . $this->table_name . " r
                  LEFT JOIN recette_aliments ra ON r.id = ra.recette_id
                  LEFT JOIN aliments a ON ra.aliment_id = a.id
                  WHERE r.id = ?
                  GROUP BY r.id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validate($data) {
        $errors = [];
        
        // Validation du titre
        if(empty($data['title'])) {
            $errors[] = "Le titre est requis";
        } elseif(strlen($data['title']) < 3) {
            $errors[] = "Le titre doit contenir au moins 3 caractères";
        } elseif(strlen($data['title']) > 30) {
            $errors[] = "Le titre ne doit pas dépasser 30 caractères";
        } elseif(!preg_match("/^[a-zA-ZÀ-ÿ0-9\s\-éèêëïîôûç'’]+$/", $data['title'])) {
            $errors[] = "Le titre contient des caractères non autorisés";
        }
        
        // Validation de la description
        if(!empty($data['description'])) {
            if(strlen($data['description']) > 1000) {
                $errors[] = "La description ne doit pas dépasser 1000 caractères";
            }
        }
        
        // Validation des instructions
        if(empty($data['instructions'])) {
            $errors[] = "Les instructions sont requises";
        } elseif(strlen($data['instructions']) < 20) {
            $errors[] = "Les instructions doivent contenir au moins 20 caractères";
        } elseif(strlen($data['instructions']) > 5000) {
            $errors[] = "Les instructions ne doivent pas dépasser 5000 caractères";
        }
        
        // Validation de la durée
        if(isset($data['duree']) && !empty($data['duree'])) {
            if(!is_numeric($data['duree'])) {
                $errors[] = "La durée doit être un nombre";
            } elseif($data['duree'] < 1) {
                $errors[] = "La durée doit être d'au moins 1 minute";
            } elseif($data['duree'] > 120) {
                $errors[] = "La durée ne peut pas dépasser 120 minutes (2 heures)";
            }
        }
        
        // Validation des aliments sélectionnés
        if(empty($data['aliments']) || count($data['aliments']) < 1) {
            $errors[] = "Veuillez sélectionner au moins un aliment pour cette recette";
        }
        
        return $errors;
    }

    public function create() {
        $errors = $this->validate($_POST);
        if(!empty($errors)) return ["success" => false, "errors" => $errors];

        try {
            $this->conn->beginTransaction();
            
            $query = "INSERT INTO " . $this->table_name . "
                      SET title=:title, description=:description, instructions=:instructions,
                          duree=:duree, difficulte=:difficulte, saison=:saison,
                          statut=:statut, date_publication=:date_publication, score_durabilite=:score_durabilite";
            if(isset($this->user_id)) {
                $query .= ", user_id=:user_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":instructions", $this->instructions);
            $stmt->bindParam(":duree", $this->duree);
            $stmt->bindParam(":difficulte", $this->difficulte);
            $stmt->bindParam(":saison", $this->saison);
            $stmt->bindParam(":statut", $this->statut);
            $stmt->bindParam(":date_publication", $this->date_publication);
            $stmt->bindParam(":score_durabilite", $this->score_durabilite);
            if(isset($this->user_id)) {
                $stmt->bindParam(":user_id", $this->user_id);
            }
            
            if(!$stmt->execute()) {
                throw new Exception("Erreur lors de la création de la recette");
            }
            
            $recette_id = $this->conn->lastInsertId();
            
            // Insertion des aliments associés
            if(isset($_POST['aliments']) && is_array($_POST['aliments'])) {
                $query_aliment = "INSERT INTO recette_aliments (recette_id, aliment_id) VALUES (:recette_id, :aliment_id)";
                $stmt_aliment = $this->conn->prepare($query_aliment);
                
                foreach($_POST['aliments'] as $aliment_id) {
                    $stmt_aliment->bindParam(":recette_id", $recette_id);
                    $stmt_aliment->bindParam(":aliment_id", $aliment_id);
                    if(!$stmt_aliment->execute()) {
                        throw new Exception("Erreur lors de l'association des aliments");
                    }
                }
            }
            
            $this->conn->commit();
            return ["success" => true];
            
        } catch(Exception $e) {
            $this->conn->rollBack();
            return ["success" => false, "errors" => [$e->getMessage()]];
        }
    }

    public function update() {
        $errors = $this->validate($_POST);
        if(!empty($errors)) return ["success" => false, "errors" => $errors];

        try {
            $this->conn->beginTransaction();
            
            $query = "UPDATE " . $this->table_name . "
                      SET title=:title, description=:description, instructions=:instructions,
                          duree=:duree, difficulte=:difficulte, saison=:saison,
                          statut=:statut, date_publication=:date_publication, score_durabilite=:score_durabilite
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":instructions", $this->instructions);
            $stmt->bindParam(":duree", $this->duree);
            $stmt->bindParam(":difficulte", $this->difficulte);
            $stmt->bindParam(":saison", $this->saison);
            $stmt->bindParam(":statut", $this->statut);
            $stmt->bindParam(":date_publication", $this->date_publication);
            $stmt->bindParam(":score_durabilite", $this->score_durabilite);
            
            if(!$stmt->execute()) {
                throw new Exception("Erreur lors de la mise à jour de la recette");
            }
            
            // Supprimer les anciennes associations
            $query_delete = "DELETE FROM recette_aliments WHERE recette_id = :recette_id";
            $stmt_delete = $this->conn->prepare($query_delete);
            $stmt_delete->bindParam(":recette_id", $this->id);
            $stmt_delete->execute();
            
            // Insertion des nouvelles associations
            if(isset($_POST['aliments']) && is_array($_POST['aliments'])) {
                $query_aliment = "INSERT INTO recette_aliments (recette_id, aliment_id) VALUES (:recette_id, :aliment_id)";
                $stmt_aliment = $this->conn->prepare($query_aliment);
                
                foreach($_POST['aliments'] as $aliment_id) {
                    $stmt_aliment->bindParam(":recette_id", $this->id);
                    $stmt_aliment->bindParam(":aliment_id", $aliment_id);
                    if(!$stmt_aliment->execute()) {
                        throw new Exception("Erreur lors de l'association des aliments");
                    }
                }
            }
            
            $this->conn->commit();
            return ["success" => true];
            
        } catch(Exception $e) {
            $this->conn->rollBack();
            return ["success" => false, "errors" => [$e->getMessage()]];
        }
    }

    public function delete() {
        try {
            $this->conn->beginTransaction();
            
            // Supprimer les associations
            $query_delete = "DELETE FROM recette_aliments WHERE recette_id = :recette_id";
            $stmt_delete = $this->conn->prepare($query_delete);
            $stmt_delete->bindParam(":recette_id", $this->id);
            $stmt_delete->execute();
            
            // Supprimer la recette
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            $result = $stmt->execute();
            
            $this->conn->commit();
            return $result;
        } catch(Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>