<?php
if (!defined('BASEDIR')) die("Cannot be run without BASEDIR defined.");

##### LOAD CONFIGURATION #####
require_once BASEDIR . 'config/config.php';
require_once BASEDIR . 'config/paths.php';
error_reporting(DEBUG_MODE ? E_ALL : E_ERROR);

##### LOAD USER-INTERFACE LANGUAGE #####
require_once BASEDIR . 'includes/language.php';
require_once BASEDIR . 'includes/helper/languageHelper.php';
$langHandler = Language::getInstance();
$langHandler->availableLanguages = $AVAILABLE_LANG;
$lang = $langHandler->loadLanguage(detectLanguage());
define('LANG', $lang);
// Save user interface language
if (isset($_GET['lang'])) {
    storeLanguage($lang);
}