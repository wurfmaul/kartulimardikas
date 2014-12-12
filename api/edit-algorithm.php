<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'config/config.php';

// in order to retrieve the uid we need access to the session
require_once BASEDIR . 'includes/authentication.php';
secure_session_start();

// prepare response
$response = array();

if (isSignedIn()) {
    // prepare variables
    $uid = $_SESSION['uid'];
    $aid = isset($_POST['aid']) ? $_POST['aid'] : -1;
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $desc = isset($_POST['title']) ? $_POST['title'] : null;
    $long = isset($_POST['desc']) ? $_POST['desc'] : null;
    $variables = isset($_POST['vars']) ? $_POST['vars'] : null;
    $script = isset($_POST['script']) ? $_POST['script'] : null;

    // for database actions
    require_once BASEDIR . 'includes/dataModel.php';
    $model = new DataModel();

    // TODO check and pre-process variables and script

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

    $model->close();
} else {
    $response['error'] = $l10n['edit_not_signed_in'];
}

echo json_encode($response);
