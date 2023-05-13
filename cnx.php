<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

$dsn = 'mysql:dbname=rober_app_bdd;host=localhost';
$login = 'root';
$motDePasse = '';

try {
	$cnx = new PDO(
		$dsn,
		$login,
		$motDePasse,
		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
	);
} catch (PDOException $e) {
	die('Erreur : ' . $e->getMessage());
}
