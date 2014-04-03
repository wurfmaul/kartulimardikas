<?php
class AssignNode extends StateNode {
	private $var;
	private $val;

	function __construct($var, $val) {
		$this->var = $var;
		$this->val = $val;
	}

	public function generateState() {
		return parent::stateTemplate("$this->var = $this->val;
				display.setval('$this->var', $this->var);");
	}

	public function generateXML() {

	}

	public function toString() {
		return "$this->var = $this->val";
	}
}

class CondNode extends Node {
	private $varLeft;
	private $varRight;
	private $op;

	function __construct($varLeft, $varRight, $op) {
		$this->varLeft = $varLeft;
		$this->varRight = $varRight;
		$this->op = $op;
	}

	public function generateXML() {

	}

	public function toString() {
		return "$this->varLeft $this->op $this->varRight";
	}

	public function instruction() {
		return "display.compare('$this->varLeft, $this->varRight);";
	}
}

class DecNode extends StateNode {
	private $var;

	function __construct($var) {
		$this->var = $var;
	}

	public function generateState() {
		return parent::stateTemplate("$this->var--;
				display.setval('$this->var', $this->var);");
	}

	public function generateXML() {

	}

	public function toString() {
		return "$this->var--";
	}
}

class DoneNode extends StateNode {
	public function generateState() {
		return parent::stateTemplate("done = true;
		pause();
		ctrl.set(ctrl.END);");
	}

	public function generateXML() {

	}

	public function toString() {
		return "DONE";
	}
}

class ForNode extends StateNode {
	/**
	 * The four parts of a for-loop:
	 * for($init; $cond; $after) { $body }
	 * @var Node
	 */
	private $init, $cond, $after, $body;
	
	function __construct($init, $cond, $after, $body) {
		$this->init = $init;
		$this->cond = $cond;
		$this->after = $after;
		$this->body = $body;
	}
	
	function generateState() {
// 		$content = $this->init->generateState();
// 		$content .= $this->after->generateState();
		$content = "case $this->state:".PHP_EOL;
		$content .= "if (".$this->cond->toString().") {".PHP_EOL;
		$content .= "state = $this->body->state;".PHP_EOL;
		$content .= "} else {".PHP_EOL;
		$content .= "state = $this->nextState;".PHP_EOL;
		$content .= "}".PHP_EOL."break;";
	}
	
	function generateXML() {
	}
	
	function toString() {
	}
	
}

class IfNode extends StateNode {
	private $cond;
	private $then;
	private $else;

	function __construct($cond, $then, $else) {
		$this->cond = $cond;
		$this->then = $then;
		$this->else = $else;
	}

	public function generateState() {
		if($this->cond == null || $this->then == null || $this->else == null) {
			throw new BadFunctionCallException("not fixed yet");
		} else {
			$content = "case $this->state:\n\tif (";
			$content .= $this->cond->toString();
			$content .= ") {\n\t\tstate = ";
			$content .= $this->then->state;
			$content .= ";\n\t} else {\n\t\tstate = ";
			if ($this->else == null)
				$content .= $this->nextState;
			else 
				$content .= $this->else->state;
			$content .= ";\n\t}\n\t";
			$content .= $this->cond->instruction();
			$content .= "\n\tbreak;";
			return $content;
		}
	}

	public function generateXML() {

	}

	public function toString() {
		return "TODO";
	}
}

class IncNode extends StateNode {
	private $var;

	function __construct($var) {
		$this->var = $var;
	}

	public function generateState() {
		return parent::stateTemplate("$this->var++;
				display.setval('$this->var', $this->var);");
	}

	public function generateXML() {

	}

	public function toString() {
		return "$this->var++";
	}
}

/**
 * Superclass of all Nodes. 
 * @author Wolfgang Küllinger
 */
abstract class Node {
	/**
	 * Returns XML code which represents the node. A bidirectional 
	 * transformation (XML <-> PHP class) should be possible.
	 */
	public abstract function generateXML();
	
	/**
	 * Returns the very basic operation of this node (e.g. "a < b"). 
	 */
	public abstract function toString();
	
	/**
	 * Optional function returning additional JS operations that 
	 * should be performed when executed. Default: empty
	 */
	public function instruction() { }
}

/**
 * Superclass of nodes that are generating states.
 * @author Wolfgang Küllinger
 */
abstract class StateNode extends Node {
	/**
	 * Represents the state of this node.
	 * @var int
	 */
	public $state;
	/**
	 * Represents the state this node will refer to.
	 * @var int
	 */
	public $nextState;
	
	public function setStates($state, $nextState) {
		$this->state = $state;
		$this->nextState = $nextState;
	}
	
	/**
	 * This is a small template giving the usual surroundings of a 
	 * typical state (e.g. "case X: ... state=nextState; break;").
	 * @param string Text that should fill the template (e.g. the "..." 
	 * above).
	 * @return string Full JS code representing one state.
	 */
	protected function stateTemplate($content) {
		return "case $this->state:
		$content
		state = $this->nextState;
		break;";
	}
	
	/**
	 * Every node that is to refer to a concrete state afterwards must
	 * implement this function. Once called, it generates the JS code for 
	 * the state.
	 * @return string Full JS code representing one state.
	 */
	public abstract function generateState();
}
?>