<?php

require_once __DIR__ . "/cnx.php";

$return = ["error" => true];

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $sql = $cnx->prepare(
            "SELECT ta.id_tache, ta.nom AS nom_tache, ta.description AS tache_desc, ty.nom AS nom_type, ty.couleur, ta.niveau 
            FROM tache ta
            INNER JOIN liste l ON l.id_liste = ta.ref_liste 
            INNER JOIN type ty ON ty.id_type = ta.ref_type 
            WHERE l.id_liste = :id_liste;"
        );
        $sql->bindParam(':id_liste', $_GET['id_liste']);
        $sql->execute();
        $return = $sql->fetchAll(PDO::FETCH_ASSOC);
        break;

    case "POST":
        $sql = $cnx->prepare(
            "INSERT INTO tache (nom, description, niveau, ref_liste, ref_type) VALUES (:nom, :description, :niveau, :ref_liste, :ref_type);"
        );
        $sql->bindParam(':nom', $_POST['nom']);
        $sql->bindParam(':description', $_POST['description']);
        $sql->bindParam(':niveau', $_POST['niveau']);
        $sql->bindParam(':ref_liste', $_POST['ref_liste']);
        $sql->bindParam(':ref_type', $_POST['ref_type']);
        $return = $sql->execute();
        break;

    case "DELETE":
        parse_str(file_get_contents('php://input'), $deleteData);
        if (empty($deleteData["id_tache"]) || !is_numeric($deleteData["id_tache"]) || intval($deleteData["id_tache"]) < 1) {
            $return["message"] = "bad id_tache field value";
            break;
        }
        $sql = $cnx->prepare("DELETE FROM tache WHERE id_tache = :id_tache;");
        $sql->bindParam(":id_tache", $deleteData["id_tache"]);
        $return = $sql->execute();
        break;

    case "PUT":
        parse_str(file_get_contents('php://input'), $putData);
        $tmpSql = "UPDATE tache SET";
        foreach ($putData["data"] as $field => $value) {
            $tmpSql .= " $field = :$field";
        }
        $tmpSql .= " WHERE id_tache = :key";
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
