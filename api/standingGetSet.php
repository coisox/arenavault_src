<?php
require_once('connection.php');

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
$rs = executeQuery($db, $sql, [$_POST['ARENA_ID']]);

for($p=0; $p<count($rs); $p++) {
	$players[$rs[$p]['PLAYER_ID']] = [
		'USER_ID' => $rs[$p]['USER_ID'],
		'USER_NAME' => $rs[$p]['USER_NAME'],
		'PLAYER_ID' => $rs[$p]['PLAYER_ID'],
		'WIN' => 0,
		'LOSE' => 0,
		'GAME_DIFF' => 0,
		'SET_DIFF' => 0,
		'POINT_DIFF' => 0,
		'PLAYED' => 0,
		'LEVEL' => '',
		'TEMP_WIN_STREAK' => 0,
		'WIN_STREAK' => 0,
		'TEMP_LOSE_STREAK' => 0,
		'LOSE_STREAK' => 0,
	];
}

//================================================================== get list of results
$sql = "
	SELECT
		*
	FROM
		RESULT
	WHERE
		RESULT_ARENA_ID = ?
	ORDER BY
		RESULT_CREATEDDATE
";
$results = executeQuery($db, $sql, [$_POST['ARENA_ID']]);

//================================================================== massage players
foreach($results as $result) {
	$player1Win = ($result['RESULT_P1_SET1']>$result['RESULT_P2_SET1']) + ($result['RESULT_P1_SET2']>$result['RESULT_P2_SET2']) + ($result['RESULT_P1_SET3']>$result['RESULT_P2_SET3']);
	if($player1Win>=2) { $w = 1; $l = 2; } else { $w = 2; $l = 1; }
	$players[$result['RESULT_P'.$w.'_PLAYER_ID']]['WIN'] ++;
	$players[$result['RESULT_P'.$l.'_PLAYER_ID']]['LOSE'] ++;
	
	$players[$result['RESULT_P'.$w.'_PLAYER_ID']]['TEMP_WIN_STREAK'] ++;
	$players[$result['RESULT_P'.$w.'_PLAYER_ID']]['TEMP_LOSE_STREAK'] = 0;
	if($players[$result['RESULT_P'.$w.'_PLAYER_ID']]['WIN_STREAK']<$players[$result['RESULT_P'.$w.'_PLAYER_ID']]['TEMP_WIN_STREAK']) $players[$result['RESULT_P'.$w.'_PLAYER_ID']]['WIN_STREAK'] = $players[$result['RESULT_P'.$w.'_PLAYER_ID']]['TEMP_WIN_STREAK'];
	if($players[$result['RESULT_P'.$w.'_PLAYER_ID']]['LOSE_STREAK']<$players[$result['RESULT_P'.$w.'_PLAYER_ID']]['TEMP_LOSE_STREAK']) $players[$result['RESULT_P'.$w.'_PLAYER_ID']]['LOSE_STREAK'] = $players[$result['RESULT_P'.$w.'_PLAYER_ID']]['TEMP_LOSE_STREAK'];
	$players[$result['RESULT_P'.$l.'_PLAYER_ID']]['TEMP_WIN_STREAK'] = 0;
	$players[$result['RESULT_P'.$l.'_PLAYER_ID']]['TEMP_LOSE_STREAK'] ++;
	if($players[$result['RESULT_P'.$l.'_PLAYER_ID']]['WIN_STREAK']<$players[$result['RESULT_P'.$l.'_PLAYER_ID']]['TEMP_WIN_STREAK']) $players[$result['RESULT_P'.$l.'_PLAYER_ID']]['WIN_STREAK'] = $players[$result['RESULT_P'.$l.'_PLAYER_ID']]['TEMP_WIN_STREAK'];
	if($players[$result['RESULT_P'.$l.'_PLAYER_ID']]['LOSE_STREAK']<$players[$result['RESULT_P'.$l.'_PLAYER_ID']]['TEMP_LOSE_STREAK']) $players[$result['RESULT_P'.$l.'_PLAYER_ID']]['LOSE_STREAK'] = $players[$result['RESULT_P'.$l.'_PLAYER_ID']]['TEMP_LOSE_STREAK'];
	
	$players[$result['RESULT_P1_PLAYER_ID']]['PLAYED'] = ($players[$result['RESULT_P1_PLAYER_ID']]['WIN']!=0 || $players[$result['RESULT_P1_PLAYER_ID']]['LOSE']!=0)?1:0;
	$players[$result['RESULT_P2_PLAYER_ID']]['PLAYED'] = ($players[$result['RESULT_P2_PLAYER_ID']]['WIN']!=0 || $players[$result['RESULT_P2_PLAYER_ID']]['LOSE']!=0)?1:0;
	
	$players[$result['RESULT_P1_PLAYER_ID']]['GAME_DIFF'] = $players[$result['RESULT_P1_PLAYER_ID']]['WIN'] - $players[$result['RESULT_P1_PLAYER_ID']]['LOSE'];
	$players[$result['RESULT_P2_PLAYER_ID']]['GAME_DIFF'] = $players[$result['RESULT_P2_PLAYER_ID']]['WIN'] - $players[$result['RESULT_P2_PLAYER_ID']]['LOSE'];
	
	$players[$result['RESULT_P1_PLAYER_ID']]['SET_DIFF'] += ((($result['RESULT_P1_SET1'] > $result['RESULT_P2_SET1'])?1:-1) + (($result['RESULT_P1_SET2'] > $result['RESULT_P2_SET2'])?1:-1) + (($result['RESULT_P1_SET3'] > $result['RESULT_P2_SET3'])?1:-1));
	$players[$result['RESULT_P2_PLAYER_ID']]['SET_DIFF'] += ((($result['RESULT_P2_SET1'] > $result['RESULT_P1_SET1'])?1:-1) + (($result['RESULT_P2_SET2'] > $result['RESULT_P1_SET2'])?1:-1) + (($result['RESULT_P2_SET3'] > $result['RESULT_P1_SET3'])?1:-1));
	
	$players[$result['RESULT_P2_PLAYER_ID']]['POINT_DIFF'] += (($result['RESULT_P2_SET1']-$result['RESULT_P1_SET1']) + ($result['RESULT_P2_SET2']-$result['RESULT_P1_SET2']) + ($result['RESULT_P2_SET3']-$result['RESULT_P1_SET3']));
	$players[$result['RESULT_P1_PLAYER_ID']]['POINT_DIFF'] += (($result['RESULT_P1_SET1']-$result['RESULT_P2_SET1']) + ($result['RESULT_P1_SET2']-$result['RESULT_P2_SET2']) + ($result['RESULT_P1_SET3']-$result['RESULT_P2_SET3']));

	for($p=1; $p<3; $p++) {
		if(
			$players[$result['RESULT_P'.$p.'_PLAYER_ID']]['TEMP_WIN_STREAK']==5
		)	$players[$result['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL'] = 'dragon';
		else if(
			$players[$result['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL']!='dragon' &&
			$players[$result['RESULT_P'.$p.'_PLAYER_ID']]['TEMP_WIN_STREAK']==3
		)	$players[$result['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL'] = 'shark';
		else if(
			$players[$result['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL']!='dragon' &&
			$players[$result['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL']!='shark' &&
			$players[$result['RESULT_P'.$p.'_PLAYER_ID']]['TEMP_LOSE_STREAK']==3
		)	$players[$result['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL'] = 'chicken';
	}
}

//================================================================== convert standings to array and sort result
foreach($players as $key => $value) {
	$standings[] = $players[$key];
}

//================================================================== statistic
$sql = "SELECT COUNT(*) C FROM PLAYER WHERE PLAYER_ARENA_ID = ?";
$totalPlayer = executeQuery($db, $sql, [$_POST['ARENA_ID']])[0]['C'];
$totalToPlay = ($totalPlayer*($totalPlayer-1))/2;
$completed = count($results);

//================================================================== return
echo json_encode([
	'status' => 'ok',
	'standings' => $standings,
	'players' => $rs,
	'completed' => $completed,
	'incomplete' => $totalToPlay - $completed
]);