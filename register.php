<?php  

require 'config/config.php'; // To include config.php file
require 'includes/form_handlers/register_handler.php'; // To include register_handler.php
require 'includes/form_handlers/login_handler.php'; // To include login_handler.php


?>


<!DOCTYPE html>
<html>
<head>
	<title>Welcome to Buddy..!!</title>
	<link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
</head>
<body>

	<?php  // For hiding login/register form

	if(isset($_POST['register_button'])) {

		echo '
		<script>

		$(document).ready(function() {
			$("#first").hide();
			$("#second").show();
		});

		</script>

		';
	}


	?>

	<div class="wrapper">

		<div class="login_box">

			<div class="login_header">
				<h1>Buddy..!!</h1>
				<p> Login or Sign up below! </p>
			</div>

			</br>

			<!-- Login Section -->

			<div id="first">

				<!-- Login form -->

				<form action="register.php" method="POST">

					<!-- Email Section -->

					<input type="email" name="log_email" placeholder="Your Email Address" value="<?php 
					if(isset($_SESSION['log_email'])) {
						echo $_SESSION['log_email'];
					} 
					?>" required>
					<br>

					<!-- Password Section -->

					<input type="password" name="log_password" placeholder="Password">
					<br>

					<!-- Error's Section -->

					<?php if(in_array("Wrong Credentials..!!<br>", $error_array)) echo  "Wrong Credentials..!!<br>"; ?>

					<!-- Login Button -->

					<input type="submit" name="login_button" value="Login">
					<br>

					<!-- Link to register form -->

					<a href="#" id="signup" class="signup">Need and account? Register here!</a>

				</form>

			</div>

			<!-- Register Section -->

			<div id="second">

				<!-- Register form -->

				<form action="register.php" method="POST">

					<!-- First Name Section -->

					<input type="text" name="reg_fname" placeholder="Your First Name" value="<?php 
					if(isset($_SESSION['reg_fname'])) {
						echo $_SESSION['reg_fname'];
					} 
					?>" required>

					<!-- Last Name Section -->
					
					<input type="text" name="reg_lname" placeholder="Your Last Name" value="<?php 
					if(isset($_SESSION['reg_lname'])) {
						echo $_SESSION['reg_lname'];
					} 
					?>" required>
					<br>

					<!-- Error's Section -->

					<?php if(in_array("Your First Name should be in between 2-25 characters</br>",$error_array)) echo "Your First Name should be in between 2-25 characters</br>"; ?>
					<?php if(in_array("Your Last Name should be in between 2-25 characters</br>",$error_array)) echo "Your Last Name should be in between 2-25 characters</br>"; ?>

					<!-- Email Section -->

					<input type="email" name="reg_email" placeholder="Your Email" value="<?php 
					if(isset($_SESSION['reg_email'])) {
						echo $_SESSION['reg_email'];
					} 
					?>" required>

					<!-- Confirm Email Section -->
					

					<input type="email" name="reg_email2" placeholder="Confirm Email" value="<?php 
					if(isset($_SESSION['reg_email2'])) {
						echo $_SESSION['reg_email2'];
					} 
					?>" required>
					<br>

					<!-- Error's Section -->

					<?php if(in_array("Email already in use</br>",$error_array)) echo "Email already in use</br>"; ?>
					<?php if(in_array("Invalid email format</br>",$error_array)) echo "Invalid email format</br>"; ?>
					<?php if(in_array("Emails doesn't match</br>",$error_array)) echo "Emails doesn't match</br>"; ?>
					

					<!-- Password Section -->


					<input type="password" name="reg_password" placeholder="Password" required>
					
					<input type="password" name="reg_password2" placeholder="Confirm Password" required>
					<br>

					<!-- Error's Section -->

					<?php if(in_array("Passwords doesn't match</br>",$error_array)) echo "Passwords doesn't match</br>"; ?>
					<?php if(in_array("Your password should contain only letters and numbers</br>",$error_array)) echo "Your password should contain only letters and numbers</br>"; ?>
					<?php if(in_array("Your Password should be in between 5-30 characters</br>",$error_array)) echo "Your Password should be in between 5-30 characters</br>"; ?>

					<!-- Register Button Section -->

					<input type="submit" name="register_button" value="Register">
					<br>

					<!-- Succesfull Registered Message -->

					<?php if(in_array("<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>", $error_array)) echo "<span style='color: #14C800; margin-left: -80px;'>You're all set! Go ahead and login!</span><br>"; ?>

					<!-- Link to login form -->
					
					<a href="#" id="signin" class="signin">Already have an account? Sign in here!</a>

				</form>

			</div>

		</div>

	</div>

</body>
</html>