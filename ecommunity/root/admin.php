<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin</title>
  <link rel="stylesheet" href="styles/styles.css">

</head>

<body>
  <div id="pageWrapper">
    <?php
      include "nav_tag.php";
      if($specific_user[0]['admin'] != 1){
        header("Location: index.php");
      }
     ?>
    <div id="whole_admin_content">
      <h1 id="admin_header" class="header">Admin</h1>
      <h3 id="user_list_header">Users</h3>
      <div id="users_list">
        <?php
        $user_profiles_array = returnUsers($conn, $greetings_array, $actions_array);
          foreach($user_profiles_array as $index){
            echo $index;
          }
         ?>
      </div>
      <p><a href="http://localhost:8888/phpMyAdmin/?lang=en" target="_blank" rel="noopener noreferrer">Go to database</a></p>

    </div>
  </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
   <script>

      var user_message =  document.getElementById('user_message');
      var user_profiles = document.getElementsByClassName('user_profile');
      var gen_message_button = document.getElementsByClassName('gen_message_button');
      var texted = document.getElementsByClassName('been_texted');
      for(var i = 0; i<user_profiles.length; i++){
        if(texted[i].textContent != 0){
          user_profiles[i].style.backgroundColor = "#ed6666";
          gen_message_button[i].textContent = "Generate Message (this user has already been texted)";
        }
      }

    var user_message_x = document.getElementById('user_message_x');
      function closeMessage(){
        user_message.style.display = "none";
      };

        user_message_x.addEventListener("click", closeMessage);

   </script>
</body>
</html>
