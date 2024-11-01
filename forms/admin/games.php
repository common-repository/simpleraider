<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');

function sr_forms_admin_games_edit_game(&$adodb,&$vars){
	$game = $vars['sr_game'];
	
	$content = $content.'<h2>edit game</h2>';
	$content = $content.'
		<form action="?page='.$_GET['page'].'&mod=Admin_Games&action=updateGame" method="post">
		title: <input type="text" name="sr_game_title" value="'.$game->title.'">
		short name: <input type="text" name="sr_game_internalname" value="'.$game->internalname.'">
		<input type="hidden" name="id" value="'.$game->id.'">
		<input type="submit" value="update">
		</form>
		
		
		
		<h3>character types</h3>
		
		<form action="?page='.$_GET['page'].'&mod=Admin_Games&action=addCharType" method="post">
		type: <input type="text" name="sr_game_char_title">
		<input type="hidden" name="id" value="'.$game->id.'">
		<input type="submit" value="create character type">
		</form>
		
		<table class="widefat" cellspacing="0">
		<thead>
		<tr>
			<th>title</th>
			<th>icon</th>
			<th>actions</th>
		</tr></thead>';
		
	for($i = 0; $i < count($vars['sr_game_characters']); $i++){
		$tChar = $vars['sr_game_characters'][$i];
		//$tRaid->fetchData($adodb);
		
		$content = $content.'<tr>';
		$content = $content.'<td>'.$tChar->getIconImageTag().$tChar->title.'</td>
		<td>
		<form action="?page='.$_GET['page'].'&mod=Admin_Games&action=updateCharTypeIcon" method="post">
			<select name="game_char_type_icon">'.SRUtil::getCharTypesIconOptions($tChar->icon).'</select>
			<input type="hidden" name="typeId" value="'.$tChar->id.'">
			<input type="hidden" name="id" value="'.$game->id.'">
			<input type="submit" value="update icon">
		</form>
		</td>
		<td><a href="?page='.$_GET['page'].'&mod=Admin_Games&action=deleteChar&id='.$game->id.'&typeId='.$tChar->id.'">[delete]</a>
		</td>
		';
		$content = $content.'</tr>';
	}
	$content = $content.'
		<tfoot>
		<tr>
			<th>title</th>
			<th>icon</th>
			<th>actions</th>
		</tr></tfoot>';
	$content = $content.'</table>';
	return $content;
}

