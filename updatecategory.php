<?php
session_start();
require 'validate.php';

if(isset($_SESSION['user_id']) && isset($_POST['eventId']) && isset($_POST['category'])){
	$username = $_SESSION['user_id'];
	$eventID = $_POST['eventId'];
	$category = $_POST['category'];
	$stmt = $mysqli->prepare("UPDATE events SET category=? WHERE user_name=? AND id=?");

	if(!$stmt){
		echo "Failed";
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt->bind_param('sss', $category, $username, $eventID);

	$stmt->execute();

	$stmt->fetch();

	$stmt->close();
	echo "Success";
}
?>