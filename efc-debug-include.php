<?php

/*
	efc-debug-include.php
	
	If you want to be able to create messages for the debug menu
	from your functions.php file, then include this script early in
	functions.php with...
	
	include_once(dirname(__FILE__).'/../../plugins/efc-debug/efc-debug-include.php');
	
	...assuming standard WordPress directory structure.
	
	Then you will be able to add messages to the Debug Report with...
	
	if (function_exists('dbug_report')) dbug_report('message: ',$variable);
*/

if ( ! function_exists('dbug_report') ) {

	function dbug_report() {
		global $dbug_note;
		global $dbug_array;
		
		if (! isset($dbug_note) ) {
			$dbug_note = '';
			add_action('wp_footer', 'dbug_report_in_footer', 9999);
		}
		
		$out = '';
		$m = func_get_args();
		foreach($m as $message) {
			if ( is_string($message) ) {
				$out .= $message;
				$dbug_array[] = '<p>'.htmlspecialchars($message).'</p>';
			} else {
				$message = print_r($message, true);
				$out .= $message;
				$dbug_array[] = '<pre>'.htmlspecialchars($message).'</pre>';
			}
		}
		$dbug_note .= "\n" . $out;
	}
	
	function dbug_report_in_footer() {
		global $dbug_note;
		global $dbug_array;
		global $DBug;
		global $wp_query;
					
		if (is_object($DBug)) {
			$DBug->add('<h2>Debug Report</h2>');
			foreach ( $dbug_array as $message ) {
				$DBug->add($message);
				echo '<!-- ' . str_replace('-->', '==>', $message ) . "-->\n";
			}
			$DBug->add('<h2>$wp_query</h2>');
			$DBug->add('<pre>'.htmlspecialchars(print_r($wp_query,true)).'</pre>');
			
			$DBug->add('<h2>$_ENV</h2>');
			$DBug->add('<pre>'.htmlspecialchars(print_r($_ENV,true)).'</pre>');
		}
	}
}

