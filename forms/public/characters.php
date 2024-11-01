<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');
include_once (SR_PLUGIN_PATH.'/models/model_character_types.php');

function sr_forms_public_characters_overview(&$adodb,&$vars){
	$content = '<a href="?page_id='.$_GET['page_id'].'&mod=Public_Character&action=newCharacter">create new character</a>';
	
	$chars = $vars['characters'];
	$css_class='STYLED_TABLE_TD_1';
	$content = $content.'
	<h2>your characters</h2>
		<table class="STYLED_TABLE" width="100%">
		<thead>
		<tr>
			<th>name</th>
			<th>game</th>
			<th>type</th>
			<th>actions</th>
		</tr></thead>';
		
	for($i = 0; $i < count($chars); $i++){
		$tChar = $chars[$i];
		
		$content = $content.'<tr>';
		$content = $content.'<td class="'.$css_class.'">'.$tChar->charname.'</td>';
		$content = $content.'<td class="'.$css_class.'">'.$tChar->getGameTitle($adodb).'</td>';
		$content = $content.'<td class="'.$css_class.'">'.$tChar->getType($adodb).'</td>';
		$content = $content.'<td class="'.$css_class.'"><a href="?page_id='.$_GET['page_id'].'&mod=Public_Character&action=deleteCharacter&id='.$tChar->id.'">[delete]</a></td>';
		$content = $content.'</tr>';
		
		if(strcmp($css_class,'STYLED_TABLE_TD_1') == 0){
			$css_class='STYLED_TABLE_TD_2';
		}else{
			$css_class='STYLED_TABLE_TD_1';
		}
	}
	$content = $content.'</table>';
	return $content;
}

function sr_forms_public_characters_new(&$adodb,&$vars){
	$content = '<b>create a new character</b>';
	
	$content = $content.'
		<form action="?page_id='.$_GET['page_id'].'&mod=Public_Character&action=createCharacter" method="post">
		<p><label>character name:</label><span> <input type="text" name="char_charname"></span></p>
		<p><label>game:</label><span> <select name="char_game_fk">'.ModelGames::getOptions($adodb).'</select></span></p>
		<p><label>type:</label><span> <select name="char_type_fk">'.ModelCharacterTypes::getOptions($adodb).'</select></span></p>
		<input type="submit" value="create new character">
		</form>
	';
	
	return $content;
}
?>