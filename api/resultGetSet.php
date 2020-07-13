<?php
require_once('connection.php');

//================================================================== update
if($_POST['type']=='edit') {
	$sql = "
		UPDATE RESULT SET
			RESULT_P1_PLAYER_ID = ?,
			RESULT_P2_PLAYER_ID = ?,
			RESULT_OVERALL = ?
		WHERE
			RESULT_ID = ?
	";
	executeQuery($sql, [
		$_POST['RESULT_P1_PLAYER_ID'],
		$_POST['RESULT_P2_PLAYER_ID'],
		$_POST['RESULT_OVERALL'],
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
			RESULT_OVERALL
		) VALUES (?, ?, ?, ?)
	";
	executeQuery($sql, [
		$_POST['ARENA_ID'],
		$_POST['RESULT_P1_PLAYER_ID'],
		$_POST['RESULT_P2_PLAYER_ID'],
		$_POST['RESULT_OVERALL']
	]);
}

//================================================================== delete
else if($_POST['type']=='delete') {
	$sql = "DELETE FROM RESULT WHERE RESULT_ID = ?";
	executeQuery($sql, [$_POST['RESULT_ID']]);
}

//================================================================== get list of players
$sql = "
	SELECT
		USER_ID,
		USER_NAME,
		USER_IMAGE,
		PLAYER_ID
	FROM
		USER,
		PLAYER
	WHERE
		USER_ID = PLAYER_USER_ID
		AND PLAYER_ARENA_ID = ?
	ORDER BY USER_NAME
";
$players = executeQuery($sql, [$_POST['ARENA_ID']]);

//================================================================== get completed match
$sql = "
	SELECT
		RESULT_ID,
		RESULT_P1_PLAYER_ID,
        RESULT_P2_PLAYER_ID,
        CONCAT('|', RESULT_P1_PLAYER_ID, '|', RESULT_P2_PLAYER_ID, '|', RESULT_P1_PLAYER_ID, '|') RESULT_PAIR,
		(SELECT USER_NAME FROM USER, PLAYER WHERE USER_ID = PLAYER_USER_ID AND PLAYER_ID = RESULT_P1_PLAYER_ID) RESULT_P1_PLAYER_NAME,
		(SELECT USER_NAME FROM USER, PLAYER WHERE USER_ID = PLAYER_USER_ID AND PLAYER_ID = RESULT_P2_PLAYER_ID) RESULT_P2_PLAYER_NAME,
		RESULT_OVERALL,
        'completed' RESULT_STATUS,
        DATE_FORMAT(RESULT_CREATEDDATE, '%d/%m/%Y') CREATEDDATE
	FROM
		RESULT
	WHERE
		RESULT_ARENA_ID = ?
	ORDER BY RESULT_CREATEDDATE DESC
";
$results = executeQuery($sql, [$_POST['ARENA_ID']]);

//================================================================== create incomplete match\
for($x=0; $x<count($results); $x++) {
    $completedMatches .= $results[$x]['RESULT_PAIR'];
}
for($x=0; $x<count($players); $x++) {
    for($y=$x+1; $y<count($players); $y++) {
        if($players[$x]['PLAYER_ID']!=$players[$y]['PLAYER_ID'] && !strpos($completedMatches, '|'.$players[$x]['PLAYER_ID'].'|'.$players[$y]['PLAYER_ID'].'|')) {
            $results[] = [
                'RESULT_ID' => null,
                'RESULT_P1_PLAYER_ID' => $players[$x]['PLAYER_ID'],
                'RESULT_P2_PLAYER_ID' => $players[$y]['PLAYER_ID'],
                'RESULT_PAIR' => '|'.$players[$x]['PLAYER_ID'].'|'.$players[$y]['PLAYER_ID'].'|'.$players[$x]['PLAYER_ID'].'|',
                'RESULT_P1_PLAYER_NAME' => $players[$x]['USER_NAME'],
                'RESULT_P2_PLAYER_NAME' => $players[$y]['USER_NAME'],
                'RESULT_OVERALL' => null,
                'RESULT_STATUS' => 'incomplete',
                'CREATEDDATE' => 'INCOMPLETE',
            ];
        }
    }
}

echo json_encode([
	'status' => 'ok',
	'results' => $results,
	'players' => $players
]);