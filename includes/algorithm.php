<?php

class ArithmeticNode extends Node
{
    /** @var BlockNode */
    protected $left;
    /** @var BlockNode */
    protected $right;
    /** @var string */
    protected $op;

    protected $ops = [
        'plus' => '+',
        'minus' => '&minus;',
        'times' => '&times;',
        'by' => '&frasl;',
        'mod' => '%'
    ];

    public function __construct($nid, $left, $right, $op)
    {
        $this->nodeId = $nid;
        $this->left = $left;
        $this->right = $right;
        $this->op = $op;
    }

    public static function parse($node, $tree)
    {
        $left = isset($node->left) ? parent::parse($tree[$node->left], $tree) : null;
        $right = isset($node->right) ? parent::parse($tree[$node->right], $tree) : null;
        $op = isset($node->operator) ? $node->operator : null;
        return new self($node->nid, $left, $right, $op);
    }

    public function getSource($params)
    {
        return sprintf("%s %s %s",
            $this->left->getSource($params),
            $this->ops[$this->op],
            $this->right->getSource($params)
        );
    }

    public function printHtml(&$params)
    {
        $leftNid = isset($this->left) ? $this->left->nodeId : null;
        $rightNid = isset($this->right) ? $this->right->nodeId : null;
        $selected_op = $this->isPrototype ? 'plus' : $this->op;
        ?>
        <!-- ARITHMETIC NODE -->
        <li id="node_<?= $this->nodeId ?>" class="node arithmetic-node" data-node-type="arithmetic"
            data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left">&nbsp;</td>
                    <td class="node-box top right bottom full-width">
                        <?= TreeHelper::l10n('arithmetic_node_title') ?>
                        <span class="label label-danger"
                              <?php if ($this->isValid()): ?>style="display: none;"<?php endif ?>><?= TreeHelper::l10n('invalid') ?></span>
                        <button type="button" class="close node-remove" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box right left">&nbsp;</td>
                    <td>
                        <ul class="arithmetic-left sortable" data-node-id="<?= $leftNid ?>">
                            <?php self::printNode($this->left, $params) ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box left">&nbsp;</td>
                    <td class="node-box top right bottom half-width">
                        <label>
                            <?= TreeHelper::l10n('compare_node_operation') ?>
                            <select class="arithmetic-operation edit-only">
                                <?php foreach ($this->ops as $op => $char): ?>
                                    <option value="<?= $op ?>"
                                            <?php if ($selected_op === $op): ?>selected="selected"<?php endif ?>>
                                        <?= $char ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <input class="arithmetic-operation-input view-only" disabled="disabled"
                                   value="<?= $this->ops[$selected_op] ?>"/>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box right bottom left">&nbsp;</td>
                    <td>
                        <ul class="arithmetic-right sortable" data-node-id="<?= $rightNid ?>">
                            <?php self::printNode($this->right, $params) ?>
                        </ul>
                    </td>
                </tr>
            </table>
        </li>
    <?php }

    public function isValid()
    {
        return true;
    }
}

class AssignNode extends Node
{
    /** @var BlockNode */
    protected $to;
    /** @var BlockNode */
    protected $from;

    public function __construct($nid, $to, $from)
    {
        $this->nodeId = $nid;
        $this->to = $to;
        $this->from = $from;
    }

    public static function parse($node, $tree)
    {
        $from = parent::parse($tree[$node->from], $tree);
        $to = parent::parse($tree[$node->to], $tree);
        return new self($node->nid, $to, $from);
    }

    public function getSource($params)
    {
        $indent = TreeHelper::getIndent($params['indent']);
        return sprintf("%s%s = %s", $indent, $this->to->getSource($params), $this->from->getSource($params));
    }

    public function printHtml(&$params)
    {
        $fromNid = isset($this->from) ? $this->from->nodeId : null;
        $toNid = isset($this->to) ? $this->to->nodeId : null;
        ?>
        <!-- ASSIGN NODE -->
        <li id="node_<?= $this->nodeId ?>" class="node assign-node" data-node-type="assign"
            data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left">&nbsp;</td>
                    <td class="node-box top right bottom full-width">
                        <?= TreeHelper::l10n('assign_node_title') ?>
                        <span class="label label-danger"
                              <?php if ($this->isValid()): ?>style="display: none;"<?php endif ?>><?= TreeHelper::l10n('invalid') ?></span>
                        <button type="button" class="close node-remove" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box left right">&nbsp;</td>
                    <td>
                        <ul class="assign-from sortable" data-node-id="<?= $fromNid ?>">
                            <?php self::printNode($this->from, $params) ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box left">&nbsp;</td>
                    <td class="node-box top right bottom full-width">&rarr;</td>
                </tr>
                <tr>
                    <td class="handle node-box left right bottom">&nbsp;</td>
                    <td>
                        <ul class="assign-to sortable" data-node-id="<?= $toNid ?>">
                            <?php self::printNode($this->to, $params) ?>
                        </ul>
                    </td>
                </tr>
            </table>
        </li>
    <?php }

    public function isValid()
    {
        return true;
//        return !$this->isPrototype && $this->from->isValid() && $this->to->isValid();
    }
}

class BlockNode extends Node
{
    /** @var array */
    protected $nodes;

    public function __construct($nid, $nodes)
    {
        $this->nodeId = $nid;
        $this->nodes = $nodes;
    }

    public static function parse($node, $tree)
    {
        $nodes = array();
        if (isset($node->nodes)) {
            foreach ($node->nodes as $nid) {
                $nodes[] = parent::parse($tree[$nid], $tree);
            }
        }
        return new self($node->nid, $nodes);
    }

    public function getSource($params)
    {
        if (sizeof($this->nodes) === 1) {
            return end($this->nodes)->getSource($params);
        } else {
            $source = "";
            foreach ($this->nodes as $node) {
                /** @var Node $node */
                $source .= $node->getSource($params) . PHP_EOL;
            }
            return $source;
        }
    }

    public function isValid()
    {
        return true;
//        foreach ($this->nodes as $node) {
//            /** @var Node $node */
//            if (!$node->isValid()) {
//                return false;
//            }
//        }
//        return true;
    }

    public function printHtml(&$params)
    {
        foreach ($this->nodes as $node) {
            self::printNode($node, $params);
        }
    }

    public function size()
    {
        return sizeof($this->nodes);
    }
}

class CompareNode extends Node
{
    /** @var BlockNode */
    protected $left;
    /** @var BlockNode */
    protected $right;
    /** @var string */
    protected $op;

    protected $ops = [
        'lt' => '&lt;',
        'le' => '&le;',
        'eq' => '&equals;',
        'ne' => '&ne;',
        'ge' => '&ge;',
        'gt' => '&gt;'
    ];

    public function __construct($nid, $left, $right, $op)
    {
        $this->nodeId = $nid;
        $this->left = $left;
        $this->right = $right;
        $this->op = $op;
    }

    public static function parse($node, $tree)
    {
        $left = isset($node->left) ? parent::parse($tree[$node->left], $tree) : null;
        $right = isset($node->right) ? parent::parse($tree[$node->right], $tree) : null;
        $op = isset($node->operator) ? $node->operator : null;
        return new self($node->nid, $left, $right, $op);
    }

    public function getSource($params)
    {
        return sprintf("%s %s %s",
            $this->left->getSource($params),
            $this->ops[$this->op],
            $this->right->getSource($params)
        );
    }

    public function printHtml(&$params)
    {
        $leftNid = isset($this->left) ? $this->left->nodeId : null;
        $rightNid = isset($this->right) ? $this->right->nodeId : null;
        $selected_op = $this->isPrototype ? 'lt' : $this->op;
        ?>
        <!-- COMPARE NODE -->
        <li id="node_<?= $this->nodeId ?>" class="node compare-node" data-node-type="compare"
            data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left">&nbsp;</td>
                    <td class="node-box top right bottom full-width">
                        <?= TreeHelper::l10n('compare_node_title') ?>
                        <span class="label label-danger"
                              <?php if ($this->isValid()): ?>style="display: none;"<?php endif ?>><?= TreeHelper::l10n('invalid') ?></span>
                        <button type="button" class="close node-remove" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box right left">&nbsp;</td>
                    <td>
                        <ul class="compare-left sortable" data-node-id="<?= $leftNid ?>">
                            <?php self::printNode($this->left, $params) ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box left">&nbsp;</td>
                    <td class="node-box top right bottom half-width">
                        <label>
                            <?= TreeHelper::l10n('compare_node_operation') ?>
                            <select class="compare-operation edit-only">
                                <?php foreach ($this->ops as $op => $char): ?>
                                    <option value="<?= $op ?>"
                                            <?php if ($selected_op === $op): ?>selected="selected"<?php endif ?>>
                                        <?= $char ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <input class="compare-operation-input view-only" disabled="disabled"
                                   value="<?= $this->ops[$selected_op] ?>"/>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box right bottom left">&nbsp;</td>
                    <td>
                        <ul class="compare-right sortable" data-node-id="<?= $rightNid ?>">
                            <?php self::printNode($this->right, $params) ?>
                        </ul>
                    </td>
                </tr>
            </table>
        </li>
    <?php }

    public function isValid()
    {
        return true;
//        return !$this->isPrototype &&
//        $this->left->isValid() && $this->left->size() === 1 &&
//        $this->right->isValid() && $this->right->size() === 1;
    }
}

class ConstantNode extends Node
{
    /** @var int */
    protected $value;

    function __construct($nid, $value)
    {
        $this->nodeId = $nid;
        $this->value = $value;
    }

    public static function parse($node, $tree)
    {
        $value = isset($node->value) ? $node->value : null;
        return new self($node->nid, $value);
    }

    public function getSource($params)
    {
        return (string)$this->value;
    }

    public function printHtml(&$params)
    { ?>
        <!-- CONSTANT NODE -->
        <li id="node_<?= $this->nodeId ?>" class="node constant-node" data-node-type="constant"
            data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left bottom">&nbsp;</td>
                    <td class="node-box top right bottom full-width">
                        <label>
                            <?= TreeHelper::l10n('constant_node_title') ?>
                            <input class="constant-value" value="<?= $this->isPrototype ? "" : $this->value ?>"/>
                        </label>
                        <span class="label label-danger"
                              <?php if ($this->isValid()): ?>style="display: none;"<?php endif ?>><?= TreeHelper::l10n('invalid') ?></span>
                        <button type="button" class="close node-remove" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>
            </table>
        </li>
    <?php }

    public function isValid()
    {
        return true;
//        return !$this->isPrototype && isset($this->value) && is_numeric($this->value);
    }
}

class IfNode extends Node
{
    /** @var BlockNode */
    protected $cond;
    /** @var BlockNode */
    protected $then;
    /** @var BlockNode */
    protected $else;

    function __construct($nid, $cond, $then, $else)
    {
        $this->nodeId = $nid;
        $this->cond = $cond;
        $this->then = $then;
        $this->else = $else;
    }

    public static function parse($node, $tree)
    {
        $cond = isset($node->condition) ? parent::parse($tree[$node->condition], $tree) : null;
        $body = isset($node->ifBody) ? parent::parse($tree[$node->ifBody], $tree) : null;
        $else = isset($node->elseBody) ? parent::parse($tree[$node->elseBody], $tree) : null;
        return new self($node->nid, $cond, $body, $else);
    }

    public function getSource($params)
    {
        $_indent = TreeHelper::getIndent($params['indent']++);
        $string = $_indent . "if (" . $this->cond->getSource($params) . ")" . PHP_EOL;
        $string .= $this->then->getSource($params) . PHP_EOL;
        if ($this->else->size()) {
            $string .= $_indent . "else" . PHP_EOL;
            $string .= $this->else->getSource($params) . PHP_EOL;
        }
        return $string;
    }

    public function printHtml(&$params)
    {
        $condNid = isset($this->cond) ? $this->cond->nodeId : null;
        $thenNid = isset($this->then) ? $this->then->nodeId : null;
        $elseNid = isset($this->else) ? $this->else->nodeId : null;
        ?>
        <!-- IF NODE -->
        <li id="node_<?= $this->nodeId ?>" class="node if-node" data-node-type="if" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left">&nbsp;</td>
                    <td class="node-box top right bottom full-width">
                        <?= TreeHelper::l10n('if_node_title') ?>
                        <span class="label label-danger"
                              <?php if ($this->isValid()): ?>style="display: none;"<?php endif ?>><?= TreeHelper::l10n('invalid') ?></span>
                        <button type="button" class="close node-remove" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box right left">&nbsp;</td>
                    <td>
                        <ul class="if-condition sortable" data-node-id="<?= $condNid ?>">
                            <?php self::printNode($this->cond, $params) ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box left">&nbsp;</td>
                    <td class="node-box top right bottom half-width"><?= TreeHelper::l10n('if_node_then') ?></td>
                </tr>
                <tr>
                    <td class="handle node-box right left">&nbsp;</td>
                    <td>
                        <ul class="if-body sortable" data-node-id="<?= $thenNid ?>">
                            <?php self::printNode($this->then, $params) ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box left">&nbsp;</td>
                    <td class="node-box top right bottom half-width"><?= TreeHelper::l10n('if_node_else') ?></td>
                </tr>
                <tr>
                    <td class="handle node-box right bottom left">&nbsp;</td>
                    <td>
                        <ul class="if-else sortable" data-node-id="<?= $elseNid ?>">
                            <?php self::printNode($this->else, $params) ?>
                        </ul>
                    </td>
                </tr>
            </table>
        </li>
    <?php }

    public function isValid()
    {
        return true;
//        return !$this->isPrototype &&
//        $this->cond->isValid() && $this->cond->size() === 1 &&
//        ($this->then->isValid() || $this->else->isValid());
    }
}

class IncNode extends Node
{
    function __construct($nid, $vid = 0)
    {
        $this->nodeId = $nid;
        $this->vid = intval($vid);
    }

    public static function parse($node, $tree)
    {
        // TODO: Implement parse() method.
        return new self(null, null);
    }

    public function getSource($params)
    {
        // TODO: Implement getSource() method.
    }

    public function printHtml(&$params)
    { ?>
        <!-- IF NODE -->
        <li id="node_<?= $this->nodeId ?>" class="node inc-node" data-node-type="inc"
            data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top bottom left">&nbsp;</td>
                    <td class="node-box top right bottom full-width">
                        <label>
                            <?= TreeHelper::l10n('inc_node_title') ?>
                            <select class="var-value">
                                <?php foreach ($this->extractVars($params) as $vid => $var): ?>
                                    <option value="<?= $vid ?>" class="var-<?= $vid ?>"
                                            <?php if ($this->vid === $vid): ?>selected="selected"<?php endif ?>>
                                        <?= $var->name ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </label>
                        <span class="label label-danger"
                              <?php if ($this->isValid()): ?>style="display: none;"<?php endif ?>><?= TreeHelper::l10n('invalid') ?></span>
                        <button type="button" class="close node-remove" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>
            </table>
        </li>
    <?php }

    public function isValid()
    {
        return true;
    }
}

class VarNode extends Node
{
    /** @param $vid int */
    protected $vid;

    function __construct($nid, $vid)
    {
        $this->nodeId = $nid;
        $this->vid = intval($vid);
    }

    public static function parse($node, $tree)
    {
        $vid = isset($node->vid) ? $node->vid : null;
        return new self($node->nid, $vid);
    }

    public function getSource($params)
    {
        return $params['vars'][$this->vid]->name;
    }

    public function printHtml(&$params)
    {
        $vars = $this->extractVars($params);
        $selected = isset($vars[$this->vid]) ? $vars[$this->vid]->name : '';
        ?>
        <!-- VAR NODE -->
        <li id="node_<?= $this->nodeId ?>" class="node var-node" data-node-type="var"
            data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left bottom">&nbsp;</td>
                    <td class="node-box top right bottom full-width">
                        <label>
                            <?= TreeHelper::l10n('variable_node_title') ?>
                            <select class="var-value edit-only">
                                <?php foreach ($vars as $vid => $var): ?>
                                    <option value="<?= $vid ?>" class="var-<?= $vid ?>"
                                            <?php if ($this->vid === $vid): ?>selected="selected"<?php endif ?>><?= $var->name ?></option>
                                <?php endforeach ?>
                            </select>
                            <input class="var-value-input var-<?= $this->vid ?> view-only" value="<?= $selected ?>"
                                   disabled="disabled"/>
                        </label>
                        <span class="label label-danger"
                              <?php if ($this->isValid()): ?>style="display: none;"<?php endif ?>><?= TreeHelper::l10n('invalid') ?></span>
                        <button type="button" class="close node-remove" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>
            </table>
        </li>
    <?php }

    public function isValid()
    {
        return true;
//        return !$this->isPrototype && isset($this->vid) && is_numeric($this->vid);
    }
}

class WhileNode extends Node
{
    /** @var BlockNode */
    protected $cond;
    /** @var BlockNode */
    protected $body;

    public function __construct($nid, $cond, $body)
    {
        $this->nodeId = $nid;
        $this->cond = $cond;
        $this->body = $body;
    }

    public static function parse($node, $tree)
    {
        $condition = isset($node->condition) ? parent::parse($tree[$node->condition], $tree) : null;
        $body = isset($node->body) ? parent::parse($tree[$node->body], $tree) : null;
        return new self($node->nid, $condition, $body);
    }

    public function getSource($params)
    {
        $_indent = TreeHelper::getIndent($params['indent']++);
        $string = $_indent . "while (" . $this->cond->getSource($params) . ")" . PHP_EOL;
        $string .= $this->body->getSource($params);
        return $string;
    }

    public function printHtml(&$params)
    {
        $condNid = isset($this->cond) ? $this->cond->nodeId : null;
        $bodyNid = isset($this->body) ? $this->body->nodeId : null;
        ?>
        <!-- WHILE NODE -->
        <li id="node_<?= $this->nodeId ?>" class="node while-node" data-node-type="while"
            data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left">&nbsp;</td>
                    <td class="node-box top right bottom full-width">
                        <?= TreeHelper::l10n('while_node_title') ?>
                        <span class="label label-danger"
                              <?php if ($this->isValid()): ?>style="display: none;"<?php endif ?>
                            ><?= TreeHelper::l10n('invalid') ?></span>
                        <button type="button" class="close node-remove" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box right left">&nbsp;</td>
                    <td>
                        <ul class="while-condition sortable" data-node-id="<?= $condNid ?>">
                            <?php self::printNode($this->cond, $params) ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box left">&nbsp;</td>
                    <td class="node-box top right bottom half-width">then</td>
                </tr>
                <tr>
                    <td class="handle node-box right bottom left">&nbsp;</td>
                    <td>
                        <ul class="while-body sortable" data-node-id="<?= $bodyNid ?>">
                            <?php self::printNode($this->body, $params) ?>
                        </ul>
                    </td>
                </tr>
            </table>
        </li>
    <?php }

    public function isValid()
    {
        return true;
//        return !$this->isPrototype &&
//        $this->cond->isValid() && $this->cond->size() === 1 &&
//        $this->body->isValid();
    }
}

/**
 * Superclass of all Nodes.
 *
 * @author Wolfgang Küllinger
 */
abstract class Node
{
    const ARITHMETIC_NODE = "arithmetic";
    const ASSIGN_NODE = "assign";
    const BLOCK_NODE = "block";
    const COMPARE_NODE = "compare";
    const CONSTANT_NODE = "constant";
    const IF_NODE = "if";
    const INC_NODE = "inc";
    const VAR_NODE = "var";
    const WHILE_NODE = "while";

    const VAR_TYPE = "VAR";
    const INT_TYPE = "INT";

    /** @var int */
    protected $nodeId;
    /** @var bool */
    protected $isPrototype = false;

    /**
     * Basically transforms stdClasses (e.g. from a JSON object) to valid Nodes.
     * Calls the parse function of a subclass according to the node's type. Subclasses
     * override this function in order to provide the concrete parsing functionality.
     *
     * @param stdClass $node
     * @param array $tree
     * @return Node
     * @throws ParseError if node is unknown
     */
    public static function parse($node, $tree)
    {
        // unpack container of one node
        if (is_array($node) && sizeof($node) == 1) {
            $node = $node[0];
        }
        // parse node
        switch ($node->node) {
            case self::ARITHMETIC_NODE:
                return ArithmeticNode::parse($node, $tree);
            case self::ASSIGN_NODE:
                return AssignNode::parse($node, $tree);
            case self::BLOCK_NODE:
                return BlockNode::parse($node, $tree);
            case self::COMPARE_NODE:
                return CompareNode::parse($node, $tree);
            case self::CONSTANT_NODE:
                return ConstantNode::parse($node, $tree);
            case self::IF_NODE:
                return IfNode::parse($node, $tree);
            case self::INC_NODE:
                return IncNode::parse($node, $tree);
            case self::VAR_NODE:
                return VarNode::parse($node, $tree);
            case self::WHILE_NODE:
                return WhileNode::parse($node, $tree);
            default:
                throw new ParseError("Unknown node: " . print_r($node, true));
        }
    }

    /**
     * Calls the printHtml method of the Node $node, if it's not null and not a prototype.
     *
     * @param $node Node|array
     * @param $params array
     * @return bool True if printHtml method was called.
     */
    public static final function printNode($node, &$params)
    {
        // unpack container of one node
        if (!($node instanceof Node) && sizeof($node) == 1) {
            $node = $node[0];
        }
        // don't handle prototypes and empty nodes
        if (is_null($node) || $node->isPrototype)
            return false;
        // call printHtml() for valid nodes
        $node->printHtml($params);
        return true;
    }

    /**
     * Prints HTML code which represents the node.
     *
     * @param $params array
     */
    public abstract function printHtml(&$params);

    /**
     * Print the HTML code for the nodes' prototypes.
     *
     * @param string $type The node type the prototype should be generated for.
     * @param array $params Optional parameters that are needed for the prototype.
     * @throws Exception If no node can be found for the specified type.
     */
    public static final function printPrototype($type, $params = [])
    {
        $params['indent'] = 0;
        /** @var $node Node */
        $node = null;
        switch ($type) {
            case self::ARITHMETIC_NODE:
                $node = new ArithmeticNode($type, null, null, null);
                break;
            case self::ASSIGN_NODE:
                $node = new AssignNode($type, null, null);
                break;
            case self::COMPARE_NODE:
                $node = new CompareNode($type, null, null, null);
                break;
            case self::CONSTANT_NODE:
                $node = new ConstantNode($type, null);
                break;
            case self::IF_NODE:
                $node = new IfNode($type, null, null, null);
                break;
            case self::INC_NODE:
                $node = new IncNode($type);
                break;
            case self::VAR_NODE:
                $node = new VarNode($type, null);
                break;
            case self::WHILE_NODE:
                $node = new WhileNode($type, null, null);
                break;
            default:
                throw new Exception("No prototype prepared for '$type'.");
        }
        $node->isPrototype = true;
        $node->printHtml($params);
    }

    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * Returns the source code representation of the node.
     *
     * @param $params array
     * @return string
     */
    public abstract function getSource($params);

    /**
     * Returns true if the node is considered valid.
     *
     * @return bool
     */
    public abstract function isValid();

    protected function extractVars($params)
    {
        $vars = !is_null($params) && isset($params['vars']) ? $params['vars'] : array();
        if (isset($vars['prototype'])) {
            unset ($vars['prototype']);
        }
        return $vars;
    }
}

class Tree
{
    /** @var $node BlockNode */
    private $root;

    public function __construct($tree)
    {
        if (is_null($tree)) {
            // if the algorithm is new, generate a BlockNode as root
            $rootNode = BlockNode::parse((object)['nid' => 0], $tree);
        } else {
            // take the tree's last element otherwise
            $rootNode = Node::parse(end($tree), $tree);
        }
        $this->root = $rootNode;
    }

    public function getRoot()
    {
        return $this->root->getNodeId();
    }

    public function printHtml($params)
    {
        $this->root->printHtml($params);
    }

    public function printSource($params)
    {
        // set indent level
        if (!isset($params['indent'])) {
            $params['indent'] = 0;
        }
        // start recursion
        print(trim($this->root->getSource($params)));
    }
}

class TreeHelper
{
    public static function getIndent($indent)
    {
        $str = "";
        for ($i = 0; $i < $indent; $i++) {
            $str .= DEFAULT_INDENT;
        }
        return $str;
    }

    public static function l10n($key)
    {
        global $l10n;
        return array_key_exists($key, $l10n) ? $l10n[$key] : "[$key]";
    }
}

class ParseError extends Exception
{
}