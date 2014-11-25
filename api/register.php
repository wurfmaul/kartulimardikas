<?php

$response = array();

if (isset($_POST['username']) && !empty(trim($_POST['username']))) $username = trim($_POST['username']);
else $response['errorUsername'] = "Please enter a username!<br />";
if (isset($_POST['email']) && !empty(trim($_POST['email']))) $email = trim($_POST['email']);
else $response['errorEmail'] = "Please enter a valid email address!<br />";
if (isset($_POST['password1']) && !empty(trim($_POST['password1']))) $password1 = $_POST['password1'];
else $response['errorPassword1'] = "Please enter a password!<br />";
if (isset($_POST['password2']) && !empty(trim($_POST['password2']))) $password2 = $_POST['password2'];
else $response['errorPassword2'] = "Please repeat the password!<br />";

// if every field has been filled:
if (isset($username, $email, $password1, $password2)) {
    require_once '../includes/dataModel.php';
    $model = new DataModel();

    $username = htmlentities($username);
    $email = htmlentities($email);

    // check if username is unique
    $result = $model->fetchUserByUsername($username);
    if ($result) {
        $response['errorUsername'] = "Username '$username' is already in use!<br />";
        unset($username);
    }
    // check if email address is unique
    $result = $model->fetchUserByMail($email);
    if ($result) {
        $response['errorEmail'] = "Email address '$email' is already in use!<br />";
        unset($email);
    }
    // check if password is sufficiently long
    if (strlen($password1) < 6) {
        $response['errorPassword1'] = "Entered password is too weak!<br />";
        unset($password1);
    } elseif ($password1 != $password2) {
        // check if passwords are the same
        $response['errorPassword2'] = "Entered passwords do not match!<br />";
        unset($password2);
    }

    // if all the checks passed -> register user!
    if (isset($username, $email, $password1, $password2)) {
        $hash = password_hash($password1, PASSWORD_BCRYPT);
        $result = $model->insertUser($username, $email, $hash);
        if ($result > 0)
            $response['success'] = "User '$username' was successfully created.";
        else
            $response['error'] = "User could not be created.";
    }
}

echo json_encode($response);