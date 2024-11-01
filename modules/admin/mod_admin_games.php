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
include_once (SR_PLUGIN_PATH.'/forms/admin/games.php');
include_once (SR_PLUGIN_PATH.'/libs/util.php');
include_once (SR_PLUGIN_PATH.'/game_plugins/wow_buffed.php');

class ModAdmin_Games extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_admin_navigation_panel($this->system->db,$this->system->VARS);
	}
	
	function index(){
	}
	
	function editGame(){
	
		$game = new ModelGames();
		if($game->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$char_types = ModelCharacterTypes::getAllOfGame($this->system->db,$game->id);
		
		$vars['sr_game'] = $game;
		$vars['sr_game_characters'] = $char_types;
		
		return sr_forms_admin_games_edit_game($this->system->db,$vars);
	}
	
	function updateGame(){
		$game = new ModelGames();
		if($game->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$game->fill($this->system->VARS,'sr_game_');
		$game->update($this->system->db);
		
		return $this->editGame();
	}
	
	function addCharType(){
		$game = new ModelGames();
		if($game->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$char_type = new ModelCharacterTypes();
		$char_type->game_fk = $game->id;
		$char_type->title = $this->system->VARS['sr_game_char_title'];
		$char_type->insert($this->system->db);
		
		return $this->editGame();
	}
	
	function deleteChar(){
		$game = new ModelGames();
		if($game->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$char_type = new ModelCharacterTypes();
		if($char_type->select($this->system->db,$this->system->VARS['typeId'])){
			if($char_type->game_fk == $game->id){
				$char_type->delete($this->system->db);
			}
		}
		
		return $this->editGame();
	}
	
	function updateCharTypeIcon(){
		$game = new ModelGames();
		if($game->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$char_type = new ModelCharacterTypes();
		if($char_type->select($this->system->db,$this->system->VARS['typeId'])){
			if($char_type->game_fk == $game->id){
				$char_type->icon = $this->system->VARS['game_char_type_icon'];
				$char_type->update($this->system->db);
				
			}
		}
		
		return $this->editGame();
	}
}
?>
