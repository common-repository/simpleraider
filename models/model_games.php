<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/

 include_once ('dbobject.php');
/**
 * database object which represents a game
 */
class ModelGames extends DBObject
{
	
	function __construct()
	{
		parent::__construct(SR_TABLE_PREFIX."sr_games",
			 				"id", array("title","internalname","gameplugin")
							);
	}
	
	function getAll(&$adodb){
		$games = array();
		
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_games');
		for($i = 0; $i < count($rs); $i++){
			$tGame = new ModelGames();
			if($tGame->select($adodb,$rs[$i]['id'])){
				array_push($games,$tGame);
			}
		}
		
		return $games;
	}
	
	function getOptions(&$adodb){
		
		$options = '';
		$rs = $adodb->GetAll('SELECT * FROM '.SR_TABLE_PREFIX.'sr_games ORDER BY title ASC');
		for($i = 0; $i < count($rs); $i++){
			 $options = $options.'<option value="'.$rs[$i]['id'].'">'.$rs[$i]['title'].'</option>';
		}
		
		return $options;
	}
	
	function getOptionsSelected(&$adodb,&$raid){

		$options = '';
		$rs = $adodb->GetAll('SELECT * FROM '.SR_TABLE_PREFIX.'sr_games ORDER BY title ASC');
		for($i = 0; $i < count($rs); $i++){
			if($raid->game->id == $rs[$i]['id']){
			 $options = $options.'<option value="'.$rs[$i]['id'].'" selected>'.$rs[$i]['title'].'</option>';
			}else{
				$options = $options.'<option value="'.$rs[$i]['id'].'">'.$rs[$i]['title'].'</option>';
			}
		}
		
		return $options;
	}
	
	
	function loadGamePlugin()
	{
		if($this->gameplugin == null || $this->gameplugin == '-')
		{ // the default plugin which can do absolute nothing ;)
			include_once (SR_PLUGIN_PATH.'/game_plugins/game_plugin.php');
			$plugin = new SRGamePlugin();
			return $plugin;
		}
		
		include_once (SR_PLUGIN_PATH.'/game_plugins/'.$this->gameplugin.'.php');
		$clsName ='SRGamePlugin_'.$this->gameplugin;
		$plugin = new $clsName();
		return $plugin;
	}
	
	function delete(&$adodb){
		parent::delete($adodb);
		$values[0] = $this->id;
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_characters WHERE game_fk=?',$values);
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_character_types WHERE game_fk=?',$values);
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_dkp WHERE game_fk=?',$values);
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_items WHERE game_fk=?',$values);
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_raidplan WHERE game_fk=?',$values);
		$adodb->Execute('DELETE FROM '.SR_TABLE_PREFIX.'sr_raidtemplates WHERE game_fk=?',$values);
	}
	
	function getGamePluginOptions($selected){
		$options = '';
		$options = $options.'<option value="-" >-</option>';
		$pluginpath = SR_PLUGIN_PATH.'/game_plugins/';
		if ($handle = opendir($pluginpath))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file != "." && $file != ".." && $file != ".svn" && $file != 'game_plugin.php')
				{
					$plugin_name = str_replace('.php','',$file);
					if($selected == $plugin_name){
						$options = $options.'<option value="'.$plugin_name.'" selected>'.$plugin_name.'</option>';
					}else{
						$options = $options.'<option value="'.$plugin_name.'">'.$plugin_name.'</option>';
					}
				}
			}
			closedir($handle);
		}
		
		return $options;
	}
}
?>