<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'api/abstract.php';

class UserAPI extends AbstractAPI
{
    public function checkAndSend()
    {
        $uid = $this->authenticate();
        $l10n = $this->loadLanguage($_POST['lang']);
        if (!$uid) {
            $this->response['error'] = $l10n['user_not_signed_in'];
        } else {
            // start processing
            $manager = new EditUserManager($uid, $l10n);
            $this->response = $manager->process();
        }
        $this->sendResponse();
    }
}

class EditUserManager
{
    private $_uid;
    private $_user;
    private $_model;
    private $_response;
    private $_l10n;
    private $_validator;

    function __construct($uid, $l10n)
    {
        $this->_uid = $uid;
        $this->_response = array();
        $this->_l10n = $l10n;
        require_once BASEDIR . 'includes/validator.php';
        $this->_validator = new Validator();
        // fetch user details from database
        require_once BASEDIR . 'includes/dataModel.php';
        $this->_model = new DataModel();
        $this->_user = $this->_model->fetchUser($this->_uid);
    }

    public function process()
    {
        if (isset($_POST['admin'])) {
            $this->_adminToggleUserRights();
        } elseif (isset($_POST['remove'])) {
            $this->_adminDeleteUser();
        } elseif (isset($_POST['erase'])) {
            $this->_adminEraseUser();
        } elseif (isset($_POST['resurrect'])) {
            $this->_adminResurrectUser();
        } elseif (isset($_POST['username'])) {
            $this->_changeUsername();
        } elseif (isset($_POST['email'])) {
            $this->_changeEmail();
        } elseif (isset($_POST['password1'], $_POST['password2'])) {
            $this->_changePassword();
        } elseif (isset($_POST['password'])) {
            $this->_deleteUser();
        }

        $this->_model->close();
        return $this->_response;
    }

    private function _adminToggleUserRights()
    {
        $uid = $_POST['admin'];
        $user = $this->_model->fetchUser($uid);
        if ($this->_user->rights > $user->rights) {
            $newRights = ($user->rights) ? 0 : 1;
            if ($this->_model->updateUserRights($uid, $newRights)) {
                $this->_response['success'] = sprintf($this->_l10n['user_rights_changed'], $user->username);
            } else {
                $this->_response['error'] = $this->_l10n['user_rights_not_changed'];
            }
        } else {
            $this->_response['error'] = $this->_l10n['not_allowed_to_change_user_rights'];
        }
    }

    private function _adminDeleteUser()
    {
        $uid = $_POST['remove'];
        $user = $this->_model->fetchUser($uid);
        if ($this->_user->rights > $user->rights) {
            if ($this->_model->updateDeleteUser($uid)) {
                $this->_response['success'] = sprintf($this->_l10n['user_deleted'], $user->username);
            } else {
                $this->_response['error'] = $this->_l10n['user_not_deleted'];
            }
        } else {
            $this->_response['error'] = $this->_l10n['not_allowed_to_delete_user'];
        }
    }

    private function _adminEraseUser()
    {
        $uid = $_POST['erase'];
        $user = $this->_model->fetchUser($uid, true);
        if ($this->_user->rights > $user->rights) {
            if ($this->_model->deleteUser($uid)) {
                $this->_response['success'] = sprintf($this->_l10n['user_erased'], $user->username);
            } else {
                $this->_response['error'] = $this->_l10n['user_not_deleted'];
            }
        } else {
            $this->_response['error'] = $this->_l10n['not_allowed_to_delete_user'];
        }
    }

    private function _adminResurrectUser()
    {
        $uid = $_POST['resurrect'];
        $user = $this->_model->fetchUser($uid, true);
        if ($this->_user->rights > $user->rights) {
            if ($this->_model->updateUnDeleteUser($uid)) {
                $this->_response['success'] = sprintf($this->_l10n['user_resurrected'], $user->username);
            } else {
                $this->_response['error'] = $this->_l10n['user_not_resurrected'];
            }
        } else {
            $this->_response['error'] = $this->_l10n['not_allowed_to_resurrect_user'];
        }
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

    private function _deleteUser()
    {
        $password = $_POST['password'];
        $login = $this->_model->fetchLoginByUID($this->_uid);
        if (!trim($password)) {
            $this->_response['error-password'] = $this->_l10n['enter_password'];
        } elseif (!password_verify($password, $login->password)) {
            $this->_response['error-password'] = $this->_l10n['password_invalid'];
        } else {
            if ($this->_model->updateDeleteUser($this->_uid)) {
                $this->_response['success'] = sprintf($this->_l10n['user_deleted'], $this->_user->username);
                signOut();
            } else {
                $this->_response['error'] = $this->_l10n['user_not_deleted'];
            }
        }
    }
}

(new UserAPI())->checkAndSend();