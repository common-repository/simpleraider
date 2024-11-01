<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/

// require_once ('libs/util.php');

/**
 * database object which represents a raid
 */
class ModelUser extends DBObject
{
	var $user_login;
		
	function __construct()
	{
		parent::__construct(SR_TABLE_PREFIX."sr_access",
			 				"id", array("access","user_id","moderator")
							);
	}
	
	
	function getUserDeniedOptions(&$adodb){
		
			$options = '';
			$rs = $adodb->GetAll('SELECT DISTINCT ID,user_login FROM '.SR_TABLE_PREFIX.'users WHERE ID NOT IN (SELECT user_id FROM '.SR_TABLE_PREFIX.'sr_access);');
			for($i = 0; $i < count($rs); $i++){
				 $options = $options.'<option value="'.$rs[$i]['ID'].'">'.$rs[$i]['user_login'].'</option>';
			}

			return $options;
	}
	
	function getUserAccessList(&$adodb){
		$users = array();
		
		
		$rs = $adodb->GetAll('SELECT id FROM '.SR_TABLE_PREFIX.'sr_access;');
		for($i = 0; $i < count($rs); $i++){
			$t = new ModelUser();
			if($t->select($adodb,$rs[$i]['id'])){
				$t->fetchData($adodb);
				array_push($users,$t);
			}
		}
		
		return $users;
	}
	
	function fetchData(&$adodb){
		$this->user_login = $adodb->GetOne('SELECT user_login FROM '.SR_TABLE_PREFIX.'users WHERE ID='.$this->user_id);
	}
	
	function hasUserAccess(&$adodb,$user_id){
		$rs = $adodb->GetAll('SELECT ID  FROM '.SR_TABLE_PREFIX.'sr_access WHERE user_id='.$user_id);
		if(count($rs) > 1){
			return true;
		}
		
		return false;
	}
	
	function isUserModerator(&$adodb,$user_id){

		$rs = $adodb->GetRow('SELECT *  FROM '.SR_TABLE_PREFIX.'sr_access WHERE user_id='.$user_id);
		if($rs){
			if(intval($rs['moderator']) == 1){
				return true;
			}
		}
		
		return false;
	}
}

?>