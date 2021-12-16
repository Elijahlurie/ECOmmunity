
			<!DOCTYPE html>
			<?php
			$time = date_default_timezone_set("America/Los_Angeles");
			include "../connection.php";
			include "../user_join.php";

			//define $path variable so links inside nav tag and footer still point to the right page even though this file is in a folder
			$path = "../";
			?>
			<html>
			<head>
				<meta charset="utf-8">
				<?php echo "<title>!!Hello</title>";?>
				<meta name="viewport" content="width=device-width,initial-scale=1"/>
				<link rel="stylesheet" href="../styles/styles.css">

			</head>
			<body>
				<?php
					include "../nav_tag.php";
				?>
				<div id="pageWrapper">
					<div id="blog_post_page">
						<p><a href="../blog.php"><--Back</a></p>
						<?php
							echo "
								<h1>Title: !!Hello</h1>
								<p>Author: Joe</p>
								<p>Published: 1639251800</p>
								<p>Category: Action of the Day</p>
								<p>Content: waabababab do</p>
							";
						?>
					</div>
				</div>
				<?php
					include "../footer.php";
				 ?>
				 <script src="../scripts/scripts.js"></script>
			</body>
			</html>
