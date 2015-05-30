<?php

function secure_session_start()
{
    // use cookies for php sessions
    if (ini_set('session.use_only_cookies', 1) === false) {
        echo "Could not start secure session!";
        exit();
    }

    // prepare and regenerate session cookie
    $oldCookie = session_get_cookie_params();
    session_set_cookie_params($oldCookie['lifetime'], $oldCookie['path'], $oldCookie['domain'], false, true);
    session_name($_SERVER['HTTP_HOST']);
    session_start();
    session_regenerate_id();
}

function signIn($username, $password)
{
    require_once BASEDIR . 'includes/settings.php';
    require_once BASEDIR . 'includes/dataModel.php';
    $model = new DataModel();
    $login = $model->fetchLoginByUsername($username);
    $model->close();

    if ($login) {
        $uid = $login->uid;
        $hash = $login->password;
        $rights = $login->rights;

        if (password_verify($password, $hash)) {
            // Password is correct!
            $_SESSION['uid'] = $uid;
            $_SESSION['username'] = $username;
            $_SESSION['lang'] = $login->language;
            $_SESSION['rights'] = $rights;
            $_SESSION['token'] = password_hash($hash . $_SERVER['HTTP_USER_AGENT'], PASSWORD_BCRYPT);
            // log signing process
            $model->open();
            $model->updateUserSignIn($uid);
            $model->close();
            // return the user id
            return $uid;
        } elseif (DEBUG_MODE) {
            echo "DEBUG_MODE: Wrong password, new hash: " . password_hash($password, PASSWORD_BCRYPT);
        }
    } elseif (DEBUG_MODE) {
        echo "DEBUG_MODE: Could not find entry in database!";
    }
    return false;
}

function signOut()
{
    $_SESSION = array();
    $oldCookie = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $oldCookie["path"], $oldCookie["domain"], $oldCookie["secure"], $oldCookie["httponly"]);
    session_destroy();
}

/**
 * @return bool|int If signed in, the uid is returned. False otherwise.
 */
function isSignedIn()
{
    if (isset($_SESSION['uid'], $_SESSION['username'], $_SESSION['token'])) {
        require_once BASEDIR . 'includes/dataModel.php';
        $model = new DataModel();

        $uid = $_SESSION['uid'];
        $token = $_SESSION['token'];
        $result = $model->fetchLoginByUID($uid);
        $model->close();

        if ($result) {
            if (password_verify($result->password . $_SERVER['HTTP_USER_AGENT'], $token)) {
                return $uid;
            } elseif (DEBUG_MODE) {
                echo "DEBUG_MODE: Token is not valid any more!";
            }
        } elseif (DEBUG_MODE) {
            echo "DEBUG_MODE: Could not find entry in the database!";
        }
    }
    return false;
}
