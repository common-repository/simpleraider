<?php
/******************************
 * SimpleRaider
 * Copyright 2009 by Alexander Bierbrauer, polyvision.org
 * Web: http://simpleraider.polyvision.org
 *
 * Licensed under the GNU GPL v3.  See license.txt for full terms.
 ******************************/
include_once (SR_PLUGIN_PATH.'/models/model_user.php');

function sr_forms_admin_user_index(&$adodb,&$vars){
	$content = $content.'<h2>user management</h2>';
	
	// adding user
	$content = $content.'
	<form action="?page='.$_GET['page'].'&mod=Admin_User&action=addUser" method="post">
	<h3>adding access to user</h3>
	<p><label>user with no access:</label><span> <select name="access_user_id">'.ModelUser::getUserDeniedOptions($adodb).'</select></span></p>
	<input type="submit" value="give access">
	</form>
	';
	
	// listing the users
	
	$access_users = ModelUser::getUserAccessList($adodb);
	
	$content = $content.'<h2>users with access</h2><table>';
	$content = $content.'<thead>';
	$content = $content.'<tr>';
	$content = $content.'<th>username</th>';
	$content = $content.'<th>moderator</th>';
	$content = $content.'<th>actions</th>';
	$content = $content.'</tr>';
	$content = $content.'</thead>';
	for($i = 0; $i < count($access_users); $i++){
		$tUser = $access_users[$i];
		
		$content = $content.'<tr><td>';
		$content = $content.$tUser->user_login;
		$content = $content.'</td><td>';
		$content = $content.'<td>';
		if(intval($tUser->moderator) == 0){
			$content = $content.'<a href="?page='.$_GET['page'].'&mod=Admin_User&action=toggleModerator&id='.$tUser->id.'">NO</a>';
		}else
		{
			$content = $content.'<a href="?page='.$_GET['page'].'&mod=Admin_User&action=toggleModerator&id='.$tUser->id.'">YES</a>';
		}

		$content = $content.'</td><td>';
		
		$content = $content.'<a href="?page='.$_GET['page'].'&mod=Admin_User&action=removeUser&id='.$tUser->id.'">remove access</a>';
		$content = $content.'</td></tr>';
	}
		
	$content = $content.'</table>';
	return $content;
}

?>