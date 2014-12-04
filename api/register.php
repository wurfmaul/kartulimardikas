<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'config/config.php';

$response = array();

if (isset($_POST['username']) && !empty(trim($_POST['username']))) $username = trim($_POST['username']);
else $response['errorUsername'] = $l10n['enter_username'] . "<br />";
if (isset($_POST['email']) && !empty(trim($_POST['email']))) $email = trim($_POST['email']);
else $response['errorEmail'] = $l10n['enter_email'] . "<br />";
if (isset($_POST['password1']) && !empty(trim($_POST['password1']))) $password1 = $_POST['password1'];
else $response['errorPassword1'] = $l10n['enter_password'] . "<br />";
if (isset($_POST['password2']) && !empty(trim($_POST['password2']))) $password2 = $_POST['password2'];
else $response['errorPassword2'] = $l10n['repeat_password'] . "<br />";

$username = "wurfmaul";
$email = "wurfmaul";
$password1 = "oberdepp";
$password2 = "oberdepp";

// if every field has been filled:
if (isset($username, $email, $password1, $password2)) {
    require_once BASEDIR . 'includes/dataModel.php';
    $model = new DataModel();

    $username = htmlentities($username);
    $email = htmlentities($email); // TODO: check for valid mail address!

    // check if username is unique
    $result = $model->fetchUserByUsername($username);
    if ($result) {
        $response['errorUsername'] = sprintf($l10n['name_in_use'], $username) . "<br />";
        unset($username);
    }
    // check if email address is unique
    $result = $model->fetchUserByMail($email);
    if ($result) {
        $response['errorEmail'] = sprintf($l10n['email_in_use'], $email) . "<br />";
        unset($email);
    }
    // check if password is sufficiently long
    if (strlen($password1) < 6) {
        $response['errorPassword1'] = $l10n['weak_password'] . "<br />";
        unset($password1);
    } elseif ($password1 != $password2) {
        // check if passwords are the same
        $response['errorPassword2'] = $l10n['passwords_no_match'] . "<br />";
        unset($password2);
    }

    // if all the checks passed -> register user!
    if (isset($username, $email, $password1, $password2)) {
        $hash = password_hash($password1, PASSWORD_BCRYPT);
        $result = $model->insertUser($username, $email, $hash);
        if ($result > 0)
            $response['success'] = sprintf($l10n['user_created'], $username);
        else
            $response['error'] = $l10n['user_not_created'];
    }

    $model->close();
}

echo json_encode($response);