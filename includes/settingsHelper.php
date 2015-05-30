<?php

/**
 * Detects the used language by reading the following (descending priority):
 * 1: get parameters
 * 2: read from session if user is signed in
 * 3: read from cookie otherwise
 * 4: take browser's preferred language
 * 5: take default value
 *
 * @return string The detected language code.
 */
function detectLanguage()
{
    // First priority: parameter
    if (isset($_GET['lang'])) {
        return $_GET['lang'];
    }
    // Second priority: session, if user is signed in
    if (isset($_SESSION['lang'])) {
        return $_SESSION['lang'];
    }
    // Third priority: cookie
    if (isset($_COOKIE['lang'])) {
        return $_COOKIE['lang'];
    }
    // Fourth priority: browser's preferred language
    $languages = preg_split('/[-,;\s]+/', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    if ($languages[0] !== "") {
        return $languages[0];
    }
    return DEFAULT_LANG;
}

function storeLanguage($lang)
{
    // write language to cookie
    if (!isset($_COOKIE['lang']) || $lang !== $_COOKIE['lang']) {
        setcookie("lang", $lang, time() + 60 * 60 * 24 * 30, '/', '', false, true);
    }
    if ($uid = isSignedIn()) {
        // write to session
        $_SESSION['lang'] = $lang;
        // write to database
        require_once BASEDIR . 'includes/dataModel.php';
        $model = new DataModel();
        $model->updateUserLang($uid, $lang);
        $model->close();
    }
}