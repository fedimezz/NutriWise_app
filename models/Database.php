<?php
/**
 * Database connection class
 * 
 * Manages PDO database connection using Singleton pattern.
 * Ensures only one database connection is created throughout the application.
 * 
 * @package NutriWise
 * @subpackage Models
 */
class Database
{
    /** @var Database|null Singleton instance */
    private static ?Database $instance = null;

    /** @var PDO|null Database connection */
    private ?PDO $conn = null;

    /** @var string Database host */
    private string $host = "localhost";

    /** @var string Database name */
    private string $db_name = "nutriwise_db";

    /** @var string Database username */
    private string $username = "root";

    /** @var string Database password */
    private string $password = "";

    /**
     * Private constructor - prevents direct instantiation
     * 
     * Establishes PDO connection with error handling
     * Uses UTF-8 encoding and exception error mode
     */
    private function __construct()
    {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    /**
     * Get singleton instance of Database
     * 
     * Creates connection on first call, returns existing on subsequent calls
     * 
     * @return Database Singleton instance
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the PDO connection object
     * 
     * @return PDO The active database connection
     */
    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
?>