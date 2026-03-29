<?php
$host = 'localhost';
$dbname = 'overclock_tech';
$username = 'root';
// $password = 'IfkingluvSIT12629!@#$'; // leave blank for XAMPP
$password = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}
