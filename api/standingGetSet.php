<?php
require_once('connection.php');

//========================================================== data migration
if($_POST['ARENA_ID']=='000000') {
	echo json_encode([
		'status' => 'ok',
		'standings' => [
            ["USER_ID"=>null, "USER_NAME"=>"Khalil",     "ELO"=>1431, "GAME"=>201, "PERCENTAGE"=>75],
            ["USER_ID"=>null, "USER_NAME"=>"Aizal",      "ELO"=>1344, "GAME"=>258, "PERCENTAGE"=>71],
            ["USER_ID"=>null, "USER_NAME"=>"Jamali",     "ELO"=>1193, "GAME"=>53,  "PERCENTAGE"=>50],
            ["USER_ID"=>null, "USER_NAME"=>"Yusri",      "ELO"=>1174, "GAME"=>211, "PERCENTAGE"=>56],
            ["USER_ID"=>null, "USER_NAME"=>"Ijat",       "ELO"=>1087, "GAME"=>58,  "PERCENTAGE"=>39],
            ["USER_ID"=>null, "USER_NAME"=>"Epul",       "ELO"=>1060, "GAME"=>71,  "PERCENTAGE"=>49],
            ["USER_ID"=>null, "USER_NAME"=>"Shahrul",    "ELO"=>1055, "GAME"=>111, "PERCENTAGE"=>49],
            ["USER_ID"=>null, "USER_NAME"=>"Zamittar",   "ELO"=>1034, "GAME"=>121, "PERCENTAGE"=>37],
            ["USER_ID"=>null, "USER_NAME"=>"Bujir",      "ELO"=>1006, "GAME"=>88,  "PERCENTAGE"=>44],
            ["USER_ID"=>null, "USER_NAME"=>"Liana",      "ELO"=>991,  "GAME"=>99,  "PERCENTAGE"=>41],
            ["USER_ID"=>null, "USER_NAME"=>"Bahazul",    "ELO"=>975,  "GAME"=>107, "PERCENTAGE"=>41],
            ["USER_ID"=>null, "USER_NAME"=>"Ezrin",      "ELO"=>974,  "GAME"=>9,   "PERCENTAGE"=>33],
            ["USER_ID"=>null, "USER_NAME"=>"Husni",      "ELO"=>968,  "GAME"=>39,  "PERCENTAGE"=>41],
            ["USER_ID"=>null, "USER_NAME"=>"Khai",       "ELO"=>968,  "GAME"=>87,  "PERCENTAGE"=>52],
            ["USER_ID"=>null, "USER_NAME"=>"Pejo",       "ELO"=>935,  "GAME"=>32,  "PERCENTAGE"=>31],
            ["USER_ID"=>null, "USER_NAME"=>"Azli",       "ELO"=>897,  "GAME"=>58,  "PERCENTAGE"=>36],
            ["USER_ID"=>null, "USER_NAME"=>"Sani",       "ELO"=>896,  "GAME"=>22,  "PERCENTAGE"=>31],
            ["USER_ID"=>null, "USER_NAME"=>"Tok Cha",    "ELO"=>887,  "GAME"=>162, "PERCENTAGE"=>30],
            ["USER_ID"=>null, "USER_NAME"=>"Rahman",     "ELO"=>862,  "GAME"=>22,  "PERCENTAGE"=>27],
            ["USER_ID"=>null, "USER_NAME"=>"Nizam Stor", "ELO"=>838,  "GAME"=>42,  "PERCENTAGE"=>35],
            ["USER_ID"=>null, "USER_NAME"=>"Zaidi",      "ELO"=>743,  "GAME"=>29,  "PERCENTAGE"=>20],
            ["USER_ID"=>null, "USER_NAME"=>"Shah",       "ELO"=>682,  "GAME"=>22,  "PERCENTAGE"=>4]
		],
		'completed' => 1902,
		'incomplete' => 0,
		'percentage' => 100,
	]);
	die;
}

//================================================================== arena stat
include 'sub_getArenaStat.php';

$sqlStat = "
	SELECT JSON_OBJECT(
		'ATTACK', IFNULL(CEILING(AVG(STAT_ATTACK)), 0),
		'DEFENSE', IFNULL(CEILING(AVG(STAT_DEFENSE)), 0),
		'SERVE', IFNULL(CEILING(AVG(STAT_SERVE)), 0),
		'PLACEMENT', IFNULL(CEILING(AVG(STAT_PLACEMENT)), 0),
		'FOOTWORK', IFNULL(CEILING(AVG(STAT_FOOTWORK)), 0)
	)
	FROM STAT
	WHERE STAT_TARGET_PLAYER_ID = PLAYER_ID
";
$sql = "
	SELECT
		USER_ID,
		USER_NAME,
		USER_IMAGE,
		PLAYER_ID,
		($sqlStat) STAT_AVERAGE,
		($sqlStat AND STAT_REVIEW_BY_PLAYER_ID = ?) STAT_REVIEW_BY_ME
	FROM
		USER,
		PLAYER
	WHERE
		USER_ID = PLAYER_USER_ID
		AND PLAYER_ARENA_ID = ?
	ORDER BY USER_NAME
";
$rs = executeQuery($db, $sql, [$_POST['PLAYER_ID'], $_POST['ARENA_ID']]);

for($p=0; $p<count($rs); $p++) {
	$players[$rs[$p]['PLAYER_ID']] = [
		'USER_ID' => $rs[$p]['USER_ID'],
		'USER_NAME' => $rs[$p]['USER_NAME'],
		'USER_IMAGE' => $rs[$p]['USER_IMAGE'],
		'PLAYER_ID' => $rs[$p]['PLAYER_ID'],
		'STAT_AVERAGE' => $rs[$p]['STAT_AVERAGE'],
		'STAT_REVIEW_BY_ME' => $rs[$p]['STAT_REVIEW_BY_ME'],

		'WIN' => 0,
		'LOSE' => 0,
		'GAME_DIFF' => 0,
		'SET_DIFF' => 0,
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
if($results) {
	foreach($results as $r) {
		$players[$r['RESULT_P1_PLAYER_ID']]['WIN'] ++;
		$players[$r['RESULT_P1_PLAYER_ID']]['GAME_DIFF'] ++;
		$players[$r['RESULT_P1_PLAYER_ID']]['SET_DIFF'] += eval('return '.$r['RESULT_OVERALL'].';');
		$players[$r['RESULT_P1_PLAYER_ID']]['PLAYED'] = 1;
		$players[$r['RESULT_P1_PLAYER_ID']]['TEMP_WIN_STREAK'] ++;
		
		$players[$r['RESULT_P2_PLAYER_ID']]['LOSE'] ++;
		$players[$r['RESULT_P2_PLAYER_ID']]['GAME_DIFF'] --;
		$players[$r['RESULT_P2_PLAYER_ID']]['SET_DIFF'] -= eval('return '.$r['RESULT_OVERALL'].';');
		$players[$r['RESULT_P2_PLAYER_ID']]['PLAYED'] = 1;
		$players[$r['RESULT_P2_PLAYER_ID']]['TEMP_LOSE_STREAK'] ++;
			
		if($players[$r['RESULT_P1_PLAYER_ID']]['WIN_STREAK']<$players[$r['RESULT_P1_PLAYER_ID']]['TEMP_WIN_STREAK']) $players[$r['RESULT_P1_PLAYER_ID']]['WIN_STREAK'] = $players[$r['RESULT_P1_PLAYER_ID']]['TEMP_WIN_STREAK'];
		if($players[$r['RESULT_P1_PLAYER_ID']]['LOSE_STREAK']<$players[$r['RESULT_P1_PLAYER_ID']]['TEMP_LOSE_STREAK']) $players[$r['RESULT_P1_PLAYER_ID']]['LOSE_STREAK'] = $players[$r['RESULT_P1_PLAYER_ID']]['TEMP_LOSE_STREAK'];
		if($players[$r['RESULT_P2_PLAYER_ID']]['WIN_STREAK']<$players[$r['RESULT_P2_PLAYER_ID']]['TEMP_WIN_STREAK']) $players[$r['RESULT_P2_PLAYER_ID']]['WIN_STREAK'] = $players[$r['RESULT_P2_PLAYER_ID']]['TEMP_WIN_STREAK'];
		if($players[$r['RESULT_P2_PLAYER_ID']]['LOSE_STREAK']<$players[$r['RESULT_P2_PLAYER_ID']]['TEMP_LOSE_STREAK']) $players[$r['RESULT_P2_PLAYER_ID']]['LOSE_STREAK'] = $players[$r['RESULT_P2_PLAYER_ID']]['TEMP_LOSE_STREAK'];
		$players[$r['RESULT_P1_PLAYER_ID']]['TEMP_LOSE_STREAK'] = 0;
		$players[$r['RESULT_P2_PLAYER_ID']]['TEMP_WIN_STREAK'] = 0;
		
		for($p=1; $p<3; $p++) {
			if(
				$players[$r['RESULT_P'.$p.'_PLAYER_ID']]['TEMP_WIN_STREAK']==5
			)	$players[$r['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL'] = 'dragon';
			else if(
				$players[$r['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL']!='dragon' &&
				$players[$r['RESULT_P'.$p.'_PLAYER_ID']]['TEMP_WIN_STREAK']==3
			)	$players[$r['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL'] = 'shark';
			else if(
				$players[$r['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL']!='dragon' &&
				$players[$r['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL']!='shark' &&
				$players[$r['RESULT_P'.$p.'_PLAYER_ID']]['TEMP_LOSE_STREAK']==3
			)	$players[$r['RESULT_P'.$p.'_PLAYER_ID']]['LEVEL'] = 'chicken';
		}
	}
}

//================================================================== convert standings to array and sort result
foreach($players as $key => $value) {
	$players[$key]['STAT_AVERAGE'] = json_decode($players[$key]['STAT_AVERAGE'], true);
	$players[$key]['STAT_REVIEW_BY_ME'] = json_decode($players[$key]['STAT_REVIEW_BY_ME'], true);
	$standings[] = $players[$key];
}

//================================================================== return
echo json_encode([
	'status' => 'ok',
	'standings' => $standings,
	'completed' => $completed,
	'incomplete' => $incomplete,
	'percentage' => $percentage,
]);