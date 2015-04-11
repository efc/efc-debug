<?php
/*
Plugin Name: 		Eric's Debug Menu
Plugin URI: 		https://github.com/efc/efc-debug
GitHub Plugin URI: 	https://github.com/efc/efc-debug
Description: 		Puts a simple debug umbrella in the Admin bar of admin users.
Version: 			1.3
Author: 			Eric Celeste
Author URI: 		http://eric.clst.org/
License: 			The MIT License 
License URI:		http://opensource.org/licenses/MIT
*/

/*
Copyright (c) 2015, Eric Celeste

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

/*
	History:
	v.1.0:   130922 (efc) beginning
	v.1.1:   131113 (efc) use the umbrella
	v.1.2:   131116 (efc) now included on admin pages as well
	v.1.2.1: 150410 (efc) MIT License
	v.1.2.2:              added CHANGES.md
	v.1.3:   140526 (efc) added jquery enqueue just in case
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