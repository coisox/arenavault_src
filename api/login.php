<?php
require_once('connection.php');

$sql = "
	INSERT INTO USER (
		USER_GMAIL,
		USER_NAME,
		USER_IMAGE,
		USER_PROFILE
	) VALUES (
		?, ?, ?, ?
	) ON DUPLICATE KEY UPDATE USER_GMAIL = USER_GMAIL
";
executeQuery($db, $sql, [
	$_POST['USER_GMAIL'],
	$_POST['USER_NAME'],
	$_POST['USER_IMAGE'],
	'{"CHOP":0, "LOOP":0, "SERVE":0, "SMASH":0, "PLACING":0}'
]);

$sql = "SELECT USER_ID, USER_IMAGE FROM USER WHERE USER_GMAIL = ?";
$user = executeQuery($db, $sql, [$_POST['USER_GMAIL']])[0];

echo json_encode([
	'status' => 'ok',
	'user' => $user
]);