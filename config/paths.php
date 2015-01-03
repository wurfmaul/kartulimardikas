<?php
# Library paths
$_jqueryVersion = '2.1.3';
$_jqueryUiVersion = '1.11.2';
$_bootstrapVersion = '3.3.1';
$_html5shivVersion = '3.7.2';
$_respondVersion = '1.4.2';

if (LIBRARY_MODE == 'LOCAL') {
    define('JQUERY_PATH', "lib/jquery/$_jqueryVersion/jquery.min.js");
    define('JQUERYUI_JS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.min.js");
    define('BOOTSTRAP_JS_PATH', "lib/bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "lib/bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
    define('HTML5SHIV_PATH', "lib/html5shiv/$_html5shivVersion/html5shiv.min.js");
    define('RESPOND_PATH', "lib/respond.js/$_respondVersion/respond.min.js");
} elseif (LIBRARY_MODE == 'DEBUG') {
    define('JQUERY_PATH',        "lib/jquery/$_jqueryVersion/jquery.js");
    define('JQUERYUI_JS_PATH',   "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.js");
    define('BOOTSTRAP_JS_PATH',  "lib/bootstrap/$_bootstrapVersion/js/bootstrap.js");
    define('BOOTSTRAP_CSS_PATH', "lib/bootstrap/$_bootstrapVersion/css/bootstrap.css");
    define('HTML5SHIV_PATH',     "lib/html5shiv/$_html5shivVersion/html5shiv.js");
    define('RESPOND_PATH',       "lib/respond.js/$_respondVersion/respond.js");
} elseif (LIBRARY_MODE == 'CLOUDFLARE') {
    define('JQUERY_PATH',        "https://cdnjs.cloudflare.com/ajax/libs/jquery/$_jqueryVersion/jquery.min.js");
    define('JQUERYUI_JS_PATH',   "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/$_jqueryUiVersion/jquery-ui.min.js");
    define('BOOTSTRAP_JS_PATH',  "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
    define('HTML5SHIV_PATH',     "https://cdnjs.cloudflare.com/ajax/libs/html5shiv/$_html5shivVersion/html5shiv.min.js");
    define('RESPOND_PATH',       "https://cdnjs.cloudflare.com/ajax/libs/respond.js/$_respondVersion/respond.min.js");
} else {
    define('JQUERY_PATH',        "https://code.jquery.com/jquery-$_jqueryVersion.min.js");
    define('JQUERYUI_JS_PATH',   "https://code.jquery.com/ui/$_jqueryUiVersion/jquery-ui.min.js");
    define('BOOTSTRAP_JS_PATH',  "https://maxcdn.bootstrapcdn.com/bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "https://maxcdn.bootstrapcdn.com/bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
    define('HTML5SHIV_PATH',     "https://oss.maxcdn.com/html5shiv/$_html5shivVersion/html5shiv.min.js");
    define('RESPOND_PATH',       "https://oss.maxcdn.com/respond/$_respondVersion/respond.min.js");
}