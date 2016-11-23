<?php
session_start();
require 'validate.php';

if(isset($_POST['year']) && isset($_POST['month']) && isset($_POST['day']) && isset($_SESSION['user_id'])){
	$seshusername = $_SESSION['user_id'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$stmt = $mysqli->prepare("select id, user_name, time, event_name, event_description, category from events where year=? and month=? and date=? and user_name=? order by time");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt->bind_param('ssss', $year, $month, $day, $seshusername);

	$stmt->execute();

	$stmt->bind_result($id, $tempuser, $time, $name, $description, $category);

	$refVal = 0;
	$events = array();
	while($stmt->fetch()){
		$eventDate = $month.'/'.$day.'/'.$year;
		$valid_ed = 1;
		$event = array(htmlspecialchars($id), htmlspecialchars($seshusername), htmlspecialchars($day), htmlspecialchars($time), htmlspecialchars($name), htmlspecialchars($description), htmlspecialchars($eventDate), htmlspecialchars($valid_ed), htmlspecialchars($category));
		$events[$refVal] = $event;
		$refVal++;
	}

	$stmt->close();


	$stmt = $mysqli->prepare("SELECT user_name, shared_calendars FROM users");

	if(!$stmt){
		echo "Failed";
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}


	$stmt->execute();

	$stmt->bind_result($sharing_user_name, $shared_usernames);
	$usernames_to_query = array();
	while($stmt->fetch()){
		$shared_users = explode(",", $shared_usernames);
		foreach ($shared_users as $usr) {
			if($usr == $seshusername){
				array_push($usernames_to_query, $sharing_user_name);
			}
		}
	}

	$stmt->close();
	foreach ($usernames_to_query as $shareuser) {
		$stmt = $mysqli->prepare("select id, user_name, time, event_name, event_description, category from events where year=? and month=? and date=? and user_name=? order by time");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('ssss', $year, $month, $day, $shareuser);

		$stmt->execute();

		$stmt->bind_result($id, $user_name, $time, $name, $description, $category);

		while($stmt->fetch()){
			$eventDate = $month.'/'.$day.'/'.$year;
			$valid_ed = ($seshusername == $user_name);
			$event = array(htmlspecialchars($id), htmlspecialchars($user_name), htmlspecialchars($day), htmlspecialchars($time), htmlspecialchars("(S) ".$name), htmlspecialchars($description), htmlspecialchars($eventDate), htmlspecialchars($valid_ed), htmlspecialchars($category));
			$events[$refVal] = $event;
			$refVal++;
		}

		$stmt->close();
	}

	echo json_encode($events);
}
?>