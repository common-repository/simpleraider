<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');

function sr_forms_admin_options_overview(&$adodb,&$vars){
	
	$content = $content.'<h2>Options</h2>';
	$content= $content.sr_forms_admin_game_list($adodb,$vars);
	$content= $content.sr_forms_admin_page_options($adodb,$vars);
	return $content;
}

function sr_forms_admin_page_options(&$adodb,&$vars){
	$content = $content.'<h3>page options</h3>';
	
	$content = $content.'
		<form action="?page='.$_GET['page'].'&mod=Admin_Options&action=updatePageDisplay" method="post">
		page id to display simple raider <input type="text" name="sr_page_id" value="'.$vars['sr_page_id'].'" size="2">
		<input type="submit" value="update">
		</form>
	';
	return $content;
}

function sr_forms_admin_game_list(&$adodb,&$vars){
	$content = $content.'<h3>games overview</h3>';
	$content = $content.'
		<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
			<th>short name</th>
			<th>title</th>
			<th>gameplugin</th>
			<th>actions</th>
		</tr></thead>';
		
	for($i = 0; $i < count($vars['sr_games']); $i++){
		$tGame = $vars['sr_games'][$i];
		//$tRaid->fetchData($adodb);
		
		$content = $content.'<tr>';
		$content = $content.'<td>'.$tGame->internalname.'</td>';
		$content = $content.'<td>'.$tGame->title.'</td>';
		$content = $content.'<td>
		<form action="?page='.$_GET['page'].'&mod=Admin_Options&action=updateGamePlugin&id='.$tGame->id.'" method="post">
		<select name="game_plugin">'.ModelGames::getGamePluginOptions($tGame->gameplugin).'</select>
		<input type="submit" value="set plugin">
		</form>
		</td>
		<td>
		<a href="?page='.$_GET['page'].'&mod=Admin_Games&action=editGame&id='.$tGame->id.'">[edit]</a>
		<a href="?page='.$_GET['page'].'&mod=Admin_Options&action=deleteGame&id='.$tGame->id.'">[delete]</a>
		</td>
		';
		$content = $content.'</tr>';
	}
	$content = $content.'
		<tfoot>
		<tr>
			<th>short name</th>
			<th>title</th>
			<th>gameplugin</th>
			<th>actions</th>
		</tr></tfoot>';
	$content = $content.'</table>';
	
	$content = $content.'
	<h3>add a new game</h3>
		<form action="?page='.$_GET['page'].'&mod=Admin_Options&action=addGame" method="post">
		title<input type="text" name="sr_game_title">
		short name<input type="text" name="sr_game_internalname">
		<input type="submit" value="add game">
		</form>
	';
	return $content;
}
?>
