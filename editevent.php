<?php
session_start();
include 'token.php';
$csrf = new csrf();
require 'validate.php';

if(isset($_POST['id']) && isset($_POST['year']) && isset($_POST['month']) && isset($_POST['day']) && isset($_POST['time']) && isset($_POST['name']) && isset($_POST['description'])&& isset($_SESSION['user_id'])){
	if($csrf->check_valid_token('post')){
		$eventId = $_POST['id'];
		$username = $_SESSION['user_id'];
		$year = $_POST['year'];
		$month = $_POST['month'];
		$day = $_POST['day'];
		$time = $_POST['time'];
		$name = $_POST['name'];
		$description = $_POST['description'];

		$stmt = $mysqli->prepare("UPDATE events SET year=?, month=?, date=?, time=?, event_name=?, event_description=? WHERE (id=? AND user_name=?)");

		if(!$stmt){
			echo "Failed";
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('ssssssss', $year, $month, $day, $time, $name, $description, $eventId, $username);

		$stmt->execute();

		$stmt->close();

		echo "Success";
	}
}
?>