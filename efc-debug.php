<?php
/*
Plugin Name: Eric's Debug Menu
Plugin URI: http://tenseg.net
Description: Puts a simple debug umbrella in the Admin bar of admin users.
Version: 1.2
Author: Eric Celeste
Author URI: http://eric.clst.org/
License: GNU Lesser GPL 2.1 (http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt)
*/

/*
	History:
	v.1.0: 130922 (efc) beginning
	v.1.1: 131113 (efc) use the umbrella
	v.1.2: 131116 (efc) now included on admin pages as well
	// v.1.3: 140526 (efc) added jquery enqueue just in case
*/
 
class EFC_Debug {

	function EFC_Debug() {
		$this->version = '1.0';
		$this->message = '';
		add_action( 'admin_bar_menu', array( $this, 'menu' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_footer', array( $this, 'render' ), 10999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_footer', array( $this, 'render' ), 10999 );
		// wp_enqueue_script('jquery');
	}
	
	function add( $message ) {
		$this->message .= $message;
	}
	
	function menu() {
		global $wp_admin_bar;
		
		if ( !current_user_can('edit_plugins') )
			return; // don't show the debug menu to non-admin users
		
		$menu_id = WP_DEBUG ? 'efc_debug_menu_wp_debug' : 'efc_debug_menu';
		
		$wp_admin_bar->add_menu( array(
			'id'   => $menu_id,
			'parent' => 'top-secondary',
			'meta' => array( 'onclick' => 'jQuery("#efc-debug").toggle()' ),
			'title' => '&#9730;', // this shows an umbrella (we hope)
			'href' => '#' 
		) );
	}
	
	function scripts() {
		wp_enqueue_style( 
			'efc-debug', // handle
			plugins_url().'/efc-debug/efc-debug.css', // source in plugins
			false, // dependencies
			$this->version, // version
			'screen' // media
		);	
	}
	
	function render() {
		global $wp_query;
		
		if ( ! $this->message ) {
			$wpq = htmlspecialchars(print_r($wp_query,true));
			$env = htmlspecialchars(print_r($_ENV,true));
			$this->message = <<<MESSAGE
<p>You can put your own messages here by putting this early in your code:</p>

<pre>include_once(dirname(__FILE__).'/../../plugins/efc-debug/efc-debug-include.php');</pre>

<p>Then add messages with:</p>

<pre>if (function_exists('dbug_report')) dbug_report('text',\$variable);</pre>

<p>If the Debug menu in the Admin Bar is red, that just means the WP_DEBUG is defined. It serves as a reminder in case you want to turn that off before going into production.</p>

<h2>\$wp_query</h2>

<pre>$wpq</pre>

<h2>\$_ENV</h2>

<pre>$env</pre>
MESSAGE;
		}
		$message = json_encode($this->message);
		echo <<<SCRIPT
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery(document.body).append('<div id="efc-debug"><div id="efc-debug-inner">'+$message+'</div></div>');
});
</script>
SCRIPT;
	}

}

add_action( "init", "efc_debug_init" );
function efc_debug_init() {
	global $DBug;
	if ( ! is_object($DBug) ) {
		$DBug = new EFC_Debug();
	}
}