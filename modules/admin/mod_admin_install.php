<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/install/install.php');
include_once (SR_PLUGIN_PATH.'/forms/admin/general.php');

class ModAdmin_Install extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_admin_navigation_panel($this->system->db,$this->system->VARS);
	}
	
	public function index()
	{
	}
	
	/**
	 * static function
	 **/
	public function checkDatabase(&$adodb){
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_games');
		if($rs == false){
			return false;
		}
		return true;
	}
	
	public function installDatabase(){
		sr_install();
		print 'installed database tables, please create the following directory with write permissons: wp-content/uploads/sr_items/';
	}
}
?>
