<?php
# Library paths
$_jqueryVersion = '2.1.3';
$_jqueryUiVersion = '1.11.4';
$_bootstrapVersion = '3.3.4';

if (LIBRARY_MODE == 'LOCAL') {
    define('JQUERY_PATH', "lib/jquery/$_jqueryVersion/jquery.min.js");
    define('JQUERYUI_JS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.min.js");
    define('BOOTSTRAP_JS_PATH', "lib/bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "lib/bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
} elseif (LIBRARY_MODE == 'DEBUG') {
    define('JQUERY_PATH', "lib/jquery/$_jqueryVersion/jquery.js");
    define('JQUERYUI_JS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.js");
    define('BOOTSTRAP_JS_PATH', "lib/bootstrap/$_bootstrapVersion/js/bootstrap.js");
    define('BOOTSTRAP_CSS_PATH', "lib/bootstrap/$_bootstrapVersion/css/bootstrap.css");
} elseif (LIBRARY_MODE == 'CLOUDFLARE') {
    define('JQUERY_PATH', "https://cdnjs.cloudflare.com/ajax/libs/jquery/$_jqueryVersion/jquery.min.js");
    define('JQUERYUI_JS_PATH', "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/$_jqueryUiVersion/jquery-ui.min.js");
    define('BOOTSTRAP_JS_PATH', "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
} else {
    define('JQUERY_PATH', "https://code.jquery.com/jquery-$_jqueryVersion.min.js");
    define('JQUERYUI_JS_PATH', "https://code.jquery.com/ui/$_jqueryUiVersion/jquery-ui.min.js");
    define('BOOTSTRAP_JS_PATH', "https://maxcdn.bootstrapcdn.com/bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "https://maxcdn.bootstrapcdn.com/bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
}