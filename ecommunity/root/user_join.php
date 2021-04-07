<?php
/*
key questions:
- how should i style user page to make users interact and want to come back?
- what should i put in my footer?
- should i have a way of remembering the specific actions a user has completed? con: less simplicity
- how do i account for different time zones (one user refreshes the page atfer midnight when it isnt midnight yet in a diff time zone)
- should i ask for their home town? - prob not bc its more about the texting and not being on the site.
    - ^would i ask for their location or to enter the name of their town? their zipcode?
    -maybe use their area code
- how do i send automated texts?
*/

//sends user info from user join form to the users database
function addUsers($connection){
if(isset($_POST['user_submit'])/* && $_POST['name'] != "" && $_POST['phone'] != ""*/):
    //get the id of the last user in the list and add 1 to get what the new user's id will be
    $login_sql = "SELECT * FROM users;";
    $login_result = mysqli_query($connection, $login_sql);
    $array_users = mysqli_fetch_all($login_result, MYSQLI_ASSOC);
    //give a randm number within length of actions array
    $actions_sql = "SELECT * FROM actions;";
    $actions_result = mysqli_query($connection, $actions_sql);
    $array_actions = mysqli_fetch_all($actions_result, MYSQLI_ASSOC);
    $rand = mt_rand(0,(count($array_actions)-1));
    //add user
    $no_space_name = str_replace(" ", "", $_POST['name']);
    $name = ucfirst(strtolower(filter_var($no_space_name, FILTER_SANITIZE_STRING)));
    //remove all special characters other than numbers from phone number
    $no_space_number = str_replace(" ", "", $_POST['phone']);
    $phone = preg_replace("/[^0-9,.]/", "", $no_space_number);
    $error_message = "";

    //check if phone number is the right length
    if(strlen($phone)>= 10 && strlen($phone)<= 15){
        //check if there is a duplicate phone number
        $i = 0;
        foreach($array_users as $user):
          if($phone == $user['phone']){
            $i+=1;
          }
        endforeach;
        if($i == 0){
          $sql = "INSERT INTO users (name, phone, texted, curr_action) VALUES ('$name','$phone',0,'$rand');";
          $result = mysqli_query($connection, $sql);
          //log in the new user so session id is the number right after the last user
          //and reload the page

          //****figure out how to add user, relad page, then log them in
          $login_result = mysqli_query($connection, $login_sql);
          $array_users = mysqli_fetch_all($login_result, MYSQLI_ASSOC);
          $count_users = count($array_users);
          echo $count_users;
          $new_session_id = $array_users[$count_users-1]['id'];
          $_SESSION['id'] = $new_session_id;
          $_SESSION['timestamp'] = time();
          header('Location: '.$_SERVER['REQUEST_URI']);
        } else{
          echo "<p id='error_message'>A user with this phone number already exists.</p>";
        }

    } else if(strlen($phone)== 7){
      echo "<p id='error_message'>Did you include your area code?</p>";
    }else{
    echo "<p id='error_message'>The format for the phone number entered is invalid.</p>";
    }
  endif;
};
function loginNewUsers(){
  echo "dog";
  $count_users = count($array_users);
  $new_session_id = $array_users[$count_users-1]['id'];
  $_SESSION['id'] = $new_session_id;
  $_SESSION['timestamp'] = time();
};
//puts all the possible greetings from the database in an array and returns the array
function getGreetings($connection){
  $greetings_sql = "SELECT * FROM messages;";
  $greetings_result = mysqli_query($connection, $greetings_sql);
  $array_greetings = mysqli_fetch_all($greetings_result, MYSQLI_ASSOC);

  $greetings_array = [];
  foreach($array_greetings as $greeting){
    $greetings_array[] = $greeting["greeting"];
  }
  return $greetings_array;
};
$greetings_array = getGreetings($conn);

//puts all the possible actions from the database in an array and returns the array
//I haven't yet differentiated between big and small actions
function getActions($connection){
  $actions_sql = "SELECT * FROM actions;";
  $actions_result = mysqli_query($connection, $actions_sql);
  $array_actions = mysqli_fetch_all($actions_result, MYSQLI_ASSOC);
  $new_array = [];
  foreach($array_actions as $action){
    $new_array[] = $action["action"];
  }
  return $new_array;
};
$actions_array = getActions($conn);

//gets all the users from the database and creates an array of divs showing the user's nme, their
//phone number, and a button to generate a message for them
function returnUsers($connection, $greetings_array, $actions_array){
  $sql = "SELECT * FROM users ORDER BY texted;";//add orderby time texted
  $result = mysqli_query($connection, $sql);
  $array_users = mysqli_fetch_all($result, MYSQLI_ASSOC);
  $new_array = [];
  foreach($array_users as $user){
    $id = $user["id"];
    $new_array[] = '
      <div class="user_profile">
        <h2>'.$user["name"].'</h2>
        <h3>'.$user["phone"].'</h3>
        <form method="GET" action="'.generateMessage($conn,$user,$greetings_array,$actions_array).'">
          <button class="gen_message_button" name="generate_message_'.$id.'">Generate Message</button>
        </form>
        <p class="been_texted">'.$user["texted"].'</p>
      </div>
    ';
  }
  return $new_array;
};

//if a user has been texted button is set when page reloads and it hasn't been
//pressed yet today, set texted time to time() for that user
$sql = "SELECT * FROM users;";
$result = mysqli_query($conn, $sql);
$array_users = mysqli_fetch_all($result, MYSQLI_ASSOC);
$new_array = [];
foreach($array_users as $user){
  $button_name = 'texted_'.$user["id"];
  if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST[$button_name]) && $user['texted'] == 0){
    $new_sql = "UPDATE users SET texted = ".time()." WHERE id=".$user['id'].";";
    $new_result = mysqli_query($conn, $new_sql);
    header('Location: '.$_SERVER['REQUEST_URI']);
  }
}

//generates a random message when a user is clicked on
function generateMessage($connection, $user, $greetings_array, $actions_array){
  $random_greeting_number = mt_rand(0,count($greetings_array)-1);
  $random_action_number = mt_rand(0,count($actions_array)-1);
  if(isset($_GET["generate_message_".$user["id"]])):
    $texted_button_name = 'texted_'.$user["id"];
    $message = $greetings_array[$random_greeting_number]." ".$user["name"]."!<br>Your eco action today is: ".$actions_array[$user['curr_action']];
    echo "
      <div id='user_message'>
      <button id='user_message_x'>Close</button>
      <br>
      <h3>".$message.".</h3>
      <form method='POST' action='admin.php'>
        <button type = 'submit' name='".$texted_button_name."'>Texted them!</button>
      </form>
      </div>

    ";
  endif;
};

//Login for users:
//adds 1 to $i for each user in database that the input doesn't match,
//if $i equals the total users in database(meaning input dosnt match any user),
//then outputs "user not found"
//if user found, if user is an admin go to admin page, otherwise go to user page
function loginUsers($connection){
  $sql = "SELECT * FROM users;";
  $result = mysqli_query($connection, $sql);
  $array_users = mysqli_fetch_all($result, MYSQLI_ASSOC);
  if(isset($_POST['login_submit'])){
    $i = 0;
    $no_space_name = str_replace(" ", "", $_POST['login_name']);
    $name = strtolower(filter_var($no_space_name, FILTER_SANITIZE_STRING));
    $no_space_number = str_replace(" ", "", $_POST['login_phone']);
    $phone = preg_replace("/[^0-9,.]/", "", $no_space_number);
    foreach($array_users as $user):
      if(strtolower($user['name']) == $name && $user['phone'] == $phone){
          $_SESSION['id'] = $user['id'];
          $_SESSION['timestamp'] = time();
          if($user['admin'] != 1){
            header("Location: user_page.php");
          echo " not admin ";
          } else if($user['admin'] == 1){
            header("Location: admin.php");
          }
      } else{
        $i += 1;
      }
    endforeach;
    if($i === count($array_users)){
      echo "user not found";
    }
  }
};

//when logout button is pressed end the session and reload the page
function logOut(){
  if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header('Location: index.php');
  }
};

//when delete_account button is pressed log out, delete account, go to home page
function deleteAccount($connection){
  //i should add an "are you sure" message

  if(isset($_POST['delete_account'])){
    $sql = "DELETE FROM users WHERE id = ".$_SESSION['id'].";";
    session_unset();
    session_destroy();
    $result = mysqli_query($connection, $sql);
    header('Location: index.php');
  }
};

//Get the profile of the logged in user
function getTheUser($connection){
  $sql = "SELECT * FROM users WHERE id=".$_SESSION['id'].";";
  $result = mysqli_query($connection, $sql);
  $array_user = mysqli_fetch_all($result, MYSQLI_ASSOC);
  return $array_user;
};

//if logged in user doesn't exist, end session and go to home page
//adds 1 to $i for each user in database that doesn't match the current session id
//if $i equals the total users in database(meaning session id dosnt match any user),
//then ends current session and goes to home page

function logOutDeletedUsers($connection){
  $sql = "SELECT * FROM users;";
  $result = mysqli_query($connection, $sql);
  $array_users = mysqli_fetch_all($result, MYSQLI_ASSOC);
  if($_SESSION['id'] != ""){
    $i = 0;
    foreach($array_users as $user):
      if($user['id'] != $_SESSION['id']){
        $i += 1;
      }
    endforeach;
    if($i == count($array_users)):
      session_unset();
      session_destroy();
      header('Location: index.php');
    endif;
  }
};
logOutDeletedUsers($conn);

//if user clicks check add 1 to their actions_completed in database and disable the completed action button
function completedAction($connection){
  $get_sql = "SELECT * FROM users WHERE id=".$_SESSION['id'].";";
  $get_result = mysqli_query($connection, $get_sql);
  $array_user = mysqli_fetch_all($get_result, MYSQLI_ASSOC);
  $new_actions_completed = $array_user[0]['actions_completed'] + 1;
  if(isset($_POST['completed_action']) && !$array_user[0]['completed_action']){
    $sql = "UPDATE users SET actions_completed = $new_actions_completed WHERE id = ".$_SESSION['id'].";";
    $result = mysqli_query($connection, $sql);
    //update the main table total_actions of the whole community
    $old_actions_sql = "SELECT * FROM main WHERE id=1;";
    $old_actions_result = mysqli_query($connection, $old_actions_sql);
    $array_main = mysqli_fetch_all($old_actions_result, MYSQLI_ASSOC);
    $new_total_actions = $array_main[0]['total_actions'] + 1;
    $total_actions_sql = "UPDATE main SET total_actions = $new_total_actions WHERE id = 1;";
    $total_actions_result = mysqli_query($connection, $total_actions_sql);
    //set completed_action to true
    $completed_action_sql = "UPDATE users SET completed_action = TRUE WHERE id=".$_SESSION['id'].";";
    $completed_action_result = mysqli_query($connection, $completed_action_sql);
    header('Location: '.$_SERVER['REQUEST_URI']);
  } else if(isset($_POST['completed_action']) && $array_user[0]['completed_action']){
    echo "You already completed the action for today.";
  }
};

//Adds the actions of every user to see how many total the community has done
function addAllActions($connection){
  $sql = "SELECT * FROM main WHERE id=1;";
  $result = mysqli_query($connection, $sql);
  $array_main = mysqli_fetch_all($result, MYSQLI_ASSOC);
  return $array_main[0]['total_actions'];
};

//check if day has changed and assign new random actions if it has
function checkDay($connection){
//get current seconds since jan 1 1970 GMT time zone and convert to nearest whole days.
  $curr_time = time();
  $days = ($curr_time/86400) - ($curr_time%86400)/86400;
//get array of users from database
  $users_sql = "SELECT * FROM users;";
  $users_result = mysqli_query($connection, $users_sql);
  $array_users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);
//get array of actions from database
  $actions_sql = "SELECT * FROM actions;";
  $actions_result = mysqli_query($connection, $actions_sql);
  $array_actions = mysqli_fetch_all($actions_result, MYSQLI_ASSOC);
//get the value for yesterday from database
  $sql = "SELECT * FROM main;";
  $result = mysqli_query($connection, $sql);
  $array_main = mysqli_fetch_all($result, MYSQLI_ASSOC);
//if today is one more day than the value for the day in yesterday, add 1 to yesterday
//and give each user a new random # for their action
  if($days > $array_main[0]['yesterday']):
    $day_sql = "UPDATE main SET yesterday = ".$days.";";
    $day_result = mysqli_query($connection, $day_sql);
    foreach($array_users as $user):
      do{
        $rand = mt_rand(0,(count($array_actions)-1));
      }while($rand == $user['curr_action']);
      $user_action_sql = "UPDATE users SET curr_action = ".$rand." WHERE id=".$user['id'].";";
      $user_action_result = mysqli_query($connection, $user_action_sql);
      $user_texted_sql = "UPDATE users SET texted = 0 WHERE id=".$user['id'].";";
      $user_texted_result = mysqli_query($connection, $user_texted_sql);
      //see if user completed the action yesterday
      $completed_yesterday = $user['completed_action'];
      //set new completed yesterday for user accordingly
      $completed_yesterday_sql = "UPDATE users SET completed_yesterday = ".$completed_yesterday." WHERE id=".$user['id'].";";
      $completed_yesterday_result = mysqli_query($connection, $completed_yesterday_sql);
      //set completed_action to false
      $completed_action_sql = "UPDATE users SET completed_action = FALSE WHERE id=".$user['id'].";";
      $completed_action_result = mysqli_query($connection, $completed_action_sql);
    endforeach;
  endif;
};
checkDay($conn);

//check for 1hr of inactivity, if so then logout, if page reloaded with time to spare,
//then reset the 'timer'
if(isset($_SESSION['id'])):
  if(time() - $_SESSION['timestamp'] > 3600){
    session_unset();
    session_destroy();
    header('Location: '.$_SERVER['REQUEST_URI']);
  } else{
    $_SESSION['timestamp'] = time();
  }
endif;
