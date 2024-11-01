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

function sr_forms_public_raid_detailview(&$adodb,&$vars){
	
	$content = '<a href="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner">back</a>';

	$raid = $vars['raid'];
	$user_chars = $vars['user_chars'];
	$raid_characters = $vars['raid_characters'];
	
	$content = $content.'
		<div id="RAID_HEADER">
		<h2>'.$raid->template->getIconImageUrl().' '.$raid->getGameTitle($adodb).' : '.$raid->title.'</h2>
		
		<p><label>needed players:</label><span>'.$raid->numplayers.'</span></p>
		<p><label>signed on:</label><span>'.$vars['num_chars_signed_on'].'</span></p>
		
		<p><label>waiting list:</label><span>'.$vars['num_chars_waiting_list'].'</span></p>
		<p><label>sign on until:</label><span>'.mysql2date('l, d.m H:i',$raid->raidsignonend).'</span></p>
		<p><label>invitation:</label><span>'.mysql2date('l, d.m H:i',$raid->raidinvitation).'</span></p>
		<p><label>start:</label><span>'.mysql2date('l, d.m H:i',$raid->raidstart).'</span></p>
		<p><label>end:</label><span>'.mysql2date('l, d.m H:i',$raid->raidfinish).'</span></p>
		</div>';
	
	
	$content = $content.'<div id="RAID_LOGON_CONTAINTER">';
	// raid sign on
	if($vars['user_has_char_in_raid']){
		$content = $content.'<h2>remove you from the raid:</h2>';
		$content = $content.'you are already in the raid, click <a href="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner&action=removeCharFromRaid&id='.$raid->id.'">here</a> to remove you from the raid';
	}
	else{
		
	if($raid->isSignOnAvailable()){
		// sign on in the raid
		$content = $content.'
		<h2>sign on to the raid:</h2>
		<form action="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner&action=raidSignOn&id='.$raid->id.'" method="post">
			character: 
			<select name="raid_user_character">';
			for($i = 0; $i < count($user_chars); $i++)
			{
				$content = $content.'<option value="'.$user_chars[$i]->id.'">'.$user_chars[$i]->charname.'</option>';
			}
			$content = $content.'</select>
			status: <select name="raid_user_character_status">'.ModelRaidplanTeam::getRaidCharacterStatusOptions().'</select>
			notice: <input type="text" name="raid_user_character_notice">
			<input type="submit" value="update">
			</form>';
		}
	} // end isSignOnAvailable
	$content = $content.'</div>';
	
	// raid characters signed on
	$content = $content.'<div class="SR_RAID_ROOSTER"><h1 class="SR_RAID_H1">rooster: signed on</h1>';
	for($i = 0; $i < count($raid_characters); $i++)
	{
		$tGroup = $raid_characters[$i];
	
		$content = $content.'<div class="TABLE_RAID_ROOSTER">';
		$content = $content.'<h1>';
		$content = $content.$tGroup->type->getIconImageTag().$tGroup->type->title.' ('.count($tGroup->characters_signed_on).')';
		$content = $content.'</h1>';
		
		$content = $content.'<table width="100%">';
		for($x = 0; $x < count($tGroup->characters_signed_on); $x++){
			$char = $tGroup->characters_signed_on[$x];
			//print 'CHAR SO: '.$char->charname.'<br>';
			$notice = ModelRaidplanTeam::getNoticeForCharacter($adodb,$raid->id,$tGroup->characters_signed_on[$x]->id);
			$content = $content.'<tr><td>';

			if($vars['user_is_moderator']){ // status options for a character, accepted for raid or not etc.
				if($char->data->cstatus == 1){
					$content = $content.'<a href="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner&action=toggleCharStatus&id='.$raid->id.'&char='.$tGroup->characters_signed_on[$x]->id.'"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/green.png"></a>';
				}else if($char->data->cstatus == -1){
					$content = $content.'<a href="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner&action=toggleCharStatus&id='.$raid->id.'&char='.$tGroup->characters_signed_on[$x]->id.'"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/red.png"></a>';
				}
				else{
					$content = $content.'<a href="?page_id='.$_GET['page_id'].'&mod=Public_Raidplaner&action=toggleCharStatus&id='.$raid->id.'&char='.$tGroup->characters_signed_on[$x]->id.'"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/yellow.png"></a>';
				}
			}else{
					if($char->data->cstatus == 1){
						$content = $content.'<img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/green.png">';
					}else if($char->data->cstatus == -1){
						$content = $content.'<img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/red.png">';
					}
					else{
						$content = $content.'<img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/yellow.png">';
					}
			}
			
			if(strlen($notice) > 0){
				$content = $content.'<div id="char_notice_'.$tGroup->characters_signed_on[$x]->id.'" class="sr_notice_tooltip">'.$notice.'</div>';
				$content = $content.'<a onmouseover="showWMTT(\'char_notice_'.$tGroup->characters_signed_on[$x]->id.'\')" onmouseout="hideWMTT()" href="#"><img src="'.SR_PLUGIN_URL.'/images/notice.png"></a>';
				$notice = null;
			}
			
			$content = $content.'<a href="?page_id='.$_GET['page_id'].'&mod=Public_DKP&action=viewCharacterDKP&id='.$tGroup->characters_signed_on[$x]->id.'">'.$tGroup->characters_signed_on[$x]->charname.'</a></td>';
			$content = $content.'<td>'.$tGroup->characters_signed_on[$x]->sumDKP.'</td>';
			$content = $content.'</tr>';
		}
		$content = $content.'</table>';
		
		// warteliste
		/*$content = $content.'<td>';		
		for($x = 0; $x < count($tGroup->characters_waiting_list); $x++){
			$content = $content.'<div class="SR_RAID_DETAIL_CHAR_ENTRY">'.$tGroup->characters_waiting_list[$x]->charname.'</div>';
		}
		$content = $content.'</td>';*/
		
		$content = $content.'</div>';
	}
	$content = $content.'</div>';
	
	
	
	// raid characters waiting list
	$content = $content.'<div class="SR_RAID_ROOSTER"><h1>rooster: waiting list</h1>';
	for($i = 0; $i < count($raid_characters); $i++)
	{
		$tGroup = $raid_characters[$i];
	
		$content = $content.'<div class="TABLE_RAID_ROOSTER">';
		$content = $content.'<h1>';
		$content = $content.$tGroup->type->getIconImageTag().$tGroup->type->title.' ('.count($tGroup->characters_waiting_list).')';
		$content = $content.'</h1>';
		
		$content = $content.'<table width="100%">';
		for($x = 0; $x < count($tGroup->characters_waiting_list); $x++){
			//print 'CHAR WL: '.$char->charname.'<br>';
			$notice = ModelRaidplanTeam::getNoticeForCharacter($adodb,$raid->id,$tGroup->characters_waiting_list[$x]->id);
			$content = $content.'<tr><td>';
			if(strlen($notice) > 0){
				$content = $content.'<div id="char_notice_'.$tGroup->characters_waiting_list[$x]->id.'" class="sr_notice_tooltip">'.$notice.'</div>';
				$content = $content.'<a onmouseover="showWMTT(\'char_notice_'.$tGroup->characters_waiting_list[$x]->id.'\')" onmouseout="hideWMTT()" href="#"><img src="'.SR_PLUGIN_URL.'/images/notice.png"></a>';
				$notice = null;
			}
			
			$content = $content.'<a href="?page_id='.$_GET['page_id'].'&mod=Public_DKP&action=viewCharacterDKP&id='.$tGroup->characters_waiting_list[$x]->id.'">'.$tGroup->characters_waiting_list[$x]->charname.'</a></td>';
			$content = $content.'<td>'.$tGroup->characters_waiting_list[$x]->sumDKP.'</td>';
			$content = $content.'</tr>';
		}
		$content = $content.'</table>';
		
		$content = $content.'</div>';
	}
	$content = $content.'</div>';
		
	// items
	$content = $content.sr_forms_public_raid_item_list($adodb,$vars);
	
	
	return $content;
}

function sr_forms_public_raid_item_list(&$adodb,&$vars){
	$items = $vars['raid_items'];
	$game_plugin = $vars['game_plugin'];
	
	$content = $content.'<h2>dropping items</h2>';
	
	$content = $content.'
		<table width="100%">
		<tr>
			<th></th>
			<th>#game id</th>
			<th>title</th>
		</tr>';
		
	for($i = 0; $i < count($items); $i++){
		$tItem = $items[$i];
		$content = $content.'<tr>';
		$content = $content.'<td>'.$game_plugin->getImageWithLinkAndTooltip($tItem,'item_css_'.$tItem->id).'</td>';
		$content = $content.'<td>'.$tItem->internal_game_item_id.'</td>';
		$content = $content.'<td>'.$tItem->title.'</td>';
		$content = $content.'</tr>';

	}
	$content = $content.'</table>';
	return $content;
}
?>
