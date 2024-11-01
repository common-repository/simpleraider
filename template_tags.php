<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sr_recent_raids($limit=5,$title)
{
	global $wpdb, $current_user, $sfvars;
	$css_switch = 0;
	$query = 'SELECT * FROM '.SR_TABLE_PREFIX.'sr_raidplan WHERE DATE(raidstart) >= CURDATE() ORDER BY raidstart ASC LIMIT 0,'.$limit;
	
	$content = '<li id="pages-3" class="widget widget_pages"><h2 class="widgettitle">'.$title.'</h2>';
	
	$rs = $wpdb->get_results( $query);

	$content = $content.'<div id="SR_RECENT_RAIDS_CONTAINER"><ul>';
	for($i = 0; $i < count($rs); $i++){
		$row = $rs[$i];
		
		if($css_switch == 0){
			$content = $content."<li class=\"li_switch_1\">";
			$css_switch = 1;
		}
		else{
			$content = $content."<li class=\"li_switch_2\">";
			$css_switch = 0;
		}
		
		$link = '?page_id='.get_option('sr_option_display_page_id','-1').'&mod=Public_Raidplaner&action=showRaidDetailView&id='.$row->id;
		
		$num_chars = $wpdb->get_var('SELECT count(*) AS num FROM '.SR_TABLE_PREFIX.'sr_raidplan_team WHERE raid_fk='.$row->id.' AND STATUS=1 AND character_fk > 0');
		$content = $content.'<a href="'.$link.'">'.$row->title.' ('.$num_chars.' / '.$row->numplayers.') </a>';
		$content = $content.mysql2date('l, d.m H:i',$row->raidstart);
		$content = $content.'</li>';
	}
	$content = $content.'</ul></div></li>';
	
	print $content;
}

?>