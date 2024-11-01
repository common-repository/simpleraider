<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
 
function sr_forms_admin_raidplan_display_raids(&$adodb,&$vars){
	
	$content = '<form action="?page='.$_GET['page'].'&mod=Admin_Raidplan&action=newRaid" method="post">
		<select name="raid_template">'.ModelRaidTemplate::getTemplatesAsOptions($adodb).'</select>
		<input type="submit" value="create raid from template">
		</form>
	';

	$raids = $vars['raids'];
	$content = $content.'
		<h2>scheduled raids</h2>
		<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
			<th>#</th>
			<th>icon</th>
			<th>start</th>
			<th>end</th>
			<th>invitation</th>
			<th>sign on end</th>
			<th>game</th>
			<th>title</th>
			<th>players</th>
			<th>signed on</th>
			<th>waiting list</th>
			<th>actions</th>
		</tr></thead>';
		
	for($i = 0; $i < count($raids); $i++){
		$tRaid = $raids[$i];
		$tRaid->fetchData($adodb);
		
		$content = $content.'<tr>';
		$content = $content.'<td>'.$tRaid->id.'</td>';
		$content = $content.'<td>'.$tRaid->template->getIconImageUrl().'</td>';
		$content = $content.'<td>'.SRUtil::formatDate($tRaid->raidstart).'</td>';
		$content = $content.'<td>'.SRUtil::formatDate($tRaid->raidfinish).'</td>';
		$content = $content.'<td>'.SRUtil::formatDate($tRaid->raidinvitation).'</td>';
		$content = $content.'<td>'.SRUtil::formatDate($tRaid->raidsignonend).'</td>';
		$content = $content.'<td>'.$tRaid->getGameTitle($adodb).'</td>';
		$content = $content.'<td>'.$tRaid->title.'</td>';
		$content = $content.'<td>'.$tRaid->numplayers.'</td>';
		$content = $content.'<td>'.$tRaid->num_players_signed_on.'</td>';
		$content = $content.'<td>'.$tRaid->num_players_waiting_list.'</td>';
		$content = $content.'<td>
		<a href="?page='.$_GET['page'].'&mod=Admin_Raidplan&action=editRaid&id='.$tRaid->id.'">[edit]</a>
			<a href="?page='.$_GET['page'].'&mod=Admin_Raidplan&action=deleteRaid&id='.$tRaid->id.'">[delete]</a>
			<a href="?page='.$_GET['page'].'&mod=Admin_Raidplan&action=managePlayers&id='.$tRaid->id.'">[players]</a>
			<a href="?page='.$_GET['page'].'&mod=Admin_DKP&action=manageRaidDKP&id='.$tRaid->id.'">[dkp]</a>
			</td>';
		$content = $content.'</tr>';
	}
	$content = $content.'
		<tfoot>
		<tr>
			<th>#</th>
			<th>icon</th>
			<th>start</th>
			<th>end</th>
			<th>invitation</th>
			<th>sign on end</th>
			<th>game</th>
			<th>title</th>
			<th>players</th>
			<th>signed on</th>
			<th>waiting list</th>
			<th>actions</th>
		</tr></tfoot>';
	$content = $content.'</table>';
	return $content;
}

function sr_forms_admin_raidplan_new_raid(&$adodb,&$vars){
	$template = $vars['raid_template'];
	$template->fetchData($adodb);
	$content = $content.'
			<div id="RAID_HEADER">
			<h2>create new  raid</h2>
			<form action="?page='.$_GET['page'].'" method="post">
			<input type="hidden" name="mod" value="Admin_Raidplan">
			<input type="hidden" name="action" value="createRaid">
			<p><label>game:</label></span><input type="hidden" name="raid_game_fk" value="'.$template->game->id.'">'.$template->game->title.'</span></p>
			<p><label>raid start:</label> <input type="text" id="raid_raidstart" name="raid_raidstart" value="'.$raid->raidstart.'">
			<a href="javascript:NewCssCal(\'raid_raidstart\',\'yyyymmdd\',\'arrow\',true,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
			
			<p><label>raid finish:</label> <input type="text" id="raid_raidfinish" name="raid_raidfinish" value="'.$raid->raidstart.'">
			<a href="javascript:NewCssCal(\'raid_raidfinish\',\'yyyymmdd\',\'arrow\',true,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
			
			<p><label>raid invitation:</label> <input type="text" id="raid_raidinvitation" name="raid_raidinvitation" value="'.$raid->raidstart.'">
			<a href="javascript:NewCssCal(\'raid_raidinvitation\',\'yyyymmdd\',\'arrow\',true,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
			
			<p><label>raid signonend:</label> <input type="text" id="raid_raidsignonend" name="raid_raidsignonend" value="'.$raid->raidstart.'">
			<a href="javascript:NewCssCal(\'raid_raidsignonend\',\'yyyymmdd\',\'arrow\',true,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
			
			<p><label>title:</label> <input type="text" name="raid_title" value="'.$template->title.'"><br>
			<p><label>num players:</label> <input type="text" name="raid_numplayers" value="'.$template->numplayers.'"><br>
			<input type="hidden" name="raid_template_fk" value="'.$template->id.'"><br>
			<input type="submit" value="create new raid">
			</form>
			</div>
	';
	
	return $content;
}

function sr_forms_admin_raidplan_edit_raid(&$adodb,&$vars){
	$raid = $vars['raid'];
	$raid->fetchData($adodb);
	
	$content= '
			<h2>edit raid</h2>
			<div id="RAID_HEADER">
			<form action="?page='.$_GET['page'].'" method="post">
			<input type="hidden" name="mod" value="Admin_Raidplan">
			<input type="hidden" name="action" value="updateRaid">
			<p><label>game:</label> <input type="hidden" name="raid_game_fk" value="'.$raid->game->id.'">'.$raid->game->title.'</span></p>
			<p><label>raid start:</label> <input type="text" id="raid_raidstart" name="raid_raidstart" value="'.$raid->raidstart.'">
			<a href="javascript:NewCssCal(\'raid_raidstart\',\'yyyymmdd\',\'arrow\',true,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
			
			<p><label>raid finish:</label> <input type="text" id="raid_raidfinish" name="raid_raidfinish" value="'.$raid->raidstart.'">
			<a href="javascript:NewCssCal(\'raid_raidfinish\',\'yyyymmdd\',\'arrow\',true,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
			
			<p><label>raid invitation:</label> <input type="text" id="raid_raidinvitation" name="raid_raidinvitation" value="'.$raid->raidstart.'">
			<a href="javascript:NewCssCal(\'raid_raidinvitation\',\'yyyymmdd\',\'arrow\',true,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
			
			<p><label>raid signonend:</label> <input type="text" id="raid_raidsignonend" name="raid_raidsignonend" value="'.$raid->raidstart.'">
			<a href="javascript:NewCssCal(\'raid_raidsignonend\',\'yyyymmdd\',\'arrow\',true,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
			
			<p><label>title:</label<input type="text" name="raid_title" value="'.$raid->title.'"></span></p>
			<p><label>num players:</label> <input type="text" name="raid_numplayers" value="'.$raid->numplayers.'"></span></p>
			<input type="hidden" name="raid_template_fk" value="'.$raid->template_fk.'"></span></p>
			<input type="hidden" name="id" value="'.$raid->id.'">
			<input type="submit" value="update raid">
			</form>
			</div>
	';
	
	return $content;
}

function sr_forms_admin_raidplan_manage_players(&$adodb,&$vars){
	$raid = $vars['raid'];
	
	$content = '<a href="?page='.$_GET['page'].'&mod=Admin_Raidplan">back</a>';
	$content =$content.'
		<h2>manage players for '.$raid->title.'</h2>
	';
	
	$content = $content ='
	<h3>add a character to the raid</h3>
		<form action="?page='.$_GET['page'].'&mod=Admin_Raidplan&action=addCaracter&id='.$raid->id.'" method="post">
			character: <select name="raid_character_id">'.ModelRaidplanTeam::getRaidCharactersNotInRaidOptions($adodb,$raid->id).'</select>
			status: <select name="raid_character_status">'.ModelRaidplanTeam::getRaidCharacterStatusOptions().'</select>
			<input type="submit" value="add character">
		</form>
	';
	
	$content = $content.'<h3>characters in the raid</h3>';
	$content = $content.'<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
			<th>character</th>
			<th>status</th>
			<th>actions</th>
		</tr></thead>';
	$chars = $vars['raid_characters'];
	for($i = 0; $i < count($chars); $i++){
		$tChar = $chars[$i];
		
		$content = $content.'<tr>
		<td>'.$tChar->charname.'</td>
		<td><form action="?page='.$_GET['page'].'&mod=Admin_Raidplan&action=changeCharacterStatus&id='.$raid->id.'" method="post">
			<input type="hidden" name="raid_character_id" value="'.$tChar->id.'">
			<select name="raid_character_status">'.ModelRaidplanTeam::getRaidCharacterStatusOptionsForCharacter($adodb,$raid->id,$tChar->id).'</select>
			<input type="submit" value="change status">
		</form></td>
		<td><a href="?page='.$_GET['page'].'&mod=Admin_Raidplan&action=removeCharacter&id='.$raid->id.'&cid='.$tChar->id.'">[remove]</a>
		</td>
		</tr>
		';
	}
	
	$content = $content.'<table class="widefat fixed" cellspacing="0">
		<tfoot>
		<tr>
			<th>character</th>
			<th>status</th>
			<th>actions</th>
		</tr></tfoot></table>';
	return $content;
}
?>
