<?php

function url($parameters)
{
    $_url = "index.php";
    $_index = 0;
    foreach ($parameters as $key => $value) {
        $_url .= ($_index++ == 0) ? '?' : '&';
        $_url .= $key . '=' . $value;
    }
    return $_url;
}

if (isset($_GET['parameters'])) {
    header('Content-type: text/plain; charset=UTF-8');
    echo url($_GET['parameters']);
}
