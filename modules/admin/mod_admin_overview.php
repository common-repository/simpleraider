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
include_once (SR_PLUGIN_PATH.'/models/model_raid_template.php');
include_once (SR_PLUGIN_PATH.'/models/model_item.php');
include_once (SR_PLUGIN_PATH.'/models/model_character.php');
include_once (SR_PLUGIN_PATH.'/models/model_character_types.php');
include_once (SR_PLUGIN_PATH.'/forms/admin/raidtemplate.php');

include_once (SR_PLUGIN_PATH.'/game_plugins/wow_buffed.php');

class ModAdmin_Overview extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_admin_navigation_panel($this->system->db,$this->system->VARS);
	}
	
	public function index()
	{
		print sr_forms_admin_overview($this->system->db,$vars);
	}
}
?>
