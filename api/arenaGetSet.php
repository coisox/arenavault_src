<?php
require_once('connection.php');

if($_POST['type']=='edit') {
	$sql = "UPDATE ARENA SET ARENA_NAME = ? WHERE ARENA_ID = ?";
	executeQuery($sql, [
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
		$rs = executeQuery($sql, [$_POST['ARENA_ID']]);
		if($rs[0]['C']==0) $duplicate = false;
	}
	
	//====================================================== add new arena
	$sql = "INSERT INTO ARENA (ARENA_ID, ARENA_NAME, ARENA_OWNER_USER_ID) VALUES (?, ?, ?)";
	executeQuery($sql, [$_POST['ARENA_ID'], $_POST['ARENA_NAME'], $_POST['USER_ID']]);
	
	//====================================================== add new arena
	$sql = "INSERT INTO PLAYER (PLAYER_ARENA_ID, PLAYER_USER_ID) VALUES (?, ?)";
    executeQuery($sql, [$_POST['ARENA_ID'], $_POST['USER_ID']]);
    
    //========================================================== initiate user stat
    // $sql = "SELECT PLAYER_ID FROM PLAYER WHERE PLAYER_ARENA_ID = ? AND PLAYER_USER_ID = ?";
    // $rs = executeQuery($sql, [$_POST['ARENA_ID'], $_POST['USER_ID']])[0];
    // $_POST['STAT_TARGET_PLAYER_ID'] = $_POST['STAT_REVIEW_BY_PLAYER_ID'] = $rs['PLAYER_ID'];
    // $_POST['STAT_ATTACK'] = $_POST['STAT_DEFENSE'] = $_POST['STAT_SERVE'] = $_POST['STAT_PLACEMENT'] = $_POST['STAT_FOOTWORK'] = 0;
    // include('sub_setUserStat.php');
}

else if($_POST['type']=='code') {
	$rs = executeQuery("SELECT 1 FROM ARENA WHERE ARENA_ID = ?", [$_POST['ARENA_ID']]);
	if(!$rs) { echo json_encode(['status' => 'ko']); die; }

	$sql = "INSERT INTO PLAYER (PLAYER_ARENA_ID, PLAYER_USER_ID) VALUES (?, ?) ON DUPLICATE KEY UPDATE PLAYER_USER_ID = PLAYER_USER_ID";
    executeQuery($sql, [$_POST['ARENA_ID'], $_POST['USER_ID']]);
    
    //========================================================== initiate user stat
    $sql = "SELECT PLAYER_ID FROM PLAYER WHERE PLAYER_ARENA_ID = ? AND PLAYER_USER_ID = ?";
    $rs = executeQuery($sql, [$_POST['ARENA_ID'], $_POST['USER_ID']])[0];
    $_POST['STAT_TARGET_PLAYER_ID'] = $_POST['STAT_REVIEW_BY_PLAYER_ID'] = $rs['PLAYER_ID'];
    $_POST['STAT_ATTACK'] = $_POST['STAT_DEFENSE'] = $_POST['STAT_SERVE'] = $_POST['STAT_PLACEMENT'] = $_POST['STAT_FOOTWORK'] = 0;
    include('sub_setUserStat.php');
}

else if($_POST['type']=='delete') {
	$sql = "DELETE FROM ARENA WHERE ARENA_ID = ?";
	executeQuery($sql, [$_POST['ARENA_ID']]);
}

$sql = "
    SELECT
        ARENA_ID,
        ARENA_NAME,
        (CASE WHEN ARENA_OWNER_USER_ID = ? THEN 1 ELSE 0 END) IS_OWNER,
        PLAYER_ID,
        ARENA_CREATEDDATE
	FROM ARENA, PLAYER
	WHERE
		PLAYER_ARENA_ID = ARENA_ID
		AND PLAYER_USER_ID = ?
	ORDER BY ARENA_CREATEDDATE DESC
";
$arenas = executeQuery($sql, [$_POST['USER_ID'], $_POST['USER_ID']]);

//========================================================== data migration
if($arenas[0]['ARENA_ID']=='0B97F3') {
    array_unshift($arenas,[
        "ARENA_ID" => '000000',
        "ARENA_NAME" => 'KeepScore ELO',
        "IS_OWNER" => 0,
        "PLAYER_ID" => 0,
        "ARENA_CREATEDDATE" => null
    ]);
}

echo json_encode([
	'status' => 'ok',
	'arenas' => $arenas
]);