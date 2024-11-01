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
 * Runes Of Magic buffed.de game plugin
 */
class SRGamePlugin_rom_buffed extends SRGamePlugin{
	
	
	public function getJavascriptHeaders(){
		$headers['rom_buffed_gameplugin_js'] = 'http://romdata.getbuffed.com/js/buffed-ext-rom-tooltips.js';
		return $headers;
	}
	
	public function getItemTitle(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://romdata.getbuffed.com/tooltiprom/items/xml/'.$game_item_id.'.xml');
		if($xmlTree){
			return $xmlTree->Name;
		}
	}
	
	public function getItemDescription(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://romdata.getbuffed.com/tooltiprom/items/xml/'.$game_item_id.'.xml');
		if($xmlTree){
			return $xmlTree->display_html;
		}
	}
	
	public function getImageURL(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://romdata.getbuffed.com/tooltiprom/items/xml/'.$game_item_id.'.xml');
		if($xmlTree){
			return 'http://wowdata.buffed.de/img/icons/wow/32/'.$xmlTree->Icon.'.png';
		}
	}
	
	public function getImageURLWithLink(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://romdata.getbuffed.com/tooltiprom/items/xml/'.$game_item_id.'.xml');
		if($xmlTree){
			return '<a href="http://romdata.getbuffed.com/?i='.$game_item_id.'" target="_blank"><img src="http://romdata.getbuffed.com/img/icons/rom/32/'.$xmlTree->Icon.'.png"></a>';
		}
	}
	
	public function getImageWithLinkAndTooltip(&$item,$css_id){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://romdata.getbuffed.com/tooltiprom/items/xml/'.$game_item_id.'.xml');
		if($xmlTree){
			return '
			<a  href="http://romdata.getbuffed.com/?i='.$game_item_id.'" target="_blank"><img src="http://romdata.getbuffed.com/img/icons/rom/32/'.$xmlTree->Icon.'.png"></a>';
		}
	}
}
?>
