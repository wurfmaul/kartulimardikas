<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'api/abstract.php';

class UrlAPI extends AbstractAPI
{
    public function checkAndSend()
    {
        if (isset($_GET['parameters'])) {
            // deal with old and new parameters
            $oldParams = json_decode($_GET['parameters']['params']);
            unset($_GET['parameters']['params']);
            $newParams = $_GET['parameters'];
            unset($_GET['parameters']);

            // call the URL helper
            require_once BASEDIR . 'includes/helper/urlHelper.php';
            $this->response = url($newParams, $oldParams, false);
            $this->sendResponse(false);
        }
    }
}

(new UrlAPI())->checkAndSend();