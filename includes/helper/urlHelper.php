<?php
/**
 * Computes a relative url inside the project. It always starts with
 * 'index.php'. The specified $parameters are used to define the request
 * query.
 *
 * @param array $parameters Associative array holding parameters as keys.
 * @param bool $replace The old parameters, in order to replace
 *  parts. If false, nothing is replaced.
 * @param bool $encode Whether special characters (&) should be encoded.
 * @param bool $keepSection Whether the section parameter should be removed.
 * @return string The newly computed relative url.
 */
function url($parameters = [], $replace = false, $encode = true, $keepSection = true)
{
    $_url = "index.php";
    $_amp = $encode ? '&amp;' : '&';
    $_index = 0;
    if ($replace) {
        // Run through all the old parameters
        foreach ($replace as $key => $value) {
            if ($keepSection && $key === 'section') {
                continue; # ignore section
            }
            $_url .= ($_index++ === 0) ? '?' : $_amp;
            // Check if there is something to replace...
            if (array_key_exists($key, $parameters)) {
                // ... use the new value.
                $value = $parameters[$key];
                unset($parameters[$key]);
            }
            if (is_object($value)) {
                foreach ($value as $_key => $_value) {
                    $_url .= $key . "[" . $_key . "]=" . $_value;
                }
            } else {
                $_url .= "$key=$value";
            }
        }
    }
    foreach ($parameters as $key => $value) {
        $_url .= ($_index++ === 0) ? '?' : $_amp;
        $_url .= $key . '=' . $value;
    }
    return $_url;
}