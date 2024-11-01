<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
 
/**
 * parent class for all game plugins
 **/
 
class SRGamePlugin{
	
	public function getJavascriptHeaders(){
		return null;
	}
	
	public function getItemTitle(&$item){
	}
	
	public function getItemDescription(&$item){
		
	}
	
	public function getImageURL(&$item){
	}
	
	public function getImageURLWithLink(&$item){
	}
	
	public function getImageWithLinkAndTooltip(&$item,$css_id){
	}
	
	public function cacheItem(&$item){
	}
	
	public function getCachedItemImage(&$item){}
	
	protected function fetchXML($url){
		$content = file_get_contents($url);
		$xmlTree = simplexml_load_string($content);
		if($xmlTree){
			return $xmlTree;
		}
		return false;
	}
}
?>