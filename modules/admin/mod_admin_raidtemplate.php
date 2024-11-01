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
include_once (SR_PLUGIN_PATH.'/models/model_raid_template.php');
include_once (SR_PLUGIN_PATH.'/models/model_item.php');
include_once (SR_PLUGIN_PATH.'/models/model_character.php');
include_once (SR_PLUGIN_PATH.'/models/model_character_types.php');
include_once (SR_PLUGIN_PATH.'/forms/admin/raidtemplate.php');

include_once (SR_PLUGIN_PATH.'/game_plugins/wow_buffed.php');

class ModAdmin_RaidTemplate extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_admin_navigation_panel($this->system->db,$this->system->VARS);
	}
	
	public function index()
	{
		$raids = ModelRaidTemplate::getAll($this->system->db);
		$vars['raid_templates'] = $raids;
		return sr_forms_admin_raidtemplate_list($this->system->db,$vars);
	}
	
	public function newRaidTemplate()
	{
		return sr_forms_admin_raidtemplate_new($this->system->db,$vars);
	}
	
	public function createRaidTemplate(){
		$template = new ModelRaidTemplate();
		$template->fill($this->system->VARS,'raidtemplate_');
		$template->insert($this->system->db);
		return $this->index();
	}
	
	public function editRaidTemplate(){
		$tRaid = new ModelRaidTemplate();
		if($tRaid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$vars['raid_template'] = $tRaid;
		$vars['raid_items'] = ModelItem::getAllOfRaidTemplate($this->system->db,$tRaid->id);
		
		$tRaid->fetchData($this->system->db);
		$game_plugin = $tRaid->game->loadGamePlugIn();
		$vars['game_plugin'] = $game_plugin;
		
		return sr_forms_admin_raidtemplate_edit($this->system->db,$vars);
	}
	
	public function updateRaidTemplate(){
		$tRaid = new ModelRaidTemplate();
		if($tRaid->select($this->system->db,$this->system->VARS['id']) == false){
			print 'fehler';
			return $this->index();
		}
		
		$tRaid->fill($this->system->VARS,'raidtemplate_');
		$tRaid->update($this->system->db);
		return $this->index();
	}
	
	public function newRaidItem(){
		$tRaid = new ModelRaidTemplate();
		if($tRaid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$vars['raid_template'] = $tRaid;
		return sr_forms_admin_raidtemplate_newitem($this->system->db,$vars);
	}
	
	public function createRaidItem(){
		$tRaid = new ModelRaidTemplate();
		if($tRaid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$item = new ModelItem();
		$item->game_fk = $tRaid->game_fk;
		$item->title = $this->system->VARS['raiditem_title'];
		$item->internal_game_item_id  = $this->system->VARS['raiditem_internal_game_id'];
		$item->raidtemplate_fk = $tRaid->id;
		$item->insert($this->system->db);
		
		return $this->editRaidTemplate();
	}
	
	public function deleteRaidItem(){
		$tRaid = new ModelRaidTemplate();
		if($tRaid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$item = new ModelItem();
		if($item->select($this->system->db,$this->system->VARS['itemId'])){
			if($item->raidtemplate_fk == $tRaid->id){
				$item->delete($this->system->db);
			}
		}
		
		return $this->editRaidTemplate();
	}
	
	
	public function importRaidItems(){
		$tRaid = new ModelRaidTemplate();
		if($tRaid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$tRaid->fetchData($this->system->db);
		$plugin = $tRaid->game->loadGamePlugin();
		
		$csv_items = $this->system->VARS['csv_game_items'];
		// extracting the items
		$tok = strtok($csv_items, "\n\t;");

		while ($tok !== false) {
			$tok = trim($tok);
			//print 'item: '.$tok.'<br>';
			$game_item_id = $tok;
			
			// we have to
			$item = new ModelItem();
			$item->game_fk = $tRaid->game_fk;
			$item->internal_game_item_id  = $game_item_id;
			$item->raidtemplate_fk = $tRaid->id;
				
			$game_item_title = $plugin->getItemTitle($item);
			$game_item_title = 	mysql_escape_string($game_item_title);
			if($game_item_title)
			{
				
				$item->title = $game_item_title;
				//$this->system->db->debug = true;
				$item->insert($this->system->db);
				//$this->system->db->debug = false;
			}
			else{
				printf("found no information for item id #%d<br>",$item->internal_game_item_id);
			}
			$tok = strtok("\n\t;"); // important !
		}
		return $this->editRaidTemplate();
	}
}
?>
