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
class ModelItem extends DBObject
{
	
	function __construct()
	{
		parent::__construct(SR_TABLE_PREFIX."sr_items",
			 				"id", array("internal_game_item_id","title","game_fk","raidtemplate_fk")
							);
	}

	public function getAllOfRaidTemplate(&$adodb,$raid_id){
		$items = array();
		
		$values[0] = $raid_id;
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_items WHERE raidtemplate_fk=?',$values);
		for($i = 0; $i < count($rs); $i++){
			$tItem = new ModelItem();
			if($tItem->select($adodb,$rs[$i]['id'])){
				array_push($items,$tItem);
			}
		}
		
		return $items;
	}
	
	public function getAllOfRaidTemplateOptions(&$adodb,$raid_id){
		$options = null;
		$values[0] = $raid_id;
		$rs = $adodb->GetAll('SELECT id,title FROM '.SR_TABLE_PREFIX.'sr_items WHERE raidtemplate_fk=? ORDER BY title ASC',$values);
		
		for($i = 0; $i < count($rs); $i++){
			$tItem = new ModelItem();
			if($tItem->select($adodb,$rs[$i]['id'])){
			 $options = $options.'<option value="'.$rs[$i]['id'].'">'.$rs[$i]['title'].'</option>';
			}
		}
		
		return $options;
	}
}
?>
