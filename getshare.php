<?php
session_start();
require 'validate.php';

if(isset($_SESSION['user_id'])){
	$username = $_SESSION['user_id'];
	$stmt = $mysqli->prepare("SELECT shared_calendars FROM users WHERE user_name=?");

	if(!$stmt){
		echo "Failed";
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt->bind_param('s', $username);

	$stmt->execute();

	$stmt->bind_result($shared_calendars);

	$stmt->fetch();

	$stmt->close();
	echo $shared_calendars;
}
?>