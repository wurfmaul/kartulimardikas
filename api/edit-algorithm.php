<?php
define('BASEDIR', realpath('..') . '/');

require_once BASEDIR . 'config/config.php';
require_once BASEDIR . 'includes/authentication.php';
require_once BASEDIR . 'includes/dataModel.php';

// in order to retrieve the uid we need access to the session
secure_session_start();

class EditManager
{
    private $_uid;
    private $_aid;
    private $_model;
    private $_response;
    private $_l10n;

    function __construct($uid, $aid)
    {
        global $l10n;
        $this->_uid = $uid;
        $this->_aid = $aid;
        $this->_model = new DataModel();
        $this->_response = array();
        $this->_l10n = $l10n;
    }

    public function process()
    {
        // fetch algorithm details from database
        $owner = $this->_model->fetchAlgorithmByAID($this->_aid)->uid;
        if ($owner != $this->_uid) {
            $this->_response['error'] = $this->_l10n['need_to_be_owner'];
        }

        // partially process data
        if (!isset($this->_response['error'])) {
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

        $this->_model->updateAlgorithmInfo($this->_aid, $name, $desc, $long);
        $this->_response['success'] = $this->_l10n['saved_to_db'];
    }

    private function _editVar()
    {
        if (!isset($_POST['vid'], $_POST['name'], $_POST['init'], $_POST['value'], $_POST['size']))
            die("Post parameters not set properly!");

        $vid = trim($_POST['vid']);
        $name = trim($_POST['name']);
        $init = trim($_POST['init']);
        $value = trim($_POST['value']);
        $size = intval($_POST['size']);

        // get variables from database
        $vars = $this->_model->fetchAlgorithmByAID($this->_aid)->variables;
        $vars = (is_null($vars)) ? array() : json_decode($vars);

        // check for correct name and duplicates
        $name = htmlspecialchars($name);

        // check if name is not specified
        if (strlen($name) == 0) {
            $this->_response['error-name'] = $this->_l10n['empty_name'] . "<br />";
            unset($name);
        } elseif (!empty($vars)) { // check for name duplicate
            foreach ($vars as $curVid => $curVar) {
                if ($curVar->name == $name && $curVid != $vid) {
                    $this->_response['error-name'] = $this->_l10n['same_name'] . "<br />";
                    unset($name);
                    break;
                }
            }
        }

        // check for correct initialization
        switch ($init) {
            case "array-custom":
                // check value field
                if ($value == "") {
                    $this->_response['error-value'] = $this->_l10n['empty_value'] . "<br />";
                    unset($value);
                } else {
                    $newValue = array();
                    $size = 0;
                    foreach (explode(',', $value) as $val) {
                        $newValue[] = intval($val);
                        $size++;
                    }
                    $value = implode(',', $newValue);
                }
                $this->_checkArraySize($size); // FIXME: highlight value field and not size field
                break;
            case "array-random":
                $this->_checkArraySize($size);
                if (isset($size)) {
                    $newValue = array();
                    for ($i = 0; $i < $size; $i++)
                        $newValue[] = rand(0, $size);
                    $value = implode(',', $newValue);
                }
                break;
            case "array-?":
                $this->_checkArraySize($size);
                if (isset($size)) {
                    $newValue = array();
                    for ($i = 0; $i < $size; $i++)
                        $newValue[] = '?';
                    $value = implode(',', $newValue);
                }
                break;
            case "elem-value":
                if ($value == "") {
                    $this->_response['error-value'] = $this->_l10n['empty_value'] . "<br />";
                    unset($value);
                } else {
                    $value = intval($value); // FIXME: only allow integer -> error message!
                }
                break;
            case "elem-?":
                $value = '?';
                break;
            default:
                $this->_response['error-init'] = $this->_l10n['invalid_init'] . "<br />";
                unset($init);
        }

        // return whatever information is still valid
        if (isset($init)) $this->_response['init'] = $init;
        if (isset($name)) $this->_response['name'] = $name;
        if (isset($value)) $this->_response['value'] = $value;
        if (isset($size)) $this->_response['size'] = $size;

        // if every field has been filled:
        if (isset($init, $name, $value, $size)) {
            $vars[$vid] = array(
                'name' => $name,
                'init' => $init,
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

    private function _processScript()
    {
        if (!isset($_POST['tree']))
            die("Post parameter 'tree' not set properly!");

        require_once BASEDIR . "includes/nodes.php";
        $rawTree = $_POST['tree'];
        try {
            ob_start();
            $tree = new Tree($rawTree);
            ob_clean(); // hide notices
            $tree->printHtml();
            $source_edit = ob_get_clean();

            $this->_model->updateAlgorithmScript($this->_aid, json_encode($rawTree), base64_encode($source_edit));
            $this->_response['success'] = $this->_l10n['saved_to_db'];
        } catch (ParseError $e) {
            $this->_response['error'] = "Parse error: " . $e->getMessage(); // FIXME proper error message!
        }
    }

    private function _removeVar() {
        if (!isset($_POST['vid']))
            die("Post parameter 'vid' not set properly!");

        $vid = trim($_POST['vid']);

        // get variables from database
        $vars = $this->_model->fetchAlgorithmByAID($this->_aid)->variables;
        $vars = (is_null($vars)) ? array() : json_decode($vars);

        if (!empty($vars)) {
            unset($vars[$vid]);
        }

        // save changes to database
        $this->_model->updateAlgorithmVariables($this->_aid, json_encode($vars));

        // generate final response
        $this->_response['success'] = $this->_l10n['saved_to_db'];
    }

    private function _checkArraySize(&$size)
    {
        if (ARRAY_MIN_SIZE > $size || $size > ARRAY_MAX_SIZE) {
            $this->_response['error-size'] = sprintf($this->_l10n['size_out_of_bounds'], ARRAY_MIN_SIZE, ARRAY_MAX_SIZE) . "<br />";
            unset ($size); // FIXME does not work out of the function!
        } else {
            $this->_response['size'] = $size;
        }
    }
}

if (!isset($_POST['aid'])) {
    $response['error'] = $l10n['no_algo_defined'];
} elseif (!isset($_GET['area'])) {
    $response['error'] = $l10n['no_area_defined'];
} elseif (isSignedIn()) {
    // prepare variables
    $uid = $_SESSION['uid'];
    $aid = $_POST['aid'];
    // start processing
    $manager = new EditManager($uid, $aid);
    $response = $manager->process();
} else {
    $response['error'] = $l10n['edit_not_signed_in'];
}

header('Content-type: application/json; charset=UTF-8');
echo json_encode($response);
