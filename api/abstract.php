<?php
require_once BASEDIR . 'includes/settings.php';
define('BR', "<br />");

abstract class AbstractAPI
{
    protected $response;

    public function __construct()
    {
        $this->response = array();
    }

    public abstract function checkAndSend();

    protected function authenticate()
    {
        require_once BASEDIR . 'includes/authentication.php';
        secure_session_start();
        return isSignedIn();
    }

    protected function loadLanguage($lang)
    {
        require_once BASEDIR . 'includes/language.php';
        global $l10n;
        $langHandler = Language::getInstance();
        $langHandler->loadLanguage($lang);
        return $l10n;
    }

    protected function sendResponse($ajax = true)
    {
        if ($ajax) {
            header('Content-type: application/json; charset=UTF-8');
            echo json_encode($this->response);
        } else {
            header('Content-type: text/plain; charset=UTF-8');
            echo $this->response;
        }
    }
}