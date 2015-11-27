<?php

class TreeHelper
{
    public static function extractVars($params)
    {
        $vars = !is_null($params) && isset($params['vars']) ? $params['vars'] : array();
        if (isset($vars['prototype'])) {
            unset ($vars['prototype']);
        }
        return $vars;
    }

    public static function getIndent($indent)
    {
        $str = "";
        for ($i = 0; $i < $indent; $i++) {
            $str .= DEFAULT_INDENT;
        }
        return $str;
    }

    public static function l10n($key)
    {
        global $l10n;
        return array_key_exists($key, $l10n) ? $l10n[$key] : "[$key]";
    }
}