<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/forms/admin/general.php');
include_once (SR_PLUGIN_PATH.'/forms/admin/user.php');
include_once (SR_PLUGIN_PATH.'/models/model_user.php');

class ModAdmin_User extends Module
{
	function __construct(&$system)
	{
		$this->system = $system;
		print sr_forms_admin_navigation_panel($this->system->db,$this->system->VARS);
	}
	
	function index(){
		return sr_forms_admin_user_index($this->system->db,$vars);
	}
	
	function addUser(){
		$user = new ModelUser();
		$user->user_id = intval($this->system->VARS['access_user_id']);
		$user->access = 1;

		if($user->user_id > 0){
			$user->insert($this->system->db);
		}
		
		return sr_forms_admin_user_index($this->system->db,$vars);
	}
	
	function removeUser(){
		$user = new ModelUser();
		if($user->select($this->system->db,$this->system->VARS['id'])){
			$user->delete($this->system->db);
		}
		return sr_forms_admin_user_index($this->system->db,$vars);
	}
	
	function toggleModerator(){
		$user = new ModelUser();
		if($user->select($this->system->db,$this->system->VARS['id'])){
			if(intval($user->moderator) == 0){
				$user->moderator = 1;
			}else{
				$user->moderator = 0;
			}
			
			$user->update($this->system->db);
		}
		return sr_forms_admin_user_index($this->system->db,$vars);
	}
}

?>