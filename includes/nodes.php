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

	public function printHtml($id, $params) { ?>
		<!-- ASSIGN NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node assign-node" data-node-type="assign">
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

	public function printHtml($id, $params) {
        $selected_op = $this->isPrototype ? "" : $this->op;
    ?>
		<!-- COMPARE NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node compare-node" data-node-type="compare">
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

	public function printHtml($id, $params)
	{ ?>
		<!-- CONSTANT NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node constant-node" data-node-type="constant">
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

	public function printHtml($id, $params) { ?>
		<!-- IF NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node if-node" data-node-type="if">
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

	public function printHtml($id, $params)
	{
        $vars = !is_null($params) && isset($params['vars']) ? $params['vars'] : array();
        unset ($vars['prototype']);
        $selected = isset($vars[$this->vid]) ? $vars[$this->vid]->name : '';
    ?>
		<!-- VAR NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node var-node" data-node-type="var">
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

	public function printHtml($id, $params)
	{ ?>
		<!-- WHILE NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node while-node" data-node-type="while">
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
	 * @param $id int
	 * @param $params array
	 */
	public abstract function printHtml($id, $params);

    /**
     * Calls the printHtml method of the Node $node, if it's not null and not a prototype.
     * @param $node Node
     * @param $params array
     * @return bool True if printHtml method was called.
     */
	public static function printNode($node, $params) {
		// unpack container of one node
		if (!($node instanceof Node) && sizeof($node) == 1) {
			$node = $node[0];
		}
		// don't handle prototypes and empty nodes
		if (is_null($node) || $node->isPrototype)
			return false;
		// call printHtml() for valid nodes
		$node->printHtml(null, $params);
		return true;
	}

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
		$node->printHtml("$type-node", $params);
	}
}

class Tree {
	/** @var array */
	private $tree;

	public function __construct($tree) {
		$this->tree = $this->parseBody($tree);
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
		foreach ($this->tree as $node) {
			/** @var $node Node */
			$node->printHtml(null, $params);
		}
	}

    /**
     * @param $body array
     * @return array
     * @throws ParseError
     */
	private function parseBody($body) {
		$nodes = array();
        if (!is_null($body)) {
            foreach ($body as $node) {
                $nodes[] = $this->parse($node);
            }
        }
		return $nodes;
	}

	/**
	 * @param $node stdClass
	 * @return Node
	 * @throws ParseError if node is unknown
	 */
	private function parse($node) {
		// unpack container of one node
		if (is_array($node) && sizeof($node) == 1) {
			$node = $node[0];
        }
        // parse node
		switch ($node->node) {
			case Node::ASSIGN_NODE: return $this->parseAssign($node);
			case Node::COMPARE_NODE: return $this->parseCompare($node);
			case Node::CONSTANT_NODE: return $this->parseConstant($node);
			case Node::IF_NODE: return $this->parseIf($node);
			case Node::VAR_NODE: return $this->parseVar($node);
			case Node::WHILE_NODE: return $this->parseWhile($node);
			default: throw new ParseError("Unknown node: " . print_r($node, true));
		}
	}

	private function parseAssign($node) {
		$from = isset($node->from) ? $this->parse($node->from) : null;
		$to = isset($node->to) ? $this->parse($node->to) : null;

		$_node = new AssignNode($to, $from);
		$_node->setValid(isset($node->from, $node->to));
		return $_node;
	}

	private function parseCompare($node) {
		$left = isset($node->left) ? $this->parse($node->left) : null;
		$right = isset($node->right) ? $this->parse($node->right) : null;
		$op = isset($node->operator) ? $node->operator : null;

		$_node = new CompareNode($left, $right, $op);
		$_node->setValid(isset($node->left, $node->right, $node->operator));
		return $_node;
	}

	private function parseConstant($node) {
		$value = isset($node->value) ? $node->value : null;

		$_node = new ConstantNode($value);
		$_node->setValid(isset($node->value) && is_numeric($value));
		return $_node;
	}

	private function parseIf($node) {
		$cond = isset($node->condition) ? $this->parse($node->condition) : null;
		$body = isset($node->ifBody) ? $this->parseBody($node->ifBody) : null;
		$else = isset($node->elseBody) ? $this->parseBody($node->elseBody) : null;

		$_node = new IfNode($cond, $body, $else);
		$_node->setValid(isset($node->condition) &&
            (isset($node->ifBody) || isset($node->elseBody)));
		return $_node;
	}

	private function parseVar($node) {
        $vid = isset($node->vid) ? $node->vid : null;

		$_node = new VarNode($vid);
		$_node->setValid(isset($node->vid));
		return $_node;
	}

	private function parseWhile($node) {
		$condition = isset($node->condition) ? $this->parse($node->condition) : null;
		$body = isset($node->body) ? $this->parseBody($node->body) : null;

		$_node = new WhileNode($condition, $body);
		$_node->setValid(isset($node->condition, $node->body));
		return $_node;
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