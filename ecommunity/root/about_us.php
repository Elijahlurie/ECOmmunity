<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>About Us</title>
  <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
  <?php
    include "nav_tag.php";
   ?>
<div id="pageWrapper">

  <div id="about_us_text">
    <h1 class="header">About Us</h3>
    <p>When we say about us, you are part of the "us." We believe that pride after completing an action inspires people to become environmentailists</p>
    <p>We exist to end environmental apathy. You care about the environment, but how are you supposed to help such a big issue? You'll see that completing some simple action each day will cure you of the apathy.</p>
    <p>This website is simple for a reason. The goal is to recieve a simple text each day, and press completed action on here if u want.</p>
    <p id="quote">“You cannot get through a single day without having an impact on the world around you. What you do makes a difference and you have to decide what kind of a difference you want to make.”
—Jane Goodall</p>
  </div>
  <img id="earthrise" src="images/earthrise.jpg" width="50%" alt="Earth rising over moon horizon">
</div>

  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
</body>

</html>
