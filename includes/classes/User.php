<?php

class User {
	private $user;
	private $con;

	// constructor

	public function __construct($con, $user){
		$this->con = $con;
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
		$this->user = mysqli_fetch_array($user_details_query);
	}

	// To get the username

	public function getUsername() {
		return $this->user['username'];
	}

	public function getNumberOfFriendRequests() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$username'");
		return mysqli_num_rows($query);
	}

	// To get the number of posts

	public function getNumPosts() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['num_posts'];
	}

	// To get the firstname and lastname

	public function getFirstAndLastName() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT first_name, last_name FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['first_name'] . " " . $row['last_name'];
	}

	// To get the profile_pic of user

	public function getProfilePic() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['profile_pic'];
	}

	// To get the friend array of user

	public function getFriendArray() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['friend_array'];
	}

	// To check whether user is closed or not

	public function isClosed() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT user_closed FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);

		if($row['user_closed'] == 'yes')
			return true;
		else 
			return false;
	}

	// To verify both are friends or to added_by = loggedin user in profile page newsfeed

	public function isFriend($username_to_check) {
		$usernameComma = "," . $username_to_check . ",";

		if((strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username'])) {
			return true;
		}
		else {
			return false;
		}
	}

	// To check for friend request receieved or not

	public function didReceiveRequest($user_from) {

		$user_to = $this->user['username'];

		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");

		if(mysqli_num_rows($check_request_query) > 0) {

			return true;
		}
		else {

			return false;
		}
	}

	// To check whether request is sent or not

	public function didSendRequest($user_to) {

		$user_from = $this->user['username'];

		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");

		if(mysqli_num_rows($check_request_query) > 0) {

			return true;
		}
		else {

			return false;
		}
	}

	// To Remove friend

	public function removeFriend($user_to_remove) {

		$logged_in_user = $this->user['username']; // To get the logged in username

		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_remove'"); // To get the friend of user_to_remove

		$row = mysqli_fetch_array($query);

		$friend_array_username = $row['friend_array']; // User_to_remove friend _array

		$new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_array']); // Replace with null in logged_in user's friend array by finding the user_to_remove substring

		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user'"); // Update friend array after replacing with null of logged_in user

		$new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username); // Same process as above but in user_to_remove friend_array

		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$user_to_remove'"); // Updating new friend array of user_to_remove
	}

	// To Send request

	public function sendRequest($user_to) {

		$user_from = $this->user['username']; // To get the username of logged_in user

		// Query to insert into friend_requests

		$query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES('', '$user_to', '$user_from')");
	}

	// To get the mutual friend count

	public function getMutualFriends($user_to_check) {
		$mutualFriends = 0;
		$user_array = $this->user['friend_array'];
		$user_array_explode = explode(",", $user_array);

		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_check'");
		$row = mysqli_fetch_array($query);
		$user_to_check_array = $row['friend_array'];
		$user_to_check_array_explode = explode(",", $user_to_check_array);

		foreach($user_array_explode as $i) {

			foreach($user_to_check_array_explode as $j) {

				if($i == $j && $i != "") {
					$mutualFriends++;
				}
			}
		}
		return $mutualFriends;

	}




}

?>