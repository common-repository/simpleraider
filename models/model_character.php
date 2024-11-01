<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
 
/**
 * database object which represents a game
 */
class ModelCharacter extends DBObject
{
	function __construct()
	{
		parent::__construct(SR_TABLE_PREFIX."sr_characters",
			 				"id", array("game_fk","user_fk","charname","stats_xml","type_fk")
							);
	}
	
	public function getOfUser(&$adodb,$user_id){
		$chars = array();
		
		$values[0] = $user_id;
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_characters WHERE user_fk=?',$values);
		for($i = 0; $i < count($rs); $i++)
		{
			$t = new ModelCharacter();
			if($t->select($adodb,$rs[$i]['id'])){
				array_push($chars,$t);
			}
		}
		
		return $chars;
	}
	
	public function getOfGame(&$adodb,$game_id){
		$chars = array();
		
		$values[0] = $game_id;
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_characters WHERE game_fk=?',$values);
		for($i = 0; $i < count($rs); $i++)
		{
			$t = new ModelCharacter();
			if($t->select($adodb,$rs[$i]['id'])){
				array_push($chars,$t);
			}
		}
		
		return $chars;
	}
	
	public function getByType(&$adodb,$type_id){
		$chars = array();
		
		$values[0] = $type_id;
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_characters WHERE type_fk=?',$values);
		for($i = 0; $i < count($rs); $i++)
		{
			$t = new ModelCharacter();
			if($t->select($adodb,$rs[$i]['id'])){
				array_push($chars,$t);
			}
		}
		
		return $chars;
	}
	
	public function getOfUserOfGame(&$adodb,$user_id,$game_id){
		$chars = array();
		
		$values[0] = $user_id;
		$values[1] = $game_id;
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_characters WHERE user_fk=? AND game_fk',$values);
		for($i = 0; $i < count($rs); $i++)
		{
			$t = new ModelCharacter();
			if($t->select($adodb,$rs[$i]['id'])){
				array_push($chars,$t);
			}
		}
		
		return $chars;
	}
	
	public function getByName(&$adodb,$charname,$game_id){
		$values[0] = $game_id;
		$values[1] = $charname;
		
		
		$rs = $adodb->GetOne('SELECT id FROM '.SR_TABLE_PREFIX.'sr_characters WHERE game_fk=? AND charname=?',$values);
		if($rs){
			$t = new ModelCharacter();
			if($t->select($adodb,$rs)){
				return $t;
			}
		}
		
		return false;
	}
	
	function getGameTitle(&$adodb){
		$values[0] = $this->game_fk;
		$title = $adodb->GetOne('SELECT title FROM '.SR_TABLE_PREFIX.'sr_games WHERE id=?',$values);
		return $title;
	}
	
	function getType(&$adodb){
		$values[0] = $this->type_fk;
		$title = $adodb->GetOne('SELECT title FROM '.SR_TABLE_PREFIX.'sr_character_types WHERE id=?',$values);
		return $title;
	}
	
	function delete(&$adodb){
		parent::delete($adodb);
		//$adodb->debug = true;
		$values[0] = $this->id;
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_dkp WHERE character_fk=?',$values);
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE character_fk=?',$values);
	}
}
?>
