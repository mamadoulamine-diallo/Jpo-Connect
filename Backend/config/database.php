<?php
class Database
{
    private $host = "localhost"; // Adresse du serveur (ici, local)
    private $dbname = "jpo_platform"; // Nom de la base de données
    private $username = "root"; // Nom d'utilisateur (à changer selon ton serveur)
    private $password = "root"; // Mot de passe (à changer selon ton serveur)
    private $conn = null;

    // Fonction pour se connecter à la base de données
    public function getConnection()
    {
        try {
            // On essaie de se connecter avec PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8",
                $this->username,
                $this->password
            );
            // On configure PDO pour qu'il affiche les erreurs
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            // Si ça ne marche pas, on affiche une erreur
            echo "Erreur de connexion : " . $e->getMessage();
            return null;
        }
    }
}
