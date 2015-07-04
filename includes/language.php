<?php

class Language
{
    /** @var array $availableLanguages Holds all the available, selectable languages. */
    public $availableLanguages;
    /** @var string $lang Holds the currently loaded language code. */
    protected $lang;

    /**
     * Is set to private in order to avoid another instance.
     */
    private function __construct()
    {
    }

    /**
     * Returns the singleton object of this class.
     * @return Language
     */
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
        require_once BASEDIR . "config/lang/l10n.$lang.php";
        $this->lang = $lang;
        return $lang;
    }
}