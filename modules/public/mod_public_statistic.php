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
include_once (SR_PLUGIN_PATH.'/forms/public/statistic.php');
include_once (SR_PLUGIN_PATH.'/forms/public/general.php');

function BubbleSort($sort_array,$reverse)
{
  for ($i = 0; $i < sizeof($sort_array); $i++){
    for ($j = $i + 1; $j < sizeof($sort_array); $j++){
      if($reverse){
        if ($sort_array[$i]->stat_activity < $sort_array[$j]->stat_activity){
          $tmp = $sort_array[$i];
          $sort_array[$i] = $sort_array[$j];
          $sort_array[$j] = $tmp;
        }
      }else{
        if ($sort_array[$i]->stat_activity > $sort_array[$j]->stat_activity){
          $tmp = $sort_array[$i];
          $sort_array[$i] = $sort_array[$j];
          $sort_array[$j] = $tmp;
        }
      }
    }
  }
  return $sort_array;
} 


class ModPublic_Statistic extends Module
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
		
		$vars['dkp_character_summary'] = $dkp_character_summary;
		
		
		// time window
		if(!isset($this->system->VARS['starting_date'])){
			$starting_date = date("Y-m").'-01';
		}else{
			$starting_date = $this->system->VARS['starting_date'];
		}
		
		if(!isset($this->system->VARS['ending_date'])){
			$ending_date = date("Y-m-d");
		}else{
			$ending_date = $this->system->VARS['ending_date'];
		}

		// number of raids in the time window
		$values[0] = $starting_date;
		$values[1] = $ending_date;

		$num_raids = $this->system->db->GetOne('SELECT count(*) FROM '.SR_TABLE_PREFIX.'sr_raidplan WHERE DATE(raidstart) >= DATE(?) AND DATE(raidstart) <= DATE(?)',$values);
		$raids = $this->system->db->GetAll('SELECT * FROM '.SR_TABLE_PREFIX.'sr_raidplan WHERE DATE(raidstart) >= DATE(?) AND DATE(raidstart) <= DATE(?)',$values);
		
		// getting the characters
		$characters = ModelCharacter::getOfGame($this->system->db,1);
		
		// calc stats
		for($i = 0; $i < count($characters); $i++){
			$num_signed_on = 0;
			$num_waiting_list = 0;
			
			for($x = 0; $x < count($raids); $x++){
				$tRaid = $raids[$x];
				$tChar = $characters[$i];
				
				$values[0] = $tRaid['id'];
				$values[1] = $tChar->id;
				//$this->system->db->debug = true;
				$rs = $this->system->db->GetRow('SELECT * FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk=? AND character_fk=?',$values);
				if($rs){
					if(intval($rs['status']) == 1){
						$num_signed_on++;
					}else{
						$num_waiting_list++;
					}
				}
			}
			
			$characters[$i]->addVar('stat_activity',sprintf("%0.2f",(floatval($num_signed_on) / floatval($num_raids) * 100.0)));
			$characters[$i]->addVar('stat_num_signed_on_raids',$num_signed_on);
			$characters[$i]->addVar('stat_num_raids_on_waiting_list',$num_waiting_list);
		}

		$characters = BubbleSort($characters,1);
		$vars['dkp_character_detail_summary'] = $dkp_character_detail_summary;
		$vars['ending_date'] = $ending_date;
		$vars['starting_date'] = $starting_date;
		$vars['stats_num_raids'] = $num_raids;
		$vars['stats_characters'] = &$characters;
		
		return sr_forms_public_statistic_overview($this->system->db,$vars);
	}	
}

?>
