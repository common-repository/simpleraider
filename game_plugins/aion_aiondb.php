<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * plugin by: h4rdc0m
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
 
include_once (SR_PLUGIN_PATH.'/game_plugins/game_plugin.php');

/**
 * AION game plugin
 */
class SRGamePlugin_aion_aiondb extends SRGamePlugin{
   
   
    public function getJavascriptHeaders(){
        $headers['aion_buffed_gameplugin_js'] = 'http://www.aiondatabase.com/js/exsyndication.js';
        return $headers;
    }
    #http://www.aiondatabase.com/xml/en_US/items/xmls/100000767.xml
    public function getItemTitle(&$item){
        $game_item_id = $item->internal_game_item_id;
        $xmlTree = $this->fetchXML('http://www.aiondatabase.com/xml/en_US/items/xmls/'.$game_item_id.'.xml');
        if($xmlTree){
            return $xmlTree->name;
        }
    }
   
    public function getItemDescription(&$item){
        $game_item_id = $item->internal_game_item_id;
        $xmlTree = $this->fetchXML('http://www.aiondatabase.com/xml/en_US/items/xmls/'.$game_item_id.'.xml');
        if($xmlTree){
            return $xmlTree->tooltip;
        }
    }
   
    public function getImageURL(&$item){
        $game_item_id = $item->internal_game_item_id;
        $xmlTree = $this->fetchXML('http://www.aiondatabase.com/xml/en_US/items/xmls/'.$game_item_id.'.xml');
        if($xmlTree){
            return 'http://www.aiondatabase.com/res/icons//40/'.$xmlTree->iconpath.'.png';
        }
    }
   
    public function getImageURLWithLink(&$item){
        $game_item_id = $item->internal_game_item_id;
        $xmlTree = $this->fetchXML('http://www.aiondatabase.com/xml/en_US/items/xmls/'.$game_item_id.'.xml');
        if($xmlTree){
            return '<a href="http://www.aiondatabase.com/item/'.$game_item_id.'/' . $xmlTree->name . '" target="_blank"><img src="http://www.aiondatabase.com/res/icons/40/'.$xmlTree->iconpath.'.png"></a>';
        }
    }
   
    public function getImageWithLinkAndTooltip(&$item,$css_id){
        $game_item_id = $item->internal_game_item_id;
        $xmlTree = $this->fetchXML('http://www.aiondatabase.com/xml/en_US/items/xmls/'.$game_item_id.'.xml');
        if($xmlTree){
            return '
            <a class="aion-item-full-medium" href="http://www.aiondatabase.com/item/'.$game_item_id.'/' . $xmlTree->name . '" target="_blank"><img src="http://www.aiondatabase.com/res/icons/40/'.$xmlTree->iconpath.'.png"></a>';
        }
    }
}
?>