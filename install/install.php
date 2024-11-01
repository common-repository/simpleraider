<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
 
// installation of SimpleRaider
function sr_install()
{ 
	
    sr_install_tables();
}

function sr_install_tables(){
	global $wpdb;
	
	$table = $wpdb->prefix."sr_characters"; 
	$structure = "CREATE TABLE IF NOT EXISTS `$table` (
		`id` int(11) NOT NULL AUTO_INCREMENT,	
		`game_fk` int(11) NOT NULL,
		`user_fk` int(11) NOT NULL,
		`charname` varchar(255) NOT NULL,
		`stats_xml` text NOT NULL,
		`type_fk` int(11) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;";
	$wpdb->query($structure);
		
	$table = $wpdb->prefix."sr_character_types";
	$structure = "CREATE TABLE IF NOT EXISTS `$table` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`game_fk` int(11) NOT NULL,
		`title` varchar(255) NOT NULL,
		`icon` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;";
	$wpdb->query($structure);

	$table = $wpdb->prefix."sr_dkp";
	$structure ="CREATE TABLE IF NOT EXISTS `$table` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`updated_at` datetime NOT NULL,
		`character_fk` int(11) NOT NULL,
		`game_fk` int(11) NOT NULL,
		`dkp` int(11) NOT NULL,
		`notice` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		`raid_fk` int(11) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;";
	$wpdb->query($structure);
	
	$table = $wpdb->prefix."sr_games";
	$structure = "CREATE TABLE IF NOT EXISTS `$table` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` varchar(255) NOT NULL,
		`internalname` varchar(255) NOT NULL,
		`gameplugin` varchar(255) NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE KEY `internalname` (`internalname`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;";
	$wpdb->query($structure);
		
	$table = $wpdb->prefix."sr_items";
	$structure = "CREATE TABLE IF NOT EXISTS `$table` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`internal_game_item_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		`title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		`game_fk` int(11) NOT NULL,
		`raidtemplate_fk` int(11) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;";
	$wpdb->query($structure);
	
	$table = $wpdb->prefix."sr_raidplan";
	$structure = "CREATE TABLE IF NOT EXISTS `$table` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`game_fk` int(11) NOT NULL,
		`raidstart` datetime NOT NULL,
		`numplayers` int(11) NOT NULL,
		`title` varchar(255) NOT NULL,
		`raidfinish` datetime NOT NULL,
		`raidinvitation` datetime NOT NULL,
		`raidsignonend` datetime NOT NULL,
		`template_fk` int(11) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;";
	$wpdb->query($structure);
		
	$table = $wpdb->prefix."sr_raidplan_team";
	$structure = "CREATE TABLE IF NOT EXISTS `$table` (
	  `id` int(11) NOT NULL auto_increment,
	  `raid_fk` int(11) NOT NULL,
	  `character_fk` int(11) NOT NULL,
	  `status` int(11) NOT NULL,
	  `notice` text NOT NULL,
	  `created_at` datetime NOT NULL,
	  `cstatus` tinyint(4) NOT NULL default '0',
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=558 ;";
	$wpdb->query($structure);

	$table = $wpdb->prefix."sr_raidtemplates";
	$structure = "CREATE TABLE IF NOT EXISTS `$table` (
		`id` int(11) NOT NULL auto_increment,
		  `game_fk` int(11) NOT NULL,
		  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
		  `numplayers` int(11) NOT NULL,
		  `icon` varchar(255) collate utf8_unicode_ci NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;";
	$wpdb->query($structure);
	
	$table = $wpdb->prefix."sr_access";
	$structure = "CREATE TABLE IF NOT EXISTS `$table` (
		`user_id` int(11) NOT NULL,
		`access` tinyint(1) NOT NULL,
		`id` int(11) NOT NULL auto_increment ,
		`moderator` tinyint(1) NOT NULL default '0',
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
		//print $structure;
	$wpdb->query($structure);
	

	
	// World Of Warcraft - Preconfigure
	$table = $wpdb->prefix."sr_games";
	
	$wpdb->query("INSERT INTO $table (`id`, `title`, `internalname`, `gameplugin`) VALUES(1, 'World Of Warcraft', 'wow', 'wow_buffed');");
	
	$table = $wpdb->prefix."sr_character_types";
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '1', 'Warrior','wow_warrior_small.gif');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '1', 'Mage','wow_mage_small.gif');");
	$wpdb->query("INSERT INTO $table (  `game_fk` , `title` ,icon)VALUES ( '1', 'Hunter','wow_hunter_small.gif');");
	$wpdb->query("INSERT INTO $table (  `game_fk` , `title` ,icon )VALUES ( '1', 'Rogue','wow_rogue_small.gif');");
	$wpdb->query("INSERT INTO $table (  `game_fk` , `title` ,icon)VALUES ( '1', 'Paladin','wow_paladin_small.gif');");
	$wpdb->query("INSERT INTO $table (  `game_fk` , `title` ,icon)VALUES ( '1', 'Priest','wow_priest_small.gif');");
	$wpdb->query("INSERT INTO $table (  `game_fk` , `title` ,icon)VALUES ( '1', 'Warlock','wow_warlock_small.gif');");
	$wpdb->query("INSERT INTO $table (  `game_fk` , `title` ,icon)VALUES ( '1', 'Deathknight','wow_deathknight_small.gif');");
	$wpdb->query("INSERT INTO $table (  `game_fk` , `title` ,icon)VALUES ( '1', 'Druid','wow_druid_small.gif');");
	$wpdb->query("INSERT INTO $table (  `game_fk` , `title` ,icon)VALUES ( '1', 'Shaman','wow_shaman_small.gif');");
	
	// Runes Of Magic - Preconfigure
	$table = $wpdb->prefix."sr_games";
	
	$wpdb->query("INSERT INTO $table (`id`, `title`, `internalname`, `gameplugin`) VALUES(2, 'Runes Of Magic', 'rom', 'rom_buffed');");
	
	$table = $wpdb->prefix."sr_character_types";
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '2', 'Warden','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '2', 'Druid','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '2', 'Warrior','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '2', 'Scout','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '2', 'Rogue','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '2', 'Mage','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '2', 'Priest','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '2', 'Knight','-');");
	
	/*
	// Age Of Conan - Preconfigure
	$table = $wpdb->prefix."sr_games";
	
	$wpdb->query("INSERT INTO $table (`id`, `title`, `internalname`, `gameplugin`) VALUES(3, 'Age Of Conan', 'aoc', '-');");
	
	$table = $wpdb->prefix."sr_character_types";
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Assasin','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Barbarian','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Bear Shaman','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Conquerer','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Dark Templar','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Demonologist','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Guardian','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Herald of Xolti','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Necromancer','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Priest of Mitra','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Ranger','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '3', 'Tempest Of Set','-');");
	
	// Warhammer Online - Preconfigure
	$table = $wpdb->prefix."sr_games";
	
	$wpdb->query("INSERT INTO $table (`id`, `title`, `internalname`, `gameplugin`) VALUES(4, 'Warhammer Online', 'war', '-');");
	
	$table = $wpdb->prefix."sr_character_types";
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Archmage','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Black Orc','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Blackguard','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Bright Wizard','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Choppa','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Chosen','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Disciple of Khaine','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Engineer','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Ironbreaker','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Knight of the blazing sun','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Magus','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Marauder','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Rune Priest','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Shadow Warrior','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Shaman','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Slayer','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Sorcerer','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Squieg Herder','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Sword Master','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Swordmaster','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Warrior Priest','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'White Lion','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Witch Elf','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Witch Hunter','-');");
	$wpdb->query("INSERT INTO $table ( `game_fk` , `title` ,icon)VALUES ( '4', 'Zealot','-');");	
	*/
}

?>
