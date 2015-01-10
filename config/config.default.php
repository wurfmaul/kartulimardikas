<?php
if (!defined('BASEDIR'))
    die("Cannot be run without BASEDIR defined.");

# Basic settings
define('PROJECT_NAME', 'kartulimardikas');
define('DEBUG_MODE', true);
define('LIBRARY_MODE', 'LOCAL'); # one of LOCAL, DEBUG, CLOUDFLARE, CDN
define('LANG', 'en');

# Database settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'kartulimardikas');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

# Algorithm settings
define('ARRAY_MIN_SIZE', 2);
define('ARRAY_MAX_SIZE', 13);

# Index settings
define('DEFAULT_PAGE', 'home');
define('NUMBER_OF_LATEST_ALGORITHMS', 5);
define('MAX_MINUTES_FOR_LABEL', 120);
define('MAX_DESCRIPTION_LENGTH', 240);

error_reporting(DEBUG_MODE ? E_ALL : E_ERROR);

require_once BASEDIR . 'config/paths.php'; # Load library paths
require_once BASEDIR . 'config/l10n.' . LANG . '.php'; # Load language settings