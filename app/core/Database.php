<?php

class Database
{
    private $conn;

    public function __construct()
    {
        $this->conn = $this->setConnection();
    }

    public function getConnection()
    {
        return $this->conn;
    }

    protected function setConnection()
    {
        try {
            $host = 'postgres';
            $user = 'db_user';
            $pass = 'db_password';
            $db = 'test_database';


            $conn = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function query($query, $params = [])
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
