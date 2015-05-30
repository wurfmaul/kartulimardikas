<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'api/abstract.php';

class UrlAPI extends AbstractAPI
{
    public function checkAndSend()
    {
        if (isset($_GET['parameters'])) {
            require_once BASEDIR . 'includes/urlHelper.php';
            $this->response = url($_GET['parameters']);
            $this->sendResponse(false);
        }
    }
}

(new UrlAPI())->checkAndSend();