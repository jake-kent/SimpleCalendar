<?php
require 'validate.php';

$user = $_POST['username'];
$pass_guess = $_POST['password'];
if($user == null || $pass_guess == null){
	header("Location: //52.2.151.227/~kentjakel/module_3/CSE330-Module3-Jake-Kent-432101-Hannah-Mehrle-429406/index.php?login=invalid");
}
else{
	$stmt = $mysqli->prepare("SELECT COUNT(*), user_name, password FROM users WHERE user_name=?");

	$stmt->bind_param('s', $user);

	$stmt->execute();

	$stmt->bind_result($cnt, $user_id, $pwd_hash);

	$stmt->fetch();

	$stmt->close();
	if( $cnt == 1 && crypt($pass_guess, $pwd_hash)==$pwd_hash){
		ini_set( 'session.cookie_httponly', 1 );
		session_start();
		$_SESSION['user_id'] = $user_id;
		header("Location: //52.2.151.227/~kentjakel/module_5/fall2015-module5-jake-kent-432101-hannah-mehrle-429406/calendar.php");
	}else{
		header("Location: //52.2.151.227/~kentjakel/module_5/fall2015-module5-jake-kent-432101-hannah-mehrle-429406/index.php?login=failed");
	}
}

?>