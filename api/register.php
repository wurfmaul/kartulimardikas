<?php
define('BASEDIR', realpath('..') . '/');
define('BR', "<br />");

require_once BASEDIR . 'config/config.php';
require_once BASEDIR . 'includes/dataModel.php';
$model = new DataModel();

$response = array();

$username = isset($_POST['username']) && !empty(trim($_POST['username'])) ? $_POST['username'] : false;
$email = isset($_POST['email']) && !empty(trim($_POST['email'])) ? $_POST['email'] : false;
$password1 = isset($_POST['password1']) && !empty(trim($_POST['password1'])) ? $_POST['password1'] : false;
$password2 = isset($_POST['password2']) && !empty(trim($_POST['password2'])) ? $_POST['password2'] : false;

// check if username was entered
if (!$username) {
    $response['error-username'] = $l10n['enter_username'] . BR;
} // check if username is valid
elseif (!preg_match('/^(\w|-)*$/', $username)) {
    $allowed = "a-z A-Z 0-9 - _";
    $response['error-username'] = sprintf($l10n['name_invalid'], $allowed) . BR;
} // check if username is unique
elseif ($model->fetchUserByUsername($username)) {
    $response['error-username'] = sprintf($l10n['name_in_use'], $username) . BR;
    $username = false;
}

// check if email address was entered
if (!$email) {
    $response['error-email'] = $l10n['enter_email'] . BR;
} // check if email address is valid
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['error-email'] = $l10n['email_invalid'] . BR;
    $email = false;
} // check if email address is unique
elseif ($model->fetchUserByMail($email)) {
    $response['error-email'] = sprintf($l10n['email_in_use'], $email) . BR;
    $email = false;
}

// check if first password was entered
if (!$password1) {
    $response['error-password1'] = $l10n['enter_password'] . BR;
}
// check if second password was entered
if (!$password2) {
    $response['error-password2'] = $l10n['repeat_password'] . BR;
} // check if password is sufficiently long
elseif (strlen($password1) < 6) {
    $response['error-password1'] = $l10n['weak_password'] . BR;
    $password1 = false;
} // check if the two passwords match
elseif ($password1 !== $password2) {
    $response['error-password2'] = $l10n['passwords_no_match'] . BR;
    $password2 = false;
}

// if all the checks passed -> register user!
if ($username && $email && $password1 && $password2) {
    $hash = password_hash($password1, PASSWORD_BCRYPT);
    $result = $model->insertUser($username, $email, $hash);
    if ($result > 0)
        $response['success'] = sprintf($l10n['user_created'], $username);
    else
        $response['error'] = $l10n['user_not_created'];
}

$model->close();

header('Content-type: application/json; charset=UTF-8');
echo json_encode($response);