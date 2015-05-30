<?php
if (!defined('BASEDIR'))
    die("Cannot be run without BASEDIR defined.");

##### BASIC SETTINGS #####
define('PROJECT_NAME', 'kartulimardikas');
# set to true in order to enable extra output in case of an error
define('DEBUG_MODE', true);
# defines, where the library packages are be taken from (one of LOCAL, DEBUG, CDN)
define('LIBRARY_MODE', 'LOCAL');
# defines the default language
define('LANG', 'en');
# defines an action that is used if the user does not specify a valid action
define('DEFAULT_PAGE', 'home');

##### DATABASE SETTINGS #####
define('DB_HOST', 'localhost');
define('DB_NAME', 'kartulimardikas');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

##### ALGORITHM SETTINGS #####
# the minimum definable size of an array
define('ARRAY_MIN_SIZE', 2);
# the maximum definable size of an array
define('ARRAY_MAX_SIZE', 13);
# specifies the indentation that is used for a block
define('DEFAULT_INDENT', '  ');

##### LIST SETTINGS #####
# specifies the number of algorithms that are displayed in the home action
define('NUMBER_OF_LATEST_ALGORITHMS', 5);
# specifies the maximum number of a registered user's algorithms
define('NUMBER_OF_MY_ALGORITHMS', 25);
# specifies the number of list entries that are displayed in the index action
define('MAX_NUMBER_OF_ENTRIES', 10);
# specifies the maximum age (in minutes) for an algorithm to be marked with a 'new' label
define('MAX_MINUTES_FOR_LABEL', 120);
# specifies how many characters the description is compressed to, in algorithm lists
define('MAX_DESCRIPTION_LENGTH', 240);

##### SECTION SETTINGS #####
# The following numbers define the default state of panels in the view/edit action.
# It uses binary numbers and simply computes the sum:
# Example:
#   assume there are 6 panels: panel 1, 3 and 4 are expanded, the rest is collapsed
#   every panel has a number (according to powers of 2):
#     panel 1 => #1, panel 2 => #2, panel 3 => #4, panel 4 => #8, etc.
#   sum all the expanded panels' numbers, in our example 1 + 4 + 8 = 13
define('SECTIONS_VIEW', 27);
define('SECTIONS_EDIT', 4);

##### DON'T TOUCH THE SETTINGS BELOW! #####
# Configure PHP's error reporting mode
error_reporting(DEBUG_MODE ? E_ALL : E_ERROR);
# Load library paths
require_once BASEDIR . 'config/paths.php';
# Load language settings
require_once BASEDIR . 'config/l10n.' . LANG . '.php';
