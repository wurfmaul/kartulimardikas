<?php
class AssignNode extends Node {
	/** @var VarNode */
	protected $to;
	/** @var Node */
	protected $from;

	function __construct($to, $from) {
		global $l10n;
		$this->l10n = $l10n;
		$this->to = $to;
		$this->from = $from;
	}

	public function printHtml($id = null, $params = null) { ?>
		<!-- ASSIGN NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node assign-node" data-node-type="assign">
			<table>
				<tr>
					<td class="handle node-box top left">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['assign_node_title'] ?>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
				<tr>
					<td class="handle node-box left right">&nbsp;</td>
					<td>
						<ul class="assign-from sortable">
							<?php self::printNode($this->from) ?>
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
							<?php self::printNode($this->to) ?>
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
	protected $op;

	function __construct($left, $right, $op) {
		global $l10n;
		$this->l10n = $l10n;
		$this->left = $left;
		$this->right = $right;
		$this->op = $op;
	}

	public function printHtml($id = null, $params = null) { ?>
		<!-- COMPARE NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node compare-node" data-node-type="compare">
			<table>
				<tr>
					<td class="handle node-box top left">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['compare_node_title'] ?>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
				<tr>
					<td class="handle node-box right left">&nbsp;</td>
					<td>
						<ul class="compare-left sortable">
							<?php self::printNode($this->left) ?>
						</ul>
					</td>
				</tr>
				<tr>
					<td class="handle node-box left">&nbsp;</td>
					<td class="node-box top right bottom half-width">
						<?= $this->l10n['compare_node_operation'] ?>:
						<select class="compare-operation">
							<?php $op = $this->isPrototype ? "" : $this->op ?>
							<option value="lt"<?php if ($op == "lt"): ?> selected="selected"<?php endif ?>>&lt;</option>
							<option value="le"<?php if ($op == "le"): ?> selected="selected"<?php endif ?>>&le;</option>
							<option value="eq"<?php if ($op == "eq"): ?> selected="selected"<?php endif ?>>&equals;</option>
							<option value="ge"<?php if ($op == "ge"): ?> selected="selected"<?php endif ?>>&ge;</option>
							<option value="gt"<?php if ($op == "gt"): ?> selected="selected"<?php endif ?>>&gt;</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="handle node-box right bottom left">&nbsp;</td>
					<td>
						<ul class="compare-right sortable">
							<?php self::printNode($this->right) ?>
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

	public function printHtml($id = null, $params = null)
	{ ?>
		<!-- CONSTANT NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node constant-node" data-node-type="constant">
			<table>
				<tr>
					<td class="handle node-box top left bottom">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['constant_node_title'] ?>
						<input class="constant-value" value="<?= $this->isPrototype ? "" : $this->value ?>" />
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
	/** @var Node */
	protected $then;
	/** @var Node */
	protected $else;

	function __construct($cond, $then, $else) {
		global $l10n;
		$this->l10n = $l10n;
		$this->cond = $cond;
		$this->then = $then;
		$this->else = $else;
	}

	public function printHtml($id = null, $params = null) { ?>
		<!-- IF NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node if-node" data-node-type="if">
			<table>
				<tr>
					<td class="handle node-box top left">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['if_node_title'] ?>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
				<tr>
					<td class="handle node-box right left">&nbsp;</td>
					<td>
						<ul class="if-condition sortable">
							<?php self::printNode($this->cond) ?>
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
							<?php self::printNode($this->then) ?>
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
							<?php self::printNode($this->else) ?>
						</ul>
					</td>
				</tr>
			</table>
		</li>
	<?php }
}

class VarNode extends Node {
	protected $id;
	protected $name;

	function __construct($varId, $varName) {
		global $l10n;
		$this->l10n = $l10n;
		$this->id = $varId;
		$this->name = $varName;
	}

	public function printHtml($id = null, $params = null)
	{ ?>
		<!-- VAR NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node var-node" data-node-type="var">
			<table>
				<tr>
					<td class="handle node-box top left bottom">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['variable_node_title'] ?>
						<select class="var-value">
							<?php if (!is_null($params) && isset($params['vars'])): ?>
							<?php foreach ($params['vars'] as $vid => $var): if ($vid === "prototype") continue ?>
							<option id="var-<?= $vid ?>" value="var-<?= $vid ?>" selected="selected"><?= $var->name ?></option>
							<?php endforeach ?>
							<?php endif ?>
						</select>
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

	function __construct($cond, $body) {
		global $l10n;
		$this->l10n = $l10n;
		$this->cond = $cond;
		$this->body = $body;
	}

	public function printHtml($id = null, $params = null)
	{ ?>
		<!-- WHILE NODE -->
		<li<?php if (!is_null($id)): ?> id="<?= $id ?>"<?php endif ?> class="node while-node" data-node-type="while">
			<table>
				<tr>
					<td class="handle node-box top left">&nbsp;</td>
					<td class="node-box top right bottom full-width">
						<?= $this->l10n['while_node_title'] ?>
						<button type="button" class="close node-remove" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</td>
				</tr>
				<tr>
					<td class="handle node-box right left">&nbsp;</td>
					<td>
						<ul class="while-condition sortable">
							<?php self::printNode($this->cond) ?>
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
							<?php self::printNode($this->body) ?>
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
	public static $ASSIGN = "assign";
	public static $COMPARE = "compare";
	public static $CONSTANT = "constant";
	public static $IF = "if";
	public static $VAR = "var";
	public static $WHILE = "while";

	/** @var bool */
	protected $isPrototype = false;
	/** @var bool */
	protected $isValid = true;

	public function setValid($valid = true) {
		$this->isValid = $valid;
	}

	/**
	 * Returns HTML code which represents the node.
	 * @param null $id int
	 * @param null $params array
	 */
	public abstract function printHtml($id = null, $params = null);

	/**
	 * Calls the printHtml method of the Node $node, if it's not null and not a prototype.
	 * @param $node Node
	 * @return bool True if printHtml method was called.
	 */
	public static function printNode($node) {
		// unpack container of one node
		if (!($node instanceof Node) && sizeof($node) == 1) {
			$node = $node[0];
		}
		// don't handle prototypes and empty nodes
		if (is_null($node) || $node->isPrototype)
			return false;
		// call printHtml() for valid nodes
		$node->printHtml();
		return true;
	}

	public static function printPrototype($type, $params = null) {
		/** @var $node Node */
		$node = null;
		switch ($type) {
			case self::$ASSIGN: $node = new AssignNode(null, null); break;
			case self::$COMPARE: $node = new CompareNode(null, null, null); break;
			case self::$CONSTANT: $node = new ConstantNode(null); break;
			case self::$IF: $node = new IfNode(null, null, null); break;
			case self::$VAR: $node = new VarNode(null, null); break;
			case self::$WHILE: $node = new WhileNode(null, null); break;
			default: throw new Exception("No prototype prepared for '$type'.");
		}
		$node->isPrototype = true;
		$node->printHtml("$type-node", $params);
	}
}

class Tree {
	/** @var array */
	private $tree;

	function __construct($tree) {
		$this->tree = $this->parseBody($tree);
	}

	public function printHtml() {
		foreach ($this->tree as $node) {
			/** @var $node Node */
			$node->printHtml();
		}
	}

	private function parseBody($body) {
		$nodes = array();
		foreach ($body as $node) {
			$nodes[] = $this->parse($node);
		}
		return $nodes;
	}

	/**
	 * @param $node array
	 * @return Node
	 * @throws ParseError if node is unknown
	 */
	private function parse($node) {
		// unpack container of one node
		if (!isset($node['node']) && sizeof($node) == 1) {
			$node = $node[0];
		}
		// parse node
		switch ($node['node']) {
			case Node::$ASSIGN: return $this->parseAssign($node);
			case Node::$COMPARE: return $this->parseCompare($node);
			case Node::$CONSTANT: return $this->parseConstant($node);
			case Node::$IF: return $this->parseIf($node);
			case Node::$VAR: return $this->parseVar($node);
			case Node::$WHILE: return $this->parseWhile($node);
			default: throw new ParseError("Unknown node: " . print_r($node, true));
		}
	}

	private function parseAssign($node) {
		$from = isset($node['from']) ? $this->parse($node['from']) : null;
		$to = isset($node['to']) ? $this->parse($node['to']) : null;

		$_node = new AssignNode($to, $from);
		$_node->setValid(isset($node['from'], $node['to']));
		return $_node;
	}

	private function parseCompare($node) {
		$left = isset($node['left']) ? $this->parse($node['left']) : null;
		$right = isset($node['right']) ? $this->parse($node['right']) : null;
		$op = isset($node['operator']) ? $node['operator'] : null;

		$_node = new CompareNode($left, $right, $op);
		$_node->setValid(isset($node['left'], $node['right'], $node['operator']));
		return $_node;
	}

	private function parseConstant($node) {
		$value = isset($node['value']) ? $node['value'] : null;

		$_node = new ConstantNode($value);
		$_node->setValid(isset($node['value']));
		return $_node;
	}

	private function parseIf($node) {
		$cond = isset($node['condition']) ? $this->parse($node['condition']) : null;
		$body = isset($node['ifBody']) ? $this->parseBody($node['ifBody']) : null;
		$else = isset($node['elseBody']) ? $this->parseBody($node['elseBody']) : null;

		$_node = new IfNode($cond, $body, $else);
		$_node->setValid(isset($node['condition'], $node['ifBody'], $node['elseBody']));
		return $_node;
	}

	private function parseVar($node) {
		$id = isset($node['id']) ? $node['id'] : null;
		$name = isset($node['name']) ? $node['name'] : null;

		$_node = new VarNode($id, $name);
		$_node->setValid(isset($node['id'], $node['name']));
		return $_node;
	}

	private function parseWhile($node) {
		$condition = isset($node['condition']) ? $this->parse($node['condition']) : null;
		$body = isset($node['body']) ? $this->parseBody($node['body']) : null;

		$_node = new WhileNode($condition, $body);
		$_node->setValid(isset($node['condition'], $node['body']));
		return $_node;
	}
}

class ParseError extends Exception {}