<?php
require_once('connection.php');

//================================================================== arena stat
include 'sub_getArenaStat.php';

$_POST['ARENA_ID'] = $_POST['ARENA_ID']?:'B481A4';
$emblem_dragon = [];
$emblem_shark = [];
$emblem_weekend = [];

$sql = "
	SELECT
		RESULT_P1_PLAYER_ID P1,
		RESULT_P2_PLAYER_ID P2,
		RESULT_OVERALL RO,
		(SELECT USER_NAME FROM USER, PLAYER WHERE USER_ID = PLAYER_USER_ID AND PLAYER_ID = RESULT_P1_PLAYER_ID) P1_NAME,
		(SELECT USER_NAME FROM USER, PLAYER WHERE USER_ID = PLAYER_USER_ID AND PLAYER_ID = RESULT_P2_PLAYER_ID) P2_NAME,
		DAYNAME(RESULT_CREATEDDATE) DAYNAME,
		DATEDIFF(NOW(), RESULT_CREATEDDATE) SLEEP
	FROM RESULT
	WHERE RESULT_ARENA_ID = ?
	ORDER BY RESULT_CREATEDDATE, RESULT_ID
";
$r = executeQuery($sql, [$_POST['ARENA_ID']]);

for($i=0; $i<count($r); $i++) {
	
	//=================================================== init data
	if(!$p[$r[$i]['P1']]['ID']) {
		$p[$r[$i]['P1']]['ID'] = $r[$i]['P1'];
		$p[$r[$i]['P1']]['NAME'] = $r[$i]['P1_NAME'];
		$p[$r[$i]['P1']]['WIN_S'] = 0;
		$p[$r[$i]['P1']]['WIN_S_T'] = 0;
		$p[$r[$i]['P1']]['LOSE_S'] = 0;
		$p[$r[$i]['P1']]['LOSE_S_T'] = 0;
		$p[$r[$i]['P1']]['BRU_S'] = 0;
		$p[$r[$i]['P1']]['BRU_S_T'] = 0;
		$p[$r[$i]['P1']]['WIN_A'] = [];
		$p[$r[$i]['P1']]['SLEEP'] = $r[$i]['SLEEP'];
	}
	if(!$p[$r[$i]['P2']]['ID']) {
		$p[$r[$i]['P2']]['ID'] = $r[$i]['P2'];
		$p[$r[$i]['P2']]['NAME'] = $r[$i]['P2_NAME'];
		$p[$r[$i]['P2']]['WIN_S'] = 0;
		$p[$r[$i]['P2']]['WIN_S_T'] = 0;
		$p[$r[$i]['P2']]['LOSE_S'] = 0;
		$p[$r[$i]['P2']]['LOSE_S_T'] = 0;
		$p[$r[$i]['P2']]['BRU_S'] = 0;
		$p[$r[$i]['P2']]['WIN_A'] = [];
		$p[$r[$i]['P2']]['SLEEP'] = $r[$i]['SLEEP'];
	}
	
	//=================================================== emblem_king
	$p[$r[$i]['P1']]['WIN_S_T'] ++;
	$p[$r[$i]['P2']]['WIN_S_T'] = 0;
	if($p[$r[$i]['P1']]['WIN_S'] < $t = $p[$r[$i]['P1']]['WIN_S_T']) $p[$r[$i]['P1']]['WIN_S'] = $t;
	if($p[$r[$i]['P2']]['WIN_S'] < $t = $p[$r[$i]['P2']]['WIN_S_T']) $p[$r[$i]['P2']]['WIN_S'] = $t;
	
	//=================================================== emblem_chicken
	$p[$r[$i]['P2']]['LOSE_S_T'] ++;
	$p[$r[$i]['P1']]['LOSE_S_T'] = 0;
	if($p[$r[$i]['P2']]['LOSE_S'] < $t = $p[$r[$i]['P2']]['LOSE_S_T']) $p[$r[$i]['P2']]['LOSE_S'] = $t;
	if($p[$r[$i]['P1']]['LOSE_S'] < $t = $p[$r[$i]['P1']]['LOSE_S_T']) $p[$r[$i]['P1']]['LOSE_S'] = $t;
	
	//=================================================== emblem_brutality
	if(explode('-', $r[$i]['RO'])[1]==='0') $p[$r[$i]['P1']]['BRU_S_T'] ++;
	if($p[$r[$i]['P1']]['BRU_S'] < $t = $p[$r[$i]['P1']]['BRU_S_T']) $p[$r[$i]['P1']]['BRU_S'] = $t;
	if($p[$r[$i]['P2']]['BRU_S'] < $t = $p[$r[$i]['P2']]['BRU_S_T']) $p[$r[$i]['P2']]['BRU_S'] = $t;
	
	//=================================================== emblem_slayer
	if(!in_array($r[$i]['P2'], $p[$r[$i]['P1']]['WIN_A'])) $p[$r[$i]['P1']]['WIN_A'][] = $r[$i]['P2_NAME'];
	for($a=1; $a<3; $a++) {
		if($p[$r[$i]['P'.$a]]['WIN_S_T']==5) $emblem_dragon[] = $r[$i]['P'.$a.'_NAME'];
		else if($p[$r[$i]['P'.$a]]['WIN_S_T']==3) $emblem_shark[] = $r[$i]['P'.$a.'_NAME'];
	}
	
	//=================================================== emblem_weekend
	if($r[$i]['DAYNAME']=='Saturday' || $r[$i]['DAYNAME']=='Sunday') {
		$emblem_weekend[] = $r[$i]['P1_NAME'];
		$emblem_weekend[] = $r[$i]['P2_NAME'];
	}
	
	//=================================================== emblem_sleeping
	if($p[$r[$i]['P1']]['SLEEP']>$r[$i]['SLEEP']) $p[$r[$i]['P1']]['SLEEP'] = $r[$i]['SLEEP'];
	if($p[$r[$i]['P2']]['SLEEP']>$r[$i]['SLEEP']) $p[$r[$i]['P2']]['SLEEP'] = $r[$i]['SLEEP'];
}

$emblem_dragon = array_values(array_unique($emblem_dragon));
$emblem_shark = array_values(array_unique(array_values(array_diff($emblem_shark, ($emblem_dragon?:[])))));
$emblem_weekend = array_values(array_unique($emblem_weekend));

if($p) {
	foreach($p as $key => $value) {
		// file_put_contents(__DIR__ . "/TEMP.log", $p[$key]['NAME']." level = ".$p[$key]['WIN_S'].PHP_EOL, FILE_APPEND);
		
		//=================================================== emblem_king
		if($p[$key]['WIN_S']>2 && (!$emblem_king || $emblem_king['WIN_STREAK']<$p[$key]['WIN_S'])) $emblem_king = ['NAME' => $p[$key]['NAME'], 'WIN_STREAK' => $p[$key]['WIN_S']];
		
		//=================================================== emblem_chicken
		if($p[$key]['WIN_S']<3 && $p[$key]['LOSE_S']>2) $emblem_chicken[] = $p[$key]['NAME'];
		
		//=================================================== emblem_brutality
		if($p[$key]['BRU_S']>=3) $emblem_brutality[] = $p[$key]['NAME'];
		
		//=================================================== emblem_dragonslayer
		if($p[$key]['WIN_S']<5 && array_intersect($p[$key]['WIN_A'], $emblem_dragon)) $emblem_dragonslayer[] = $p[$key]['NAME'];
		
		//=================================================== emblem_sharkslayer
		if($p[$key]['WIN_S']<3 && array_intersect($p[$key]['WIN_A'], $emblem_shark)) $emblem_sharkslayer[] = $p[$key]['NAME'];
		
		//=================================================== emblem_sleeping
		if($p[$key]['SLEEP']>5 && ($totalPlayer-1)!=($p[$key]['WIN_S']+$p[$key]['LOSE_S'])) $emblem_sleeping[] = $p[$key]['NAME'];
	}
}

echo json_encode([
	'status' => 'ok',
	'emblem_king' => $emblem_king?['NAME' => $emblem_king['NAME'], 'WIN_STREAK' => '('.$emblem_king['WIN_STREAK'].')']:['NAME' => 'No candidate', 'WIN_STREAK' => '(min 3)'],
	'emblem_brutality' => $emblem_brutality?:['No candidate'],
	'emblem_dragonslayer' => $emblem_dragonslayer?:['No candidate'],
	'emblem_sharkslayer' => $emblem_sharkslayer?:['No candidate'],
	'emblem_dragon' => $emblem_dragon?:['No candidate'],
	'emblem_shark' => $emblem_shark?:['No candidate'],
	'emblem_chicken' => $emblem_chicken?:['No candidate'],
	'emblem_weekend' => $emblem_weekend?:['No candidate'],
	'emblem_sleeping' => $emblem_sleeping?:['No candidate'],
	// 'players' => $p,
]);