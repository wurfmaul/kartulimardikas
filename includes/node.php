<?php
require_once(BASEDIR . 'includes/helper/treeHelper.php');
require_once(BASEDIR . 'includes/value.php');

/**
 * Superclass of all Nodes.
 */
abstract class Node
{
    const ASSIGN_NODE = "as";
    const BLOCK_NODE = "bk";
    const COMMENT_NODE = "cm";
    const COMPARE_NODE = "cp";
    const FUNCTION_NODE = "ft";
    const IF_NODE = "if";
    const INC_NODE = "ic";
    const RETURN_NODE = "rt";
    const SWAP_NODE = "sw";
    const VALUE_NODE = "vl";
    const WHILE_NODE = "wl";

    const NAME_KEY = "n";

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
     * @param array $scopes
     * @return Node
     * @throws ParseError if node is unknown
     */
    public static function parse($node, $tree, &$scopes)
    {
        // unpack container of one node
        if (is_array($node) && sizeof($node) == 1) {
            $node = $node[0];
        }
        // parse node
        switch ($node->{self::NAME_KEY}) {
            case self::ASSIGN_NODE:
                return AssignNode::parse($node, $tree, $scopes);
            case self::BLOCK_NODE:
                return BlockNode::parse($node, $tree, $scopes);
            case self::COMMENT_NODE:
                return CommentNode::parse($node, $tree, $scopes);
            case self::COMPARE_NODE:
                return CompareNode::parse($node, $tree, $scopes);
            case self::FUNCTION_NODE:
                return FunctionNode::parse($node, $tree, $scopes);
            case self::IF_NODE:
                return IfNode::parse($node, $tree, $scopes);
            case self::INC_NODE:
                return IncNode::parse($node, $tree, $scopes);
            case self::RETURN_NODE:
                return ReturnNode::parse($node, $tree, $scopes);
            case self::SWAP_NODE:
                return SwapNode::parse($node, $tree, $scopes);
            case self::VALUE_NODE:
                return ValueNode::parse($node, $tree, $scopes);
            case self::WHILE_NODE:
                return WhileNode::parse($node, $tree, $scopes);
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
     * @param array $params Parameters that are needed for the prototype.
     * @throws Exception If no node can be found for the specified type.
     */
    public static final function printPrototype($type, $params)
    {
        /** @var $node Node */
        $node = null;
        switch ($type) {
            case self::ASSIGN_NODE:
                $node = new AssignNode('assign', null, null);
                break;
            case self::COMMENT_NODE:
                $node = new CommentNode('comment', null);
                break;
            case self::COMPARE_NODE:
                $node = new CompareNode('compare', null, null, null);
                break;
            case self::FUNCTION_NODE:
                $node = new FunctionNode('function', null, null, null);
                break;
            case self::IF_NODE:
                $node = new IfNode('if', null, null, null, null);
                break;
            case self::INC_NODE:
                $node = new IncNode('inc', null, null);
                break;
            case self::RETURN_NODE:
                $node = new ReturnNode('return', null);
                break;
            case self::SWAP_NODE:
                $node = new SwapNode('swap', null, null, null);
                break;
            case self::VALUE_NODE:
                $node = new ValueNode('value', null);
                break;
            case self::WHILE_NODE:
                $node = new WhileNode('while', null, null, null);
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
     * @param array $params
     * @return string
     */
    public abstract function getSource($params);

    /**
     * Returns the string representation of a variable.
     *
     * @param Value $var
     * @param array $params
     * @return string
     */
    public final function printVar($var, $params) {
        return is_null($var) ? '' : $var->printVal($params);
    }

    /**
     * Wraps a source code line in surrounding HTML tag.
     *
     * @param string $line The plain source code line of the Node.
     * @return string HTML code of wrapped source code line.
     */
    public final function wrapLine($line)
    {
        return "<span class=\"source-node-$this->nodeId\">$line</span>";
    }
}

class AssignNode extends Node
{
    /** @var Value|null */
    protected $to;
    /** @var BlockNode */
    protected $from;

    public function __construct($nid, $to, $from)
    {
        $this->nodeId = $nid;
        $this->to = $to;
        $this->from = $from;
    }

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $from = isset($node->f) ? parent::parse($tree[$node->f], $tree, $scopes) : null;
        $to = isset($node->t) ? Value::parse($node->t) : null;
        return new self($nid, $to, $from);
    }

    public function getSource($params)
    {
        return $this->wrapLine(
            sprintf("%s := %s",
                $this->printVar($this->to, $params),
                trim($this->from->getSource($params))
            )
        );
    }

    public function printHtml(&$params)
    {
        $toValue = $this->printVar($this->to, $params);
        $fromNid = isset($this->from) ? $this->from->nodeId : null;
        ?>
        <!-- ASSIGN NODE -->
        <li class="node assign-node node_<?= $this->nodeId ?>" data-node-type="assign" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <label>
                                <?= TreeHelper::l10n('assign_node_title') ?>
                                <div class="ui-widget combobox-container">
                                    <input class="assign-to combobox" value="<?= $toValue ?>"/>
                                </div>
                                :=
                            </label>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php else: ?>
                            <label>
                                <?= $toValue ?> :=
                                <div style="display: none;">
                                    <input class="assign-to" value="<?= $toValue ?>"/>
                                </div>
                            </label>
                        <?php endif ?>
                    </td>
                </tr>
                <tr>
                    <td class="handle node-box left right bottom">
                        <span class="cursor-icon"></span>
                    </td>
                    <td>
                        <ul class="assign-from sortable" data-node-id="<?= $fromNid ?>">
                            <?php self::printNode($this->from, $params) ?>
                        </ul>
                    </td>
                </tr>
            </table>
        </li>
    <?php }
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

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $nodes = [];
        if (isset($node->c)) {
            foreach ($node->c as $childNid) {
                $nodes[] = parent::parse($tree[$childNid], $tree, $scopes);
            }
        }
        return new self($nid, $nodes);
    }

    public function getSource($params, $combine = false)
    {
        $_indent = TreeHelper::getIndent($params['indent']);
        $source = "";
        foreach ($this->nodes as $index => $node) {
            /** @var Node $node */
            if (!$combine) {
                $source .= $_indent;
            }
            $source .= $node->getSource($params);
            if ($index < sizeof($this->nodes) - 1) // not the last node
                if ($combine) {
                    $source .= $combine === 'any' ? ' or ' : ' and ';
                } else {
                    $source .= PHP_EOL;
                }
        }
        return $source;
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

class CommentNode extends Node
{
    /** @var string */
    protected $comment;

    public function __construct($nid, $comment)
    {
        $this->nodeId = $nid;
        $this->comment = $comment;
    }

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $comment = isset($node->c) ? $node->c : null;
        return new self($nid, $comment);
    }

    public function getSource($params)
    {
        require_once BASEDIR . 'includes/helper/markdownHelper.php';
        $comment = parseMarkdown($this->comment, false, false);
        $comment = preg_replace('/\n/', '<br/># ', $comment);
        return '<span style="color:grey"># ' . $comment . '</span>';
    }

    public function printHtml(&$params)
    { ?>
        <!-- COMMENT NODE -->
        <li class="node comment-node node_<?= $this->nodeId ?>" data-node-type="comment" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top bottom left">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <?= TreeHelper::l10n('comment_node_title') ?>
                            <span class="toggle-comment fa fa-plus-square"></span>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <div class="comment-container" style="display: none;">
                                <textarea class="form-control comment-text" rows="1"><?= $this->comment ?></textarea>
                            </div>
                        <?php else: ?>
                            <div class="comment-container collapsed">
                                <span class="toggle-comment fa fa-plus-square"></span>
                                <span class="fa fa-comment"></span>
                                <?= parseMarkdown($this->comment, false) ?>
                            </div>
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </li>
    <?php }
}

class CompareNode extends Node
{
    /** @var Value|null */
    protected $left;
    /** @var Value|null */
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

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $left = isset($node->l) ? Value::parse($node->l) : null;
        $right = isset($node->r) ? Value::parse($node->r) : null;
        $op = isset($node->o) ? $node->o : null;
        return new self($nid, $left, $right, $op);
    }

    public function getSource($params)
    {
        return $this->wrapLine(
            sprintf("%s %s %s",
                $this->printVar($this->left, $params),
                $this->ops[$this->op],
                $this->printVar($this->right, $params)
            )
        );
    }

    public function printHtml(&$params)
    {
        $leftVal = $this->printVar($this->left, $params);
        $rightVal = $this->printVar($this->right, $params);
        $selected_op = $this->isPrototype ? 'lt' : $this->op;
        ?>
        <!-- COMPARE NODE -->
        <li class="node compare-node node_<?= $this->nodeId ?>" data-node-type="compare" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top bottom left">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <label>
                                <?= TreeHelper::l10n('compare_node_title') ?>
                                <div class="ui-widget combobox-container">
                                    <input class="compare-left combobox" value="<?= $leftVal ?>"/>
                                </div>
                                <select class="compare-operation">
                                    <?php foreach ($this->ops as $op => $char): ?>
                                        <option value="<?= $op ?>"
                                                <?php if ($selected_op === $op): ?>selected="selected"<?php endif ?>>
                                            <?= $char ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>

                                <div class="ui-widget combobox-container">
                                    <input class="compare-right combobox" value="<?= $rightVal ?>"/>
                                </div>
                            </label>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php else: ?>
                            <label>
                                <?= $leftVal ?>
                                <?= $this->ops[$selected_op] ?>
                                <?= $rightVal ?>
                                <div style="display: none;">
                                    <input class="compare-left" value="<?= $leftVal ?>"/>
                                    <input class="compare-operation" value="<?= $selected_op ?>"/>
                                    <input class="compare-right" value="<?= $rightVal ?>"/>
                                </div>
                            </label>
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </li>
    <?php }
}

class FunctionNode extends Node
{
    /** @var int Node ID of the callee. */
    protected $calleeId;
    /** @var string Name of the callee. */
    protected $calleeName;
    /** @var BlockNode */
    protected $actPars;
    /** @var array */
    protected $actParsLine;

    public function __construct($nid, $callee, $actParsLine, $actPars)
    {
        $this->nodeId = $nid;
        $this->calleeId = $callee;
        $this->actParsLine = $actParsLine;
        $this->actPars = $actPars;

        // load callee information
        if ($this->calleeId > 0) {
            require_once(BASEDIR . 'includes/dataModel.php');
            $_model = new DataModel();
            $this->calleeName = $_model->fetchAlgorithm($this->calleeId)->name;
            $_model->close();
        }
    }

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $actParsLine = isset($node->l) ? $node->l : "";
        $actPars = isset($node->p) ? parent::parse($tree[$node->p], $tree, $scopes) : null;
        if (isset($node->c)) {
            $callee = $node->c;
            if (!isset($scopes[$callee])) {
                $scopes[] = $callee;
            }
        } else {
            $callee = -1;
        }
        return new self($nid, $callee, $actParsLine, $actPars);
    }

    public function getSource($params)
    {
        return $this->wrapLine(
            sprintf("%s(%s)",
                $this->calleeName,
                trim($this->actPars->getSource($params))
            )
        );
    }

    public function printHtml(&$params)
    {
        $actParsNid = isset($this->actPars) ? $this->actPars->nodeId : null;
        $actParsLine = "";
        if (!empty($this->actParsLine)) {
            foreach($this->actParsLine as $i => $par) {
                $actParsLine .= $par->value . "; ";
            }
            $actParsLine = substr($actParsLine, 0, sizeof($actParsLine)-3);
        }
        ?>
        <!-- FUNCTION NODE -->
        <li class="node function-node node_<?= $this->nodeId ?>" data-node-type="function" data-node-id="<?= $this->nodeId ?>" data-callee-id="<?= $this->calleeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top bottom left">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <label>
                                <?= TreeHelper::l10n('function_node_title') ?>
                                <div class="ui-widget combobox-container">
                                    <input class="function-name combobox-functions" value="<?= $this->calleeName ?>"/>
                                </div>
                                (
                                <div class="ui-widget combobox-container">
                                    <input class="combobox act-pars-line" value="<?= $actParsLine ?>" />
                                </div>
                                )
                            </label>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php else: ?>
                            <label>
                                <?= TreeHelper::l10n('function_node_title') ?>
                                <?= $this->calleeName ?>(<?= $actParsLine ?>)
                                <div style="display: none;">
                                    <input class="function-name" value="<?= $this->calleeName ?>"/>
                                    <input class="act-pars-line" value="<?= $actParsLine ?>" />
                                </div>
                            </label>
                        <?php endif ?>
                    </td>
                </tr>
                <tr<?php if (is_null($actParsNid) || $actParsNid === '0' || $this->actPars->size() === 0): ?> style="display: none;"<?php endif ?>>
                    <td class="handle node-box left right bottom">
                        <span class="cursor-icon"></span>
                    </td>
                    <td>
                        <ul class="act-pars sortable" data-node-id="<?= $actParsNid ?>">
                            <?php self::printNode($this->actPars, $params) ?>
                        </ul>
                    </td>
                </tr>
            </table>
        </li>
    <?php }
}

class IfNode extends Node
{
    /** @var BlockNode */
    protected $cond;
    /** @var BlockNode */
    protected $then;
    /** @var BlockNode */
    protected $else;
    /** @var string */
    protected $op;

    protected $ops = [
        'l' => 'condition_all',
        'y' => 'condition_any'
    ];

    function __construct($nid, $cond, $then, $else, $op)
    {
        $this->nodeId = $nid;
        $this->cond = $cond;
        $this->then = $then;
        $this->else = $else;
        $this->op = $op;
    }

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $cond = isset($node->c) ? parent::parse($tree[$node->c], $tree, $scopes) : null;
        $body = isset($node->b) ? parent::parse($tree[$node->b], $tree, $scopes) : null;
        $else = isset($node->e) ? parent::parse($tree[$node->e], $tree, $scopes) : null;
        $op = isset($node->o) ? $node->o : null;
        return new self($nid, $cond, $body, $else, $op);
    }

    public function getSource($params)
    {
        $_indent = TreeHelper::getIndent($params['indent']++);
        $string = "if (" . trim($this->cond->getSource($params, $this->op)) . ")" . PHP_EOL;
        $string .= $this->then->getSource($params);
        if ($this->else->size()) {
            $string .= PHP_EOL;
            $string .= $_indent . "else" . PHP_EOL;
            $string .= $this->else->getSource($params);
        }
        return $string;
    }

    public function printHtml(&$params)
    {
        $condNid = isset($this->cond) ? $this->cond->nodeId : null;
        $thenNid = isset($this->then) ? $this->then->nodeId : null;
        $elseNid = isset($this->else) ? $this->else->nodeId : null;
        $selected_op = $this->isPrototype ? 'all' : $this->op;
        ?>
        <!-- IF NODE -->
        <li class="node if-node node_<?= $this->nodeId ?>" data-node-type="if" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <label>
                                <?= TreeHelper::l10n('if_node_title') ?>
                                <select class="if-operator" style="display: none;">
                                    <?php foreach ($this->ops as $op => $char): ?>
                                        <option value="<?= $op ?>"
                                                <?php if ($selected_op === $op): ?>selected="selected"<?php endif ?>>
                                            <?= TreeHelper::l10n($char) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </label>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php else: ?>
                            <?= TreeHelper::l10n('if_node_title') ?>
                            <?= $this->cond->size() > 1 ? TreeHelper::l10n($this->ops[$selected_op]) : '' ?>
                            <div style="display: none;">
                                <input class="if-operator" value="<?= $selected_op ?>"/>
                            </div>
                        <?php endif ?>
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
}

class IncNode extends Node
{
    /** @var Value|null */
    protected $var;
    /** @var string */
    protected $op;
    protected $ops = [
        'i' => '++',
        'd' => '--'
    ];

    function __construct($nid, $var, $op)
    {
        $this->nodeId = $nid;
        $this->var = $var;
        $this->op = $op;
    }

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $var = isset($node->v) ? Value::parse($node->v) : null;
        $op = isset($node->o) ? $node->o : null;
        return new self($nid, $var, $op);
    }

    public function getSource($params)
    {
        return $this->wrapLine($this->printVar($this->var, $params) . "++");
    }

    public function printHtml(&$params)
    {
        $varValue = $this->printVar($this->var, $params);
        $selected_op = $this->isPrototype ? 'inc' : $this->op;
        ?>
        <!-- INC NODE -->
        <li class="node inc-node node_<?= $this->nodeId ?>" data-node-type="inc" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top bottom left">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <label>
                                <?= TreeHelper::l10n('inc_node_title') ?>
                                <div class="ui-widget combobox-container">
                                    <input class="inc-var combobox" value="<?= $varValue ?>"/>
                                </div>
                                <select class="inc-operation">
                                    <?php foreach ($this->ops as $op => $char): ?>
                                        <option value="<?= $op ?>"
                                                <?php if ($selected_op === $op): ?>selected="selected"<?php endif ?>>
                                            <?= $char ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </label>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php else: ?>
                            <label>
                                <?= $varValue . $this->ops[$selected_op] ?>
                                <div style="display: none;">
                                    <input class="inc-var" value="<?= $varValue ?>"/>
                                    <input class="inc-operation" value="<?= $selected_op ?>"/>
                                </div>
                            </label>
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </li>
    <?php }
}

class ReturnNode extends Node
{
    /** @var Value */
    protected $value;

    function __construct($nid, $value)
    {
        $this->nodeId = $nid;
        $this->value = $value;
    }

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $value = isset($node->v) ? Value::parse($node->v) : null;
        return new self($nid, $value);
    }

    public function getSource($params)
    {
        return $this->wrapLine("return " . $this->printVar($this->value, $params));
    }

    public function printHtml(&$params)
    {
        $varValue = $this->printVar($this->value, $params);
        ?>
        <!-- RETURN NODE -->
        <li class="node return-node node_<?= $this->nodeId ?>" data-node-type="return" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left bottom">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <label>
                                <?= TreeHelper::l10n('return_node_title') ?>
                                <div class="ui-widget combobox-container">
                                    <input class="return-val combobox" value="<?= $varValue ?>"/>
                                </div>
                            </label>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php else: ?>
                            <label>
                                <?= TreeHelper::l10n('return_node_title') ?>
                                <?= $varValue ?>
                                <div style="display: none;">
                                    <input class="return-val" value="<?= $varValue ?>"/>
                                </div>
                            </label>
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </li>
    <?php }
}

class SwapNode extends Node
{
    /** @var Value */
    protected $left;
    /** @var Value */
    protected $right;

    public function __construct($nid, $left, $right)
    {
        $this->nodeId = $nid;
        $this->left = $left;
        $this->right = $right;
    }

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $left = isset($node->l) ? Value::parse($node->l) : null;
        $right = isset($node->r) ? Value::parse($node->r) : null;
        return new self($nid, $left, $right);
    }

    public function getSource($params)
    {
        return $this->wrapLine(
            sprintf("%s %s %s",
                $this->printVar($this->left, $params),
                '&hArr;',
                $this->printVar($this->right, $params)
            )
        );
    }

    public function printHtml(&$params)
    {
        $leftVal = $this->printVar($this->left, $params);
        $rightVal = $this->printVar($this->right, $params);
        ?>
        <!-- SWAP NODE -->
        <li class="node swap-node node_<?= $this->nodeId ?>" data-node-type="swap" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top bottom left">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <label>
                                <?= TreeHelper::l10n('swap_node_title') ?>
                                <div class="ui-widget combobox-container">
                                    <input class="swap-left combobox" value="<?= $leftVal ?>"/>
                                </div>
                                &hArr;
                                <div class="ui-widget combobox-container">
                                    <input class="swap-right combobox" value="<?= $rightVal ?>"/>
                                </div>
                            </label>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php else: ?>
                            <label>
                                <?= $leftVal ?> &hArr; <?= $rightVal ?>
                                <div style="display: none;">
                                    <input class="swap-left" value="<?= $leftVal ?>"/>
                                    <input class="swap-right" value="<?= $rightVal ?>"/>
                                </div>
                            </label>
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </li>
    <?php }
}

class ValueNode extends Node
{
    /** @var Value */
    protected $value;

    function __construct($nid, $value)
    {
        $this->nodeId = $nid;
        $this->value = $value;
    }

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $value = isset($node->v) ? Value::parse($node->v) : null;
        return new self($nid, $value);
    }

    public function getSource($params)
    {
        return $this->wrapLine($this->printVar($this->value, $params));
    }

    public function printHtml(&$params)
    {
        $varValue = $this->printVar($this->value, $params);
        ?>
        <!-- VALUE NODE -->
        <li class="node value-node node_<?= $this->nodeId ?>" data-node-type="value" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left bottom">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <label>
                                <?= TreeHelper::l10n('value_node_title') ?>
                                <div class="ui-widget combobox-container">
                                    <input class="value-var combobox" value="<?= $varValue ?>"/>
                                </div>
                            </label>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php else: ?>
                            <label>
                                <?= $varValue ?>
                                <div style="display: none;">
                                    <input class="value-var" value="<?= $varValue ?>"/>
                                </div>
                            </label>
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </li>
    <?php }
}

class WhileNode extends Node
{
    /** @var BlockNode */
    protected $cond;
    /** @var BlockNode */
    protected $body;
    /** @var string */
    protected $op;

    protected $ops = [
        'l' => 'condition_all',
        'y' => 'condition_any'
    ];

    public function __construct($nid, $cond, $body, $op)
    {
        $this->nodeId = $nid;
        $this->cond = $cond;
        $this->body = $body;
        $this->op = $op;
    }

    public static function parse($node, $tree, &$scopes)
    {
        $nid = $node->i;
        $condition = isset($node->c) ? parent::parse($tree[$node->c], $tree, $scopes) : null;
        $body = isset($node->b) ? parent::parse($tree[$node->b], $tree, $scopes) : null;
        $op = isset($node->o) ? $node->o : null;
        return new self($nid, $condition, $body, $op);
    }

    public function getSource($params)
    {
        // increase the indent for the body
        $params['indent']++;
        // build string
        $string = "while (" . trim($this->cond->getSource($params, $this->op)) . ")" . PHP_EOL;
        $string .= $this->body->getSource($params);
        return $string;
    }

    public function printHtml(&$params)
    {
        $condNid = isset($this->cond) ? $this->cond->nodeId : null;
        $bodyNid = isset($this->body) ? $this->body->nodeId : null;
        $selected_op = $this->isPrototype ? 'all' : $this->op;
        ?>
        <!-- WHILE NODE -->
        <li class="node while-node node_<?= $this->nodeId ?>" data-node-type="while" data-node-id="<?= $this->nodeId ?>">
            <table>
                <tr>
                    <td class="handle node-box top left">
                        <span class="cursor-icon"></span>
                    </td>
                    <td class="node-box top right bottom full-width">
                        <?php if ($params['mode'] === 'edit'): ?>
                            <label>
                                <?= TreeHelper::l10n('while_node_title') ?>
                                <select class="while-operator" style="display: none;">
                                    <?php foreach ($this->ops as $op => $char): ?>
                                        <option value="<?= $op ?>"
                                                <?php if ($selected_op === $op): ?>selected="selected"<?php endif ?>>
                                            <?= TreeHelper::l10n($char) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </label>
                            <span class="invalid-flag label label-danger"><?= TreeHelper::l10n('invalid') ?></span>
                            <button type="button" class="close node-remove" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        <?php else: ?>
                            <?= TreeHelper::l10n('while_node_title') ?>
                            <?= $this->cond->size() > 1 ? TreeHelper::l10n($this->ops[$selected_op]) : '' ?>
                            <div style="display: none;">
                                <input class="while-operator" value="<?= $selected_op ?>"/>
                            </div>
                        <?php endif ?>
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
                    <td class="node-box top right bottom half-width"><?= TreeHelper::l10n('while_node_do') ?></td>
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
}