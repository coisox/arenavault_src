<?php

//------------------------------------------------------ get user id
	$sql = "SELECT USER_ID FROM USER WHERE USER_GMAIL = ?";
	$user = executeQuery($db, $sql, [$_POST['USER_GMAIL']])[0];

	//------------------------------------------------------ save profilepic
	$filename = "profilepic/".($user['USER_ID']).'_'.uniqid().".jpg";
	$filecontent = file_get_contents($_POST['USER_IMAGE']);
	file_put_contents($filename, $filecontent);

	//------------------------------------------------------ get new profilepic location
	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$path = pathinfo($protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	$filename = $path['dirname'].'/'.$filename;
	
	//------------------------------------------------------ update profilepic location into DB
	$sql = "UPDATE USER SET USER_IMAGE = ? WHERE USER_GMAIL = ?";
	executeQuery($db, $sql, [$filename, $_POST['USER_GMAIL']]);