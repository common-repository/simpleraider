<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_character.php');
include_once (SR_PLUGIN_PATH.'/models/model_raidplan.php');
// require_once ('libs/util.php');

/**
 * database object which represents a game
 */
class ModelDKP extends DBObject
{
	public $character;
	public $raid;
	
	function __construct()
	{
		parent::__construct(SR_TABLE_PREFIX."sr_dkp",
			 				"id", array("updated_at","character_fk","game_fk","dkp","notice","raid_fk")
							);
	}

	public function getAllOfRaid(&$adodb,$raid_id){
		$items = array();
		
		$values[0] = $raid_id;
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_dkp WHERE raid_fk=? ORDER BY updated_at DESC',$values);
		for($i = 0; $i < count($rs); $i++){
			$tItem = new ModelDKP();
			if($tItem->select($adodb,$rs[$i]['id'])){
				$tItem->fetchData($adodb);
				array_push($items,$tItem);
			}
		}
		
		return $items;
	}
	
	public function getAllOfCharacter(&$adodb,$char_id){
		$items = array();
		
		$values[0] = $char_id;
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_dkp WHERE character_fk=? ORDER BY updated_at DESC',$values);
		
		for($i = 0; $i < count($rs); $i++){
			$tItem = new ModelDKP();
			if($tItem->select($adodb,$rs[$i]['id'])){
				$tItem->fetchData($adodb);
				array_push($items,$tItem);
			}
		}
		
		return $items;
	}
	
	public function getDKPSummaryOfCharacter(&$adodb,$char_id){
		$items = array();
		
		$values[0] = $char_id;
		$rs = $adodb->GetOne('SELECT sum(dkp) FROM '.SR_TABLE_PREFIX.'sr_dkp WHERE character_fk=?',$values);
		if($rs == null){
			$rs = 0;
		}
		return $rs;
	}
	
	public function resetDKP(&$adodb,&$game_id,$dkp,$notice){
		// at first we get all characters of a game
		$values[0] = $game_id;
		$chars = $adodb->GetAll('SELECT id,charname FROM '.SR_TABLE_PREFIX.'sr_characters WHERE game_fk=?',$values);
		
		
		for($i = 0; $i < count($chars); $i++){
			$tCharID = $chars[$i]['id'];
			$sum = ModelDKP::getDKPSummaryOfCharacter($adodb,$tCharID);
			print 'char '.$chars[$i]['charname'].' -> '.$sum.'<br>';
			if($sum > $dkp){
				$t = ($sum - $dkp)*-1;
			}
			else{
				$t = $dkp - $sum;
			}
			print 'char '.$chars[$i]['charname'].' correct '.$sum.' with '.$t.'  to '.$dkp.'<br>';
			
			$dkp_entry= new ModelDKP();
			$dkp_entry->updated_at = date( 'Y-m-d H:i:s');
			$dkp_entry->character_fk = $tCharID;
			$dkp_entry->game_fk = $game_id;
			$dkp_entry->dkp = $t;
			$dkp_entry->notice = $notice;
			$dkp_entry->raid_fk = 0;
			$dkp_entry->insert($this->system->db);
		}
	}
	
	public function fetchData(&$adodb){
		$this->character = new ModelCharacter();
		$this->character->select($adodb,$this->character_fk);
		
		$this->raid = new ModelRaidplan();
		$this->raid->select($adodb,$this->raid_fk);
	}
}
?>
