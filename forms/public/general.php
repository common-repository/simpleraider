<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');

function sr_forms_public_navigation_panel(&$adodb,&$vars){
	
	$content = $content.'
	<div id="SR_NAVIGATION">
	<img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/simpleraider_logo.png">
	<ul>
		
			<li><a href="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner">raids</a></li>
			<li><a href="?page_id='.$_GET['page_id'].'&mod=Public_DKP">DKP</a></li>
			<li><a href="?page_id='.$_GET['page_id'].'&mod=Public_Statistic">statistics</a></li>
			<li><a href="?page_id='.$_GET['page_id'].'&mod=Public_Character">characters</a></li>
			<li><a href="?page_id='.$_GET['page_id'].'&mod=Public_Rooster">rooster</a></li>
	</ul>
	</div>
	<br><br>
	';
	return $content;
}
?>
