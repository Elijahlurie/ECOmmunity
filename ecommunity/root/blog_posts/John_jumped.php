
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
					<?php echo "<title>John jumped</title>";?>
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
									<h1>Title: John jumped</h1>
									<p>Author: Elijah</p>
									<p>Published: 1639469402</p>
									<p>Category: Editorial</p>
									<p>Content: <p>Here is the content.</p>
<p>Let's try a list:</p>
<ul class="tox-checklist">
<li>List item here</li>
</ul>
<ul class="tox-checklist">
<li>woah here too??</li>
</ul>
<ul>
<li><strong><span style="font-family: arial, helvetica, sans-serif; color: #e74c3c; font-size: 12pt;">dfdfsdf</span></strong></li>
</ul></p>
						</div>
					</div>
					<?php
						include "../footer.php";
					 ?>
					 <script src="../scripts/scripts.js"></script>
				</body>
				</html>
				