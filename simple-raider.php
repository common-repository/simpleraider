<?php 
/*
Plugin Name: Simple Raider
Plugin URI: http://simpleraider.polyvision.org
Description: a plugin for easy DKPG management and raid planing
Author: Alexander Bierbrauer
Version: 1.0rc3
Author URI: http://simpleraider.polyvision.org
*/

/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
require_once ('sr-system.php');

define ('SR_PLUGIN_PATH',ABSPATH.'/wp-content/plugins/simpleraider/');
define ('SR_PLUGIN_URL',get_option('siteurl').'/wp-content/plugins/simpleraider/');
define ('SR_VERSION','1.0.1');
define ('SR_TABLE_PREFIX',$table_prefix);
include_once (SR_PLUGIN_PATH.'/models/model_games.php');
include_once (SR_PLUGIN_PATH.'/template_tags.php');
/*
 * displaying the administration menu
 */
function sr_admin_actions()
{
	add_menu_page('simpleraider', 'Simple Raider', 10, __FILE__,'sr_admin_overview');
	
	//add_menu_page('Page title', 'Top-level menu title', 8, __FILE__, 'my_magic_function');
	//add_submenu_page(__FILE__, 'Page title', 'Sub-menu title', 8, __FILE__, 'my_magic_function');
	
}

/*
 hook functions
 */
 function sr_public_display(&$content){
	 $t = array_merge($_GET,$_POST);
	 if($t['page_id'] != get_option('sr_option_display_page_id','-1'))
	 {
		 return $content;
	 }
	
	// check user level for access
	// this has been modified, now you have to give explicity access for an user in the admin console of simpleraider
	global $current_user;
	get_currentuserinfo();
	global $wpdb;
	$rs = $wpdb->query('SELECT id FROM '.SR_TABLE_PREFIX.'sr_access WHERE user_id='.$current_user->ID);
	if($rs <= 0){
			print 'access denied to SimpleRaider';
			return;
	}; 
		
	  // we're in the front end mode, so let's set the default module to be public_raidplaner
	  if(isset($_GET['mod']) == false){ // we don't to overwrite the mod variable
		  $_GET['mod'] = 'Public_Raidplaner';
	  }
	 $sr_system = new SimpleRaiderSystem();
	 $sr_system->run();
 }

function sr_admin_overview(){
	 // we're in the front end mode, so let's set the default module to be public_raidplaner
	  if(isset($_GET['mod']) == false){ // we don't to overwrite the mod variable
		  $_GET['mod'] = 'Admin_Overview';
	  }
	  
	 $sr_system = new SimpleRaiderSystem();
	 $sr_system->run();
}
 
function sr_admin_general_options()
{
	
}

function sr_startup(){
	wp_enqueue_script( 'datetimepicker_css.js', get_option('siteurl').'/wp-content/plugins/simpleraider/js/datetimepicker_css.js');
	wp_enqueue_script( 'sr_tooltips.js', get_option('siteurl').'/wp-content/plugins/simpleraider/js/sr_tooltips.js');
	wp_enqueue_style('sr_general.css', get_option('siteurl').'/wp-content/plugins/simpleraider/css/sr_general.css');
	sr_get_headers_of_game_plugins();
}

function sr_get_headers_of_game_plugins(){
	global $wpdb;
	$res = $wpdb->get_results('SELECT gameplugin FROM '.$wpdb->prefix.'sr_games');
	for($i = 0; $i < count($res); $i++){
		$plugin_name = $res[$i]->gameplugin;
		if(strlen($plugin_name) > 3){
			// loading and getting the headers
			include_once (SR_PLUGIN_PATH.'/game_plugins/'.$plugin_name.'.php');
			$clsName ='SRGamePlugin_'.$plugin_name;
			$tClass = new $clsName();
			$headers_js = $tClass->getJavascriptHeaders();
			if($headers_js != null){
				$js_keys = array_keys($headers_js);
				for($x = 0; $x < count($js_keys); $x++){
					$tKey = $js_keys[$x];
					wp_enqueue_script( $tKey,$headers_js[$tKey]);
				}
			}
		}
	}
	
}

add_filter('the_content', 'sr_public_display');


//admin menu
add_action('admin_menu', 'sr_admin_actions'); 
add_action('init', 'sr_startup');
?>