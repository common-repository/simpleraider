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
include_once (SR_PLUGIN_PATH.'/models/model_dkp.php');
include_once (SR_PLUGIN_PATH.'/forms/public/dkp.php');
include_once (SR_PLUGIN_PATH.'/forms/public/general.php');

class ModPublic_DKP extends Module
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
		
		// overview
		$dkp_character_summary = array();
		for($i = 0; $i < count($characters); $i++){
			$tChar = $characters[$i];
			$sum = ModelDKP::getDKPSummaryOfCharacter($this->system->db,$tChar->id);
			$entry = new StdClass();
			$entry->char = $tChar;
			$entry->sum = $sum;
			array_push($dkp_character_summary,$entry);
		}
		
		$vars['dkp_character_summary'] = $dkp_character_summary;
		
		
		//detail view
		$dkp_character_detail_summary = array();
		for($i = 0; $i < count($characters); $i++){
			$tChar = $characters[$i];
			$dkp = ModelDKP::getAllOfCharacter($this->system->db,$tChar->id);
			$entry = new StdClass();
			$entry->char = $tChar;
			$entry->dkp_entries = $dkp;
			array_push($dkp_character_detail_summary,$entry);
		}
		
		$vars['dkp_character_detail_summary'] = $dkp_character_detail_summary;
		
		return sr_forms_public_dkp_overview($this->system->db,$vars);
	}
	
	public function viewCharacterDKP(){
		// overview
		$char = new ModelCharacter();
		if($char->select($this->system->db,$this->system->VARS['id']) == false){
			return 'could not find specified character';
		}
		
		$sum = ModelDKP::getDKPSummaryOfCharacter($this->system->db,$char->id);
		$entry = new StdClass();
		$entry->char = $char;
		$entry->sum = $sum;
		
		$vars['dkp_character_summary'] = $entry;
		
		
		//detail view
		$dkp = ModelDKP::getAllOfCharacter($this->system->db,$char->id);
		$entry = new StdClass();
		$entry->char = $char;
		$entry->dkp_entries = $dkp;
		
		$vars['dkp_character_detail_summary'] = $entry;
		return sr_forms_public_dkp_viewCharacterDKP($this->system->db,$vars);
	}
	
	
}

?>
