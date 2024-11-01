<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');
// require_once ('libs/util.php');

/**
 * database object which represents a game
 */
class ModelRaidTemplate extends DBObject
{
	public $game;
	
	function __construct()
	{
		parent::__construct(SR_TABLE_PREFIX."sr_raidtemplates",
			 				"id", array("game_fk","title","numplayers","icon")
							);
	}
	
	public function getAll(&$adodb){
		$templates = array();
		
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_raidtemplates ORDER BY game_fk');
		for($i = 0; $i < count($rs); $i++)
		{
			$t = new ModelRaidTemplate();
			if($t->select($adodb,$rs[$i]['id'])){
				array_push($templates,$t);
			}
		}
		
		return $templates;
	}
	
	function getGameTitle(&$adodb){
		$values[0] = $this->game_fk;
		$title = $adodb->GetOne('SELECT title FROM '.SR_TABLE_PREFIX.'sr_games WHERE id=?',$values);
		return $title;
	}
	
	public function getTemplatesAsOptions(&$adodb){
		$templates = '';
		
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_raidtemplates ORDER BY game_fk');
		for($i = 0; $i < count($rs); $i++)
		{
			$t = new ModelRaidTemplate();
			if($t->select($adodb,$rs[$i]['id'])){
				$t->fetchData($adodb);
				$templates = $templates.'<option value="'.$t->id.'">'.$t->game->title.' - '.$t->title.' ('.$t->numplayers.')</option>';
			}
		}
		
		return $templates;
	}
	
	public function fetchData(&$adodb){
		$this->game = new ModelGames();
		$this->game->select($adodb,$this->game_fk);
	}
	
	public function numItems(&$adodb){
		$values[0] = $this->id;
		$rs = $adodb->GetOne('SELECT count(*) FROM '.SR_TABLE_PREFIX.'sr_items WHERE raidtemplate_fk=?',$values);
		return $rs;
	}
	
	function getRaidIconOptions($selected){
		$options = '';
		$options = $options.'<option value="-" >-</option>';
		$pluginpath = SR_PLUGIN_PATH.'/images/raidicons/';
		if ($handle = opendir($pluginpath))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file != "." && $file != ".." && $file != ".svn")
				{
					$icon = $file;
					if($selected == $icon){
						$options = $options.'<option value="'.$icon.'" selected>'.$icon.'</option>';
					}else{
						$options = $options.'<option value="'.$icon.'">'.$icon.'</option>';
					}
				}
			}
			closedir($handle);
		}
		
		return $options;
	}
	
	public function getIconImageUrl(){
		if(strlen($this->icon) > 0){
			return '<img src="'.SR_PLUGIN_URL.'/images/raidicons/'.$this->icon.'">';
		}
	}
}
?>
