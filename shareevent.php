<?php
session_start();
require 'validate.php';

if(isset($_POST['year']) && isset($_POST['month']) && isset($_POST['day']) && isset($_POST['time']) && isset($_POST['name']) && isset($_POST['description'])&& isset($_POST['user_name'])){
	$username = $_POST['user_name'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$time = $_POST['time'];
	$name = $_POST['name'];
	$description = $_POST['description'];

	$stmt = $mysqli->prepare("INSERT INTO events (user_name, year, month, date, time, event_name, event_description) VALUES (?, ?, ?, ?, ?, ?, ?)");
	if(!$stmt){
		echo "Failed";
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt->bind_param('sssssss', $username, $year, $month, $day, $time, $name, $description);

	$stmt->execute();

	$stmt->close();

	echo "Success";
}
?>