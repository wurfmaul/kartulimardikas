<?php
require 'nodes.php';

function createStates() {
	$nodes[0] = new DoneNode();
	$nodes[1] = new AssignNode("a[i]", "t");
	$nodes[2] = new AssignNode("a[min]", "a[i]");
	$nodes[3] = new AssignNode("t", "a[min]");
	$nodes[4] = new AssignNode("min", "j");
	$nodes[5] = new CondNode("a[j]", "a[min]", "<");
	$nodes[6] = new IfNode($nodes[5], $nodes[4], null);
	$nodes[7] = new AssignNode("j", "i + 1");
	$nodes[8] = new CondNode("j", "len", "<");
	$nodes[9] = new IncNode("j");
	$nodes[10] = new ForNode($nodes[7], $nodes[8], $nodes[9], $nodes[6]);
	$nodes[11] = new AssignNode("min", "i");
	$nodes[12] = new AssignNode("i", 0);
	$nodes[13] = new CondNode("i", "len", "<");
	$nodes[14] = new IncNode("i");
	$nodes[15] = new ForNode($nodes[12], $nodes[13], $nodes[14], $nodes[11]);
	
	// fix states
	$nodes[12]->setStates(0, 1);
	$nodes[13]->setStates(1, 2);
	$nodes[11]->setStates(2, 3);
	$nodes[7]->setStates(3, 4);
	
	return $nodes;
}
?>

<pre>
switch (state) {
<?php 
$nodes = createStates();
foreach ($nodes as $node) {
	try {
		if($node instanceof StateNode) {
			echo $node->generateState();
			echo "<br />";
		}
	} catch (Exception $e) {
		echo "EXCEPTION: " . $e->getMessage();
	}
}
?>
<br />
default:
	alert("ERROR");
}
</pre>