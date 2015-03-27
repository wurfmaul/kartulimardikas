<?php
// PHP >= 5.2.0
define ("FOLDER", "js-gen/");

// FIXME remove example!
generate(2749);

function generate($id)
{
    // compute filename
// 	$filename = "algo$id-";
// 	$stamp = new DateTime ( "now", new DateTimeZone ( "UTC" ) );
// 	$filename .= $stamp->format ( "YmdHis" );
// 	$filename .= ".js";

    $filename = "algo.js";
    // FIXME remove static name!

    // contents
    $varDecl = file_get_contents("db/algo_vardecl.js");
    $varInit = file_get_contents("db/algo_init.js");
    $states = file_get_contents("db/algo_states.js");

    // write to file
    $content = file_get_contents("machine/algo.frame.js");
    if ($content) {
        $content = str_replace("//~VARDECL~//", $varDecl, $content);
        $content = str_replace("//~VARRESET~//", $varInit, $content);
        $content = str_replace("//~STATEMACHINE~//", $states, $content);

        if (file_put_contents(FOLDER . $filename, $content)) {
// 			echo "JavaScript file '$filename' successfully generated!";
        } else {
            echo "<h1>Error: could not write generated file!</h1>";
        }
    } else {
        echo "<h1>Could not read frame file!</h1>";
    }
}

?>