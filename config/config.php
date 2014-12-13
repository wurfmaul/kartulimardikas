<?php
if (!defined('BASEDIR'))
    die("Cannot be run without BASEDIR defined.");

# Basic settings
define('PROJECT_NAME', 'kartulimardikas');
define('DEBUG_MODE', true);
define('LANG', 'en');

# Database settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'kartulimardikas');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

# Algorithm settings
define('ARRAY_MIN_SIZE', 2);
define('ARRAY_MAX_SIZE', 13);

# Load language settings
require_once BASEDIR . 'config/l10n.' . LANG . '.php';