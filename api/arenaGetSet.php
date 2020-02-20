<?php
require_once('connection.php');

if($_POST['type']=='edit') {
	$sql = "UPDATE ARENA SET ARENA_NAME = ? WHERE ARENA_ID = ?";
	executeQuery($db, $sql, [
		$_POST['ARENA_NAME'],
		$_POST['ARENA_ID'],
	]);
}
else if($_POST['type']=='new') {
	
	//====================================================== generate unique 6 character
	$duplicate = true;
	while($duplicate) {
		$_POST['ARENA_ID'] = substr(strtoupper(uniqid()), -6);
		$sql = "SELECT COUNT(*) C FROM ARENA WHERE ARENA_ID = ?";
		$rs = executeQuery($db, $sql, [$_POST['ARENA_ID']]);
		if($rs[0]['C']==0) $duplicate = false;
	}
	
	$sql = "
		INSERT INTO ARENA (
			ARENA_ID,
			ARENA_NAME,
			ARENA_OWNER_USER_ID
		) VALUES (
			?, ?, ?
		)
	";
	executeQuery($db, $sql, [
		$_POST['ARENA_ID'],
		$_POST['ARENA_NAME'],
		$_POST['USER_ID']
	]);
}
else if($_POST['type']=='code') {
	$sql = "INSERT INTO PLAYER (PLAYER_ARENA_ID, PLAYER_USER_ID) VALUES (?, ?) ON DUPLICATE KEY UPDATE PLAYER_USER_ID = PLAYER_USER_ID";
	executeQuery($db, $sql, [$_POST['ARENA_ID'], $_POST['USER_ID']]);
}
else if($_POST['type']=='delete') {
	$sql = "DELETE FROM ARENA WHERE ARENA_ID = ?";
	executeQuery($db, $sql, [
		$_POST['ARENA_ID'],
	]);
}

$sql = "
	SELECT ARENA_ID, ARENA_NAME, 1 IS_OWNER, ARENA_CREATEDDATE
	FROM ARENA
	WHERE
		ARENA_OWNER_USER_ID = ?

	UNION
	
	SELECT ARENA_ID, ARENA_NAME, 0 IS_OWNER, ARENA_CREATEDDATE
	FROM ARENA, PLAYER
	WHERE
		PLAYER_ARENA_ID = ARENA_ID
		AND ARENA_OWNER_USER_ID != ?
		AND PLAYER_USER_ID = ?
		
	ORDER BY ARENA_CREATEDDATE DESC
";
$arenas = executeQuery($db, $sql, [$_POST['USER_ID'], $_POST['USER_ID'], $_POST['USER_ID']]);

echo json_encode([
	'status' => 'ok',
	'arenas' => $arenas
]);