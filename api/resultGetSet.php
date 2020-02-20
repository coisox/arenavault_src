<?php
require_once('connection.php');

//================================================================== update
if($_POST['type']=='edit') {
	$sql = "
		UPDATE RESULT SET
			RESULT_P1_PLAYER_ID = ?,
			RESULT_P2_PLAYER_ID = ?,
			RESULT_P1_SET1 = ?,
			RESULT_P1_SET2 = ?,
			RESULT_P1_SET3 = ?,
			RESULT_P2_SET1 = ?,
			RESULT_P2_SET2 = ?,
			RESULT_P2_SET3 = ?
		WHERE
			RESULT_ID = ?
	";
	executeQuery($db, $sql, [
		$_POST['RESULT_P1_PLAYER_ID'],
		$_POST['RESULT_P2_PLAYER_ID'],
		$_POST['RESULT_P1_SET1'],
		$_POST['RESULT_P1_SET2'],
		$_POST['RESULT_P1_SET3'],
		$_POST['RESULT_P2_SET1'],
		$_POST['RESULT_P2_SET2'],
		$_POST['RESULT_P2_SET3'],
		$_POST['RESULT_ID']
	]);
}

//================================================================== update
else if($_POST['type']=='new') {
	$sql = "
		INSERT INTO RESULT (
			RESULT_ARENA_ID,
			RESULT_P1_PLAYER_ID,
			RESULT_P2_PLAYER_ID,
			RESULT_P1_SET1,
			RESULT_P1_SET2,
			RESULT_P1_SET3,
			RESULT_P2_SET1,
			RESULT_P2_SET2,
			RESULT_P2_SET3
		) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
	";
	executeQuery($db, $sql, [
		$_POST['ARENA_ID'],
		$_POST['RESULT_P1_PLAYER_ID'],
		$_POST['RESULT_P2_PLAYER_ID'],
		$_POST['RESULT_P1_SET1'],
		$_POST['RESULT_P1_SET2'],
		$_POST['RESULT_P1_SET3'],
		$_POST['RESULT_P2_SET1'],
		$_POST['RESULT_P2_SET2'],
		$_POST['RESULT_P2_SET3']
	]);
}

//================================================================== delete
else if($_POST['type']=='delete') {
	$sql = "DELETE FROM RESULT WHERE RESULT_ID = ?";
	executeQuery($db, $sql, [$_POST['RESULT_ID']]);
}

//================================================================== get list of players
$sql = "
	SELECT
		USER_ID,
		USER_NAME,
		USER_IMAGE,
		USER_PROFILE,
		PLAYER_ID
	FROM
		USER,
		PLAYER
	WHERE
		USER_ID = PLAYER_USER_ID
		AND PLAYER_ARENA_ID = ?
	ORDER BY USER_NAME
";
$players = executeQuery($db, $sql, [$_POST['ARENA_ID']]);

//================================================================== get list of results
$sql = "
	SELECT
		RESULT_ID,
		RESULT_P1_PLAYER_ID,
		RESULT_P2_PLAYER_ID,
		(SELECT USER_NAME FROM USER, PLAYER WHERE USER_ID = PLAYER_USER_ID AND PLAYER_ID = RESULT_P1_PLAYER_ID) RESULT_P1_PLAYER_NAME,
		(SELECT USER_NAME FROM USER, PLAYER WHERE USER_ID = PLAYER_USER_ID AND PLAYER_ID = RESULT_P2_PLAYER_ID) RESULT_P2_PLAYER_NAME,
		RESULT_P1_SET1,
		RESULT_P1_SET2,
		RESULT_P1_SET3,
		RESULT_P2_SET1,
		RESULT_P2_SET2,
		RESULT_P2_SET3,
		DATE_FORMAT(RESULT_CREATEDDATE, '%d/%m/%Y') CREATEDDATE
	FROM
		RESULT
	WHERE
		RESULT_ARENA_ID = ?
	ORDER BY RESULT_CREATEDDATE DESC
";
$results = executeQuery($db, $sql, [$_POST['ARENA_ID']]);

echo json_encode([
	'status' => 'ok',
	'results' => $results,
	'players' => $players
]);