<?php

/**
 * Importer for WOW-Raidtracker-Data
 * Written by Alexander B. Bierbrauer 2.10.09
 * 
**/
class RaidTrackerImporter{

	public $players;
	public $bosskills;
	public $loot;
	
	public function load($xml_data){
		$data = simplexml_load_string($xml_data);
		$this->extractPlayers($data);
		$this->extractBossKills($data);
		$this->extractLoot($data);
	}
	
	/**
	 * extract player info from the raid tracker
	**/
	private function extractPlayers(&$data){
		$this->players = array();

		$children = $data->PlayerInfos->children();
		foreach ($children as $node)		{
			//echo "player: ".$node->name."\n";
			array_push($this->players,$node->name);
		}
	}
	
	private function extractBossKills(&$data){
		$this->bosskills = array();

		$children = $data->BossKills->children();
		foreach ($children as $node)		{
			
			$tBossKill = new StdClass();
			
			//echo "bosskill: ".$node->name."\n";
			$tBossKill->name = $node->name;
			
			$kill_time = $node->time;
			$kill_timestamp = strtotime($kill_time);
			$kill_convtime = strftime("%a %d %b %H:%M:%S %Y",$kill_timestamp);
			//printf("xml date: %s\n",$kill_time);
			//printf("converted timestamp: %s\n",$kill_timestamp);
			//printf("converted date: %s\n",$kill_convtime);
			
			$tBossKill->kill_time = $kill_time;
			$tBossKill->kill_timestamp = $kill_timestamp;
			$tBossKill->kill_convtime = $kill_convtime;
			
			$tBossKill->players = array();
			$attendees = $node->attendees->children();
			foreach($attendees as $player){
				$t = (string)$player->name;
				array_push($tBossKill->players,$t);
			}
			
			array_push($this->bosskills,$tBossKill);
		}
	}
	
	private function extractLoot($data){
		$this->loot = array();
		
		$children = $data->Loot->children();
		foreach($children as $node){
			$tLoot = new StdClass();
			$tLoot->itemName = $node->ItemName;
			$tLoot->player = $node->Player;
			$tLoot->dkp = $node->Costs;
			$tLoot->bossKill = $node->Boss;
			$tLoot->notice = $node->Note;
			
			array_push($this->loot,$tLoot);
			
			//printf("loot: <%s> %s <%s dkp> > %s\n",$tLoot->itemName,$tLoot->player,$tLoot->dkp,$tLoot->notice);
		}
	}
}

/*
$data = file_get_contents("1.xml");

$rt_import = new RaidTrackerImporter();
$rt_import->load($data);*/

?>