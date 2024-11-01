<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/forms/admin/general.php');
include_once (SR_PLUGIN_PATH.'/models/model_raidplan.php');
include_once (SR_PLUGIN_PATH.'/models/model_raidplan_team.php');
include_once (SR_PLUGIN_PATH.'/models/model_raid_template.php');
include_once (SR_PLUGIN_PATH.'/models/model_item.php');
include_once (SR_PLUGIN_PATH.'/models/model_character.php');
include_once (SR_PLUGIN_PATH.'/models/model_character_types.php');
include_once (SR_PLUGIN_PATH.'/forms/admin/raidplan.php');
include_once (SR_PLUGIN_PATH.'/libs/util.php');
include_once (SR_PLUGIN_PATH.'/game_plugins/wow_buffed.php');

class ModAdmin_RaidPlan extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_admin_navigation_panel($this->system->db,$this->system->VARS);
	}
	
	public function index()
	{
		
		$raids = ModelRaidPlan::getAll($this->system->db);
		$vars['raids'] = $raids;
		return sr_forms_admin_raidplan_display_raids($this->system->db,$vars);
	}
	
	public function newRaid()
	{
		$raid_template = new ModelRaidTemplate();
		if($raid_template->select($this->system->db,$this->system->VARS['raid_template']) == false){
			return $this->index();
		}
		
		$vars['raid_template'] = $raid_template;
		return sr_forms_admin_raidplan_new_raid($this->system->db,$vars);
	}
	
	public function createRaid(){
		$raid_template = new ModelRaidTemplate();
		if($raid_template->select($this->system->db,$this->system->VARS['raid_template_fk']) == false){
			return $this->index();
		}
		
		$raid = new ModelRaidplan();
		$raid->fill($this->system->VARS,'raid_');
		$raid->insert($this->system->db);
		return $this->index();
	}
	
	public function updateRaid(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		$raid->fill($this->system->VARS,'raid_');
		$raid->update($this->system->db);
		return $this->index();
	}
	
	public function deleteRaid(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		$raid->delete($this->system->db);
		return $this->index();
	}
	
	public function managePlayers(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		$vars['raid'] = $raid;
		
		$vars['raid_characters'] = ModelRaidplanTeam::getCharacters($this->system->db,$raid->id);
		
		return sr_forms_admin_raidplan_manage_players($this->system->db,$vars);
	}
	
	public function changeCharacterStatus(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		ModelRaidplanTeam::changeCharacterStatus($this->system->db,$raid->id,$this->system->VARS['raid_character_id'],$this->system->VARS['raid_character_status']);
		return $this->managePlayers();
	}
	
	public function addCaracter(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$char = new ModelCharacter();
		if($char->select($this->system->db,$this->system->VARS['raid_character_id'])){
			$signon = new ModelRaidPlanTeam();
			$signon->raid_fk = $raid->id;
			$signon->character_fk = $char->id;
			$signon->status = $this->system->VARS['raid_character_status'];
			$signon->notice = 'added by raid manager';
			$signon->created_at = date("Y-m-d H:i:s");
			$signon->insert($this->system->db);
		}
		
		return $this->managePlayers();
	}
	
	public function removeCharacter(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		ModelRaidplanteam::removeCharFromRaid($this->system->db,$raid->id,$this->system->VARS['cid']);
		return $this->managePlayers();
	}
	
	public function editRaid(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$vars['raid'] = $raid;
		return sr_forms_admin_raidplan_edit_raid($this->system->db,$vars);
	}
}
?>
