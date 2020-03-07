<?php
require_once('connection.php');

$_POST['USER_GMAIL'] = $_POST['USER_GMAIL']?:'coisox@gmail.com';
$_POST['USER_NAME'] = $_POST['USER_NAME']?:'Aizal';
$_POST['USER_IMAGE'] = $_POST['USER_IMAGE']?:'https://lh3.googleusercontent.com/a-/AAuE7mC0rlObcQOudb77CTsx36zCUYvwngiVeF0yO8XXxA=s96-c';

//========================================================== add user
$sql = "
	INSERT INTO USER (
		USER_GMAIL,
		USER_NAME
	) VALUES (
		?, ?
	) ON DUPLICATE KEY UPDATE USER_GMAIL = USER_GMAIL
";
executeQuery($db, $sql, [
	$_POST['USER_GMAIL'],
	$_POST['USER_NAME']
]);

//========================================================== store user profilepic
include 'sub_setProfilepic.php';

//========================================================== select user session data
$sql = "SELECT USER_ID, USER_IMAGE, USER_GMAIL FROM USER WHERE USER_GMAIL = ?";
$user = executeQuery($db, $sql, [$_POST['USER_GMAIL']])[0];

//========================================================== output
echo json_encode([
	'status' => 'ok',
	'user' => $user
]);