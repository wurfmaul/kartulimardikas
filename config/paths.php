<?php
# Library paths
define('MONOTHEIST_MODE', false); # take all sources from the same CDN

if (DEBUG_MODE) {
    define('JQUERY_PATH',        'lib/jquery/jquery.min.js');
    define('JQUERYUI_JS_PATH',   'lib/jquery-ui-interactions/jquery-ui.min.js');
    define('BOOTSTRAP_JS_PATH',  'lib/bootstrap/js/bootstrap.min.js');
    define('BOOTSTRAP_CSS_PATH', 'lib/bootstrap/css/bootstrap.min.css');
    define('HTML5SHIV_PATH',     'lib/html5shiv/html5shiv.min.js');
    define('RESPOND_PATH',       'lib/respond/respond.min.js');
} elseif (MONOTHEIST_MODE) {
    define('JQUERY_PATH',        'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js');
    define('JQUERYUI_JS_PATH',   'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js');
    define('BOOTSTRAP_JS_PATH',  'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js');
    define('BOOTSTRAP_CSS_PATH', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/css/bootstrap.min.css');
    define('HTML5SHIV_PATH',     'https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js');
    define('RESPOND_PATH',       'https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js');
} else {
    define('JQUERY_PATH',        'https://code.jquery.com/jquery-2.1.3.min.js');
    define('JQUERYUI_JS_PATH',   'https://code.jquery.com/ui/1.11.2/jquery-ui.min.js');
    define('BOOTSTRAP_JS_PATH',  'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js');
    define('BOOTSTRAP_CSS_PATH', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css');
    define('HTML5SHIV_PATH',     'https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js');
    define('RESPOND_PATH',       'https://oss.maxcdn.com/respond/1.4.2/respond.min.js');
}