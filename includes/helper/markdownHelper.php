<?php
function parseMarkdown($code, $printContainer = true, $printBreaks = true)
{
    require_once BASEDIR . 'lib/parsedown/Parsedown.php';
    $parser = Parsedown::instance();
    $parser->setBreaksEnabled($printBreaks);
    $parser->setMarkupEscaped(true);
    return $printContainer ? $parser->text($code) : $parser->line($code);
}
