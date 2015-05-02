<?php
require_once BASEDIR . 'includes/dataModel.php';

class Validator
{
    const BR = "<br />";

    private $_l10n;
    private $_model;

    public function __construct()
    {
        global $l10n;
        $this->_l10n = $l10n;
        $this->_model = new DataModel();
    }

    public function checkUserName($username, &$response)
    {
        $username = isset($username) && !empty(trim($username)) ? $username : false;

        // check if username was entered
        if (!$username) {
            $response['error-username'] = $this->_l10n['enter_username'] . self::BR;
            return false;
        }
        // check if username is valid
        if (!preg_match('/^(\w|-)*$/', $username)) {
            $allowed = "a-z A-Z 0-9 - _";
            $response['error-username'] = sprintf($this->_l10n['name_invalid'], $allowed) . self::BR;
            return false;
        }
        // check if username is unique
        if ($this->_model->fetchUserByUsername($username)) {
            $response['error-username'] = sprintf($this->_l10n['name_in_use'], $username) . self::BR;
            return false;
        }
        return $username;
    }

    public function checkEmailAddress($email, &$response)
    {
        $email = isset($email) && !empty(trim($email)) ? $email : false;

        // check if email address was entered
        if (!$email) {
            $response['error-email'] = $this->_l10n['enter_email'] . self::BR;
            return false;
        }
        // check if email address is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['error-email'] = $this->_l10n['email_invalid'] . self::BR;
            return false;
        }
        // check if email address is unique
        if ($this->_model->fetchUserByMail($email)) {
            $response['error-email'] = sprintf($this->_l10n['email_in_use'], $email) . self::BR;
            return false;
        }
        return $email;
    }

    public function checkPasswords($password1, $password2, &$response)
    {
        $password1 = isset($password1) && !empty(trim($password1)) ? $password1 : false;
        $password2 = isset($password2) && !empty(trim($password2)) ? $password2 : false;

        // check if first password was entered
        if (!$password1) {
            $response['error-password1'] = $this->_l10n['enter_password'] . self::BR;
        }
        // check if second password was entered
        if (!$password2) {
            $response['error-password2'] = $this->_l10n['repeat_password'] . self::BR;
        }
        if (!$password1 || !$password2) {
            return false;
        }
        // check if password is sufficiently long
        if (strlen($password1) < 6) {
            $response['error-password1'] = $this->_l10n['weak_password'] . self::BR;
            return false;
        }
        // check if the two passwords match
        if ($password1 !== $password2) {
            $response['error-password2'] = $this->_l10n['passwords_no_match'] . self::BR;
            return false;
        }
        return $password1;
    }
}