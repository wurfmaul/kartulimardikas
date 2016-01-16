<?php
# Library paths
$_jqueryVersion = '2.2.0';
$_jqueryUiVersion = '1.11.4';
$_bootstrapVersion = '3.3.6';
$_fontAwesomeVersion = '4.5.0';
$_tableSorterVersion = '2.25.2';

if (LIBRARY_MODE == 'LOCAL') {
    define('JQUERY_PATH', "lib/jquery/$_jqueryVersion/jquery.min.js");
    define('JQUERYUI_JS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.min.js");
    define('JQUERYUI_CSS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.min.css");
    define('BOOTSTRAP_JS_PATH', "lib/bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "lib/bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
    define('FONT_AWESOME_PATH', "lib/font-awesome/$_fontAwesomeVersion/css/font-awesome.min.css");
    define('TABLESORTER_JS_PATH', "lib/tablesorter/$_tableSorterVersion/js/jquery.tablesorter.min.js");
    define('TABLESORTER_CSS_PATH', "lib/tablesorter/$_tableSorterVersion/css/theme.bootstrap.min.css");
    define('TABLESORTER_WIDGETS_JS_PATH', "lib/tablesorter/$_tableSorterVersion/js/jquery.tablesorter.widgets.min.js");
    define('TABLESORTER_PAGER_JS_PATH', "lib/tablesorter/$_tableSorterVersion/js/extras/jquery.tablesorter.pager.min.js");
    define('TABLESORTER_PAGER_CSS_PATH', "lib/tablesorter/$_tableSorterVersion/css/jquery.tablesorter.pager.min.css");
} elseif (LIBRARY_MODE == 'DEBUG') {
    define('JQUERY_PATH', "lib/jquery/$_jqueryVersion/jquery.js");
    define('JQUERYUI_JS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.js");
    define('JQUERYUI_CSS_PATH', "lib/jquery-ui/$_jqueryUiVersion/jquery-ui.css");
    define('BOOTSTRAP_JS_PATH', "lib/bootstrap/$_bootstrapVersion/js/bootstrap.js");
    define('BOOTSTRAP_CSS_PATH', "lib/bootstrap/$_bootstrapVersion/css/bootstrap.css");
    define('FONT_AWESOME_PATH', "lib/font-awesome/$_fontAwesomeVersion/css/font-awesome.css");
    define('TABLESORTER_JS_PATH', "lib/tablesorter/$_tableSorterVersion/js/jquery.tablesorter.js");
    define('TABLESORTER_CSS_PATH', "lib/tablesorter/$_tableSorterVersion/css/theme.bootstrap.min.css");
    define('TABLESORTER_WIDGETS_JS_PATH', "lib/tablesorter/$_tableSorterVersion/js/jquery.tablesorter.widgets.js");
    define('TABLESORTER_PAGER_JS_PATH', "lib/tablesorter/$_tableSorterVersion/js/extras/jquery.tablesorter.pager.min.js");
    define('TABLESORTER_PAGER_CSS_PATH', "lib/tablesorter/$_tableSorterVersion/css/jquery.tablesorter.pager.min.css");
} else { // CDN
    define('JQUERY_PATH', "https://code.jquery.com/jquery-$_jqueryVersion.min.js");
    define('JQUERYUI_JS_PATH', "https://code.jquery.com/ui/$_jqueryUiVersion/jquery-ui.min.js");
    define('JQUERYUI_CSS_PATH', "https://code.jquery.com/ui/$_jqueryUiVersion/themes/smoothness/jquery-ui.min.css");
    define('BOOTSTRAP_JS_PATH', "https://maxcdn.bootstrapcdn.com/bootstrap/$_bootstrapVersion/js/bootstrap.min.js");
    define('BOOTSTRAP_CSS_PATH', "https://maxcdn.bootstrapcdn.com/bootstrap/$_bootstrapVersion/css/bootstrap.min.css");
    define('FONT_AWESOME_PATH', "https://maxcdn.bootstrapcdn.com/font-awesome/$_fontAwesomeVersion/css/font-awesome.min.css");
    define('TABLESORTER_JS_PATH', "https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/$_tableSorterVersion/js/jquery.tablesorter.min.js");
    define('TABLESORTER_CSS_PATH', "https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/$_tableSorterVersion/css/theme.bootstrap.min.css");
    define('TABLESORTER_WIDGETS_JS_PATH', "https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/$_tableSorterVersion/js/jquery.tablesorter.widgets.min.js");
    define('TABLESORTER_PAGER_JS_PATH', "https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/$_tableSorterVersion/js/extras/jquery.tablesorter.pager.min.js");
    define('TABLESORTER_PAGER_CSS_PATH', "https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/$_tableSorterVersion/css/jquery.tablesorter.pager.min.css");
}