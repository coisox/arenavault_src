<?php
require_once('connection.php');

if($_POST['USER_IMAGE']) {
	include 'sub_setProfilepic.php';
	echo json_encode([
		'status' => 'ok',
		'USER_IMAGE' => $filename
	]);
}
else if($_POST['STAT_TARGET_PLAYER_ID']) {
	include('sub_setUserStat.php');
	echo json_encode([
		'status' => 'ok',
	]);
}