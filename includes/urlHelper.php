<?php
/**
 * Computes a relative url inside the project. It always starts with
 * 'index.php'. The specified $parameters are used to define the request
 * query.
 *
 * @param array $parameters Associative array holding parameters as keys.
 * @param bool $replace The old parameters, in order to replace
 *  parts. If null, nothing is replaced.
 * @return string The newly computed relative url.
 */
function url($parameters = [], $replace = false)
{
    $_url = "index.php";
    $_index = 0;
    if ($replace) {
        // Run through all the old parameters
        foreach ($_GET as $key => $value) {
            $_url .= ($_index++ === 0) ? '?' : '&';
            // Check if there is something to replace...
            if (array_key_exists($key, $parameters)) {
                // ... use the new value.
                $value = $parameters[$key];
                unset($parameters[$key]);
            }
            $_url .= $key . '=' . $value;
        }
    }
    foreach ($parameters as $key => $value) {
        $_url .= ($_index++ === 0) ? '?' : '&';
        $_url .= $key . '=' . $value;
    }
    return $_url;
}