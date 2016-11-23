<?php
require 'validate.php';

$first = $_POST['first_name'];
$last = $_POST['last_name'];
$user = $_POST['user_name'];
$plain_pass = $_POST['password'];

if($first == null || $last == null || $user == null || $plain_pass == null){
	header("Location: ********************************/new_user.php?signup=invalid");
}
else{
	$pass = crypt($plain_pass);
	echo $first;
	echo $last;
	echo $user;
	echo $plain_pass;
	$dupe = $mysqli->prepare("SELECT COUNT( * ) FROM `users` WHERE user_name = 'kentjakel'");
	//$dupe->bind_param('s', $user);
	if(!$dupe){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$dupe->execute();
	$dupe->bind_result($num_users);
	$dupe->close();
	echo $user;
	echo $num_users;
	if($num_users > 0){
		$duplicate_user = true;
	}
	else{
		$duplicate_user = false;
	}

	if($duplicate_user){
		header("Location: ********************************/new_user.php?signup=dupe");
	}
	else{
		$stmt = $mysqli->prepare("insert into users (first_name, last_name, user_name, password) values (?, ?, ?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('ssss', $first, $last, $user, $pass);

		$stmt->execute();

		$stmt->close();

		header("Location: ********************************/");
	}
}
?>