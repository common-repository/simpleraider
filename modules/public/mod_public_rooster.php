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
include_once (SR_PLUGIN_PATH.'/forms/public/overview.php');
include_once (SR_PLUGIN_PATH.'/forms/public/rooster.php');
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

class ModPublic_Rooster extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_public_navigation_panel($this->system->db,$vars);
	}
	
	public function index()
	{
		$game_id = 1;
		if(isset($this->system->VARS['game_id'])){
			$game_id = $this->system->VARS['game_id'];
		}
		
		$char_types = ModelCharacterTypes::getAllOfGame($this->system->db,$game_id);
		$groups = array();
		for($i = 0; $i < count($char_types); $i++){

			$chars = ModelCharacter::getByType($this->system->db,$char_types[$i]->id);
			for($x = 0; $x < count($chars); $x++){
				$chars[$x]->addVar('sumDKP',ModelDKP::getDKPSummaryOfCharacter($this->system->db,$chars[$x]->id));
			}
			$chars = sortCharDKP($chars,1);
			
			$g = new StdClass();
			$g->type = $char_types[$i];
			$g->characters = $chars;
			
			array_push($groups,$g);
		}
		
		$vars['rooster_groups'] = &$groups;
		return sr_forms_public_rooster_index($this->system->db,$vars);
	}
}

?>