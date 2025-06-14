<?php
class Database
{
  private static $instance = null;
  private $connection;

  private $host = 'localhost';
  private $dbname = 'jpo_platform';
  private $username = 'root';
  private $password = 'root';

  private function __construct()
  {
    try {
      $this->connection = new PDO(
        "mysql:host=$this->host;dbname=$this->dbname;charset=utf8",
        $this->username,
        $this->password
      );
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      die('Erreur de connexion : ' . $e->getMessage());
    }
  }

  public static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new Database();
    }
    return self::$instance;
  }

  public function getConnection()
  {
    return $this->connection;
  }
}
