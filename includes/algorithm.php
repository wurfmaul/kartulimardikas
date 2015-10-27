<?php

class AssignNode extends Node
{
    /** @var Value */
    protected $to;
    /** @var BlockNode */
    protected $from;

    public function __construct($nid, $to, $from)
    {
        $this->nodeId = $nid;
        $this->to = new Value($to);
        $this->from = $from;
    }

    public static function parse($node, $tree)
    {
        $from = isset($node->from) ? parent::parse($tree[$node->from], $tree) : null;
        $to = isset($node->to) ? $node->to : null;
        return new self($node->nid, $to, $from);
    }

    public function getSource($params)
    {
        return $this->wrapLine(
            sprintf("%s := %s",
                $this->to->parse($params),
                trim($this->from->getSource($params))
            )
        );
    }

    public function printHtml(&$params)
    {
        $toValue = $this->to->parse($params);
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

    public static function parse($node, $tree)
    {
        $comment = isset($node->comment) ? $node->comment : null;
        return new self($node->nid, $comment);
    }

    public function getSource($params)
    {
        require_once BASEDIR . 'includes/markdownHelper.php';
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
                                <?php require_once BASEDIR . 'includes/markdownHelper.php' ?>
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
    /** @var Value */
    protected $left;
    /** @var Value */
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
        $this->left = new Value($left);
        $this->right = new Value($right);
        $this->op = $op;
    }

    public static function parse($node, $tree)
    {
        $left = isset($node->left) ? $node->left : null;
        $right = isset($node->right) ? $node->right : null;
        $op = isset($node->operator) ? $node->operator : null;
        return new self($node->nid, $left, $right, $op);
    }

    public function getSource($params)
    {
        return $this->wrapLine(
            sprintf("%s %s %s",
                $this->left->parse($params),
                $this->ops[$this->op],
                $this->right->parse($params)
            )
        );
    }

    public function printHtml(&$params)
    {
        $leftVal = $this->left->parse($params);
        $rightVal = $this->right->parse($params);
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

    public static function parse($node, $tree)
    {
        $actParsLine = isset($node->paramsLine) ? $node->paramsLine : "";
        $actPars = isset($node->params) ? parent::parse($tree[$node->params], $tree) : null;
        $callee = isset($node->callee) ? $node->callee : -1;
        return new self($node->nid, $callee, $actParsLine, $actPars);
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
                <tr<?php if (is_null($actParsNid) || $actParsNid === '0'): ?> style="display: none;"<?php endif ?>>
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
        'all' => 'condition_all',
        'any' => 'condition_any'
    ];

    function __construct($nid, $cond, $then, $else, $op)
    {
        $this->nodeId = $nid;
        $this->cond = $cond;
        $this->then = $then;
        $this->else = $else;
        $this->op = $op;
    }

    public static function parse($node, $tree)
    {
        $cond = isset($node->condition) ? parent::parse($tree[$node->condition], $tree) : null;
        $body = isset($node->ifBody) ? parent::parse($tree[$node->ifBody], $tree) : null;
        $else = isset($node->elseBody) ? parent::parse($tree[$node->elseBody], $tree) : null;
        $op = isset($node->op) ? $node->op : null;
        return new self($node->nid, $cond, $body, $else, $op);
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
    /** @var Value */
    protected $var;
    /** @var string */
    protected $op;
    protected $ops = [
        'inc' => '++',
        'dec' => '--'
    ];

    function __construct($nid, $var, $op)
    {
        $this->nodeId = $nid;
        $this->var = new Value($var);
        $this->op = $op;
    }

    public static function parse($node, $tree)
    {
        $var = isset($node->var) ? $node->var : null;
        $op = isset($node->operator) ? $node->operator : null;
        return new self($node->nid, $var, $op);
    }

    public function getSource($params)
    {
        return $this->wrapLine($this->var->parse($params) . "++");
    }

    public function printHtml(&$params)
    {
        $varValue = $this->var->parse($params);
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
        $this->value = new Value($value);
    }

    public static function parse($node, $tree)
    {
        $value = isset($node->value) ? $node->value : null;
        return new self($node->nid, $value);
    }

    public function getSource($params)
    {
        return $this->wrapLine("return " . $this->value->parse($params));
    }

    public function printHtml(&$params)
    {
        $varValue = $this->value->parse($params);
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
                                    <input class="return-value combobox" value="<?= $varValue ?>"/>
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
                                    <input class="return-value" value="<?= $varValue ?>"/>
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
        $this->left = new Value($left);
        $this->right = new Value($right);
    }

    public static function parse($node, $tree)
    {
        $left = isset($node->left) ? $node->left : null;
        $right = isset($node->right) ? $node->right : null;
        return new self($node->nid, $left, $right);
    }

    public function getSource($params)
    {
        return $this->wrapLine(
            sprintf("%s %s %s",
                $this->left->parse($params),
                '&hArr;',
                $this->right->parse($params)
            )
        );
    }

    public function printHtml(&$params)
    {
        $leftVal = $this->left->parse($params);
        $rightVal = $this->right->parse($params);
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
        $this->value = new Value($value);
    }

    public static function parse($node, $tree)
    {
        $value = isset($node->value) ? $node->value : null;
        return new self($node->nid, $value);
    }

    public function getSource($params)
    {
        return $this->wrapLine($this->value->parse($params));
    }

    public function printHtml(&$params)
    {
        $varValue = $this->value->parse($params);
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
        'all' => 'condition_all',
        'any' => 'condition_any'
    ];

    public function __construct($nid, $cond, $body, $op)
    {
        $this->nodeId = $nid;
        $this->cond = $cond;
        $this->body = $body;
        $this->op = $op;
    }

    public static function parse($node, $tree)
    {
        $condition = isset($node->condition) ? parent::parse($tree[$node->condition], $tree) : null;
        $body = isset($node->body) ? parent::parse($tree[$node->body], $tree) : null;
        $op = isset($node->op) ? $node->op : null;
        return new self($node->nid, $condition, $body, $op);
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

/**
 * Superclass of all Nodes.
 *
 * @author Wolfgang Küllinger
 */
abstract class Node
{
    const ASSIGN_NODE = "assign";
    const BLOCK_NODE = "block";
    const COMMENT_NODE = "comment";
    const COMPARE_NODE = "compare";
    const FUNCTION_NODE = "function";
    const IF_NODE = "if";
    const INC_NODE = "inc";
    const RETURN_NODE = "return";
    const SWAP_NODE = "swap";
    const VALUE_NODE = "value";
    const WHILE_NODE = "while";

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
            case self::ASSIGN_NODE:
                return AssignNode::parse($node, $tree);
            case self::BLOCK_NODE:
                return BlockNode::parse($node, $tree);
            case self::COMMENT_NODE:
                return CommentNode::parse($node, $tree);
            case self::COMPARE_NODE:
                return CompareNode::parse($node, $tree);
            case self::FUNCTION_NODE:
                return FunctionNode::parse($node, $tree);
            case self::IF_NODE:
                return IfNode::parse($node, $tree);
            case self::INC_NODE:
                return IncNode::parse($node, $tree);
            case self::RETURN_NODE:
                return ReturnNode::parse($node, $tree);
            case self::SWAP_NODE:
                return SwapNode::parse($node, $tree);
            case self::VALUE_NODE:
                return ValueNode::parse($node, $tree);
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
     * @param array $params Parameters that are needed for the prototype.
     * @throws Exception If no node can be found for the specified type.
     */
    public static final function printPrototype($type, $params)
    {
        /** @var $node Node */
        $node = null;
        switch ($type) {
            case self::ASSIGN_NODE:
                $node = new AssignNode($type, null, null);
                break;
            case self::COMMENT_NODE:
                $node = new CommentNode($type, null);
                break;
            case self::COMPARE_NODE:
                $node = new CompareNode($type, null, null, null);
                break;
            case self::FUNCTION_NODE:
                $node = new FunctionNode($type, null, null, null);
                break;
            case self::IF_NODE:
                $node = new IfNode($type, null, null, null, null);
                break;
            case self::INC_NODE:
                $node = new IncNode($type, null, null);
                break;
            case self::RETURN_NODE:
                $node = new ReturnNode($type, null);
                break;
            case self::SWAP_NODE:
                $node = new SwapNode($type, null, null, null);
                break;
            case self::VALUE_NODE:
                $node = new ValueNode($type, null);
                break;
            case self::WHILE_NODE:
                $node = new WhileNode($type, null, null, null);
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
        $source = "";
        foreach (explode(PHP_EOL, trim($this->root->getSource($params))) as $line) {
            $source .= '<div class="line">' . $line . '</div>';
        }
        print($source);
    }
}

class Value
{
    const VAR_KIND = "var";
    const CONST_KIND = "const";
    const INDEX_KIND = "index";
    const PROP_KIND = "prop";
    const COMP_KIND = "comp";

    const INT_TYPE = "int";

    const LEN_PROP = "length";

    /** @var string One of *_KIND. Used by constants. */
    protected $kind;
    /** @var string One of *_TYPE. Used by constants. */
    protected $type;
    /** @var mixed Used by constants. */
    protected $value;
    /** @var int Variable ID, used by variables, array accesses and array properties. */
    protected $vid;
    /** @var Value Used by array accesses, contains index value. */
    protected $index;
    /** @var string One of *_PROP. Used by array properties. */
    protected $prop;
    /** @var Value Used by compound values. */
    protected $left, $right;
    /** @var string Used by compound values. */
    protected $op;

    public function __construct($value)
    {
        if (isset($value->kind)) $this->kind = $value->kind;
        if (isset($value->type)) $this->type = $value->type;
        if (isset($value->value)) $this->value = $value->value;
        if (isset($value->vid)) $this->vid = intval($value->vid);
        if (isset($value->index)) $this->index = new Value($value->index);
        if (isset($value->prop)) $this->prop = $value->prop;
        if (isset($value->left)) $this->left = new Value($value->left);
        if (isset($value->right)) $this->right = new Value($value->right);
        if (isset($value->op)) $this->op = $value->op;
    }

    public function parse($params)
    {
        $vars = TreeHelper::extractVars($params);
        if (!isset($this->kind))
            return "";

        switch ($this->kind) {
            case self::CONST_KIND:
                return $this->value;
            case self::INDEX_KIND:
                return sprintf("%s[%s]",
                    $vars[$this->vid]['name'],
                    $this->index->parse($params)
                );
            case self::PROP_KIND:
                return sprintf("%s.%s",
                    $vars[$this->vid]['name'],
                    $this->prop
                );
            case self::VAR_KIND:
                return $vars[$this->vid]['name'];
            case self::COMP_KIND:
                $left = $this->left->parse($params);
                $right = $this->right->parse($params);
                if ($this->left->kind === self::COMP_KIND)
                    $left = "($left)";
                if ($this->right->kind === self::COMP_KIND)
                    $right = "($right)";
                return $left . $this->op . $right;
            default:
                throw new ParseError("Kind not found: '$this->kind'!");
        }
    }
}

class TreeHelper
{
    public static function extractVars($params)
    {
        $vars = !is_null($params) && isset($params['vars']) ? $params['vars'] : array();
        if (isset($vars['prototype'])) {
            unset ($vars['prototype']);
        }
        return $vars;
    }

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