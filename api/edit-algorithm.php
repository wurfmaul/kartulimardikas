<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'config/config.php';

// in order to retrieve the uid we need access to the session
require_once BASEDIR . 'includes/authentication.php';
secure_session_start();

// for database actions
require_once BASEDIR . 'includes/dataModel.php';
$model = new DataModel();

// prepare variables
$aid = isset($_POST['aid']) ? $_POST['aid'] : -1;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$desc = isset($_POST['title']) ? $_POST['title'] : null;
$long = isset($_POST['desc']) ? $_POST['desc'] : null;
$variables = ""; // TODO format and insert blubblub
$script = "";

// prepare response
$response = array();

if (isSignedIn()) {
    $uid = $_SESSION['uid'];

    if ($aid == -1) { // create new entry
        $aid = $model->insertAlgorithm($uid, $name, $desc, $long, $variables, $script);
        $response['aid'] = $aid;
        $response['success'] = $l10n['saved_to_db'];
    } else { // update entry
        $owner = $model->fetchAlgorithmByAID($aid)->uid;
        if ($owner != $uid) {
            $response['error'] = $l10n['need_to_be_owner'];
        } else {
            $model->updateAlgorithm($aid, $uid, $name, $desc, $long, $variables, $script);
            $response['success'] = $l10n['saved_to_db'];
        }
    }
} else {
    $response['error'] = $l10n['edit_not_signed_in'];
}

$model->close();
echo json_encode($response);
