<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/forms/admin/general.php');
include_once (SR_PLUGIN_PATH.'/models/model_raidplan.php');
include_once (SR_PLUGIN_PATH.'/models/model_raidplan_team.php');
include_once (SR_PLUGIN_PATH.'/models/model_raid_template.php');
include_once (SR_PLUGIN_PATH.'/models/model_item.php');
include_once (SR_PLUGIN_PATH.'/models/model_character.php');
include_once (SR_PLUGIN_PATH.'/models/model_character_types.php');
include_once (SR_PLUGIN_PATH.'/models/model_dkp.php');
include_once (SR_PLUGIN_PATH.'/models/model_games.php');
include_once (SR_PLUGIN_PATH.'/forms/admin/dkp.php');

include_once (SR_PLUGIN_PATH.'/game_plugins/wow_buffed.php');
include_once (SR_PLUGIN_PATH.'/libs/rtimport.php');

class ModAdmin_DKP extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_admin_navigation_panel($this->system->db,$this->system->VARS);
	}
	
	function index(){
		$vars['games'] = $games;
		return sr_forms_admin_dkp_index($this->system->db,$vars);
	}
	
	function manageRaidDKP(){
		
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$dkp_entries = ModelDKP::getAllOfRaid($this->system->db,$raid->id);
		
		// getting the sumary of dkp from each character
		$raid_characters = ModelRaidplanTeam::getCharacters($this->system->db,$raid->id);
		$dkp_character_summary = array();
		for($i = 0; $i < count($raid_characters); $i++){
			$tChar = $raid_characters[$i];
			$sum = ModelDKP::getDKPSummaryOfCharacter($this->system->db,$tChar->id);
			$entry = new StdClass();
			$entry->char = $tChar;
			$entry->sum = $sum;
			array_push($dkp_character_summary,$entry);
		}
		
		$vars['raid'] = $raid;
		$vars['dkp_entries'] = $dkp_entries;
		$vars['dkp_character_summary'] = $dkp_character_summary;
		
		return sr_forms_admin_dkp_raid_dkp_list($this->system->db,$vars);
	}
	
	function giveCharactersDKP(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		// getting all raid characters and giving them the dkp
		$raid_chars = ModelRaidplanTeam::getCharactersByStatus($this->system->db,$raid->id,$this->system->VARS['raid_character_status']);
		for($i = 0; $i < count($raid_chars); $i++){
			$tChar = $raid_chars[$i];
			
			$tDKP = new ModelDKP();
			$tDKP->updated_at = date( 'Y-m-d H:i:s');
			$tDKP->character_fk = $tChar->id;
			$tDKP->game_fk = $raid->game_fk;
			$tDKP->dkp = $this->system->VARS['dkp_points'];
			$tDKP->notice = $this->system->VARS['dpk_notice'];
			$tDKP->raid_fk = $raid->id;
			$tDKP->insert($this->system->db);
		}
		
		return $this->manageRaidDKP();
	}
	
	function deleteDKPEntry(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$dkp_entry = new ModelDKP();
		if($dkp_entry->select($this->system->db,$this->system->VARS['did'])){
			if($dkp_entry->raid_fk == $raid->id){
				$dkp_entry->delete($this->system->db);
			}
		}
		
		return $this->manageRaidDKP();
	}
	
	function editDKPEntry(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$dkp_entry = new ModelDKP();
		if($dkp_entry->select($this->system->db,$this->system->VARS['did'])){
			if($dkp_entry->raid_fk != $raid->id){
				return $this->manageRaidDKP();
			}
		}
		
		$dkp_entry->fetchData($this->system->db);
		
		$vars['raid'] = $raid;
		$vars['dkp_entry'] = $dkp_entry;

		
		return sr_forms_admin_dkp_edit_dkp_entry($this->system->db,$vars);
	}
	
	function updateDKPEntry(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$dkp_entry = new ModelDKP();
		if($dkp_entry->select($this->system->db,$this->system->VARS['did'])){
			if($dkp_entry->raid_fk != $raid->id){
				return $this->manageRaidDKP();
			}
		}
		
		$dkp_entry->updated_at = date( 'Y-m-d H:i:s');
		$dkp_entry->dkp = $this->system->VARS['dkp'];
		$dkp_entry->notice = $this->system->VARS['notice'];
		$dkp_entry->update($this->system->db);
		
		return $this->manageRaidDKP();
	}
	
	function giveDKPToCharacter(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		// getting all raid characters and giving them the dkp
		$char = new ModelCharacter();
		if($char->select($this->system->db,$this->system->VARS['raid_character_id']) == false){
			return $this->manageRaidDKP();
		}
		
		
		$tDKP = new ModelDKP();
		$tDKP->updated_at = date( 'Y-m-d H:i:s');
		$tDKP->character_fk = $char->id;
		$tDKP->game_fk = $raid->game_fk;
		$tDKP->dkp = $this->system->VARS['dkp_points'];
		$tDKP->notice = $this->system->VARS['dpk_notice'];
		$tDKP->raid_fk = $raid->id;
		$tDKP->insert($this->system->db);
		
		return $this->manageRaidDKP();
	}
	
	function giveItemToCharacter(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		// getting all raid characters and giving them the dkp
		$char = new ModelCharacter();
		if($char->select($this->system->db,$this->system->VARS['raid_character_id']) == false){
			return $this->manageRaidDKP();
		}
		
		$game_item = new ModelItem();
		if($game_item->select($this->system->db,$this->system->VARS['raid_item_id'])){
			$this->system->VARS['dpk_notice'] = 'retrieved item: '.$game_item->title;
		}
		else{
			$this->system->VARS['dpk_notice'] = 'minus dkp for a retrieved game item, item not stored in the database';
		}
		$tDKP = new ModelDKP();
		$tDKP->updated_at = date( 'Y-m-d H:i:s');
		$tDKP->character_fk = $char->id;
		$tDKP->game_fk = $raid->game_fk;
		$tDKP->dkp = -1* intval($this->system->VARS['dkp_points']);
		$tDKP->notice = $this->system->VARS['dpk_notice'];
		$tDKP->raid_fk = $raid->id;
		$tDKP->insert($this->system->db);
		
		return $this->manageRaidDKP();
	}
	
	function resetDKP(){
		$game = new ModelGames();
		if($game->select($this->system->db,$this->system->VARS['game_id']) == false){
			return $this->index();
		}
		
		ModelDKP::resetDKP($this->system->db,$game->id,$this->system->VARS['dkp_points'],$this->system->VARS['dkp_notice']);
		return $this->index();
	}
	
	function previewRaidTrackerImport(){
		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$rtImporter = new RaidTrackerImporter();
		$rtImporter->load($this->system->VARS['dkp_string']);
		
		$vars['raid'] = $raid;
		$vars['raid_rt_import'] = $rtImporter;
		$vars['raid_dkp_string'] = $this->system->VARS['dkp_string'];
		
		return sr_forms_admin_dkp_raid_rtpreview($this->system->db,$vars);
	}
	
	function doRaidTrackerImport(){

		$raid = new ModelRaidplan();
		if($raid->select($this->system->db,$this->system->VARS['id']) == false){
			return $this->index();
		}
		
		$rtImporter = new RaidTrackerImporter();
		$rtImporter->load($this->system->VARS['dkp_string']);
		
		
		$content = $content.'<a href="?page='.$_GET['page'].'&mod=Admin_DKP&action=manageRaidDKP&id='.$raid->id.'">backto raid dkp overview</a>';
		$content = $content.'<pre>';
		// dkp für bosskills
		for($i = 0; $i < count($rtImporter->bosskills); $i++){
			$bosskillName = $rtImporter->bosskills[$i]->name;
			$bosskillDKP = $this->system->VARS['bosskill_pos_'.$i];
			
			$content = $content.'processing bosskill: '. $bosskillName.' ['.$bosskillDKP.' DKP], '.count($rtImporter->bosskills[$i]->players).' players'."\n";
			for($p = 0; $p < count($rtImporter->bosskills[$i]->players); $p++){
				$tPlayerCharname = $rtImporter->bosskills[$i]->players[$p];
				$character = ModelCharacter::getByName($this->system->db,$tPlayerCharname,$raid->game_fk);
				if($character != false){
					$content = $content.'player &lt;'.$tPlayerCharname.'&gt; found, giving DKP'."\n";
					
					$tDKP = new ModelDKP();
					$tDKP->updated_at = date( 'Y-m-d H:i:s');
					$tDKP->character_fk = $character->id;
					$tDKP->game_fk = $raid->game_fk;
					$tDKP->dkp = $bosskillDKP;
					$tDKP->notice = 'bosskill: '.$bosskillName;
					$tDKP->raid_fk = $raid->id;
					$tDKP->insert($this->system->db);
					
				}else{
					$content = $content.'player &lt;'.$tPlayerCharname.'&gt; could not be found, ignoring'."\n";
				}
			}
		}
		
		// dkp für loot
		for($i = 0; $i < count($rtImporter->loot); $i++){
			$content = $content.'proccessing loot &lt;'.$rtImporter->loot[$i]->itemName.'&gt;: player &lt;'.$rtImporter->loot[$i]->player.'&gt; boss &lt;'.$rtImporter->loot[$i]->bossKill.'&gt; '.(intval($this->system->VARS['loot_dkp_pos_'.$i]) *-1).'dkp'."\n";
			
			$tPlayerCharname = $rtImporter->loot[$i]->player;
			$character = ModelCharacter::getByName($this->system->db,$tPlayerCharname,$raid->game_fk);
			if($character != false){
				$content = $content.'result: player &lt;'.$tPlayerCharname.'&gt; found, setting loot DKP'."\n";
				
				$tDKP = new ModelDKP();
				$tDKP->updated_at = date( 'Y-m-d H:i:s');
				$tDKP->character_fk = $character->id;
				$tDKP->game_fk = $raid->game_fk;
				$tDKP->dkp = intval($this->system->VARS['loot_dkp_pos_'.$i]) *-1;
				$tDKP->notice = 'item: '.$rtImporter->loot[$i]->itemName.' &lt;'.$rtImporter->loot[$i]->bossKill.'&gt;';
				$tDKP->raid_fk = $raid->id;
				$tDKP->insert($this->system->db);
				
			}else{
				$content = $content.'result: player &lt;'.$tPlayerCharname.'&gt; could not be found, ignoring'."\n";
			}
		}
		$content = $content.'</pre>';
		
		$content = $content.'<a href="?page='.$_GET['page'].'&mod=Admin_DKP&action=manageRaidDKP&id='.$raid->id.'">backto raid dkp overview</a>';
		return $content;
	}
}
?>