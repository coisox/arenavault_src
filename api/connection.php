<?php
header("Access-Control-Allow-Origin: *");
error_reporting(E_WARNING | E_ERROR | E_PARSE);
define('APP_ENVIRONMENT', 'stg');

$db = new mysqli('127.0.0.1', 'root', '', 'arenavault');
if(mysqli_connect_errno()) {
	$timestamp = date("YmdHis");
	file_put_contents("log/error.log", $timestamp . "\t\t" . 'Connection error: '. mysqli_connect_errno() . PHP_EOL, FILE_APPEND);
	echo json_encode(['status' => "Connection error"]);
	die();
}

function executeQuery($conn, $sql, $params=[]) {
	$stmt = $conn->prepare($sql);

	if($stmt === false) {
		$timestamp = date("YmdHis");
		if(APP_ENVIRONMENT=='prod') {
			file_put_contents(__DIR__ . "/log/QueryError_$timestamp.log", "Prepare statement error." . PHP_EOL . PHP_EOL . $conn->error . PHP_EOL . PHP_EOL . $sql, FILE_APPEND);
			echo json_encode(["status" => "error", "message" => "QueryError_$timestamp"]);
		}
		else {
			$sql = preg_replace_callback( '/\?/', function($match) use(&$params) {
				$p = array_shift($params);
				return is_numeric($p)?$p:"'".addslashes($p)."'";
			}, $sql);
			echo "<b>Prepare statement error</b><br>".preg_replace('!\s+!', ' ', $conn->error)."<pre>$sql</pre>";
		}
		die;
	}
	
	for($i=0, $types=''; $i<count($params); $i++) $types .= 's';
	$stmt->bind_param($types, ...$params);
	$stmt->execute();
	
	if($stmt->error) {
		$timestamp = date("YmdHis");
		if(APP_ENVIRONMENT=='prod') {
			file_put_contents(__DIR__ . "/log/QueryError_$timestamp.log", "Execute statement error." . PHP_EOL . PHP_EOL . $stmt->error . PHP_EOL . PHP_EOL . $sql, FILE_APPEND);
			echo json_encode(["status" => "error", "message" => "QueryError_$timestamp"]);
		}
		else {
			$sql = preg_replace_callback( '/\?/', function($match) use(&$params) {
				$p = array_shift($params);
				return is_numeric($p)?$p:"'".addslashes($p)."'";
			}, $sql);
			echo "<b>Execute statement error</b><br>".preg_replace('!\s+!', ' ', $stmt->error)."<pre>$sql</pre>";
		}
		die;
	}
	
	$result = $stmt->get_result();
	$stmt->close();

	return $result?mysqli_fetch_all($result, MYSQLI_ASSOC):false;
}
