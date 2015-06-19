<?php
# Library paths
$_jqueryVersion = '2.1.4';
$_jqueryUiVersion = '1.11.4';
$_bootstrapVersion = '3.3.5';
$_fontAwesomeVersion = '4.3.0';

if (LIBRARY_MODE == 'LOCAL') {
    define('JQUERY_PATH', "lib/jquery/$_jqueryVersion/jquery.min.js");
    define('JQUERYUI_JS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.min.js");
    define('JQUERYUI_CSS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.min.css");
    define('BOOTSTRAP_JS_PATH', "lib/bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "lib/bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
    define('FONT_AWESOME_PATH', "lib/font-awesome/$_fontAwesomeVersion/css/font-awesome.min.css");
} elseif (LIBRARY_MODE == 'DEBUG') {
    define('JQUERY_PATH', "lib/jquery/$_jqueryVersion/jquery.js");
    define('JQUERYUI_JS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.js");
    define('JQUERYUI_CSS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.css");
    define('BOOTSTRAP_JS_PATH', "lib/bootstrap/$_bootstrapVersion/js/bootstrap.js");
    define('BOOTSTRAP_CSS_PATH', "lib/bootstrap/$_bootstrapVersion/css/bootstrap.css");
    define('FONT_AWESOME_PATH', "lib/font-awesome/$_fontAwesomeVersion/css/font-awesome.css");
} else { // CDN
    define('JQUERY_PATH', "https://code.jquery.com/jquery-$_jqueryVersion.min.js");
    define('JQUERYUI_JS_PATH', "https://code.jquery.com/ui/$_jqueryUiVersion/jquery-ui.min.js");
    define('JQUERYUI_CSS_PATH', "https://code.jquery.com/ui/$_jqueryUiVersion/themes/smoothness/jquery-ui.min.css");
    define('BOOTSTRAP_JS_PATH', "https://maxcdn.bootstrapcdn.com/bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "https://maxcdn.bootstrapcdn.com/bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
    define('FONT_AWESOME_PATH', "https://maxcdn.bootstrapcdn.com/font-awesome/$_fontAwesomeVersion/css/font-awesome.min.css");
}