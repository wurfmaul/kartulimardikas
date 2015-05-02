<?php
define('BASEDIR', realpath('..') . '/');

require_once BASEDIR . 'config/config.php';
require_once BASEDIR . 'includes/dataModel.php';
require_once BASEDIR . 'includes/validator.php';

$response = array();
$validator = new Validator();
$username = $validator->checkUserName($_POST['username'], $response);
$email = $validator->checkEmailAddress($_POST['email'], $response);
$password = $validator->checkPasswords($_POST['password1'], $_POST['password2'], $response);

// if all the checks passed -> register user!
if ($username && $email && $password) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $model = new DataModel();
    if ($model->insertUser($username, $email, $hash)) {
        $response['success'] = sprintf($l10n['user_created'], $username);
    } else {
        $response['error'] = $l10n['user_not_created'];
    }
    $model->close();
}

header('Content-type: application/json; charset=UTF-8');
echo json_encode($response);