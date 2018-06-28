<?php


// Declaring variables to prevent errors

$fname = ""; // Variable for storing first name
$lname = ""; // Variable for storing last name
$em = ""; // Variable for storing email
$em2 = ""; // Variable for storing email2
$password = ""; // Variable for storing password
$password2 = ""; // Variable for storing password 2
$date = ""; // Variable for storing sign up date 
$error_array = array(); // Variable that holds error messages


if(isset($_POST['register_button'])){

	// Registration form values

	// First name

	$fname = strip_tags($_POST['reg_fname']); // For removing html tags from input
	$fname = str_replace(' ', '', $fname); // To replace the spaces in the input
	$fname = ucfirst(strtolower($fname)); // String is converted such that upper case first and remaining lowercase
	$_SESSION['reg_fname'] = $fname; // Stores first name into session variable

	// Last name

	$lname = strip_tags($_POST['reg_lname']); // For removing html tags from input
	$lname = str_replace(' ', '', $lname); // To replace the spaces in the input
	$lname = ucfirst(strtolower($lname)); // String is converted such that upper case first and remaining lowercase
	$_SESSION['reg_lname'] = $lname; // Stores first name into session variable

	// Email

	$em = strip_tags($_POST['reg_email']); // For removing html tags from input
	$em = str_replace(' ', '', $em); // To replace the spaces in the input
	$_SESSION['reg_email'] = $em; // Stores email into session variable

	// Email 2

	$em2 = strip_tags($_POST['reg_email2']); // For removing html tags from input
	$em2 = str_replace(' ', '', $em2); // To replace the spaces in the input
	$_SESSION['reg_email2'] = $em2; // Stores email2 into session variable

	// Password

	$password = strip_tags($_POST['reg_password']); // For removing html tags from input
	$password2 = strip_tags($_POST['reg_password2']); // For removing html tags from input

	// Sign Up date 

	$date = date("Y-m-d"); // Current date

	// Email Validation

	if($em == $em2) { // Triggers if both emails are same

		// Check if email is in valid format 

		if(filter_var($em, FILTER_VALIDATE_EMAIL)) { 

			$em = filter_var($em, FILTER_VALIDATE_EMAIL);

			// Check if email already exists 

			$e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

			//Count the number of rows returned

			$num_rows = mysqli_num_rows($e_check);

			if($num_rows > 0) {

				array_push($error_array, "Email already in use</br>");
			}

		}
		else {
			array_push($error_array, "Invalid email format</br>");
		}

	}
	else {
		array_push($error_array, "Emails doesn't match</br>");
	}

	// First Name length Validation


	if(strlen($fname) > 25 || strlen($fname) < 2) {

		array_push($error_array, "Your First Name should be in between 2-25 characters</br>");
	}

	// Second Name length Validation

	if(strlen($lname) > 25 || strlen($lname) < 2) {
		array_push($error_array,  "Your Last Name should be in between 2-25 characters</br>");
	}

	// Password validation

	if($password != $password2) {

		array_push($error_array,  "Passwords doesn't match</br>");
	}
	else {
		if(preg_match('/[^A-Za-z0-9]/', $password)) {

			array_push($error_array, "Your password should contain only letters and numbers</br>");
		}
	}

	// Password length Validation

	if(strlen($password > 30 || strlen($password) < 5)) {

		array_push($error_array, "Your Password should be in between 5-30 characters</br>");
	}
    
    // Triggers when no errors in $error_array

	if(empty($error_array)) {

		$password = md5($password); // Encrypt password before sending to database

		// Generate username by concatenating first name and last name

		$username = strtolower($fname . "_" . $lname);
		$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");

		$i = 0; 

		// If username exists add number to username

		while(mysqli_num_rows($check_username_query) != 0) {

			$i++; // Add 1 to i
			$username = $username . "_" . $i;
			$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
		}

		// Profile picture assignment randomly

		$rand = rand(1, 2); // Random number generation between 1 and 2

		if($rand == 1)
			$profile_pic = "assets/images/profile_pics/defaults/head_green_sea.png";
		else if($rand == 2)
			$profile_pic = "assets/images/profile_pics/defaults/head_wet_asphalt.png";

		// Inserting values into the database after all succesfull validations

		$query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");

		array_push($error_array, "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>");

		// Clear session variables 

		$_SESSION['reg_fname'] = "";
		$_SESSION['reg_lname'] = "";
		$_SESSION['reg_email'] = "";
		$_SESSION['reg_email2'] = "";
	}

}
?>