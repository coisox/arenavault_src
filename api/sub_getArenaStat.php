<?php

$sql = "SELECT COUNT(*) C FROM RESULT WHERE RESULT_ARENA_ID = ?";
$completed = executeQuery($db, $sql, [$_POST['ARENA_ID']])[0]['C'];

$sql = "SELECT COUNT(*) C FROM PLAYER WHERE PLAYER_ARENA_ID = ?";
$totalPlayer = executeQuery($db, $sql, [$_POST['ARENA_ID']])[0]['C'];

$totalToPlay = ($totalPlayer*($totalPlayer-1))/2;
$incomplete = $totalToPlay - $completed;
$percentage = $totalToPlay?(round($completed/($completed+$incomplete)*100)):0;