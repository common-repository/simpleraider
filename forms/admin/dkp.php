<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');

function sr_forms_admin_dkp_index(&$adodb,&$vars){
	$content = $content.'<h2>DKP management</h2>';
	
	$content = $content.'
	<form action="?page='.$_GET['page'].'&mod=Admin_DKP&action=resetDKP" method="post">
	<h3>reset DKP of game</h3>
	<p><label>DKP:</label><span> <input type="text" name="dkp_points"></span></p> 
	<p><label>Game:</label><span> <select name="game_id">'.ModelGames::getOptions($adodb).'</select></span></p>
	<p><label>Notice:</label><span> <textarea name="dkp_notice"></textarea></span></p>
	<input type="submit" value="reset dkp">
	</form>
	';
	return $content;
}

function sr_forms_admin_dkp_raid_dkp_list(&$adodb,&$vars){
	$raid = $vars['raid'];
	$content = '<h2>manage raid dkp: '.$raid->title.' - '.SRUtil::formatDate($raid->raidstart).'</h2>';
	
	//give dkp to many chars
	$content = $content.'
	<table  width="100%"><tr><td width="50%">
	<form class="sr_form_dkp_management" action="?page='.$_GET['page'].'&mod=Admin_DKP&action=giveCharactersDKP" method="post">
	<h3>DKP to all raid characters:</h3>
	<p><label>DKP:</label><span> <input type="text" name="dkp_points"></span></p> 
	<p><label>Status:</span></p>  <select name="raid_character_status">'.ModelRaidplanTeam::getRaidCharacterStatusOptions().'</select></span></p> 
	<p><label>Notice:</label><span> <textarea name="dpk_notice"></textarea></span></p> 
	<input type="hidden" name="id" value="'.$raid->id.'">
	<input type="submit" value="submit dkp">
	</form></td>
	';
	
	// give dkp to one char
	$content = $content.'<td width="50%">
	<form class="sr_form_dkp_management" action="?page='.$_GET['page'].'&mod=Admin_DKP&action=giveDKPToCharacter" method="post">
	<h3>DKP to one character:</h3>
	<p><label>DKP:</label><span> <input type="text" name="dkp_points"></span></p> 
	<p><label>Character: </span></p> <select name="raid_character_id">'.ModelRaidplanTeam::getCharactersOptions($adodb,$raid->id).'</select></span></p> 
	<p><label>Notice:</label><span> <textarea name="dpk_notice"></textarea></span></p> 
	<input type="hidden" name="id" value="'.$raid->id.'">
	<input type="submit" value="submit dkp">
	</form>
	</td></tr>
	';
	
	// give item to one char
	$content = $content.'<tr><td width="50%">
	<form class="sr_form_dkp_management" action="?page='.$_GET['page'].'&mod=Admin_DKP&action=giveItemToCharacter" method="post">
	<h3>item to one character:</h3>
	<p><label>DKP:</label><span> <input type="text" name="dkp_points"></span></p> 
	<p><label>Character: </span></p> <select name="raid_character_id">'.ModelRaidplanTeam::getCharactersOptions($adodb,$raid->id).'</select></span></p> 
	<p><label>Item:</label><span> <select name="raid_item_id">'.ModelItem::getAllOfRaidTemplateOptions($adodb,$raid->template_fk).'</a></span></p> 
	<input type="hidden" name="id" value="'.$raid->id.'">
	<input type="submit" value="submit dkp">
	</form>
	</td>';
	
	// import raidtracker dkp
	$content = $content.'<td width="50%">
	<form class="sr_form_dkp_management" action="?page='.$_GET['page'].'&mod=Admin_DKP&action=previewRaidTrackerImport" method="post">
	<h3>import raidtracker dkp string:</h3>
	<p><label>DKP:</label><span> <textarea cols="50" name="dkp_string"></textarea></span></p> 
	<input type="hidden" name="id" value="'.$raid->id.'">
	<input type="submit" value="submit dkp">
	</form>
	</td></tr>';
	
	$content = $content.'</table>';
	
	// dkp character summaries
	$content = $content.'<h3>character dkp overview</h3>';
	$dkp_character_summary = $vars['dkp_character_summary'];
	for($i = 0; $i < count($dkp_character_summary); $i++){
		$tDKPSummary = $dkp_character_summary[$i];
		$content = $content.'<span class="SR_CHARACTER_DKP_OVERVIEW">'.$tDKPSummary->char->charname.' '.$tDKPSummary->sum.'</span>';
	}
	
	// dkp entries list
	$dkp_entries = $vars['dkp_entries'];
	$content = $content.'
		<h1>DKP entries for this raid</h1>
		<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
			<th>date</th>
			<th>character</th>
			<th>DKP</th>
			<th>notice</th>
			<th>actions</th>
		</tr></thead>';
		
	for($i = 0; $i < count($dkp_entries); $i++){
		$tDKP = $dkp_entries[$i];
		
		$content = $content.'<tr>';
		$content = $content.'<td>'.SRUtil::formatDate($tDKP->updated_at).'</td>';
		$content = $content.'<td>'.$tDKP->character->charname.'</td>';
		$content = $content.'<td>'.$tDKP->dkp.'</td>';
		$content = $content.'<td>'.$tDKP->notice.'</td>';
		$content = $content.'<td>
			<a href="?page='.$_GET['page'].'&mod=Admin_DKP&action=editDKPEntry&id='.$raid->id.'&did='.$tDKP->id.'">[edit]</a>
			<a href="?page='.$_GET['page'].'&mod=Admin_DKP&action=deleteDKPEntry&id='.$raid->id.'&did='.$tDKP->id.'">[delete]</a>
			</td>';
		$content = $content.'</tr>';
	}
	$content = $content.'
		<tfoot>
		<tr>
			<th>date</th>
			<th>character</th>
			<th>DKP</th>
			<th>notice</th>
			<th>actions</th>
		</tr></tfoot>';
	$content = $content.'</table>';
	return $content;
}


function sr_forms_admin_dkp_edit_dkp_entry(&$adodb,&$vars){
	$raid = $vars['raid'];
	$dkp_entry = $vars['dkp_entry'];
	$content = '<h2>edit dkp entry for : '.$raid->title.' - '.SRUtil::formatDate($raid->raidstart).'</h2>';
	$content = $content.'
	<form action="?page='.$_GET['page'].'&mod=Admin_DKP&action=updateDKPEntry" method="post">
	<p><label>character:</label><span>'.$dkp_entry->character->charname.'</span></p> 
	<p><label>DKP:</label><span><input type="text" name="dkp" value="'.$dkp_entry->dkp.'"></span></p> 
	<p><label>notice:</label><span><input type="text" name="notice" value="'.$dkp_entry->notice.'"></span></p> 
	<input type="hidden" name="id" value="'.$dkp_entry->raid_fk.'">
	<input type="hidden" name="did" value="'.$dkp_entry->id.'">
	<input type="submit" value="update">
	</form>
	';
	return $content;
}

function sr_forms_admin_dkp_raid_rtpreview(&$adodb,&$vars){
	$raid = $vars['raid'];
	$rtImport = $vars['raid_rt_import'];
	
	$content = '<h2>dkp raidtracker import preview: '.$raid->title.' - '.$raid->raidstart.'</h2>';
	
	// bosskills
	$content = $content.'<form action="?page='.$_GET['page'].'&mod=Admin_DKP&action=doRaidTrackerImport" method="post">';
	$content = $content.'<input type="hidden" name="dkp_string" value="'.$vars['raid_dkp_string'].'">';
	$content = $content.'<input type="hidden" name="id" value="'.$raid->id.'">';
	$content = $content.'<h2>bosskills</h2>';
	$content = $content.'<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
		<th>boss</th>
		<th>date</th>
		<th>players</th>
		<th>dkp</th>
	</tr></thead>';
	for($i = 0; $i < count($rtImport->bosskills); $i++){
		$content = $content.'<tr><td>'.$rtImport->bosskills[$i]->name.'</td>';
		$content = $content.'<td>'.$rtImport->bosskills[$i]->kill_convtime.'</td>';
		$content = $content.'<td>'.count($rtImport->bosskills[$i]->players).'</td>';
		$content = $content.'<td><input type="text" name="bosskill_pos_'.$i.'"></td>';
		$content = $content.'</tr>';
	}
	$content = $content.'</table>';
	
	// loot
	$content = $content.'<h2>loot</h2>';
	$content = $content.'<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
		<th>item</th>
		<th>player</th>
		<th>dkp</th>
		<th>boss</th>
	</tr></thead>';
	for($i = 0; $i < count($rtImport->loot); $i++){
		$content = $content.'<tr>';
		$content = $content.'<td>'.$rtImport->loot[$i]->itemName.'</td>';
		$content = $content.'<td>'.$rtImport->loot[$i]->player.'</td>';
		$content = $content.'<td><input type="text" name="loot_dkp_pos_'.$i.'" value="'.$rtImport->loot[$i]->dkp.'"></td>';
		$content = $content.'<td>'.$rtImport->loot[$i]->bossKill.'</td>';
		$content = $content.'</tr>';
	}
	$content = $content.'</table>
	<input type="submit" value="start import">
	</form>';
	
	$content = $content.'notice: characters not found in the database are getting ignored';
	return $content;
}
?>
