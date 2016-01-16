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
        // check administration rights or ownership
        $_admin = $this->_model->fetchUser($this->_uid)->rights > 0;
        if ($_admin || $this->_algorithm->uid === $this->_uid) {
            switch (trim($_GET['area'])) {
                case 'admin':
                    $this->_processAdmin();
                    break;
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
        } else {
            $this->_response['error'] = $this->_l10n['need_to_be_owner'];
        }

        $this->_model->close();
        return $this->_response;
    }

    private function _processAdmin()
    {
        switch($_POST['action']) {
            case 'remove':
                $this->_processDeletion();
                $this->_response['status'] = $this->_l10n['deleted'];
                break;
            case 'erase':
                $this->_model->deleteAlgorithm($this->_aid);
                $this->_response['success'] = $this->_l10n['algorithm_deleted'];
                break;
            case 'resurrect':
                $this->_model->updateUnDeleteAlgorithm($this->_aid);
                $this->_response['success'] = $this->_l10n['algorithm_resurrected'];
                $this->_response['status'] = $this->_l10n['active'];
                break;
        }
    }

    private function _processDeletion()
    {
        $this->_model->updateDeleteAlgorithm($this->_aid);
        $this->_response['success'] = $this->_l10n['algorithm_deleted'];
    }

    private function _processInfo()
    {
        if (!isset($_POST['name'], $_POST['desc'], $_POST['long']))
            die("Post parameters not set properly!");

        $name = htmlspecialchars(trim($_POST['name']));
        $desc = htmlspecialchars(trim($_POST['desc']));
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

        require_once(BASEDIR . 'includes/value.php');
        $vid = trim($_POST['vid']);
        $name = trim($_POST['name']);
        $type = trim($_POST['type']);
        $value = trim($_POST['value']);
        $size = intval($_POST['size']);

        // get variables from database
        $vars = $this->_model->fetchAlgorithm($this->_aid)->variables;
        $vars = (is_null($vars)) ? array() : json_decode($vars, true);

        // check if name is valid
        $blacklist = ['true', 'false'];
        $name = htmlspecialchars($name);
        if (strlen($name) === 0) { // check if name is specified
            $this->_response['error-name'] = $this->_l10n['empty_name'] . BR;
            $name = false;
        } elseif (preg_match('/^[\p{L}\p{Mn}\p{Pd}]+$/u', $name) !== 1) { // check if name contains forbidden characters
            // \p{L} -> Unicode letters, \p{Mn} -> Unicode accents, \p{Pd} -> Unicode hyphens
            $this->_response['error-name'] = $this->_l10n['invalid_name'] . BR;
            $name = false;
        } elseif (in_array($name, $blacklist)) { // check if name is not allowed
            $this->_response['error-name'] = sprintf($this->_l10n['name_no_keyword'], implode(', ', $blacklist)) . BR;
            $name = false;
        } elseif (!empty($vars)) { // check for name duplicate
            foreach ($vars as $curVid => $curVar) {
                if ($curVar[VarValue::KEY_NAME] === $name && $curVid != $vid) {
                    $this->_response['error-name'] = $this->_l10n['same_name'] . BR;
                    $name = false;
                    break;
                }
            }
        }

        // check for correct initialization
        $RANDOM_VALUE = $this->_l10n['random'];
        $UNINIT_VALUE = $this->_l10n['uninitialized'];
        $PARAM_VALUE = $this->_l10n['parameter'];
        $viewLabel = $name;
        $viewMode = null;
        $isList = DataType::isListType($type);

        switch ($value) {
            case $RANDOM_VALUE: // random
                $value = VarValue::RANDOM_INIT;
                $viewMode = sprintf($this->_l10n[$isList ? 'array_randomized' : 'var_randomized'], $size);
                break;
            case $UNINIT_VALUE: // uninitialized
                $value = VarValue::NO_INIT;
                $viewMode = sprintf($this->_l10n[$isList ? 'array_uninitialized' : 'var_uninitialized'], $size);
                break;
            case $PARAM_VALUE: // parameter
                $value = VarValue::PARAMETER_INIT;
                $viewMode = $this->_l10n['var_parameter'];
                break;
            case '':
                $this->_response['error-value'] = $this->_l10n['empty_value'] . BR;
                unset($value);
                break;
            default:
                try {
                    $listValues = explode(',', $value);
                    $listSize = sizeof($listValues);
                    if ($isList || $listSize > 1) {
                        $newValue = [];
                        $listType = [
                            DataType::BOOL_TYPE => 0,
                            DataType::INT_TYPE => 0
                        ];
                        foreach ($listValues as $val) {
                            $curValue = DataType::check($val);
                            $newValue[] = $curValue->val;
                            $listType[$curValue->type]++;
                        }
                        $value = implode(',', $newValue);
                        $size = $listSize;
                        // compute the lists data type
                        if ($listType[DataType::BOOL_TYPE] + $listType[DataType::INT_TYPE] === 0) {
                            throw new Exception();
                        }
                        $type = DataType::ARRAY_TYPE;
                        if ($listType[DataType::BOOL_TYPE] === 0) {
                            $type .= DataType::INT_TYPE;
                        } elseif ($listType[DataType::INT_TYPE] === 0) {
                            $type .= DataType::BOOL_TYPE;
                        }
                    } else {
                        $data = DataType::check($value);
                        $value = $data->val;
                        $type = $data->type;
                    }
                    $viewLabel = sprintf($this->_l10n['var_defined'], $name, $value);
                } catch (Exception $e) {
                    $this->_response['error-type'] = $this->_l10n['invalid_init'] . BR;
                    $type = false;
                }
        }

        // check size of lists
        if ($isList && isset($value)) {
            $this->_checkArraySize($size);
        }

        // return whatever information is still valid
        if ($type) $this->_response['type'] = $type;
        if ($name) $this->_response['name'] = $name;
        if (isset($value)) $this->_response['value'] = $value;
        if ($size) $this->_response['size'] = $size;
        $this->_response['viewMode'] = $viewMode;
        $this->_response['viewLabel'] = $viewLabel;

        // if every field has been filled:
        if ($type && $name && isset($value) && $size) {
            $vars[$vid] = VarValue::compress([
                'name' => $name,
                'type' => $type,
                'value' => $value,
                'size' => $size
            ]);
            // save changes to database
            $this->_model->updateAlgorithmVariables($this->_aid, json_encode($vars, JSON_FORCE_OBJECT));
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
            switch($status = $_POST['status']) {
                /** @noinspection PhpMissingBreakStatementInspection */
                case 'public':
                    // forbid name duplicates
                    if (!is_null($this->_model->fetchAlgorithmsOfUserByName($this->_uid, $this->_algorithm->name))) {
                        $this->_response['error'] = $this->_l10n['public_name_duplicate'];
                        break;
                    }
                case 'private':
                    $this->_model->updateAlgorithmVisibility($this->_aid, $status);
                    $this->_response['success'] = $this->_l10n['saved_to_db'];
                    break;
                default:
                    $this->_response['error'] = sprintf($this->_l10n['unknown_status'], $status);
            }
        } else {
            die("Post parameter 'status' not set properly!");
        }
    }
}

(new AlgorithmAPI())->checkAndSend();