<?php
session_start();
require 'validate.php';

if(isset($_SESSION['user_id']) && isset($_POST['shared_usernames'])){
	$username = $_SESSION['user_id'];
	$shared_usernames = $_POST['shared_usernames'];
	$stmt = $mysqli->prepare("UPDATE users SET shared_calendars=? WHERE user_name=?");

	if(!$stmt){
		echo "Failed";
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt->bind_param('ss', $shared_usernames, $username);

	$stmt->execute();

	$stmt->fetch();

	$stmt->close();
	echo "Success";
}
?>