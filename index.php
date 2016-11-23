<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<title>GoCal: Login Page</title>
	<style type="text/css">
	body h1{text-align: center;}
	body #content{width: 325px;; margin: 25% auto 0 auto;}
	.new_user{width: 175px; margin: 10px auto 0 auto;}
	.new_user a {text-decoration: none; font-size: 12px;}
	</style>
	<link rel="stylesheet" type="text/css" href="header.css">
</head>
<body>
	<div class="page_header">
		<h2>GoCal</h2>
	</div>
	<div id="content">
		<?php if(isset($_POST['log_out'])){session_destroy(); echo '<script type="text/javascript">', 'user_logged_out(); function user_logged_out(){ alert("You Have Been Logged Out");}', '</script>';} ?>
		<?php if(isset($_GET['login']) && $_GET['login']=='failed'){echo '<script type="text/javascript">', 'login_failed(); function login_failed(){ alert("Your entered username or password do not match our records. Please try again.");}', '</script>';} ?>
		<?php if(isset($_GET['login']) && $_GET['login']=='invalid'){echo '<script type="text/javascript">', 'login_form_invalid(); function login_form_invalid(){ alert("Your forgot to enter your username or password. Please try again.");}', '</script>';} ?>
		<form action="login.php" method="POST">
			<input type="text" name="username" placeholder="username">
			<input type="password" name="password" placeholder="password">
			<input type="submit" name="submit" value="Log In">
		</form>
	</div>
	<div class="new_user"><a href="********************************/new_user.php"> New User: Create An Account Here</a></div>
</body>
</html>