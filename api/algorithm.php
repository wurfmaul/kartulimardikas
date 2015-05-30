<?php
define('BASEDIR', realpath('..') . '/');
require_once BASEDIR . 'api/abstract.php';

class AlgorithmAPI extends AbstractAPI
{
    public function checkAndSend()
    {
        $l10n = $this->loadLanguage($_POST['lang']);
        $uid = $this->authenticate();

        if (!isset($_POST['aid'])) {
            $this->response['error'] = $l10n['no_algo_defined'];
        } elseif (!isset($_GET['area'])) {
            $this->response['error'] = $l10n['no_area_defined'];
        } elseif (!$uid) {
            $this->response['error'] = $l10n['edit_not_signed_in'];
        } else {
            // prepare variables
            $aid = $_POST['aid'];
            // start processing
            $manager = new EditAlgorithmManager($uid, $aid, $l10n);
            $this->response = $manager->process();
        }
        $this->sendResponse();
    }
}

class EditAlgorithmManager
{
    const INT_TYPE = 'elem-int';
    const INT_ARRAY_TYPE = 'array-int';

    private $_uid;
    private $_aid;
    private $_algorithm;
    private $_model;
    private $_response;
    private $_l10n;

    function __construct($uid, $aid, $l10n)
    {
        $this->_uid = $uid;
        $this->_aid = $aid;
        $this->_response = array();
        $this->_l10n = $l10n;
        // fetch algorithm details from database
        require_once BASEDIR . 'includes/dataModel.php';
        $this->_model = new DataModel();
        $this->_algorithm = $this->_model->fetchAlgorithm($this->_aid);
    }

    public function process()
    {
        // check ownership
        if ($this->_algorithm->uid !== $this->_uid) {
            $this->_response['error'] = $this->_l10n['need_to_be_owner'];
        } else {
            switch (trim($_GET['area'])) {
                case 'info':
                    $this->_processInfo();
                    break;
                case 'var':
                    switch ($_GET['action']) {
                        case 'edit':
                            $this->_editVar();
                            break;
                        case 'remove':
                            $this->_removeVar();
                            break;
                    }
                    break;
                case 'script':
                    $this->_processScript();
                    break;
                case 'settings':
                    $this->_processSettings();
                    break;
                case 'delete':
                    $this->_processDeletion();
                    break;
            }
        }

        $this->_model->close();
        return $this->_response;
    }

    private function _processInfo()
    {
        if (!isset($_POST['name'], $_POST['desc'], $_POST['long']))
            die("Post parameters not set properly!");

        $name = trim($_POST['name']);
        $desc = trim($_POST['desc']);
        $long = trim($_POST['long']);

        if (empty($name) && $this->_algorithm->date_publish) {
            $this->_response['error'] = $this->_l10n['public_without_name'];
            $this->_response['name'] = $this->_algorithm->name;
        } else {
            $this->_model->updateAlgorithmInfo($this->_aid, $name, $desc, $long);
            $this->_response['success'] = $this->_l10n['saved_to_db'];
        }
    }

    private function _editVar()
    {
        if (!isset($_POST['vid'], $_POST['name'], $_POST['type'], $_POST['value'], $_POST['size']))
            die("Post parameters not set properly!");

        $vid = trim($_POST['vid']);
        $name = trim($_POST['name']);
        $type = trim($_POST['type']);
        $value = trim($_POST['value']);
        $size = intval($_POST['size']);

        // get variables from database
        $vars = $this->_model->fetchAlgorithm($this->_aid)->variables;
        $vars = (is_null($vars)) ? array() : json_decode($vars, true);

        // check for correct name and duplicates
        $name = htmlspecialchars($name);

        // check if name is not specified
        if (strlen($name) === 0) {
            $this->_response['error-name'] = $this->_l10n['empty_name'] . BR;
            $name = false;
        } elseif (!empty($vars)) { // check for name duplicate
            foreach ($vars as $curVid => $curVar) {
                if ($curVar['name'] === $name && $curVid != $vid) {
                    $this->_response['error-name'] = $this->_l10n['same_name'] . BR;
                    $name = false;
                    break;
                }
            }
        }

        // check for correct initialization
        $RANDOM_VALUE = $this->_l10n['random'];
        $UNINIT_VALUE = $this->_l10n['uninitialized'];
        switch ($type) {

            // deal with int elements
            case self::INT_TYPE:
                if ($value === $RANDOM_VALUE) {
                    $value = rand(0, 100); // TODO: max-number or range definable
                } elseif ($value === $UNINIT_VALUE) {
                    $value = '?';
                } elseif ($value !== "") {
                    $value = intval($value);
                } else {
                    $this->_response['error-value'] = $this->_l10n['empty_value'] . BR;
                    unset($value);
                }
                break;

            // deal with int arrays
            case self::INT_ARRAY_TYPE:
                if ($value === $RANDOM_VALUE) {
                    $this->_checkArraySize($size);
                    if ($size) {
                        $newValue = array();
                        for ($i = 0; $i < $size; $i++)
                            $newValue[] = rand(0, $size);
                        $value = implode(',', $newValue);
                    }
                } elseif ($value === $UNINIT_VALUE) {
                    $this->_checkArraySize($size);
                    if ($size) {
                        $newValue = array();
                        for ($i = 0; $i < $size; $i++)
                            $newValue[] = '?';
                        $value = implode(',', $newValue);
                    }
                } elseif (!empty($value)) {
                    $newValue = array();
                    $size = 0;
                    foreach (explode(',', $value) as $val) {
                        $newValue[] = intval($val);
                        $size++;
                    }
                    $value = implode(',', $newValue);
                    $this->_checkArraySize($size);
                } else {
                    $this->_response['error-value'] = $this->_l10n['empty_value'] . BR;
                    $value = false;
                }
                break;
            default:
                $this->_response['error-type'] = $this->_l10n['invalid_init'] . BR;
                $type = false;
        }

        // return whatever information is still valid
        if ($type) $this->_response['type'] = $type;
        if ($name) $this->_response['name'] = $name;
        if (isset($value)) $this->_response['value'] = $value;
        if ($size) $this->_response['size'] = $size;

        // if every field has been filled:
        if ($type && $name && isset($value) && $size) {
            $vars[$vid] = array(
                'name' => $name,
                'type' => $type,
                'value' => $value,
                'size' => $size
            );
            $this->_response['viewMode'] = sprintf("%s = %s", $name, $value);

            // save changes to database
            $this->_model->updateAlgorithmVariables($this->_aid, json_encode($vars));

            // generate final response
            $this->_response['success'] = $this->_l10n['saved_to_db'];
        }
    }

    private function _checkArraySize(&$size)
    {
        if (ARRAY_MIN_SIZE > $size || $size > ARRAY_MAX_SIZE) {
            $this->_response['error-size'] = sprintf($this->_l10n['size_out_of_bounds'], ARRAY_MIN_SIZE, ARRAY_MAX_SIZE) . BR;
            $size = false;
        } else {
            $this->_response['size'] = $size;
        }
    }

    private function _removeVar()
    {
        if (!isset($_POST['vid']))
            die("Post parameter 'vid' not set properly!");

        $vid = trim($_POST['vid']);

        // get variables from database
        $vars = $this->_model->fetchAlgorithm($this->_aid)->variables;
        $vars = (is_null($vars)) ? array() : json_decode($vars, true);

        if (!empty($vars)) {
            unset($vars[$vid]);
        }

        // save changes to database
        $this->_model->updateAlgorithmVariables($this->_aid, json_encode($vars));

        // generate final response
        $this->_response['success'] = $this->_l10n['saved_to_db'];
    }

    private function _processScript()
    {
        if (!isset($_POST['tree']))
            die("Post parameter 'tree' not set properly!");

        $this->_model->updateAlgorithmScript($this->_aid, json_encode($_POST['tree']));
        $this->_response['success'] = $this->_l10n['saved_to_db'];
    }

    private function _processSettings()
    {
        if (isset($_POST['status'])) {
            $this->_model->updateAlgorithmVisibility($this->_aid, $_POST['status']);
            $this->_response['success'] = $this->_l10n['saved_to_db'];
        } else {
            die("Post parameter 'tree' not set properly!");
        }
    }

    private function _processDeletion()
    {
        $this->_model->updateDeleteAlgorithm($this->_aid);
        $this->_response['success'] = $this->_l10n['algorithm_deleted'];
    }
}

(new AlgorithmAPI())->checkAndSend();