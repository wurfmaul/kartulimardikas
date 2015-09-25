<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'api/abstract.php';

class ListAPI extends AbstractAPI
{
    public function checkAndSend()
    {
        if (isset($_GET['query'])) {
            $query = $_GET['query'];

            // exclude empty requests
            if (strlen($query) === 0) {
                $this->sendResponse();
                return;
            }

            require_once BASEDIR . 'includes/dataModel.php';
            $_model = new DataModel();
            foreach ($_model->findAlgorithm($query) as $item) {
                $this->response[] = [
                    'label' => sprintf('[%d] %s (%s)', $item['aid'], $item['name'], $item['username']),
                    'name' => $item['name'],
                    'value' => $item['aid']
                ];
            }
            $this->sendResponse();
            $_model->close();
        }
    }
}

(new ListAPI())->checkAndSend();