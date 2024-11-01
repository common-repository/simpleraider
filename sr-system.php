<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
require_once('libs/adodb5/adodb.inc.php');
require_once ('module.php');

/*
 * this is the heart of SimpleRaider
 */
class SimpleRaiderSystem 
{
	public $VARS;
	public $db;
		
	function __construct()
	{
		$this->VARS = array_merge($_GET,$_POST); // wir holen uns alle Variablen, die gepostet wurden
		
		$this->db = NewADOConnection('mysql');
		if(!$this->db->Connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME))
		{
			print 'FATAL: Eine Verbindung zur Datenbank konnte nicht hergestellt werden!';
			die();
		}
		$this->db->Execute("SET NAMES 'utf8';");
	}
	
	
	
	public function run()
	{
		if(!isset($this->VARS['mod']))
		{ 
			// es wurde kein modul angegeben, also setzen wir als Modul das Standardmodul welches in der 
			// config.inc.php angegeben wurde
			$this->VARS['mod'] = DEFAULT_MOD;
		}
		
		if(!isset($this->VARS['action']))
		{ 
			// es wurde kein modul angegeben, also setzen wir als Aktion 'index'
			$this->VARS['action'] = 'index';
		}
		
		// wir laden das Modul
		$mod = $this->loadModule($this->VARS['mod']);
		if($mod == NULL)
		{
			print 'FATAL: Das Standardmodul konnte nicht geladen werden!';
			die();
		}
		
		$content = $this->executeAction($mod,$this->VARS['action']);
		
		print '<div id="SR_CONTENT">'.$content.'</div>';
	}
	
	private function loadModule($mod_name)
	{
		$moduleName = 'Mod'.$mod_name;
		
		$basePath = ABSPATH.'/wp-content/plugins/simpleraider/';
		if(stristr($moduleName, '_') === FALSE)
		{
			$modPath = $basePath.'modules/'.strtolower($mod_name).'/mod_'.strtolower($mod_name).'.php';
		}
		else
		{ // Module wie z.b. Admin_User... ist eine Modul im Admin-Verzeichnis
			$modPath = $basePath.'modules/'.strtolower(strtok($mod_name, "_")).'/mod_'.strtolower($mod_name).'.php';
		}

		
		// überprüfen ob es das Modul als Datei überhaupt gibt
		if(!file_exists($modPath))
		{
			print 'FATAL: Das Modul existiert nicht in: '.$modPath;
			return NULL;
		}
		
		// laden des Moduls
		include_once ($modPath);
		$module = new $moduleName($this);
		return $module;
	}
	
	/*
	 * führt eine Aktion auf ein Modul aus
	 * @return STRING full of content
	 */
	private function executeAction(&$module,$action_name)
	{
		return $module->$action_name();
	}
}
?>