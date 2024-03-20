<?php
$host = 'postgres';
$user = 'db_user';
$pass = 'db_password';
$db = 'test_database';


try {
    $conn = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to PostgreSQL successfully";
} catch (PDOException $e) {
    die("Connection failed " . $e->getMessage());
}

define('DB_CONNECTION', $conn);

// echo "Connected to PostgreSQL successfully";

// Your PHP code for PostgreSQL interactions goes here

// $conn = null;
