<?php

class Language
{
    public $availableLanguages;
    protected $lang;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new Language();
        }
        return $inst;
    }

    /**
     * Checks if there is a file for the wanted language. If yes, the file is loaded
     * and prepared for being used. Otherwise the default language is loaded.
     *
     * @param string $lang The language code.
     * @return string The code of the loaded language.
     */
    public function loadLanguage($lang)
    {
        // check if language configuration file exists
        if (!file_exists(BASEDIR . "config/lang/l10n.$lang.php")) {
            $lang = DEFAULT_LANG;
        }
        // prepare and load language file
        global $l10n;
        require_once BASEDIR . 'config/lang/l10n.' . $lang . '.php';
        $this->lang = $lang;
        return $lang;
    }
}