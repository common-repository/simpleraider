<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_games.php');

function sr_forms_admin_raidtemplate_list(&$adodb,&$vars){
	
	$content = '<a href="?page='.$_GET['page'].'&mod=Admin_RaidTemplate&action=newRaidTemplate">create new raidtemplate</a>';

	$raid_templates = $vars['raid_templates'];
	$content = $content.'
		<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
			<th>icon</th>
			<th>game</th>
			<th>title</th>
			<th>players</th>
			<th>items</th>
			<th>actions</th>
		</thead>
		</tr>';
		
	for($i = 0; $i < count($raid_templates); $i++){
		$tRaid = $raid_templates[$i];
		$content = $content.'<tr>';
		$content = $content.'<td>'.$tRaid->getIconImageUrl().'</td>';
		$content = $content.'<td>'.$tRaid->getGameTitle($adodb).'</td>';
		$content = $content.'<td>'.$tRaid->title.'</td>';
		$content = $content.'<td>'.$tRaid->numplayers.'</td>';
		$content = $content.'<td>'.$tRaid->numItems($adodb).'</td>';
		$content = $content.'<td><a href="?page='.$_GET['page'].'&mod=Admin_RaidTemplate&action=editRaidTemplate&id='.$tRaid->id.'">[edit]</a></td>';
		$content = $content.'</tr>';

	}
	$content = $content.'<tfoot>
		<tr>
			<th>icon</th>
			<th>game</th>
			<th>title</th>
			<th>players</th>
			<th>items</th>
			<th>items</th>
			
		</tfoot>
		</tr>';
	$content = $content.'</table>';
	return $content;
}

function sr_forms_admin_raidtemplate_new(&$adodb,&$vars){
		$content = $content.'
		<h2>create a new raid template</h2>
			<form action="?page='.$_GET['page'].'&mod=Admin_RaidTemplate&action=createRaidTemplate" method="post">
				<p><label>game:</label> <span><select name="raidtemplate_game_fk" size="1">'.ModelGames::getOptions($adodb).'</select></span></p>
				<p><label>title:</label><span> <input type="text" name="raidtemplate_title"></span></p>
				<p><label>num players:</label><span> <input type="text" name="raidtemplate_numplayers"></span></p>
				<p><label>icon:</label><span><select name="raidtemplate_icon" size="1">'.ModelRaidTemplate::getRaidIconOptions('-').'</select></span></p>
				<input type="submit" value="create template">
				</form>
				';
			return $content;
}

function sr_forms_admin_raidtemplate_edit(&$adodb,&$vars){
	$tRaid = $vars['raid_template'];
	
	$content = $content.'
	<h2>edit raid template</h2>
	<form action="?page='.$_GET['page'].'&mod=Admin_RaidTemplate&action=updateRaidTemplate&id='.$tRaid->id.'" method="post">
		<p><label>game:</label><span> <select name="raidtemplate_game_fk" size="1">'.ModelGames::getOptionsSelected($adodb,$tRaid).'</select></span></p>
		<p><label>title:</label><span> <input type="text" name="raidtemplate_title" value="'.$tRaid->title.'"></span></p>
		<p><label>num players:</label><span> <input type="text" name="raidtemplate_numplayers" value="'.$tRaid->numplayers.'"></span></p>
		<p><label>icon:</label><span><select name="raidtemplate_icon" size="1">'.ModelRaidTemplate::getRaidIconOptions($tRaid->icon).'</select></span></p>
		<input type="submit" value="update template">
		</form>
		';
	
		$content = $content.sr_forms_admin_raidtemplate_listitems($adodb,$vars);
	return $content;
}

function sr_forms_admin_raidtemplate_listitems(&$adodb,&$vars){
	$tRaid = $vars['raid_template'];
	$items = $vars['raid_items'];
	$game_plugin = $vars['game_plugin'];
	
	$content = $content.'<h3>items dropping in raid</h3>';

	$content = $content.'<a href="?page='.$_GET['page'].'&mod=Admin_RaidTemplate&action=newRaidItem&id='.$tRaid->id.'">add new item</a>';
	$content = $content.'
		<table class="widefat " cellspacing="0">
		<thead><tr>
			<th></th>
			<th>#game item id</th>
			<th>title</th>
			<th>actions</th>
		</tr></thead>';
		
	for($i = 0; $i < count($items); $i++){
		$tItem = $items[$i];
		$content = $content.'<tr>';
		$content = $content.'<td>'.$game_plugin->getImageURLWithLink($tItem,'item_css_'.$tItem->id).'</td>';
		$content = $content.'<td>'.$tItem->internal_game_item_id.'</td>';
		$content = $content.'<td>'.$tItem->title.'</td>';
		$content = $content.'<td><a href="?page='.$_GET['page'].'&mod=Admin_RaidTemplate&action=deleteRaidItem&id='.$tRaid->id.'&itemId='.$tItem->id.'">[delete]</a></td>';
		$content = $content.'</tr>';

	}
	$content = $content.'
	<tfoot><tr>
			<th></th>
			<th>#game item id</th>
			<th>title</th>
			<th>actions</th>
		</tr></tfoot>
	</table>';
	return $content;
}

function sr_forms_admin_raidtemplate_newitem(&$adodb,&$vars){
	$tRaid = $vars['raid_template'];
	
	$content = $content.'
	<h2>add a new item</h2>
			<form action="?page='.$_GET['page'].'&mod=Admin_RaidTemplate&action=createRaidItem" method="post">
			<input type="hidden" name="id" value="'.$tRaid->id.'">
				title: <input type="text" name="raiditem_title"> and additionally game item id for more informations:	 
				<input type="text" name="raiditem_internal_game_id"><br>
				<input type="submit" value="create item">
				</form>
				';
				
	$content = $content.'
	<h2>mass items import</h2>
	simply add game item id with a ; as delimiter to import them with the game plugin. Example: 4578;456789;2365<br>
			<form action="?page='.$_GET['page'].'&mod=Admin_RaidTemplate&action=importRaidItems" method="post">
			<input type="hidden" name="id" value="'.$tRaid->id.'">
				<textarea name="csv_game_items"></textarea>
				<input type="submit" value="import items">
				</form>
				';
			return $content;
}
?>
