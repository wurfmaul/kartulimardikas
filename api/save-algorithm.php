<?php
require_once '../config.php';

$sql = new mysqli($db_host, $db_user, $db_password, $db_name);
if (!$sql->connect_errno) {
    $uid = 1; // TODO retrieve uid from cookie
    $name =

    $stmt = $sql->prepare("INSERT INTO algorithms(uid, name, description, variables, script) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("i", $uid);
    $stmt->bind_param("s", $name);
    $stmt->bind_param("s", $description);
    $stmt->bind_param("i", $variables);
    $stmt->bind_param("i", $script);
    $stmt->execute();
    $res = $stmt->get_result();

    header('Content-Type: text/plain');
    echo 'success';
}