<h1>Selection sort</h1>
<?php
$a = array(7, 5, 3, 1, 2, 6, 4);
$len = sizeof($a);
$r = array();

print_r($a); echo "<br>";
$a = sel_sort($a, $len, $r);
print_r($a);

function sel_sort($a, $len, $r) {
	eval(file_get_contents('selection.txt'));
	return $a;
}
?>