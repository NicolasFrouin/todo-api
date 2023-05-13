<?php

require_once __DIR__ . "/cnx.php";

$return = ["error" => true];

switch ($_SERVER["REQUEST_METHOD"]) {
	case "GET":
		$sql = $cnx->prepare("SELECT id_type, nom, couleur, ref_type_parent FROM type;");
		$sql->execute();
		$return = $sql->fetchAll(PDO::FETCH_ASSOC);
		break;

	case "POST":
		$sql = $cnx->prepare("INSERT INTO type (nom, couleur, ref_type_parent) VALUES (:nom, :couleur, :ref_type_parent)");
		$sql->bindParam(":nom", $_POST["nom"]);
		$sql->bindParam(":couleur", $_POST["couleur"]);
		$sql->bindParam(":ref_type_parent", empty($_POST["parentType"]) ? null : $_POST["parentType"]);
		$return = $sql->execute();
		break;

	case "DELETE":
		parse_str(file_get_contents('php://input'), $deleteData);
		if (empty($deleteData["id"]) || !is_numeric($deleteData["id"]) || intval($deleteData["id"]) < 1) {
			$return["message"] = "bad id field value";
			break;
		}
		$sql = $cnx->prepare("DELETE FROM type WHERE id_type = :id_type;");
		$sql->bindParam(":id_type", $deleteData["id"]);
		try {
			$return = $sql->execute();
		} catch (\Throwable $th) {
			$return["message"] = "Impossible de supprimer un type possédant un enfant";
		}
		break;

	default:
		break;
}

$return = json_encode($return);

die($return);
