<?php

class Database
{
    private $conn;
    private $tableName;
    private $column = [];

    public function __construct()
    {
        $this->conn = $this->setConnection();
    }
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function setColumn($column)
    {
        $this->column = $column;
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

    public function get($params = [])
    {
        $column = implode(', ', $this->column);
        $query = "SELECT $column FROM {$this->tableName}";
        $paramValue = [];
        if (!empty($params)) {
            $query .= " WHERE 1=1 ";
            foreach ($params as $key => $value) {
                $query .= "AND {$key} = ? ";
                array_push($paramValue, $value);
            }
        }
        return $this->query($query, $paramValue);
    }

    public function insertData($data = [])
    {
        if (empty($data)) {
            return false;
        }
        $columnValue = [];
        $kolom = [];
        $param = [];
        foreach ($data as $key => $value) {
            array_push($kolom, $key);
            array_push($columnValue, $value);
            array_push($param, "?");
        }
        $kolom = implode(', ', $kolom);
        $param = implode(', ', $param);
        $query = "INSERT INTO {$this->tableName} ($kolom) VALUES ($param)";
        return $this->query($query, $columnValue);
    }

    public function updateData($data = [], $param = [])
    {
        if (empty($data)) {
            return false;
        }
        $columnValue = [];
        $kolom = [];
        $query = "UPDATE {$this->tableName} ";
        foreach ($data as $key => $value) {
            array_push($kolom, $key . " = ?");
            array_push($columnValue, $value);
        }
        $kolom = implode(', ', $kolom);
        $query = $query . "SET $kolom WHERE 1=1 ";
        $whereColumn = [];
        foreach ($param as $key => $value) {
            array_push($whereColumn, "AND {$key} = ?");
            array_push($columnValue, $value);
        }
        $whereColumn = implode(', ', $whereColumn);
        $query = $query . $whereColumn;
        return $this->query($query, $columnValue);
    }

    public function deleteData($param = [])
    {
        if (empty($param)) {
            return false;
        }
        $query = "DELETE FROM {$this->tableName} WHERE 1=1 ";
        $whereColumn = [];
        $columnValue = [];
        foreach ($param as $key => $value) {
            array_push($whereColumn, "AND {$key} = ?");
            array_push($columnValue, $value);
        }
        $whereColumn = implode(', ', $whereColumn);
        $query = $query . $whereColumn;
        return $this->query($query, $columnValue);
    }
}
