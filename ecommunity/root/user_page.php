<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <?php
  $specific_user = getTheUser($conn);
    echo '<title>'.$specific_user[0]['name'].'\'s Dashboard</title>';
  ?>
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
  <?php
  include "nav_tag.php";

//if user not logged in, send them to login page
  if(!isset($_SESSION['id'])){
    header("Location:login_page.php");
  }
   ?>
  <div id="pageWrapper">
    <div id="whole_usersPage">
    <?php
      echo "<h2 class='header'>Hey, ".$specific_user[0]['name']."!</h2><br>";
    //messages depending on if they did their action yesterday and if they've done their action today
      if($specific_user[0]['completed_yesterday'] && !$specific_user[0]['completed_action']){
        echo '<h4 id="user_page_greeting">Great job completing your action yesterday! You  have a new one today!</h4>';
      } else if(!$specific_user[0]['completed_yesterday'] && !$specific_user[0]['completed_action']){
        echo '<h4 id="user_page_greeting">You have a new action today!</h4>';
      } else if($specific_user[0]['completed_action']){
        echo '<h4 id="user_page_greeting">Great job today!</h4>';
      } else{
        echo '<h4 id="user_page_greeting">You have a new action today!</h4>';
      }
     ?>
     <div id="users_action_container">
       <div id="users_action">
        <?php
          echo '<p id="action_text">'.$actions_array[$specific_user[0]['curr_action']].'</p>';
          echo '
          <br><form id="completed_action_form" method="POST" action="'.completedAction($conn).'">
          <button id="completed_action_button" type="submit" name="completed_action">Completed It</button>
          </form>
          ';
         ?>
       </div>
    </div>
     <div id="after_action">
     <?php
      $get_sql = "SELECT * FROM users WHERE id=".$_SESSION['id'].";";
      $get_result = mysqli_query($conn, $get_sql);
      $array_user = mysqli_fetch_all($get_result, MYSQLI_ASSOC);
      //different writings for if only 1 or 0 actions have been done
      if($array_user[0]["actions_completed"] > 1){
        echo '<h3 class="totaled_actions_h3">You have done '.$array_user[0]["actions_completed"].' actions for the environment.</h3>';
      } else if($array_user[0]["actions_completed"] == 1){
        echo '<h3 class="totaled_actions_h3">You have done '.$array_user[0]["actions_completed"].' action for the environment.</h3>';
      } else{
        echo '<h3 class="totaled_actions_h3">If you\'ve done your action for today, press \'Completed It\' and come back tomorrow when you complete your next action!</h3>';
      }
      //different writings for if only 1 or 0 actions have been done
      if(addAllActions($conn) > 1){
        echo '
          <h3 class="totaled_actions_h3">The entire ecommunity has done '.addAllActions($conn).' actions for the environment.</h3>
        ';
      } else if (addAllActions($conn) == 1){
        echo '
          <h3 class="totaled_actions_h3">The entire ecommunity has done '.addAllActions($conn).' action for the environment.</h3>
        ';
      } else{
        echo '
        <h3 class="totaled_actions_h3">Be the first in the ecommunity to complete an action!</h3>
        ';
      }
      ?>
      <h3 class="totaled_actions_h3">Keep up the good work!</h3>
    </div>
    </div>
  </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>

</body>

</html>
