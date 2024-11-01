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
 * WoW buffed.de game plugin
 */
class SRGamePlugin_wow_buffed extends SRGamePlugin{
	
	public function getItemTitle(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://wowdata.buffed.de/xml/i'.$game_item_id.'.xml');
		if($xmlTree){
			return $xmlTree->InventoryName;
		}
	}
	
	public function getItemDescription(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://wowdata.buffed.de/xml/i'.$game_item_id.'.xml');
		if($xmlTree){
			return $xmlTree->display_html;
		}
	}
	
	public function getImageURL(&$item){
		$game_item_id = $item->internal_game_item_id;
		$xmlTree = $this->fetchXML('http://wowdata.buffed.de/xml/i'.$game_item_id.'.xml');
		if($xmlTree){
			return 'http://wowdata.buffed.de/img/icons/wow/32/'.$xmlTree->Icon.'.png';
		}
	}
	
	public function getImageURLWithLink(&$item){
		$game_item_id = $item->internal_game_item_id;
		
		$item_image = $this->getCachedItemImage($item);
		if($item_image == false){
			$this->cacheItem($item);
			$xmlTree = $this->fetchXML('http://wowdata.buffed.de/xml/i'.$game_item_id.'.xml');
			if($xmlTree){
				return '<a href="http://wowdata.buffed.de/?i='.$game_item_id.'" target="_blank"><img src="http://wowdata.buffed.de/img/icons/wow/32/'.$xmlTree->Icon.'.png"></a>';
			}
		}else{
			return '<a href="http://wowdata.buffed.de/?i='.$game_item_id.'" target="_blank"><img src="'.$item_image.'"></a>';
		}
	}
	
	public function getImageWithLinkAndTooltip(&$item,$css_id){
		$game_item_id = $item->internal_game_item_id;
		
		$item_image = $this->getCachedItemImage($item);
		if($item_image == false){
			$this->cacheItem($item);
			$xmlTree = $this->fetchXML('http://wowdata.buffed.de/xml/i'.$game_item_id.'.xml');
			if($xmlTree){
				$item_image = 'http://wowdata.buffed.de/img/icons/wow/32/'.$xmlTree->Icon.'.png';
			}
		}
			return '
			<div id="'.$css_id.'" class="sr_item_tooltip"><img src="http://wowdata.buffed.de/tooltips/items/gif/'.$game_item_id.'.gif"></div>
			<a onmouseover="showWMTT(\''.$css_id.'\')" onmouseout="hideWMTT()" href="http://wowdata.buffed.de/?i='.$game_item_id.'" target="_blank"><img src="'.$item_image.'"></a>';
		
	}
	
	public function cacheItem(&$item){
		$storage_path = get_option('upload_path').'/sr_items/wow_buffed/';
		$img_data = file_get_contents($this->getImageURL($item));
		if($img_data){
			if(file_put_contents($storage_path.$item->internal_game_item_id.'.png',$img_data) == false){
				return false;
			}
		}
		else{
			printf("error caching data");
			return false;
		}
		return true;
	}
	
	public function getCachedItemImage(&$item){
		$storage_path = get_option('upload_path').'/sr_items/wow_buffed/';
		

		if(file_exists($storage_path.$item->internal_game_item_id.'.png')){
			$url = get_option('siteurl').'/wp-content/uploads/sr_items/wow_buffed/'.$item->internal_game_item_id.'.png';
			return $url;
		}
		
		return false;
	}
}
?>