<?php

class Database
{
    private $pdo;

    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'jpo_connect';
        $username = 'root';
        $password = 'root';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // En cas d’erreur, on renvoie un message clair
            http_response_code(500);
            echo json_encode(['error' => 'Erreur de connexion à la base : ' . $e->getMessage()]);
            exit;
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
