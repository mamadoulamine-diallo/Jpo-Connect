<?php
require_once __DIR__ . '/../config/database.php';

class JPO
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lister toutes les JPO
    public function getAll()
    {
        $query = "SELECT * FROM jpos ORDER BY date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter une JPO (seulement pour directeur ou responsable)
    public function add($title, $date, $location, $capacity, $description, $user_role)
    {
        if ($user_role !== 'director' && $user_role !== 'manager') {
            return ['error' => 'Permission refusée'];
        }

        $query = "INSERT INTO jpos (title, date, location, capacity, description) VALUES (:title, :date, :location, :capacity, :description)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            return ['success' => 'JPO ajoutée avec succès'];
        } else {
            return ['error' => 'Erreur lors de l\'ajout'];
        }
    }

    // Modifier une JPO
    public function update($id, $title, $date, $location, $capacity, $description, $user_role)
    {
        if ($user_role !== 'director' && $user_role !== 'manager') {
            return ['error' => 'Permission refusée'];
        }

        $query = "UPDATE jpos SET title = :title, date = :date, location = :location, capacity = :capacity, description = :description WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            return ['success' => 'JPO modifiée avec succès'];
        } else {
            return ['error' => 'Erreur lors de la modification'];
        }
    }

    // Supprimer une JPO
    public function delete($id, $user_role)
    {
        if ($user_role !== 'director') {
            return ['error' => 'Permission refusée'];
        }

        $query = "DELETE FROM jpos WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return ['success' => 'JPO supprimée avec succès'];
        } else {
            return ['error' => 'Erreur lors de la suppression'];
        }
    }
}
