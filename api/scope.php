<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'api/abstract.php';

class ScopeAPI extends AbstractAPI
{
    public function checkAndSend()
    {
        if (isset($_POST['aid'], $_POST['scope'], $_POST['lang'])) {
            // prepare algorithm data
            $aid = $_POST['aid'];
            $scope = $_POST['scope'];
            $l10n = $this->loadLanguage($_POST['lang']);
            require_once BASEDIR . 'includes/dataModel.php';
            $model = new DataModel();
            $__algorithm = $model->fetchAlgorithm($aid);
            $__algorithm->tags = $model->fetchTags($aid);
            $model->close();
            // load helpers
            require_once BASEDIR . 'includes/urlHelper.php';
            // load algorithm
            ob_start();
            require_once BASEDIR . 'partials/partials/show_algorithm.phtml';
            $algorithm = ob_get_clean();
            // send algorithm
            $this->response['aid'] = $aid;
            $this->response['algorithm'] = $algorithm;
            $this->sendResponse();
        }
    }
}

(new ScopeAPI())->checkAndSend();