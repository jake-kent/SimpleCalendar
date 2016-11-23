<?php
session_start();
include 'token.php';
$csrf = new csrf();
require 'validate.php';
if(isset($_POST['eventId']) && isset($_SESSION['user_id']) && isset($_POST[$csrf->get_token_id()])){
	if($csrf->check_valid_token('post')){
		$eventId = $_POST['eventId'];
		$username = $_SESSION['user_id'];
		$stmt = $mysqli->prepare("DELETE FROM events WHERE (id=? AND user_name=?)");

		if(!$stmt){
			echo "Failed";
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('ss', $eventId, $username);

		$stmt->execute();

		$stmt->close();

		echo "Success";
	}
}
?>