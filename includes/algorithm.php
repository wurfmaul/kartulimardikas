<?php
class AssignNode extends Node {
	/** @var VarNode */
	protected $to;
	/** @var Node */
	protected $from;

	public function __construct($to, $from) {
		global $l10n;
		$this->l10n = $l10n;
		$this->to = $to;
		$this->from = $from;
	}

    public function getSource($params) {
        $indent = TreeHelper::getIndent($params['indent']);
        return sprintf("%s%s = %s", $indent, $this->to->getSource($params), $this->from->getSource($params));
    }

	public function printHtml(&$params) { ?>
		<!-- ASSIGN NODE -->
		<li id="step_<?= $params['step']++ ?>" class="node assign-node" data-node-type="assign">
			<table>
				<tr>
					<td class="handle node-box top left">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['assign_node_title'] ?>
						<span class="label label-danger" <?php if ($this->isValid): ?>style="display: none;"<?php endif ?> 							><?= $this->l10n['invalid'] ?></span>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
				<tr>
					<td class="handle node-box left right">&nbsp;</td>
					<td>
						<ul class="assign-from sortable">
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
						<ul class="assign-to sortable">
							<?php self::printNode($this->to, $params) ?>
						</ul>
					</td>
				</tr>
			</table>
		</li>
	<?php }

    public static function parse($node) {
        $from = isset($node->from) ? parent::parse($node->from) : null;
        $to = isset($node->to) ? parent::parse($node->to) : null;

        $_node = new self($to, $from);
        $_node->setValid(isset($node->from, $node->to));
        return $_node;
    }
}

class CompareNode extends Node {
	/** @var Node */
	protected $left;
	/** @var Node */
	protected $right;
    /** @var string */
	protected $op;

    protected $ops = [
        'lt' => '&lt;',
        'le' => '&le;',
        'eq' => '&equals',
        'ge' => '&ge;',
        'gt' => '&gt;'
    ];

	public function __construct($left, $right, $op) {
		global $l10n;
		$this->l10n = $l10n;
		$this->left = $left;
		$this->right = $right;
		$this->op = $op;
	}

    public function getSource($params) {
        return sprintf("%s %s %s", $this->left->getSource($params), $this->ops[$this->op], $this->right->getSource($params));
    }

	public function printHtml(&$params) {
        $selected_op = $this->isPrototype ? "" : $this->op;
    ?>
		<!-- COMPARE NODE -->
		<li id="step_<?= $params['step']++ ?>" class="node compare-node" data-node-type="compare">
			<table>
				<tr>
					<td class="handle node-box top left">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['compare_node_title'] ?>
						<span class="label label-danger" <?php if ($this->isValid): ?>style="display: none;"<?php endif ?> 							><?= $this->l10n['invalid'] ?></span>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
				<tr>
					<td class="handle node-box right left">&nbsp;</td>
					<td>
						<ul class="compare-left sortable">
							<?php self::printNode($this->left, $params) ?>
						</ul>
					</td>
				</tr>
				<tr>
					<td class="handle node-box left">&nbsp;</td>
					<td class="node-box top right bottom half-width">
						<?= $this->l10n['compare_node_operation'] ?>:
						<select class="compare-operation">
                            <?php foreach ($this->ops as $op => $char): ?>
							<option value="<?= $op ?>"<?php if ($selected_op === $op): ?> selected="selected"<?php endif ?>><?= $char ?></option>
                            <?php endforeach ?>
						</select>
                        <input class="compare-operation-input" disabled="disabled" value="<?= $this->ops[$selected_op] ?>" />
					</td>
				</tr>
				<tr>
					<td class="handle node-box right bottom left">&nbsp;</td>
					<td>
						<ul class="compare-right sortable">
							<?php self::printNode($this->right, $params) ?>
						</ul>
					</td>
				</tr>
			</table>
		</li>
	<?php }

    public static function parse($node)
    {
        $left = isset($node->left) ? parent::parse($node->left) : null;
        $right = isset($node->right) ? parent::parse($node->right) : null;
        $op = isset($node->operator) ? $node->operator : null;

        $_node = new self($left, $right, $op);
        $_node->setValid(isset($node->left, $node->right, $node->operator));
        return $_node;
    }
}

class ConstantNode extends Node {
	protected $value;

	function __construct($value) {
		global $l10n;
		$this->l10n = $l10n;
		$this->value = $value;
	}

    public function getSource($params) {
        return (string) $this->value;
    }

	public function printHtml(&$params)
	{ ?>
		<!-- CONSTANT NODE -->
		<li id="step_<?= $params['step']++ ?>" class="node constant-node" data-node-type="constant">
			<table>
				<tr>
					<td class="handle node-box top left bottom">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['constant_node_title'] ?>
						<input class="constant-value" value="<?= $this->isPrototype ? "" : $this->value ?>" />
						<span class="label label-danger" <?php if ($this->isValid): ?>style="display: none;"<?php endif ?> 							><?= $this->l10n['invalid'] ?></span>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
			</table>
		</li>
	<?php }

    public static function parse($node)
    {
        $value = isset($node->value) ? $node->value : null;

        $_node = new self($value);
        $_node->setValid(isset($node->value) && is_numeric($value));
        return $_node;
    }
}

class IfNode extends Node {
	/** @var Node */
	protected $cond;
	/** @var array */
	protected $then;
	/** @var array */
	protected $else;

	function __construct($cond, $then, $else) {
		global $l10n;
		$this->l10n = $l10n;
		$this->cond = $cond;
		$this->then = $then;
		$this->else = $else;
	}

    public function getSource($params) {
        $indent = TreeHelper::getIndent($params['indent']);
        $string = $indent . "if (" . $this->cond->getSource($params) . ")" . PHP_EOL;
        foreach ($this->then as $node) {
            $params['indent']++;
            /** @var $node Node */
            $string .= $node->getSource($params) . PHP_EOL;
        }
        if ($this->else) {
            $string .= $indent . "else";
            foreach ($this->else as $node){
                $params['indent']++;
                /** @var $node Node */
                $string .= $node->getSource($params) . PHP_EOL;
            }
        }
        return $string;
    }

	public function printHtml(&$params) { ?>
		<!-- IF NODE -->
		<li id="step_<?= $params['step']++ ?>" class="node if-node" data-node-type="if">
			<table>
				<tr>
					<td class="handle node-box top left">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['if_node_title'] ?>
						<span class="label label-danger" <?php if ($this->isValid): ?>style="display: none;"<?php endif ?> 							><?= $this->l10n['invalid'] ?></span>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
				<tr>
					<td class="handle node-box right left">&nbsp;</td>
					<td>
						<ul class="if-condition sortable">
							<?php self::printNode($this->cond, $params) ?>
						</ul>
					</td>
				</tr>
				<tr>
					<td class="handle node-box left">&nbsp;</td>
					<td class="node-box top right bottom half-width"><?= $this->l10n['if_node_then'] ?></td>
				</tr>
				<tr>
					<td class="handle node-box right left">&nbsp;</td>
					<td>
						<ul class="if-body sortable">
							<?php self::printNode($this->then, $params) ?>
						</ul>
					</td>
				</tr>
				<tr>
					<td class="handle node-box left">&nbsp;</td>
					<td class="node-box top right bottom half-width"><?= $this->l10n['if_node_else'] ?></td>
				</tr>
				<tr>
					<td class="handle node-box right bottom left">&nbsp;</td>
					<td>
						<ul class="if-else sortable">
							<?php self::printNode($this->else, $params) ?>
						</ul>
					</td>
				</tr>
			</table>
		</li>
	<?php }

    public static function parse($node)
    {
        $cond = isset($node->condition) ? parent::parse($node->condition) : null;
        $body = isset($node->ifBody) ? Tree::parseBody($node->ifBody) : null;
        $else = isset($node->elseBody) ? Tree::parseBody($node->elseBody) : null;

        $_node = new self($cond, $body, $else);
        $_node->setValid(isset($node->condition) &&
            (isset($node->ifBody) || isset($node->elseBody)));
        return $_node;
    }
}

class VarNode extends Node {
    protected $vid;

    /**
     * @param $vid int
     */
	function __construct($vid) {
		global $l10n;
		$this->l10n = $l10n;
        $this->vid = intval($vid);
	}

    public function getSource($params) {
        return $params['vars'][$this->vid]->name;
    }

	public function printHtml(&$params)
	{
        $vars = !is_null($params) && isset($params['vars']) ? $params['vars'] : array();
        unset ($vars['prototype']);
        $selected = isset($vars[$this->vid]) ? $vars[$this->vid]->name : '';
    ?>
		<!-- VAR NODE -->
		<li id="step_<?= $params['step']++ ?>" class="node var-node" data-node-type="var">
			<table>
				<tr>
					<td class="handle node-box top left bottom">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['variable_node_title'] ?>
						<select class="var-value">
							<?php foreach ($vars as $vid => $var): ?>
							<option value="<?= $vid ?>" class="var-<?= $vid ?>"
                                <?php if ($this->vid === $vid): ?>selected="selected"<?php endif ?>><?= $var->name ?></option>
							<?php endforeach ?>
						</select>
                        <input class="var-value-input var-<?= $this->vid ?>" value="<?= $selected ?>" disabled="disabled" />
						<span class="label label-danger" <?php if ($this->isValid): ?>style="display: none;"<?php endif ?> 							><?= $this->l10n['invalid'] ?></span>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
			</table>
		</li>
	<?php }

    public static function parse($node)
    {
        $vid = isset($node->vid) ? $node->vid : null;

        $_node = new self($vid);
        $_node->setValid(isset($node->vid));
        return $_node;
    }
}

class WhileNode extends Node {
	/** @var Node */
	protected $cond;
	/** @var Node */
	protected $body;

	public function __construct($cond, $body) {
		global $l10n;
		$this->l10n = $l10n;
		$this->cond = $cond;
		$this->body = $body;
	}

    public function getSource($params) {
        $indent = TreeHelper::getIndent($params['indent']);
        $string = $indent . "while (" . $this->cond->getSource($params) . ")";
        foreach ($this->body as $node) {
            $params['indent']++;
            /** @var $node Node */
            $string .= $node->getSource($params);
        }
        return $string;
    }

	public function printHtml(&$params)
	{ ?>
		<!-- WHILE NODE -->
		<li id="step_<?= $params['step']++ ?>" class="node while-node" data-node-type="while">
			<table>
				<tr>
					<td class="handle node-box top left">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['while_node_title'] ?>
						<span class="label label-danger" <?php if ($this->isValid): ?>style="display: none;"<?php endif ?>
							><?= $this->l10n['invalid'] ?></span>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
				<tr>
					<td class="handle node-box right left">&nbsp;</td>
					<td>
						<ul class="while-condition sortable">
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
						<ul class="while-body sortable">
							<?php self::printNode($this->body, $params) ?>
						</ul>
					</td>
				</tr>
			</table>
		</li>
	<?php }

    public static function parse($node)
    {
        $condition = isset($node->condition) ? parent::parse($node->condition) : null;
        $body = isset($node->body) ? Tree::parseBody($node->body) : null;

        $_node = new self($condition, $body);
        $_node->setValid(isset($node->condition, $node->body));
        return $_node;
    }
}

/**
 * Superclass of all Nodes. 
 * @author Wolfgang Küllinger
 */
abstract class Node {
	const ASSIGN_NODE = "assign";
	const COMPARE_NODE = "compare";
	const CONSTANT_NODE = "constant";
	const IF_NODE = "if";
	const VAR_NODE = "var";
	const WHILE_NODE = "while";

	/** @var bool */
	protected $isPrototype = false;
	/** @var bool */
	protected $isValid = false;

    /**
     * Set or unset the valid flag.
     * @param bool $valid
     */
	public function setValid($valid = true) {
		$this->isValid = $valid;
	}

    /**
     * Returns the source code representation of the node.
     * @param $params array
     * @return string
     */
    public abstract function getSource($params);

	/**
	 * Prints HTML code which represents the node.
	 * @param $params array
	 */
	public abstract function printHtml(&$params);

    /**
     * Calls the printHtml method of the Node $node, if it's not null and not a prototype.
     * @param $node Node|array
     * @param $params array
     * @return bool True if printHtml method was called.
     */
	public static function printNode($node, &$params) {
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
     * Print the HTML code for the nodes' prototypes.
     * @param string $type The node type the prototype should be generated for.
     * @param array $params Optional parameters that are needed for the prototype.
     * @throws Exception If no node can be found for the specified type.
     */
	public static function printPrototype($type, $params = []) {
		/** @var $node Node */
		$node = null;
		switch ($type) {
			case self::ASSIGN_NODE: $node = new AssignNode(null, null); break;
			case self::COMPARE_NODE: $node = new CompareNode(null, null, null); break;
			case self::CONSTANT_NODE: $node = new ConstantNode(null); break;
			case self::IF_NODE: $node = new IfNode(null, null, null); break;
			case self::VAR_NODE: $node = new VarNode(null); break;
			case self::WHILE_NODE: $node = new WhileNode(null, null); break;
			default: throw new Exception("No prototype prepared for '$type'.");
		}
		$node->isPrototype = true;
		$node->printHtml($params);
	}

    /**
     * Basically transforms stdClasses (e.g. from a JSON object) to valid Nodes.
     * Calls the parse function of a subclass according to the node's type. Subclasses
     * override this function in order to provide the concrete parsing functionality.
     *
     * @param stdClass $node
     * @return Node
     * @throws ParseError if node is unknown
     */
    public static function parse($node) {
        // unpack container of one node
        if (is_array($node) && sizeof($node) == 1) {
            $node = $node[0];
        }
        // parse node
        switch ($node->node) {
            case self::ASSIGN_NODE: return AssignNode::parse($node);
            case self::COMPARE_NODE: return CompareNode::parse($node);
            case self::CONSTANT_NODE: return ConstantNode::parse($node);
            case self::IF_NODE: return IfNode::parse($node);
            case self::VAR_NODE: return VarNode::parse($node);
            case self::WHILE_NODE: return WhileNode::parse($node);
            default: throw new ParseError("Unknown node: " . print_r($node, true));
        }
    }
}

class Tree {
	/** @var array */
	private $tree;

	public function __construct($tree) {
		$this->tree = self::parseBody($tree);
	}

    public function printSource($params = []) {
        if (!isset($params['indent'])) {
            $params['indent'] = 0;
        }
        foreach ($this->tree as $node) {
            /** @var $node Node */
            print($node->getSource($params) . PHP_EOL);
        }
    }

	public function printHtml($params = []) {
        $params['step'] = 0;
        foreach ($this->tree as $node) {
			/** @var $node Node */
			$node->printHtml($params);
		}
	}

    /**
     * @param $body array
     * @return array
     * @throws ParseError
     */
	public static function parseBody($body) {
		$nodes = array();
        if (!is_null($body)) {
            foreach ($body as $node) {
                $nodes[] = Node::parse($node);
            }
        }
		return $nodes;
	}
}

class TreeHelper {
    public static function getIndent($indent) {
        $str = "";
        for ($i = 0; $i < $indent; $i++){
            $str .= DEFAULT_INDENT;
        }
        return $str;
    }
}

class ParseError extends Exception {}