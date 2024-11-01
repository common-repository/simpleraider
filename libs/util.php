<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
 
class SRUtil{
	
	/**
	* getting the icons files as an html options array
	**/
	function getCharTypesIconOptions($icon){
		$options = '';
		if($icon == '-' || strlen($icon) == 0){
			$options = $options.'<option value="-" selected>-</option>';
		}
		else{
			$options = $options.'<option value="-">-</option>';
		}
		
		$icon_path = SR_PLUGIN_PATH.'/images/chartypes/';
		if ($handle = opendir($icon_path))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file != "." && $file != ".." && $file != ".svn")
				{
					if($icon == $file)
						$options = $options.'<option value="'.$file.'" selected>'.$file.'</option>';
					else
						$options = $options.'<option value="'.$file.'" >'.$file.'</option>';
				}
			}
			closedir($handle);
		}
		
		return $options;
	}
	
	function formatDate($input){
		$t = strtotime($input);
		return strftime("%a %d %b %H:%M",$t);
	}
}
?>
