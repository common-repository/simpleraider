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

function sr_forms_public_statistic_overview(&$adodb,&$vars){
	$content = $content.'<h2>raid statistics overview</h2>';
	$content = $content.'<form action="index.php" method="get">

	<input type="hidden" name="page_id" value="'.$_GET['page_id'].'">
	<input type="hidden" name="mod" value="Public_Statistic">
	<input type="hidden" name="action" value="index">
	<p><label>starting date:</label> <input type="text" id="starting_date" name="starting_date" value="'.$vars['starting_date'].'">
	<a href="javascript:NewCssCal(\'starting_date\',\'yyyymmdd\',\'arrow\',false,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
	
	<p><label>raid finish:</label> <input type="text" id="ending_date" name="ending_date" value="'.$vars['ending_date'].'">
	<a href="javascript:NewCssCal(\'ending_date\',\'yyyymmdd\',\'arrow\',false,24)")"><img src="'.get_option('siteurl').'/wp-content/plugins/simpleraider/images/cal.gif" width="16" height="16" alt="Pick a date"></a></span></p>
	
	<input type="submit" value="update statistics">
	</form>
	</div>';
	
	$content = $content.'<table class="STYLED_TABLE"><thead><tr>
				<th>Character</th>
				<th>activity in '.$vars['stats_num_raids'].' raids</th>
				<th>num signed on raids</th>
				<th>num raids on waiting list</th>
				</tr></thead>
				<tbody>';
	
	$chars = $vars['stats_characters'];
	$css_class='STYLED_TABLE_TD_1';
	for($i = 0; $i < count($chars); $i++){
		$tChar = $chars[$i];
		$content= $content.'<tr>';
		$content= $content.'<td class="'.$css_class.'">'.$tChar->charname.'</td>';
		$content= $content.'<td class="'.$css_class.'">'.$tChar->stat_activity.' % </td>';
		$content= $content.'<td class="'.$css_class.'">'.$tChar->stat_num_signed_on_raids.'</td>';
		$content= $content.'<td class="'.$css_class.'">'.$tChar->stat_num_raids_on_waiting_list.'</td>';
		$content= $content.'</tr>';
		if(strcmp($css_class,'STYLED_TABLE_TD_1') == 0){
			$css_class='STYLED_TABLE_TD_2';
		}else{
			$css_class='STYLED_TABLE_TD_1';
		}
	}
	$content = $content.'</tbody></table>';
	return $content;
}