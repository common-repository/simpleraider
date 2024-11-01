<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
 
include_once (SR_PLUGIN_PATH.'/game_plugins/game_plugin.php');

/**
 * WoW WOWHead game plugin
 */
class SRGamePlugin_wow_wowhead extends SRGamePlugin{
	
	public function getJavascriptHeaders(){
		$headers['wow_wowhead_gameplugin_js'] = 'http://static.wowhead.com/widgets/power.js';
		return $headers;
	}
	
	public function getItemTitle(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://www.wowhead.com/?item='.$game_item_id.'&xml');
		if($xmlTree){
			return $xmlTree->item->name;
		}
	}
	
	public function getItemDescription(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://www.wowhead.com/?item='.$game_item_id.'&xml');
		if($xmlTree){
			return $xmlTree->item->htmlTooltip;
		}
	}
	
	public function getImageURL(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://www.wowhead.com/?item='.$game_item_id.'&xml');
		if($xmlTree){
			return 'http://wowdata.buffed.de/img/icons/wow/32/'.$xmlTree->Icon.'.png';
		}
	}
	
	public function getImageURLWithLink(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://www.wowhead.com/?item='.$game_item_id.'&xml');
		if($xmlTree){
			return '<a href="http://www.wowhead.com/?item='.$game_item_id.'" target="_blank">icons not supported yet in wowhead plugin</a>';
		}
	}
	
	public function getImageWithLinkAndTooltip(&$item,$css_id){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://www.wowhead.com/?item='.$game_item_id.'&xml');
		if($xmlTree){
			return '
			<a href="http://www.wowhead.com/?item='.$game_item_id.'" class="q4"><img src="http://wowdata.buffed.de/img/icons/wow/32/'.$xmlTree->Icon.'.png"></a>';
		}
	}
}
?>