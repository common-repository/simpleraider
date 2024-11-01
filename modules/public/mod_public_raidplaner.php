<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_raidplan.php');
include_once (SR_PLUGIN_PATH.'/models/model_raidplan_team.php');
include_once (SR_PLUGIN_PATH.'/models/model_character.php');
include_once (SR_PLUGIN_PATH.'/models/model_character_types.php');
include_once (SR_PLUGIN_PATH.'/models/model_item.php');
include_once (SR_PLUGIN_PATH.'/models/model_user.php');
include_once (SR_PLUGIN_PATH.'/forms/public/overview.php');
include_once (SR_PLUGIN_PATH.'/forms/public/raid.php');
include_once (SR_PLUGIN_PATH.'/forms/public/general.php');

function sortCharDKP($sort_array,$reverse)
{
  for ($i = 0; $i < sizeof($sort_array); $i++){
    for ($j = $i + 1; $j < sizeof($sort_array); $j++){
      if($reverse){
        if (intval($sort_array[$i]->sumDKP) < intval($sort_array[$j]->sumDKP)){
          $tmp = $sort_array[$i];
          $sort_array[$i] = $sort_array[$j];
          $sort_array[$j] = $tmp;
        }
      }else{

        if (intval($sort_array[$i]->sumDKP) > intval($sort_array[$j]->sumDKP)){
          $tmp = $sort_array[$i];
          $sort_array[$i] = $sort_array[$j];
          $sort_array[$j] = $tmp;
        }
      }
    }
  }
  return $sort_array;
} 


class ModPublic_Raidplaner extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_public_navigation_panel($this->system->db,$vars);
	}
	
	public function index()
	{
		$raids = ModelRaidplan::getPublicRaids($this->system->db);
		$vars['raids'] = &$raids;
		return sr_forms_public_overview_raidplan($this->system->db,$vars);
	}
	
	public function showRaidDetailView()
	{
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false)
		{
			return $this->index();
		}
		
		// characters already in the raid
		// at first we get the character types to group them
		$raid_characters = array();
		$char_types = ModelCharacterTypes::getAllOfGame($this->system->db,$raid->game_fk);
		
		$num_chars_signed_on = 0;
		$num_chars_waiting_list = 0;
		for($i = 0; $i < count($char_types); $i++)
		{
			$charType = $char_types[$i];
			//print 'getting char type '.$charType->title.' -> '.$charType->id.'<br>';
			//$this->system->db->debug = true;
			$chars_signed_on =ModelRaidplanTeam::getCharactersByType($this->system->db,$charType->id,$raid->id,RAID_CHARACTER_STATUS_SIGNED_ON);
			$chars_waiting_list =ModelRaidplanTeam::getCharactersByType($this->system->db,$charType->id,$raid->id,RAID_CHARACTER_STATUS_WAITING_LIST);
			//$this->system->db->debug = false;
			
			$num_chars_signed_on = $num_chars_signed_on + count($chars_signed_on);
			$num_chars_waiting_list = $num_chars_waiting_list + count($chars_waiting_list);
			
			$charGroup = new StdClass();
			$charGroup->type = $charType;
			$charGroup->characters_signed_on = $chars_signed_on;
			$charGroup->characters_waiting_list = $chars_waiting_list;
	
			array_push($raid_characters,$charGroup);
		}
		
		
		// getting the dkp
		for($i = 0; $i < count($raid_characters); $i++){
			$tgroup = &$raid_characters[$i];
			for($x = 0; $x < count($tgroup->characters_signed_on); $x++){
				$tchar = &$tgroup->characters_signed_on[$x];
				//print 'getting so dkp for:'.$tchar->charname.'<br>';
				$tchar->addVar('sumDKP',ModelDKP::getDKPSummaryOfCharacter($this->system->db,$tchar->id));
				//var_dump($tchar);
			}
			for($x = 0; $x < count($tgroup->characters_waiting_list); $x++){
				$tchar = &$tgroup->characters_waiting_list[$x];
				//print 'getting wl: dkp for:'.$tchar->charname.'<br>';
				$tchar->addVar('sumDKP',ModelDKP::getDKPSummaryOfCharacter($this->system->db,$tchar->id));
				
			}
			
			//$tgroup->characters_signed_on = sortCharDKP($tgroup->characters_signed_on,true);
			//$tgroup->characters_waiting_list = sortCharDKP($tgroup->characters_waiting_list,true);
		}
		
		// own character information
		global $current_user;
		get_currentuserinfo();
		$user_chars = ModelCharacter::getOfUserOfGame($this->system->db,$current_user->ID,$raid->game_fk);
		
		// items
		$vars['raid_items'] = ModelItem::getAllOfRaidTemplate($this->system->db,$raid->template_fk);
		
		$raid->fetchData($this->system->db);
		$vars['game_plugin'] = $raid->game->loadGamePlugin();
		
		$vars['raid'] = &$raid;
		$vars['user_chars'] = &$user_chars;
		$vars['raid_characters'] = &$raid_characters;
		
		$vars['num_chars_signed_on'] = &$num_chars_signed_on;
		$vars['num_chars_waiting_list'] = &$num_chars_waiting_list;
		
		//print '<b>hasUserCharacterInRaid: '.$current_user->login.'</b>';
		$vars['user_has_char_in_raid'] = ModelRaidplanTeam::hasUserCharacterInRaid($this->system->db,$current_user->ID,$raid->id);
		$vars['user_is_moderator'] = ModelUser::isUserModerator($this->system->db,$current_user->id);
		
		return sr_forms_public_raid_detailview($this->system->db,$vars);
	}
	
	public function raidSignOn()
	{
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false)
		{
			return $this->index();
		}
		
		// no duplicate sign ons
		global $current_user;
		get_currentuserinfo();
		if(ModelRaidplanTeam::hasUserCharacterInRaid($this->system->db,$current_user->ID,$raid->id)){
			return $this->index();
		}
		
		$signon = new ModelRaidPlanTeam();
		$signon->raid_fk = $raid->id;
		$signon->character_fk = $this->system->VARS['raid_user_character'];
		$signon->status = $this->system->VARS['raid_user_character_status'];
		$signon->notice = $this->system->VARS['raid_user_character_notice'];
		$signon->created_at = date("Y-m-d H:i:s");
		
		$signon->insert($this->system->db);
		
		return $this->showRaidDetailView();
	}
	
	public function toggleCharStatus(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false)
		{
			return $this->index();
		}
		
		$char = new ModelCharacter();
		if($char->select($this->system->db,$this->system->VARS['char']) == false){
			return $this->index();
		}
		
		$status = ModelRaidplanTeam::getByChar($this->system->db,$char->id,$raid->id);
		if($status){
			$x = intval($status->cstatus);
			switch($x){
				case 0:
					$x = 1;
					break;
				case 1:
					$x = -1;
					break;
				case -1:
					$x = 0;
					break;
			}
			
			$status->cstatus = $x;
			$status->update($this->system->db);
		}
		
		return $this->showRaidDetailView();
	}
	
	public function removeCharFromRaid(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false)
		{
			return $this->index();
		}
		
		global $current_user;
		get_currentuserinfo();
		
		ModelRaidplanTeam::removeUserCharFromRaid($this->system->db,$current_user->ID,$raid->id,$raid->game_fk);
		
		return $this->showRaidDetailView();
	}
}

?>
