<?php
class Post {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	// To submit the post which was posted in index page

	public function submitPost($body, $user_to) {

		$body = strip_tags($body); // Removes html tags 
		$body = mysqli_real_escape_string($this->con, $body); // Which escapes special characters in the body
		$check_empty = preg_replace('/\s+/', '', $body); // Deletes all spaces 
      
		if($check_empty != "") {


			// Current date and time

			$date_added = date("Y-m-d H:i:s");

			// Get username

			$added_by = $this->user_obj->getUsername();

			// If user is on own profile, user_to is 'none'

			if($user_to == $added_by)
				$user_to = "none";

			// Insert post 

			$query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')");

			$returned_id = mysqli_insert_id($this->con); // which returns the id of post

			// Update post count for user 

			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");


			

		}
	}

	
	// To load posts in index page

	public function loadPostsFriends($data, $limit) {

		$page = $data['page'];

		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;


		$str = ""; // String to return 

		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; // Number of results checked 
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				// Prepare user_to string so it can be included even if not posted to a user

				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				// Check if user who posted, has their account closed

				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by)){ // Checks whether added_by is friend of userLOGGEDin or not

					if($num_iterations++ < $start)
						continue; 


					// Once 10 posts have been loaded, break

					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					// Display's delete button when userLoggedIn equals to added_by

					//if($userLoggedIn == $added_by)
						//$delete_button = "<button class='delete_button btn-danger' id='post$id'>Delete</button>";
					//else 
						//$delete_button = "";

					// Query to get added_by user's firstname lastname and profile pic

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");

					$user_row = mysqli_fetch_array($user_details_query);

					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];

					// Timeframe

					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); // Time of post
					$end_date = new DateTime($date_time_now); // Current time
					$interval = $start_date->diff($end_date); // Difference between dates 

					if($interval->y >= 1) {

						if($interval == 1)
							$time_message = $interval->y . " year ago"; // 1 year ago
						else 
							$time_message = $interval->y . " year's ago"; // 1+ year ago
					}
					else if ($interval->m >= 1) {

						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " day ago";
						}
						else {
							$days = $interval->d . " day's ago";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " month". $days;
						}
						else {
							$time_message = $interval->m . " month's". $days;
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Yesterday";
						}
						else {
							$time_message = $interval->d . " day's ago";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " hour ago";
						}
						else {
							$time_message = $interval->h . " hour's ago";
						}
					}
					else if($interval->i >= 1) {
						if($interval->i == 1) {
							$time_message = $interval->i . " minute ago";
						}
						else {
							$time_message = $interval->i . " minutes' ago";
						}
					}
					else {
						if($interval->s < 30) {
							$time_message = "Just now";
						}
						else {
							$time_message = $interval->s . " second's ago";
						}
					}

					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class = 'post_main_frame' style='margin-left: 8px;'>

									<div class='posted_by' style='color:#ACACAC;'>
										<a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
										$delete_button
									</div>
									<div id='post_body'>
										<p style='font-size: 27px; line-height: 30px; margin-top: 10px;'>$body<p>
										<br>
										<br>
										<br>
									</div>
								</div>

								<div class='newsfeedPostOptions'>
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>
								

							</div>
							<hr>";
				}

				?>

				<!-- Triggers when delete post is pressed using post_id -->



					<script>

							$(document).ready(function() {

								$('#post<?php echo $id; ?>').on('click', function() {

									bootbox.confirm("Are you sure you want to delete this post?", function(result) {

										$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

										if(result)
											location.reload();

									});
								});


							});

					</script>

			  

				<?php

			} // End while loop

			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		}

		echo $str;
	}


	public function loadProfilePosts($data, $limit) {

		$page = $data['page']; 
		$profileUser = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser')  ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; // Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];


					if($num_iterations++ < $start)
						continue; 


					// Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					//if($userLoggedIn == $added_by)

						//$delete_button = "<button class='delete_button btn-danger' id='post$id'>Delete</button>";
					//else 
						//$delete_button = "";


					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];




					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else 
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
					else if ($interval->m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " day ago";
						}
						else {
							$days = $interval->d . " days ago";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " month". $days;
						}
						else {
							$time_message = $interval->m . " months". $days;
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Yesterday";
						}
						else {
							$time_message = $interval->d . " days ago";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " hour ago";
						}
						else {
							$time_message = $interval->h . " hours ago";
						}
					}
					else if($interval->i >= 1) {
						if($interval->i == 1) {
							$time_message = $interval->i . " minute ago";
						}
						else {
							$time_message = $interval->i . " minutes ago";
						}
					}
					else {
						if($interval->s < 30) {
							$time_message = "Just now";
						}
						else {
							$time_message = $interval->s . " seconds ago";
						}
					}

					$str .= "<div class='status_post' onClick='javascript:toggle$id()'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' width='50'>
								</div>

								<div class='posted_by' style='color:#ACACAC;'>
									<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
									$delete_button
								</div>
								<div id='post_body'>
									<p style='font-size: 27px; line-height: 30px; margin-top: 10px;'>$body<p>
									<br>
									<br>
									<br>
								</div>

								<div class='newsfeedPostOptions'>
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<hr>";

				?>
				<!--<script>

					$(document).ready(function() {



						$('#post<?php echo $id; ?>').on('click', function() {

							

							bootbox.confirm("Are you sure you want to delete this post??", function(result) {

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

								if(result)
									location.reload();

							});
						});


					});

				</script> -->
				<?php

			} //End while loop

			if($count > $limit) 
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
			else 
				$str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
		}

		echo $str;


	}



}

?>