<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');
include_once (SR_PLUGIN_PATH.'/models/model_raid_template.php');
include_once (SR_PLUGIN_PATH.'/libs/util.php');

/**
 * database object which represents a raid
 */
class ModelRaidplan extends DBObject
{
	public $game;
	public $template;
	public $num_players_signed_on;
	public $num_players_waiting_list;
	
	function __construct()
	{
		parent::__construct(SR_TABLE_PREFIX."sr_raidplan",
			 				"id", array("game_fk","raidstart","numplayers","title","template_fk","raidfinish","raidinvitation","raidsignonend")
							);
	}
	
	/**
	 * overloaded function
	 **/
	function select(&$adodb,$id)
	{
		
		if(intval($id) == 0){
			$this->game_fk = 0;
			$this->title = '[unknown raid / admin correction]';
			return false;
		}
		else{
			return parent::select($adodb,$id);
		}
	}
	
	function getAll(&$adodb){
		$raids = array();
		
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_raidplan ORDER BY raidstart DESC');
		for($i = 0; $i < count($rs); $i++){
			$raid = new ModelRaidplan();
			$raid->select($adodb,$rs[$i]['id']);
			array_push($raids,$raid);
		}
		
		return $raids;
	}
	
	/**
	 * getting raids for the public/front end view
	 **/
	function getPublicRaids(&$adodb){
		$raids = array();
		
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_raidplan WHERE DATE(raidstart) >= CURDATE() ORDER BY raidstart ASC');
		for($i = 0; $i < count($rs); $i++){
			$raid = new ModelRaidplan();
			$raid->select($adodb,$rs[$i]['id']);
			array_push($raids,$raid);
		}
		
		return $raids;
	}
	
	function getGameTitle(&$adodb){
		$values[0] = $this->game_fk;
		$title = $adodb->GetOne('SELECT title FROM '.SR_TABLE_PREFIX.'sr_games WHERE id=?',$values);
		return $title;
	}
	
	function fetchData(&$adodb){
		$this->game = new ModelGames();
		$this->game->select($adodb,$this->game_fk);
		
		$this->template = new ModelRaidTemplate();
		$this->template->select($adodb,$this->template_fk);
		
		$values[0] = $this->id;
		$this->num_players_signed_on = $adodb->GetOne('SELECT count(*) FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk =? AND status=1',$values);
		$this->num_players_waiting_list = $adodb->GetOne('SELECT count(*) FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk =? AND status=2',$values);
	}
	
	/**
	 * check if a player can still sign on to a raid
	 **/
	function isSignOnAvailable(){
		$now = time();
		
		$traidstart = strtotime($this->raidstart);
		$traidfinish = strtotime($this->raidfinish);
		$traidinvitation = strtotime($this->raidinvitation);
		$traidsignonend = strtotime($this->raidsignonend);
		
		if($now > $traidsignonend)
			return false;
		
		return true;
	}
	
	function delete(&$adodb){
		parent::delete($adodb);
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk='.$this->id);
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_dkp WHERE raid_fk='.$this->id);
	}
}
?>