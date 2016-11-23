<!DOCTYPE html>
<html>
<head>
	<title>UDP: Create An Account</title>
	<style type="text/css">
	body h1{text-align: center;}
	.page_header #go_to_login {position: absolute; top: 29px; left: 20px;}
	body .signup{width: 45%; margin: 25% auto 0 auto;}
	body .signup div{width: 50%; margin: 0;}
	</style>
	<link rel="stylesheet" type="text/css" href="header.css">
</head>
<body>
	<div class="page_header">
		<form id="go_to_login" action="index.php" method="POST">
			<input type="submit" name="go_to_login" value="Return to Login">
		</form>
		<h2>The U-Drive Daily Post: Create An Account</h2>
	</div>
	<form action="create_account.php" method="POST" class="signup">
		<?php if(isset($_GET['signup']) && $_GET['signup']=='invalid'){echo '<script type="text/javascript">', 'signup_invalid(); function signup_invalid(){ alert("You forgot to fill out one of the required First Name, Last Name, Username or Password fields. Please try again.");}', '</script>';} ?>
		<?php if(isset($_GET['signup']) && $_GET['signup']=='dupe'){echo '<script type="text/javascript">', 'signup_dupe(); function signup_dupe(){ alert("That user name is not available. Please try another one.");}', '</script>';} ?>
		<input type="text" name="first_name" placeholder="First Name">
		<input type="text" name="last_name" placeholder="Last Name">
		<input type="text" name="user_name" placeholder="Username">
		<input type="password" name="password" placeholder="Password">
		<input type="submit" name="submit" value="Create Account">
	</form>
</body>
</html>
