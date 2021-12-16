
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
					<?php echo "<title>The title</title>";?>
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
									<h1>Title: The title</h1>
									<p>Author: Elijah</p>
									<p>Published: 1639469584</p>
									<p>Category: Action of the Day</p>
									<p>Write content here</p>
<ul class="tox-checklist">
<li>jlkjlk</li>
<li>kkkj</li>
<li><strong><span style="font-family: arial, helvetica, sans-serif; color: #e74c3c; font-size: 12pt;">jhkjhkj</span></strong></li>
</ul>
						</div>
					</div>
					<?php
						include "../footer.php";
					 ?>
					 <script src="../scripts/scripts.js"></script>
				</body>
				</html>
				