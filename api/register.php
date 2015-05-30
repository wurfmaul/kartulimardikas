<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'api/abstract.php';

class RegisterAPI extends AbstractAPI
{
    public function checkAndSend()
    {
        $l10n = $this->loadLanguage($_POST['lang']);

        require_once BASEDIR . 'includes/validator.php';
        $validator = new Validator();
        $username = $validator->checkUserName($_POST['username'], $this->response);
        $email = $validator->checkEmailAddress($_POST['email'], $this->response);
        $password = $validator->checkPasswords($_POST['password1'], $_POST['password2'], $this->response);

        // if all the checks passed -> register user!
        if ($username && $email && $password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            require_once BASEDIR . 'includes/dataModel.php';
            $model = new DataModel();
            if ($model->insertUser($username, $email, $hash)) {
                $this->response['success'] = sprintf($l10n['user_created'], $username);
            } else {
                $this->response['error'] = $l10n['user_not_created'];
            }
            $model->close();
        }

        $this->sendResponse();
    }
}

(new RegisterAPI())->checkAndSend();
