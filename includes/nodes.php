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

	public function toHtml($id = null, $params = null) { ?>
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
							<?= $this->isPrototype ? "" : $this->from->toHtml() ?>
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
							<?= $this->isPrototype ? "" : $this->to->toHtml() ?>
						</ul>
					</td>
				</tr>
			</table>
		</li>
	<?php }

	public static function getPrototype($params = null)
	{
		$node = new self(null, null, null);
		$node->isPrototype = true;
		$node->toHtml("assign-node", $params);
	}
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

	public function toHtml($id = null, $params = null) { ?>
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
							<?php $this->isPrototype ? "" : $this->left->toHtml() ?>
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
							<?php $this->isPrototype ? "" : $this->right->toHtml() ?>
						</ul>
					</td>
				</tr>
			</table>
		</li>
	<?php }

	public static function getPrototype($params = null)
	{
		$node = new self(null, null, null);
		$node->isPrototype = true;
		$node->toHtml("compare-node", $params);
	}
}

class ConstantNode extends Node {
	protected $value;

	function __construct($value) {
		global $l10n;
		$this->l10n = $l10n;
		$this->value = $value;
	}

	public function toHtml($id = null, $params = null)
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

	public static function getPrototype($params = null)
	{
		$node = new self(null);
		$node->isPrototype = true;
		$node->toHtml("constant-node", $params);
	}
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

	public function toHtml($id = null, $params = null) { ?>
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
							<?= $this->isPrototype ? "" : $this->cond->toHtml() ?>
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
							<?= $this->isPrototype ? "" : $this->then->toHtml() ?>
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
							<?= $this->isPrototype ? "" : $this->else->toHtml() ?>
						</ul>
					</td>
				</tr>
			</table>
		</li>
	<?php }

	public static function getPrototype($params = null)
	{
		$node = new self(null, null, null);
		$node->isPrototype = true;
		$node->toHtml("if-node", $params);
	}
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

	public function toHtml($id = null, $params = null)
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

	public static function getPrototype($params = null)
	{
		$node = new self(null, null);
		$node->isPrototype = true;
		$node->toHtml("var-node", $params);
	}
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

	public function toHtml($id = null, $params = null)
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
							<?= $this->isPrototype ? "" : $this->cond->toHtml() ?>
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
							<?= $this->isPrototype ? "" : $this->body->toHtml() ?>
						</ul>
					</td>
				</tr>
			</table>
		</li>
	<?php }

	public static function getPrototype($params = null)
	{
		$node = new self(null, null);
		$node->isPrototype = true;
		$node->toHtml("while-node", $params);
	}
}

/**
 * Superclass of all Nodes. 
 * @author Wolfgang Küllinger
 */
abstract class Node {
	/** @var bool */
	protected $isPrototype = false;

	/**
	 * Returns HTML code which represents the node.
	 * @param null $id int
	 * @param null $params array
	 */
	public abstract function toHtml($id = null, $params = null);

	public static function getPrototype($params = null) {}
}