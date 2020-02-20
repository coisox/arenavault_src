<?php
require_once('connection.php');

$sql = "
	SELECT
		USER_PROFILE
	FROM
		USER
	WHERE
		USER_ID = ?
	ORDER BY USER_NAME
";
$rs = executeQuery($db, $sql, [$_POST['USER_ID']]);

echo json_encode([
	'status' => 'ok',
	'standings' => $standings,
	'players' => $rs,
	'completed' => $completed,
	'incomplete' => $totalToPlay - $completed
]);