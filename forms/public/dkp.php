<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');
include_once (SR_PLUGIN_PATH.'/models/model_character_types.php');

function sr_forms_public_dkp_overview(&$adodb,&$vars){
	
	$dkp_character_summary = $vars['dkp_character_summary'];
	
	$content = $content.'<h2>character dkp overview</h2>';
	
	for($i = 0; $i < count($dkp_character_summary); $i++){
		$entry = $dkp_character_summary[$i];
		
		$content = $content.'<p>'.$entry->char->charname.' '.$entry->sum.' dkp</p>';	
	}
	
	// dkp detail summary
	$dkp_character_detail_summary = $vars['dkp_character_detail_summary'];
	for($i = 0; $i < count($dkp_character_detail_summary); $i++){
		$detailEntry = 	$dkp_character_detail_summary[$i];
		
		$css_class='STYLED_TABLE_TD_1';
		$content = $content.'<h2>'.$detailEntry->char->charname.'</h2>
		<table class="STYLED_TABLE" cellspacing="0">
		<thead>
		<tr>
			<th>date</th>
			<th>raid</th>
			<th>dkp</th>
			<th>notice</th>
		</tr></thead>';
		
		for($x = 0; $x < count($detailEntry->dkp_entries); $x++){
			$dkp_entries = $detailEntry->dkp_entries[$x];
			
			$content = $content.'<tr>';
			$content = $content.'<td class="'.$css_class.'">'.SRUtil::formatDate($dkp_entries->updated_at).'</td>';
			$content = $content.'<td class="'.$css_class.'"><a href="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner&action=showRaidDetailView&id='.$dkp_entries->raid->id.'">'.$dkp_entries->raid->title.'</a></td>';
			$content = $content.'<td class="'.$css_class.'">'.$dkp_entries->dkp.'</td>';
			$content = $content.'<td class="'.$css_class.'">'.$dkp_entries->notice.'</td>';
			$content = $content.'</tr>';
		}
		if(strcmp($css_class,'STYLED_TABLE_TD_1') == 0){
			$css_class='STYLED_TABLE_TD_2';
		}else{
			$css_class='STYLED_TABLE_TD_1';
		}
		
		$content = $content.'</table>';
	}
	return $content;
}

function sr_forms_public_dkp_viewCharacterDKP(&$adodb,&$vars){
		$dkp_character_summary = $vars['dkp_character_summary'];
		$detailEntry = $vars['dkp_character_detail_summary'];
	
		$content = $content.'<h2>character dkp overview: '.$dkp_character_summary->char->charname.' '.$dkp_character_summary->sum.' dkp</h2>';	
		$content = $content.'<br>
		<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
			<th>date</th>
			<th>raid</th>
			<th>dkp</th>
			<th>notice</th>
		</tr></thead>';
		
		for($x = 0; $x < count($detailEntry->dkp_entries); $x++){
			$dkp_entries = $detailEntry->dkp_entries[$x];
			
			$content = $content.'<tr>';
			$content = $content.'<td>'.$dkp_entries->updated_at.'</td>';
			$content = $content.'<td><a href="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner&action=showRaidDetailView&id='.$dkp_entries->raid->id.'">'.$dkp_entries->raid->title.'</a></td>';
			$content = $content.'<td>'.$dkp_entries->dkp.'</td>';
			$content = $content.'<td>'.$dkp_entries->notice.'</td>';
			$content = $content.'</tr>';
		}
		
		$content = $content.'
		<tfoot>
		<tr>
			<th>date</th>
			<th>raid</th>
			<th>dkp</th>
			<th>notice</th>
		</tr></tfoot>';
		$content = $content.'</table>';
	return $content;
}
?>
