<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
 
function sr_forms_public_overview_raidplan(&$adodb,&$vars){
	
	$content = $content.'<h2>scheduled raids</h2>';
	
	$raids = $vars['raids'];
	$content = $content.'
		<table class="STYLED_TABLE" width="100%">
		<thead>
		<tr>
			<th>icon</th>
			<th>raid start</th>
			<th>title</th>
			<th>wl | so | players</th>
		</tr></thead>';
	
	$css_class='STYLED_TABLE_TD_1';
	for($i = 0; $i < count($raids); $i++){
		$tRaid = $raids[$i];
		$tRaid->fetchData($adodb);
		$num_players_signed_on = count(ModelRaidplanTeam::getCharactersByStatus($adodb,$tRaid->id,1));
		$num_players_waiting = count(ModelRaidplanTeam::getCharactersByStatus($adodb,$tRaid->id,2));
		$content = $content.'<tr>';
		$content = $content.'<td class="'.$css_class.'">'.$tRaid->template->getIconImageUrl().'</td>';
		$content = $content.'<td class="'.$css_class.'"><a href="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner&action=showRaidDetailView&id='.$tRaid->id.'">'.mysql2date('l, d.m H:i',$tRaid->raidstart).'</a></td>';
		$content = $content.'<td class="'.$css_class.'">'.$tRaid->title.'</td>';
		$content = $content.'<td class="'.$css_class.'"> '.$num_players_waiting.' | '.$num_players_signed_on.' | '.$tRaid->numplayers.'</td>';
		$content = $content.'</tr>';

		if(strcmp($css_class,'STYLED_TABLE_TD_1') == 0){
			$css_class='STYLED_TABLE_TD_2';
		}else{
			$css_class='STYLED_TABLE_TD_1';
		}
	}
	$content = $content.'</table>';
	return $content;
}
?>