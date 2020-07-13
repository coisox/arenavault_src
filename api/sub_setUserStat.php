<?php

// $sql = "SELECT PLAYER_ID FROM PLAYER WHERE PLAYER_ARENA_ID = ? AND PLAYER_USER_ID = ?";
// $playerID = executeQuery($sql, [$_POST['ARENA_ID'], $_POST['USER_ID']])[0]['PLAYER_ID'];

$sql = "
    INSERT INTO STAT (STAT_TARGET_PLAYER_ID, STAT_REVIEW_BY_PLAYER_ID, STAT_ATTACK, STAT_DEFENSE, STAT_SERVE, STAT_PLACEMENT, STAT_FOOTWORK) VALUES (?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE STAT_ATTACK = ?, STAT_DEFENSE = ?, STAT_SERVE = ?, STAT_PLACEMENT = ?, STAT_FOOTWORK = ?
";
$rs = executeQuery($sql, [
    $_POST['STAT_TARGET_PLAYER_ID'],
    $_POST['PLAYER_ID'],
        $_POST['STAT_ATTACK'],
        $_POST['STAT_DEFENSE'],
        $_POST['STAT_SERVE'],
        $_POST['STAT_PLACEMENT'],
        $_POST['STAT_FOOTWORK'],
            $_POST['STAT_ATTACK'],
            $_POST['STAT_DEFENSE'],
            $_POST['STAT_SERVE'],
            $_POST['STAT_PLACEMENT'],
            $_POST['STAT_FOOTWORK']
]);