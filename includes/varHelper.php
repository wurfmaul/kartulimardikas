<?php
class VarHelper
{
    const KEY_NAME = "n";
    const KEY_TYPE = "t";
    const KEY_VALUE = "v";
    const KEY_SIZE = "s";

    const ARRAY_TYPE = "[";
    const BOOL_TYPE = "b";
    const INT_TYPE = "i";

    private static $_MATRIX = [
        self::KEY_NAME => 'name',
        self::KEY_TYPE => 'type',
        self::KEY_VALUE => 'value',
        self::KEY_SIZE => 'size'
    ];

    /**
     * @param array $var
     * @return array
     */
    public static function compress($var) {
        $arr = [];
        foreach (self::$_MATRIX as $key => $value) {
            $arr[$key] = $var[$value];
        }
        return $arr;
    }

    /**
     * @param array $var
     * @return stdClass
     */
    public static function uncompress($var) {
        $obj = new stdClass();
        foreach ($var as $key => $value) {
            $obj->{self::$_MATRIX[$key]} = $value;
        }
        return $obj;
    }
}