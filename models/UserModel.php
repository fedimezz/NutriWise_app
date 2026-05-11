<?php
// models/UserModel.php
require_once 'Database.php';

class UserModel {
    private $conn;
    private $table = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Inscription
    public function register($prenom, $nom, $email, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table . " (prenom, nom, email, password, role, statut) 
                  VALUES (:prenom, :nom, :email, :password, 'user', 'actif')";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':prenom' => htmlspecialchars($prenom),
            ':nom' => htmlspecialchars($nom),
            ':email' => htmlspecialchars($email),
            ':password' => $hash
        ]);
    }

    // Créer un utilisateur (admin)
    public function createUser($prenom, $nom, $email, $password, $role = 'user') {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table . " (prenom, nom, email, password, role, statut) 
                  VALUES (:prenom, :nom, :email, :password, :role, 'actif')";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':prenom' => htmlspecialchars($prenom),
            ':nom' => htmlspecialchars($nom),
            ':email' => htmlspecialchars($email),
            ':password' => $hash,
            ':role' => $role
        ]);
    }

    // Connexion
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email AND statut = 'actif'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }

    // Récupérer un utilisateur par ID
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour le profil
    public function updateProfile($id, $prenom, $nom, $email, $telephone, $taille, $poids, $objectif) {
        $imc = null;
        if($taille > 0 && $poids > 0) {
            $tailleM = $taille / 100;
            $imc = round($poids / ($tailleM * $tailleM), 1);
        }

        $query = "UPDATE " . $this->table . " 
                  SET prenom = :prenom, nom = :nom, email = :email, 
                      telephone = :telephone, taille = :taille, poids = :poids, 
                      imc = :imc, objectif = :objectif 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':prenom' => $prenom, 
            ':nom' => $nom, 
            ':email' => $email, 
            ':telephone' => $telephone, 
            ':taille' => $taille, 
            ':poids' => $poids, 
            ':imc' => $imc, 
            ':objectif' => $objectif, 
            ':id' => $id
        ]);
    }

    // Mettre à jour un utilisateur (admin)
    public function updateUser($id, $prenom, $nom, $email, $role, $statut) {
        $query = "UPDATE " . $this->table . " 
                  SET prenom = :prenom, nom = :nom, email = :email, role = :role, statut = :statut 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':email' => $email,
            ':role' => $role,
            ':statut' => $statut,
            ':id' => $id
        ]);
    }

    // Supprimer un utilisateur
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // Récupérer tous les utilisateurs
    public function getAllUsers() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Compter les utilisateurs
    public function countUsers() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>