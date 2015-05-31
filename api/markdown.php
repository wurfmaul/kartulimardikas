<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'api/abstract.php';

class MarkdownAPI extends AbstractAPI
{
    public function checkAndSend()
    {
        require_once BASEDIR . 'includes/markdownHelper.php';
        $this->response['html'] = parseMarkdown($_POST['source']);
        $this->sendResponse();
    }
}

(new MarkdownAPI())->checkAndSend();