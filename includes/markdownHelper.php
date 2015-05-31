<?php
function parseMarkdown($code)
{
    require_once BASEDIR . 'lib/parsedown/Parsedown.php';
    $parser = Parsedown::instance();
    $parser->setBreaksEnabled(true);
    $parser->setMarkupEscaped(true);
    return $parser->text($code);
}
