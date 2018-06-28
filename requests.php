<?php
include("includes/header.php"); // To include header.php file 
?>

<!-- Friend Request's Page -->

<div class="main_column column" id="main_column">

	<h2> Friend Requests </h2>

	<hr></hr>

	<?php  

	$query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'"); // Query to get freind_request's where user_to equals to loggedInUSER

	if(mysqli_num_rows($query) == 0) // If no rows in friend_requests table

		echo "You have no friend requests at this time!";
	else {

		while($row = mysqli_fetch_array($query)) {

			$user_from = $row['user_from']; // Request from

			$user_from_obj = new User($con, $user_from);

			echo $user_from_obj->getFirstAndLastName() . " sent you a friend request!";

			$user_from_friend_array = $user_from_obj->getFriendArray();

			// Triggers when accept_request is clicked

			if(isset($_POST['accept_request' . $user_from ])) {

				$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'"); // Query to update friend_array in userLoggedIn using concatenating

				$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'"); // Query to update friend_array in user_from using concatenating

				$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'"); // Delete's row from friend_request's table

				echo "You are now friends!";

				header("Location: requests.php");
			}

			// Triggers when ignore_request is clicked

			if(isset($_POST['ignore_request' . $user_from ])) {

				$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'"); // Delete's from friend_request's table

				echo "Request ignored!";

				header("Location: requests.php");
			}

			?>

			<!-- Form to handle friend request's -->

			<form action="requests.php" method="POST">

				<input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept">

				<input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Ignore">
				<hr></hr>

			</form>

			<?php


		}

	}

	?>


</div>