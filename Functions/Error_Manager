<?php
$error_in_manager = false;
function error_function(...$string) {
	global $error_in_manager;
	$error_in_manager = true;
	echo("\e[1;31mERROR : \e[0m");
	foreach ($string as $morcel) {
		echo($morcel);
	}
	echo("\n");
	
}

function stop_if_error() {
	global $error_in_manager;
	if ($error_in_manager) {
		echo ("\nAt least one error have been detected. Program is terminated.\n");
		exit();
	}
}