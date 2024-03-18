<?php
$host = 'postgres';
$user = 'db_user';
$pass = 'db_password';
$db = 'test_database';


try {
    $conn = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed " . $e->getMessage());
}

define('connection', $conn);

// echo "Connected to PostgreSQL successfully";

// Your PHP code for PostgreSQL interactions goes here

// $conn = null;
