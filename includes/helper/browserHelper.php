<?php
class BrowserHelper {
    public static function isUnsupported() {
        $_agent = $_SERVER['HTTP_USER_AGENT'];
        // check for Microsoft Internet Explorer
        if (preg_match('/MSIE (.*?);/', $_agent, $matches)) {
            if (version_compare($matches[1], '9.0', '<')) {
                // IE < 9.0 are not supported by jquery and uglify
                return "Microsoft Internet Explorer &leq; 8.0";
            }
        }
        // check for Apple Safari
        if (preg_match('/AppleWebKit.*Version\/(.*?) /', $_agent, $matches)) {
            if (preg_match('/Windows/', $_agent)) {
                // Safari for Windows is not supported by bootstrap
                return "Safari for Windows";
            } elseif (version_compare($matches[1], '5.1', '<')) {
                // Safari < 5.1 is not supported by jquery
                return "Safari &leq; 5.1";
            }
        }
        /** TODO: check browsers
         * - iOS 6.1+ (jquery ui)
         * - Android 4.0+ (jquery ui)
         */
        return false;
    }
}