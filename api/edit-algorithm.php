<?php
require_once '../includes/dataModel.php';

// prepare variables
$aid = base64_decode($_GET['aid']);
$uid = 1; // TODO retrieve uid from cookie
$name = base64_decode($_GET['name']);
$desc = base64_decode($_GET['desc']);
$long = base64_decode($_GET['long']);
$variables = ""; // TODO format and insert blubblub
$script = "";

// prepare and execute statement
// @var $sql mysqli defined in dataModel.php
$stmt = $sql->stmt_init();
if ($aid == -1) { // create new entry
    $stmt = $sql->prepare("INSERT INTO algorithms (uid, name, description, long_description, variables, script) VALUES (?, ?, ?, ?, ?, ?)");
} else { // update entry
    $stmt = $sql->prepare("UPDATE algorithms SET uid=?, name=?, description=?, long_description=?, variables=?, script=? WHERE aid=$aid");
}
$stmt->bind_param("isssbb", $uid, $name, $desc, $long, $variables, $script);
$stmt->execute();

// tidy up
$stmt->close();

// print 0 in case of success.
header('Content-Type: text/plain');
echo 0;