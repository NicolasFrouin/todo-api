<?php

require_once __DIR__ . "/cnx.php";

$return = ["error" => true];

switch ($_SERVER["REQUEST_METHOD"]) {
	case "GET":
		$sql = $cnx->prepare("SELECT id_liste, nom, description FROM liste;");
		$sql->execute();
		$return = $sql->fetchAll(PDO::FETCH_ASSOC);
		break;

	case "POST":
		$sql = $cnx->prepare(
			"INSERT INTO liste (nom, description) VALUES (:nom, :description)"
		);
		$sql->bindParam(':nom', $_POST['nom']);
		$sql->bindParam(':description', $_POST['description']);
		$return = $sql->execute();
		break;

	case "DELETE":
		parse_str(file_get_contents('php://input'), $deleteData);
		if (empty($deleteData["id_liste"]) || !is_numeric($deleteData["id_liste"]) || intval($deleteData["id_liste"]) < 1) {
			$return["message"] = "bad id_liste field value";
			break;
		}
		$sql = $cnx->prepare(
			"DELETE FROM tache WHERE ref_liste = :id_liste; DELETE FROM liste WHERE id_liste = :id_liste;"
		);
		$sql->bindParam(":id_liste", $deleteData["id_liste"]);
		$return = $sql->execute();
		break;

	case "PUT":
		parse_str(file_get_contents('php://input'), $putData);
		$tmpSql = "UPDATE liste SET";
		foreach ($putData["data"] as $field => $value) {
			$tmpSql .= " $field = :$field";
		}
		$tmpSql .= " WHERE id_liste = :key";
		$sql = $cnx->prepare($tmpSql);
		foreach ($putData["data"] as $field => $value) {
			$sql->bindParam(":$field", $putData["data"][$field]);
		}
		$sql->bindParam(":key", $putData["key"]);
		$return = $sql->execute();
		break;
}

$return = json_encode($return);

die($return);
