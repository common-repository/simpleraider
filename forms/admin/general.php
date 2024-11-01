<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');
include_once (SR_PLUGIN_PATH.'/modules/admin/mod_admin_install.php');

function sr_forms_admin_navigation_panel(&$adodb,&$vars){
	$content = $content.'<div id="SR_NAVIGATION">';
	$content = $content.'<a href="?page='.$_GET['page'].'&mod=Admin_Overview"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/simpleraider_logo.png"></a>';
	$content = $content.'<table class="widefat fixed"><thead><tr>';
	$content = $content.'<th><a href="?page='.$_GET['page'].'&mod=Admin_RaidTemplate">raid templates</a></td>';
	$content = $content.'<th><a href="?page='.$_GET['page'].'&mod=Admin_Raidplan">raid management</a></td>';
	$content = $content.'<th><a href="?page='.$_GET['page'].'&mod=Admin_DKP">DKP management</a></td>';
	$content = $content.'<th><a href="?page='.$_GET['page'].'&mod=Admin_User">user management</a></td>';
	$content = $content.'<th><a href="?page='.$_GET['page'].'&mod=Admin_Options">options</a></td>';
	$content = $content.'</tr><thead></table>';
	$content = $content.'</div>';
	return $content;
}

function sr_forms_admin_footer(&$adodb,&$vars){
	
	$content = $content.'<div id="SR_FOOTER">';
	$content = $content.'SimpleRaider v'.SR_VERSION.', written by <a href="http://simpleraider.polyvision.org">Alexander Bierbrauer, polyvision</a> | this software is licensed under GNU GPL v3';
	$content = $content.'</div>';
	return $content;
}

function sr_forms_admin_overview(&$adodb,&$vars){
	$content = $content.'
	<h2>Welcome to SimpleRaider v'.SR_VERSION.'</h2>
	<br><br>
	'.sr_forms_admin_overview_check_database($adodb,$vars).'
	<p>
	<h3><a href="?page='.$_GET['page'].'&mod=Admin_RaidTemplate">raid templates</a></h3>
	Create raid templates which you can use to schedule raids for your guild. You can even specify items which are dropping during a raid.
	</p>
	<br><br>
	<p>
	<h3><a href="?page='.$_GET['page'].'&mod=Admin_Raidplan">raid management</a></h3>
	Schedule new raids with the raid manager or give DKP points to characters who participated with a raid
	</p>
	<br><br>
	<p>
	<h3><a href="?page='.$_GET['page'].'&mod=Admin_Options">options</a></h3>
	manage options for SimpleRaider like supported games. Here you can also specify game plugins for retrieving special information for your raid
	</p>
	';
	return $content;
}

function sr_forms_admin_overview_check_database(&$adodb,&$vars){
	if(ModAdmin_Install::checkDatabase($adodb) == false){
		$content = '<p><h3><a href="?page='.$_GET['page'].'&mod=Admin_Install&action=installDatabase">click here to create the database tables for SimpleRaider</h3><br><br></p>';
	}
	return $content;
}
?>
