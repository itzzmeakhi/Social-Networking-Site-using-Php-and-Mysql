<?php  

// Triggers when login button is pressed

if(isset($_POST['login_button'])) {

	$email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); // Sanitizing email

	$_SESSION['log_email'] = $email; // Store email into session variable 

	$password = md5($_POST['log_password']); // Get password

	// Query to check email and password to log in

	$check_database_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND password='$password'");

	$check_login_query = mysqli_num_rows($check_database_query);

	if($check_login_query == 1) {

		$row = mysqli_fetch_array($check_database_query);
		$username = $row['username'];

		// Update's user_closed='yes' for successfull logging in

		$user_closed_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND user_closed='yes'");
		if(mysqli_num_rows($user_closed_query) == 1) {

			$reopen_account = mysqli_query($con, "UPDATE users SET user_closed='no' WHERE email='$email'");
		}

		$_SESSION['username'] = $username; // Storing username into session variable
		header("Location: index.php"); // Redirecting to index page
		exit();
	}
	else {
		array_push($error_array, "Wrong Credentials..!!<br>");
	}


}

?>