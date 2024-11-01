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
include_once (SR_PLUGIN_PATH.'/models/model_dkp.php');
include_once (SR_PLUGIN_PATH.'/models/model_games.php');
include_once (SR_PLUGIN_PATH.'/forms/admin/options.php');

include_once (SR_PLUGIN_PATH.'/game_plugins/wow_buffed.php');

class ModAdmin_Options extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_admin_navigation_panel($this->system->db,$this->system->VARS);
	}
	
	function index(){
	
		$games = ModelGames::getAll($this->system->db);
		$vars['sr_games'] = $games;
		$vars['sr_page_id'] = get_option("sr_option_display_page_id",'-1');
		return sr_forms_admin_options_overview($this->system->db,$vars);
	}
	
	function updatePageDisplay(){
		$page_id = $this->system->VARS['sr_page_id'];
		add_option("sr_option_display_page_id", $page_id, '', 'no');
		update_option("sr_option_display_page_id", $page_id);
		return $this->index();
	}
	
	function updateGamePlugin(){
		$game = new ModelGames();
		if($game->select($this->system->db,$this->system->VARS['id'])){
			$game->gameplugin = $this->system->VARS['game_plugin'];
			$game->update($this->system->db);
		}
		
		return $this->index();
	}
	
	function addGame(){
		$game = new ModelGames();
		$game->internalname = $this->system->VARS['sr_game_internalname'];
		$game->title = $this->system->VARS['sr_game_title'];
		$game->insert($this->system->db);
		return $this->index();
	}
	
	function deleteGame(){
		$game = new ModelGames();
		if($game->select($this->system->db,$this->system->VARS['id'])){
			$game->delete($this->system->db);
		}
		
		return $this->index();
	}
}
?>
