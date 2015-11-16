<?php
if (!defined('BASEDIR'))
    die("Cannot be run without BASEDIR defined.");

##### BASIC SETTINGS #####
define('PROJECT_NAME', 'kartulimardikas');
# set to true in order to enable extra output in case of an error
define('DEBUG_MODE', true);
# defines, where the library packages are be taken from (one of LOCAL, DEBUG, CDN)
define('LIBRARY_MODE', 'LOCAL');
# Action that is used if the user does not specify a valid action
define('DEFAULT_PAGE', 'home');

##### LANGUAGE SETTINGS #####
$AVAILABLE_LANG = [
    'en' => 'English',
    'de' => 'German (Deutsch)'
];
# Default language
define('DEFAULT_LANG', 'en');

##### DATABASE SETTINGS #####
define('DB_HOST', 'localhost');
define('DB_NAME', 'kartulimardikas');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

##### ALGORITHM SETTINGS #####
# Minimum definable size of an array
define('ARRAY_MIN_SIZE', 2);
# Maximum definable size of an array
define('ARRAY_MAX_SIZE', 13);
# Indentation that is used for a block
define('DEFAULT_INDENT', '  ');
# Animation speed, when playing an algorithm (milliseconds between the steps)
define('SPEED', 500);
# Number of steps an algorithm is allowed to take
define('MAX_STEPS', 1000);
# Default breakpoint (values: 'before', 'after', 'both')
define('DEFAULT_BREAKPOINT', 'before');
# Whether or not short circuit evaluation is enabled
define('SHORT_CIRCUIT', true);
# Maximum number that can be generated for random variables
define('MAX_RANDOM_INT', 100);

##### LIST SETTINGS #####
# Number of algorithms that are displayed in the home action
define('NUMBER_OF_LATEST_ALGORITHMS', 5);
# Maximum number of a registered user's algorithms
define('NUMBER_OF_MY_ALGORITHMS', 25);
# Number of list entries that are displayed in the index action
define('MAX_NUMBER_OF_ENTRIES', 10);
# Maximum age (in minutes) for an algorithm to be marked with a 'new' label
define('MAX_MINUTES_FOR_LABEL', 120);
# Number of characters the description is compressed to, in algorithm lists
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
