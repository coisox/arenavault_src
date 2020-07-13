<?php
require_once('connection.php');

$_POST['USER_NAME'] = ucwords($_POST['USER_NAME']);

if($_POST['type']=='edit') {
	$sql = "UPDATE USER SET USER_NAME = ? WHERE USER_ID = ?";
	executeQuery($sql, [
		$_POST['USER_NAME'],
		$_POST['USER_ID'],
    ]);
    
    //====================================================== bind player
    if($_POST['BIND_PLAYER_ID']) {

        $sql = "UPDATE RESULT SET RESULT_P1_PLAYER_ID = ? WHERE RESULT_P1_PLAYER_ID = ?";
        executeQuery($sql, [
            $_POST['BIND_PLAYER_ID'],
            $_POST['PLAYER_ID']
        ]);

        $sql = "UPDATE RESULT SET RESULT_P2_PLAYER_ID = ? WHERE RESULT_P2_PLAYER_ID = ?";
        executeQuery($sql, [
            $_POST['BIND_PLAYER_ID'],
            $_POST['PLAYER_ID']
        ]);

        $sql = "UPDATE IGNORE STAT SET STAT_TARGET_PLAYER_ID = ? WHERE STAT_TARGET_PLAYER_ID = ?";
        executeQuery($sql, [
            $_POST['BIND_PLAYER_ID'],
            $_POST['PLAYER_ID']
        ]);

        $sql = "UPDATE IGNORE STAT SET STAT_REVIEW_BY_PLAYER_ID = ? WHERE STAT_REVIEW_BY_PLAYER_ID = ?";
        executeQuery($sql, [
            $_POST['BIND_PLAYER_ID'],
            $_POST['PLAYER_ID']
        ]);

        $sql = "SELECT PLAYER_USER_ID FROM PLAYER WHERE PLAYER_ID = ?";
        $rs = executeQuery($sql, [$_POST['PLAYER_ID']]);
        $sql = "DELETE FROM USER WHERE USER_ID = ?";
        executeQuery($sql, [$rs[0]['PLAYER_USER_ID']]);
    }
}

else if($_POST['type']=='new') {
	
	//====================================================== create new user
    $sql = "INSERT INTO USER (USER_GMAIL, USER_NAME) VALUES (?, ?)";
    $USER_GMAIL = 'ghost.'.uniqid().'@gmail.com';
    executeQuery($sql, [$USER_GMAIL, $_POST['USER_NAME']]);
    $sql = "SELECT USER_ID FROM USER WHERE USER_GMAIL = ?";
    $rs = executeQuery($sql, [$USER_GMAIL]);

    //====================================================== create new arena player
    $sql = "INSERT INTO PLAYER (PLAYER_ARENA_ID, PLAYER_USER_ID) VALUES (? , ?)";
    executeQuery($sql, [$_POST['ARENA_ID'], $rs[0]['USER_ID']]);
}

else if($_POST['type']=='delete') {
	$sql = "DELETE FROM PLAYER WHERE PLAYER_ARENA_ID = ? AND PLAYER_USER_ID = ?";
    executeQuery($sql, [$_POST['ARENA_ID'], $_POST['USER_ID']]);
    
    $sql = "DELETE FROM USER WHERE USER_ID = ? AND USER_GMAIL LIKE 'ghost.%'";
    executeQuery($sql, [$_POST['USER_ID']]);
}

$sql = "
    SELECT
        PLAYER_ID,
        USER_NAME,
        USER_GMAIL,
        USER_ID
    FROM
        PLAYER,
        USER
    WHERE
        PLAYER_USER_ID = USER_ID
        AND PLAYER_ARENA_ID = ?
	ORDER BY USER_NAME
";
$players = executeQuery($sql, [$_POST['ARENA_ID']]);

echo json_encode([
	'status' => 'ok',
	'players' => $players
]);