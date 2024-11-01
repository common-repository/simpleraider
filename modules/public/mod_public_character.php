<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_raidplan.php');
include_once (SR_PLUGIN_PATH.'/models/model_character.php');
include_once (SR_PLUGIN_PATH.'/forms/public/characters.php');
include_once (SR_PLUGIN_PATH.'/forms/public/general.php');

class ModPublic_Character extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_public_navigation_panel($this->system->db,$vars);
	}
	
	public function index()
	{
		global $current_user;
		get_currentuserinfo();
		$characters = ModelCharacter::getOfUser($this->system->db,$current_user->ID);
		$vars['characters'] = &$characters;
		return sr_forms_public_characters_overview($this->system->db,$vars);
	}
	
	public function newCharacter(){
		return sr_forms_public_characters_new($this->system->db,$vars);
	}
	
	public function createCharacter(){
		$char = new ModelCharacter();
		$char->fill($this->system->VARS,'char_');
		
		global $current_user;
		get_currentuserinfo();
		$char->user_fk = $current_user->ID;

		if(strlen($char->charname) > 0){ // there has to be a char name
			$char->insert($this->system->db);
		}
		
		
		return $this->index();
	}
	
	public function deleteCharacter()
	{
		$char = new ModelCharacter();
		if($char->select($this->system->db,$this->system->VARS['id'])){
			global $current_user;
			get_currentuserinfo();
			// we delete the char only if the user is owner of the char
			if($current_user->ID == $char->user_fk){
				$char->delete($this->system->db);
			}
		}
		
		return $this->index();
	}
}

?>
