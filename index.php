<?php 
include("includes/header.php"); // To include header.php file


if(isset($_POST['post'])){ // Creates instance of Post class when post button is triggered
	$post = new Post($con, $userLoggedIn);
	$post->submitPost($_POST['post_text'], 'none');
}


 ?>

    <!-- Index page user details section -->

	<div class="user_details column">

		<a href="<?php echo $userLoggedIn; ?>">  <img src="<?php echo $user['profile_pic']; ?>"> </a>

		<div class="user_details_left_right">

			<a href="<?php echo $userLoggedIn; ?>">
				
			<?php 
			echo $user['first_name'] . " " . $user['last_name'];

			 ?>
			</a>
			<br>
			<?php echo "Posts: " . $user['num_posts']. "<br>"; 
			echo "Likes: " . $user['num_likes'];

			?>
		</div>

	</div>



	<!-- Index page main_column section -->

	<div class="main_column column">

		<!-- Post form section index page -->

		<form class="post_form" action="index.php" method="POST">

			<textarea name="post_text" id="post_text" placeholder="What's on your mind?"></textarea>
			<input type="submit" name="post" id="post_button" value="Post">
			<hr>

		</form>
        
        <!-- loading gif show while processing ajax request -->

		<div class="posts_area"></div> 
		<img id="loading" src="assets/images/icons/loading.gif">


	</div>

	

	<div class="user_details column">

		<img src = "assets/images/logo/buddy_logo.png" alt="logo">

	</div> 


     <!-- Ajax request to limit the posts in index page newsfeed -->

	<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	$(document).ready(function() {

		$('#loading').show();

		//Original ajax request for loading first posts 
		$.ajax({
			url: "includes/handlers/ajax_load_posts.php", // url
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn, // request
			cache:false,

			success: function(data) {
				$('#loading').hide();
				$('.posts_area').html(data);
			}
		});

		$(window).scroll(function() {
			var height = $('.posts_area').height(); //Div containing posts
			var scroll_top = $(this).scrollTop();
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();

			if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
				$('#loading').show();

				var ajaxReq = $.ajax({
					url: "includes/handlers/ajax_load_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache:false,

					success: function(response) {
						$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
						$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

						$('#loading').hide();
						$('.posts_area').append(response);
					}
				});

			} //End if 

			return false;

		}); //End (window).scroll(function())


	});

	</script>




	</div>
</body>
</html>