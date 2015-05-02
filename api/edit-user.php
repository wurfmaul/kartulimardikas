<?php
define('BASEDIR', realpath('..') . '/');

require_once BASEDIR . 'config/config.php';
require_once BASEDIR . 'includes/authentication.php';
require_once BASEDIR . 'includes/dataModel.php';
require_once BASEDIR . 'includes/validator.php';

// in order to retrieve the uid we need access to the session
secure_session_start();
$uid = isSignedIn();

class EditUserManager
{
    private $_uid;
    private $_user;
    private $_model;
    private $_response;
    private $_l10n;
    private $_validator;

    function __construct($uid)
    {
        global $l10n;
        $this->_uid = $uid;
        $this->_model = new DataModel();
        $this->_response = array();
        $this->_l10n = $l10n;
        $this->_validator = new Validator();
        // fetch user details from database
        $this->_user = $this->_model->fetchUser($this->_uid);
    }

    public function process()
    {
        if (isset($_POST['username'])) {
            $this->_changeUsername();
        } elseif (isset($_POST['email'])) {
            $this->_changeEmail();
        } elseif (isset($_POST['password1'], $_POST['password2'])) {
            $this->_changePassword();
        }

        $this->_model->close();
        return $this->_response;
    }

    private function _changeUsername()
    {
        $username = $this->_validator->checkUserName($_POST['username'], $this->_response);
        if ($username) {
            if ($this->_model->updateUserName($this->_uid, $username)) {
                $this->_response['success'] = sprintf($this->_l10n['username_changed'], $username);
                signOut();
            } else {
                $this->_response['error'] = $this->_l10n['username_not_changed'];
            }
        }
    }

    private function _changeEmail()
    {
        $email = $this->_validator->checkEmailAddress($_POST['email'], $this->_response);
        if ($email) {
            if ($this->_model->updateUserEmail($this->_uid, $email)) {
                $this->_response['success'] = sprintf($this->_l10n['email_changed'], $email);
            } else {
                $this->_response['error'] = $this->_l10n['email_not_changed'];
                $this->_response['email'] = $this->_user->email;
            }
        }
    }

    private function _changePassword()
    {
        $password = $this->_validator->checkPasswords($_POST['password1'], $_POST['password2'], $this->_response);
        if ($password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            if ($this->_model->updateUserPassword($this->_uid, $hash)) {
                $this->_response['success'] = $this->_l10n['password_changed'];
                signOut();
            } else {
                $this->_response['error'] = $this->_l10n['password_not_changed'];
            }
        }
    }
}

if (!$uid) {
    $response['error'] = $l10n['user_not_signed_in'];
} else {
    // start processing
    $manager = new EditUserManager($uid);
    $response = $manager->process();
}

header('Content-type: application/json; charset=UTF-8');
echo json_encode($response);