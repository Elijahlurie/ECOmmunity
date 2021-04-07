<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
  <?php


  //if user is already logged in, send them to user page
    if(isset($_SESSION['id'])){
      header("Location:user_page.php");
    }

     include "nav_tag.php";
    ?>

  <div id="pageWrapper">
    <div id="whole_usersPage">
      <h2 id="login_title">Login</h2>
      <?php
      echo '
      <div id="user_login_form">
        <form method="POST" action="'.loginUsers($conn).'">
            <input class="form_input" type="text" name="login_name" placeholder="First Name">
            <input  class="form_input" type="text" name="login_phone" placeholder="Phone Number">
          <button type="submit" name="login_submit">Login</button>
        </form>
      </div>
      ';
      ?>
    </div>
  </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
</body>

</html>
