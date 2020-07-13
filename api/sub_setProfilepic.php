<?php

//------------------------------------------------------ save profilepic
$filename = "profilepic/".($_POST['USER_ID']).'_'.uniqid().".jpg";
$filecontent = file_get_contents($_POST['USER_IMAGE']);
file_put_contents($filename, $filecontent);

//------------------------------------------------------ get new profilepic location
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$path = pathinfo($protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$filename = $path['dirname'].'/'.$filename;

//------------------------------------------------------ update profilepic location into DB
$sql = "UPDATE USER SET USER_IMAGE = ? WHERE USER_ID = ?";
executeQuery($sql, [$filename, $_POST['USER_ID']]);