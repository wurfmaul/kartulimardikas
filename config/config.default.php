<?php
if (!defined('BASEDIR'))
    die("Cannot be run without BASEDIR defined.");

# Basic settings
define('PROJECT_NAME', 'kartulimardikas');
define('DEBUG_MODE', true);
define('LIBRARY_MODE', 'LOCAL'); # one of LOCAL, DEBUG, CLOUDFLARE, CDN
define('LANG', 'en');
define('DEFAULT_PAGE', 'home');

# Database settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'kartulimardikas');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

# Algorithm settings
define('ARRAY_MIN_SIZE', 2);
define('ARRAY_MAX_SIZE', 13);
define('DEFAULT_INDENT', '  ');

# Home settings
define('NUMBER_OF_LATEST_ALGORITHMS', 5);

# Index settings
define('MAX_NUMBER_OF_ENTRIES', 10);
define('MAX_MINUTES_FOR_LABEL', 120);
define('MAX_DESCRIPTION_LENGTH', 240);

# User settings
define('MAX_NUMBER_OF_ALGORITHMS', 25);

# Section settings
define('SECTIONS_VIEW', 2);
define('SECTIONS_EDIT', 4);

error_reporting(DEBUG_MODE ? E_ALL : E_ERROR);

require_once BASEDIR . 'config/paths.php'; # Load library paths
require_once BASEDIR . 'config/l10n.' . LANG . '.php'; # Load language settings