<!DOCTYPE html>
<?php

$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>ECOmmunity</title>
  <link rel="stylesheet" href="styles/styles.css">

</head>

<body>
  <?php
  include "nav_tag.php";

   ?>
  <div id="pageWrapper">
    <div id="main_block">
      <h1 id="join_title">one easy eco action, every day.<br>
        it adds up.
      </h1><br>
      <?php
      if(!isset($_SESSION['id'])){
        echo '
        <div id="user_join_form">
          <form method="POST" action="'.addUsers($conn).'">
              <input  class="form_input" type="text" name="name" placeholder="First Name">
              <input  class="form_input" type="text" name="phone" placeholder="Phone Number">
            <button type="submit" name="user_submit">Sign Up</button>
          </form>
        </div><br>
        ';
     }
      ?>
      <h2 id="join_title_cont">we'll text you :)</h2>
    </div>

      <?php
        echo '
        <div id="explanation">
          <div id="explanation_content">
            <p id="exp_1">We text you one environmental action every day.</p>
            <p id="exp_2">Together, our actions make a huge difference!</p>
        ';
        //different writings for if only 1 or 0 actions have been done
        if(addAllActions($conn) > 1){
          echo '
            <p id="exp_3">The entire ecommunity has done '.addAllActions($conn).' actions for the environment.</p>
            </div>
            </div>
          ';
        } else if (addAllActions($conn) == 1){
          echo '
            <p id="exp_3">The entire ecommunity has done '.addAllActions($conn).' action for the environment.</p>
            </div>
            </div>
          ';
        } else{
          echo '
            <p id="exp_3">Let\'s start making a difference!</p>
            </div>
            </div>
          ';
        }

       ?>
  </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
   <script>
   var explanation = document.getElementById('explanation');
   var exp_3 = document.getElementById('exp_3');
   //if not on a mobile device have a background image
   if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
     console.log("mobile");
     explanation.style.backgroundImage = "none";
     explanation.style.color = "inherit";
     exp_3.style.float = "inherit";
     exp_3.style.top = "inherit";
   }else{
     console.log("not mobile");
   }
   </script>
</body>

</html>
