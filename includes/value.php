<?php
abstract class Value
{
    const VAR_KIND   = 'v';
    const CONST_KIND = 'c';
    const INDEX_KIND = 'i';
    const PROP_KIND  = 'p';
    const COMP_KIND  = 'e';

    /** @var string One of *_KIND. */
    public $kind;

    protected function __construct($kind)
    {
        $this->kind = $kind;
    }

    public final static function parse($value)
    {
        switch ($value->k) {
            case self::COMP_KIND:
                return new CompValue(self::parse($value->l), self::parse($value->r), $value->o);
            case self::CONST_KIND:
                return new ConstValue($value->t, $value->v);
            case self::INDEX_KIND:
                return new IndexValue($value->i, self::parse($value->x));
            case self::PROP_KIND:
                return new PropValue($value->i, $value->p);
            case self::VAR_KIND:
                return new VarValue($value->i);
            default:
                throw new ParseError("Kind not found: '$value->k'!");
        }
    }

    public abstract function printVal($params);
}

class CompValue extends Value
{
    /** @var Value */
    public $left, $right;
    /** @var string */
    public $op;

    protected function __construct($left, $right, $op)
    {
        parent::__construct(self::COMP_KIND);
        $this->left = $left;
        $this->right = $right;
        $this->op = $op;
    }

    public function printVal($params)
    {
        $left = $this->left->printVal($params);
        $right = $this->right->printVal($params);
        if ($this->left->kind === self::COMP_KIND)
            $left = "($left)";
        if ($this->right->kind === self::COMP_KIND)
            $right = "($right)";
        return $left . $this->op . $right;
    }
}

class ConstValue extends Value
{
    /** @var string */
    public $type, $value;

    protected function __construct($type, $value)
    {
        parent::__construct(self::CONST_KIND);
        $this->type = $type;
        $this->value = $value;
    }

    public function printVal($params)
    {
        return $this->value;
    }
}

class IndexValue extends Value
{
    /** @var int */
    public $vid;
    /** @var Value */
    public $index;

    protected function __construct($vid, $index)
    {
        parent::__construct(self::INDEX_KIND);
        $this->vid = $vid;
        $this->index = $index;
    }

    public function printVal($params)
    {
        $vars = TreeHelper::extractVars($params);
        return sprintf("%s[%s]",
            $vars[$this->vid][VarValue::KEY_NAME],
            $this->index->printVal($params)
        );
    }
}

class PropValue extends Value
{
    const LEN_PROP = 'l';

    /** @var int */
    public $vid;
    /** @var string */
    public $prop;

    protected function __construct($vid, $prop)
    {
        parent::__construct(self::PROP_KIND);
        $this->vid = $vid;
        $this->prop = $prop;
    }

    public function printVal($params)
    {
        $vars = TreeHelper::extractVars($params);
        return sprintf("%s.%s",
            $vars[$this->vid][VarValue::KEY_NAME],
            $this->prop
        );
    }
}

class VarValue extends Value
{
    const KEY_NAME = 'n';
    const KEY_TYPE = 't';
    const KEY_VALUE = 'v';
    const KEY_SIZE = 's';

    const CUSTOM_INIT = 'C';
    const NO_INIT = '?';
    const PARAMETER_INIT = 'P';
    const RANDOM_INIT = 'R';

    private static $_MATRIX = [
        self::KEY_NAME => 'name',
        self::KEY_TYPE => 'type',
        self::KEY_VALUE => 'value',
        self::KEY_SIZE => 'size'
    ];
    /** @var int */
    public $vid;

    protected function __construct($vid)
    {
        parent::__construct(self::VAR_KIND);
        $this->vid = $vid;
    }

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
    public static function decompress($var) {
        $obj = new stdClass();
        foreach ($var as $key => $value) {
            $obj->{self::$_MATRIX[$key]} = $value;
        }
        return $obj;
    }

    public function printVal($params)
    {
        $vars = TreeHelper::extractVars($params);
        return $vars[$this->vid][self::KEY_NAME];
    }
}

class DataType {
    const ARRAY_TYPE = '[';
    const BOOL_TYPE = 'b';
    const INT_TYPE = 'i';

    /**
     * @param string $val The raw value.
     * @return object Value and type of the given raw value.
     * @throws Exception if the provided value is of no valid data type.
     */
    public static function check($val) {
        if (strtolower($val) === 'true' || strtolower($val) === 'false') {
            $type = self::BOOL_TYPE;
            $value = $val;
        } elseif (intval($val) . '' === $val) {
            $type = self::INT_TYPE;
            $value = intval($val);
        } else {
            throw new Exception();
        }

        return (object) [
            'type' => $type,
            'val' => $value
        ];
    }

    /**
     * @param string $type A string representing the variable type.
     * @return bool True if the given variable is a list.
     */
    public static function isListType($type) {
        return !empty($type) && $type{0} === self::ARRAY_TYPE;
    }
}