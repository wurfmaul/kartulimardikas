<?php
class BrowserChecker {
    public static function isUnsupported() {
        $_agent = $_SERVER['HTTP_USER_AGENT'];

        // check for old internet explorers
        if (preg_match('/MSIE (.*?);/', $_agent, $matches)) {
            // IE < 9.0 are not supported by jquery and uglify
            if (version_compare($matches[1], '9.0', '<')) {
                return "Microsoft Internet Explorer &leq; 8.0";
            }
        }
        /** TODO: check browsers
         * - Safari on Windows (bootstrap)
         * - Safari 5.1+ (jquery ui)
         * - iOS 6.1+ (jquery ui)
         * - Android 4.0+ (jquery ui)
         *
         */
        return false;
    }
}