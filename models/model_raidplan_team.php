<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_character.php');

define ('RAID_CHARACTER_STATUS_SIGNED_ON',1);
define ('RAID_CHARACTER_STATUS_WAITING_LIST',2);

/**
 * database object which represents a game
 */
class ModelRaidplanTeam extends DBObject
{
	
	function __construct()
	{
		parent::__construct(SR_TABLE_PREFIX."sr_raidplan_team",
			 				"id", array("raid_fk","character_fk","status","notice","created_at","cstatus")
							);
	}
	
	function getRaidCharacterStatusOptions(){
		
		$options = '';
		$options = $options.'<option value="1">sign on</option>';
		$options = $options.'<option value="2">waiting list</option>';
		
		return $options;
	}
	
	function getStatusTitle($status){
		switch($status){
		case RAID_CHARACTER_STATUS_SIGNED_ON:
			return 'signed on';
			break;
		case RAID_CHARACTER_STATUS_WAITING_LIST:
			return 'waiting list';
			break;
		}
		return 'unknown status';
	}
	
	function getRaidCharacterStatusOptionsForCharacter(&$adodb,$raid_id,$char_id){
		
		$status = ModelRaidplanTeam::getCharacterStatus($adodb,$raid_id,$char_id);
		
		$options = '';
		if($status == 1)
			$options = $options.'<option value="1" selected>sign on</option>';
		else
			$options = $options.'<option value="1">sign on</option>';
		
		if($status == 2)
			$options = $options.'<option value="2" selected>waiting list</option>';
		else
			$options = $options.'<option value="2">waiting list</option>';
			
		return $options;
	}
	
	
	function getCharactersByType(&$adodb,$type_id,$raid_id,$status)
	{
		$chars = array();
		
		$values[0] = $type_id;
		
		$chars_res = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_characters WHERE type_fk=?',$values);
		
		for($i = 0; $i < count($chars_res); $i++){
			$tChar = new ModelCharacter();
			//print 'char: '.$chars_res[$i]['id'].'<br>';
			if($tChar->select($adodb,$chars_res[$i]['id'])){
				//print 'char: '.$tChar->charname.'<br>';
				$values[0] = $raid_id;
				$values[1] = $tChar->id;
				$values[2] = $status;
				
				$tRes = $adodb->GetRow('SELECT * FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk=? and character_fk=? and status=?',$values);
				
				if($tRes){
					// more information
					$data = new StdClass();
					$data->cstatus = intval($tRes['cstatus']);
					$data->signon_date = $tRes['created_at'];
					$tChar->addVar('data',$data);
					array_push($chars,$tChar);
				}
			}
		}
		
		return $chars;
	}
	
	function getCharacters(&$adodb,$raid_id)
	{
		$chars = array();
		
		$values[0] = $raid_id;
		
		$rs = $adodb->GetAll('SELECT character_fk FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk=?',$values);
		
		for($i = 0; $i < count($rs); $i++){
			$tChar = new ModelCharacter();
			if($tChar->select($adodb,$rs[$i]['character_fk'])){
				array_push($chars,$tChar);
				}
		}
		return $chars;
	}
	
	function getRaidCharactersNotInRaidOptions(&$adodb,$raid_id)
	{
		$chars = '';
		$values[0] = $raid_id;
		$rs = $adodb->GetAll('SELECT DISTINCT id,charname FROM '.SR_TABLE_PREFIX.'sr_characters  
			WHERE NOT EXISTS (SELECT * FROM '.SR_TABLE_PREFIX.'sr_raidplan_team 
			WHERE '.SR_TABLE_PREFIX.'sr_raidplan_team.character_fk = '.SR_TABLE_PREFIX.'sr_characters.id 
			AND '.SR_TABLE_PREFIX.'sr_raidplan_team.raid_fk=?)',$values);
		for($i = 0; $i < count($rs); $i++){
			$chars = $chars.'<option value="'.$rs[$i]['id'].'">'.$rs[$i]['charname'].'</option>';
		}
		
		return $chars;
	}
	
	function getCharactersOptions(&$adodb,$raid_id)
	{
		$chars = '';
		
		$values[0] = $raid_id;
		
		$rs = $adodb->GetAll('SELECT * FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk=?',$values);
		
		for($i = 0; $i < count($rs); $i++){
			$tChar = new ModelCharacter();
			if($tChar->select($adodb,$rs[$i]['character_fk'])){
				$chars = $chars.'<option value="'.$tChar->id.'">'.$tChar->charname.'('.ModelRaidplanTeam::getStatusTitle($rs[$i]['status']).')</option>';
			}
		}
		return $chars;
	}
	
	function getCharactersByStatus(&$adodb,$raid_id,$status)
	{
		$chars = array();
		
		$values[0] = $raid_id;
		$values[1] = $status;
		
		$rs = $adodb->GetAll('SELECT character_fk FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk=? AND status=?',$values);
		
		for($i = 0; $i < count($rs); $i++){
			$tChar = new ModelCharacter();
			if($tChar->select($adodb,$rs[$i]['character_fk'])){
				array_push($chars,$tChar);
				}
		}
		return $chars;
	}
	
	function getCharacterStatus(&$adodb,$raid_id,$char_id){
		$values[0] = $raid_id;
		$values[1] = $char_id;
		$rs = $adodb->GetOne('SELECT status FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk=? AND character_fk	=?',$values);
		return $rs;
	}
	
	function getCharacterStatusForOption(&$adodb,$raid_id,$char_id,$status){
		$r = ModelRaidplanTeam::getCharacterStatus($adodb,$raid_id,$char_id);
		if($status == $r){
			return 'selected';
		}
		return '';
	}
	
	function hasUserCharacterInRaid(&$adodb,$user_id,&$raid_id){
		$values[0] = $raid_id;
		$values[1] = $user_id;
		//$adodb->debug = true;
		$tRes = $adodb->GetOne('SELECT '.SR_TABLE_PREFIX.'sr_raidplan_team.id AS id FROM '.SR_TABLE_PREFIX.'sr_raidplan_team,'.SR_TABLE_PREFIX.'sr_characters WHERE '.SR_TABLE_PREFIX.'sr_raidplan_team.raid_fk=? AND '.SR_TABLE_PREFIX.'sr_raidplan_team.character_fk = '.SR_TABLE_PREFIX.'sr_characters.id AND '.SR_TABLE_PREFIX.'sr_characters.user_fk = ?',$values);
		//$adodb->debug = false;
		if($tRes){
			return true;
		}
		
		return false;
	}
	
	function removeUserCharFromRaid(&$adodb,$user_id,&$raid_id,$game_id){
		$values[0] = $user_id;
		$values[1] = $game_id;
		$char_res = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_characters WHERE user_fk=? AND game_fk=?',$values);

		for($i = 0; $i < count($char_res); $i++)
		{
			$values[0] = $char_res[$i]['id'];
			$values[1] = $raid_id;	
			$tRes = $adodb->GetOne('DELETE FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE character_fk=? and raid_fk=?',$values);
		}
	}
	
	function getByChar(&$adodb,$char_id,$raid_id){
		$values[0] = $char_id;
		$values[1] = $raid_id;
		$rs = $adodb->GetOne('SELECT id FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE character_fk=? and raid_fk=?',$values);
		if($rs){
			$t = new ModelRaidplanTeam();
			if($t->select($adodb,$rs)){
				return $t;
			}
		}
		return false;
	}
	
	function removeCharFromRaid(&$adodb,&$raid_id,$char_id){
		$values[0] = $char_id;
		$values[1] = $raid_id;
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE character_fk=? AND raid_fk=?',$values);
	}
	
	function changeCharacterStatus(&$adodb,$raid_id,$char_id,$status){
		$values[0] = $status;
		$values[1] = $char_id;
		$values[2] = $raid_id;
		
		$adodb->Execute('UPDATE '.SR_TABLE_PREFIX.'sr_raidplan_team SET status=? WHERE character_fk=? AND raid_fk=?',$values);
	}
	
	function getNoticeForCharacter(&$adodb,$raid_id,$char_id){
		$values[0] = $raid_id;
		$values[1] = $char_id;
		$notice = $adodb->GetOne('SELECT notice FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk=? and character_fk=?',$values);
		return $notice;
	}
}
?>
