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
class ModelCharacterTypes extends DBObject
{
	
	function __construct()
	{
		parent::__construct(SR_TABLE_PREFIX."sr_character_types",
			 				"id", array("game_fk","title","icon")
							);
	}
	
	function getAllOfGame(&$adodb,$game_id){
		$char_types = array();
		
		$values[0] = $game_id;
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_character_types WHERE game_fk=?',$values);
		for($i = 0; $i < count($rs); $i++)
		{
			$type = new ModelCharacterTypes();
			if($type->select($adodb,$rs[$i]['id'])){
				array_push($char_types,$type);
			}
		}
		
		return $char_types;
	}
	
	function getOptions(&$adodb){
		
		$options = '';
		$rs = $adodb->GetAll('SELECT '.SR_TABLE_PREFIX.'sr_character_types.id AS id,'.SR_TABLE_PREFIX.'sr_character_types.title AS title ,'.SR_TABLE_PREFIX.'sr_games.internalname AS game FROM '.SR_TABLE_PREFIX.'sr_character_types,'.SR_TABLE_PREFIX.'sr_games WHERE '.SR_TABLE_PREFIX.'sr_character_types.game_fk = '.SR_TABLE_PREFIX.'sr_games.id ORDER BY title ASC');
		for($i = 0; $i < count($rs); $i++){
			 $options = $options.'<option value="'.$rs[$i]['id'].'">('.$rs[$i]['game'].') '.$rs[$i]['title'].'</option>';
		}
		
		return $options;
	}
	
	function getIconImageTag(){
		if($this->icon == '-' || strlen($this->icon) == 0){
			return '';
		}
		
		return $icon_path = '<img src="'.SR_PLUGIN_URL.'/images/chartypes/'.$this->icon.'">';
	}

}
?>
