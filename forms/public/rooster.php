<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_raidplan_team.php');
include_once (SR_PLUGIN_PATH.'/models/model_dkp.php');

function sr_forms_public_rooster_index(&$adodb,&$vars){

	$rooster_groups = &$vars['rooster_groups'];
	$content = $content.'
		<form action="?page_id='.$_GET['page_id'].'&mod=Public_Rooster&action=index" method="post">
		<p>game:<span> <select name="game_id">'.ModelGames::getOptions($adodb).'</select></span>
		<input type="submit" value="show roster"></p>
		</form>
	';
	
	$content = $content.'<div class="SR_RAID_ROOSTER"><h1 class="SR_RAID_H1">rooster</h1>';
	for($i = 0; $i < count($rooster_groups); $i++)
	{
		$tGroup = $rooster_groups[$i];
	
		$content = $content.'<div class="TABLE_OVERVIEW_ROOSTER">';
		$content = $content.'<h1>';
		$content = $content.$tGroup->type->getIconImageTag().$tGroup->type->title.' ('.count($tGroup->characters).')';
		$content = $content.'</h1>';
		
		$content = $content.'<table width="100%">';
		for($x = 0; $x < count($tGroup->characters); $x++){
			$char = $tGroup->characters[$x];
			//print 'CHAR SO: '.$char->charname.'<br>';
			$content = $content.'<tr><td>';
			$content = $content.'<a href="?page_id='.$_GET['page_id'].'&mod=Public_DKP&action=viewCharacterDKP&id='.$char->id.'">'.$char->charname.'</a></td>';
			$content = $content.'<td>'.$char->sumDKP.'</td>';
			$content = $content.'</tr>';
		}
		$content = $content.'</table></div>';
	}
	return $content;
}
?>
