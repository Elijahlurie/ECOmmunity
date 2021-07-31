<?php
/*
key questions:

- should i let them choose which time to be texted at? >> yes but wait until i know how often the site will be refreshed bc they might gt texted later than they say and get annoyed
- will the site be available outside the US?
  - make users put in the +1 or other phone start once i know
- do i need a terms and conditions page?

things to do:
- make it clearer how to sign up - sign up should be bigger than login
  - add a 'not signned up?' thing on log in page
- figure out how to account for daylight savings time
- on the edit profile page make the timezone dropdown default to the user's timezone not automatically pacific
- add error message for if they have javascript disabled
- put actual content in the about us page (problem we re fixing, what inspired us, how we come up with the actions)
- curate an effective list of actions
  - try to make the list good from the start bc if an action gets deleted so its id is skipped in the table, the missing id is given as an action number to a user
- add some other greetings to messages table
- when ready to upload site actvate the texting function
- style the special action of the day div >> maybe include in first 'update' of the site
- survey ppl to give them actions more relevant to their lifestyles
*/
$sql = "SELECT * FROM users;";
$result = mysqli_query($conn, $sql);
$array_users = mysqli_fetch_all($result, MYSQLI_ASSOC);

//sends user info from user join form to the users database
function addUsers($connection){
  if(isset($_POST['user_submit'])):
    //save the inputs in a session so if their inputs were wrong the computer can remember wht they had so they dot start from scratch
    $_SESSION['input_name'] = $_POST['name'];
    $_SESSION['input_phone'] = $_POST['phone'];
    $_SESSION['input_timezone'] = $_POST['timezone_offset'];
    $_SESSION['input_timestamp'] = time();

    if($_POST['name'] != "" && $_POST['phone'] != ""){
      //get an array of the current list of users
      $login_sql = "SELECT * FROM users;";
      $login_result = mysqli_query($connection, $login_sql);
      $users_array = mysqli_fetch_all($login_result, MYSQLI_ASSOC);
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

      $timezone = $_POST['timezone_offset'];
      //check if phone number is the right length
      if(strlen($phone)== 10){
          //check if there is a duplicate phone number
          $i = 0;
          foreach($users_array as $user):
            if($phone == $user['phone']){
              $i+=1;
            }
          endforeach;
          if($i == 0){
            $sql = "INSERT INTO users (name, phone, texted, curr_action, timezone) VALUES ('$name','$phone',0,'$rand', '$timezone');";
            $result = mysqli_query($connection, $sql);
            //log in the new user so session id is the number right after the last user
            //and reload the page

            //run this sql again to get the updated list of users
            $login_result = mysqli_query($connection, $login_sql);
            $users_array = mysqli_fetch_all($login_result, MYSQLI_ASSOC);

            $count_users = count($users_array);
            $new_session_id = $users_array[$count_users-1]['id'];
            $_SESSION['id'] = $new_session_id;
            $_SESSION['timestamp'] = time();
            //reset the session values for what they inputted because it's not needed anymore
            $_SESSION['input_name'] = "First Name";
            $_SESSION['input_phone'] = "(xxx) xxx-xxxx";
            $_SESSION['input_timezone'] = "";

            header('Location: user_page.php');
          } else{
            echo "<p id='signup_error'>A user with this phone number already exists.</p>";
          }

      } else if(strlen($phone)== 7){
        echo "<p id='signup_error'>Did you include your area code?</p>";
      }else{
      echo "<p id='signup_error'>The format for the phone number entered is invalid.</p>";
      }
    } else{
      echo "<p id='signup_error'>An input is still empty.</p>";
    }
  endif;
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
        <h4>'.$user["name"].'</h4>
        <p class="user_profile_number">'.$user["phone"].'</p>
        <p class="been_texted">'.$user["texted"].'</p>
      </div>
    ';
  }
  return $new_array;
};

//if a user has been texted button is set when page reloads and it hasn't been
//pressed yet today, set texted time to time() for that user
foreach($array_users as $user){
  $button_name = 'texted_'.$user["id"];
  if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST[$button_name]) && $user['texted'] == 0){
    $new_sql = "UPDATE users SET texted = ".time()." WHERE id=".$user['id'].";";
    $new_result = mysqli_query($conn, $new_sql);
    header('Location: '.$_SERVER['REQUEST_URI']);
  }
}


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
          } else if($user['admin'] == 1){
            header("Location: admin.php");
          }
      } else{
        $i += 1;
      }
    endforeach;
    if($i === count($array_users)){
      echo "<p id='login_error'>User not found</p>";
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

//lets users change their profiles
function editProfile($connection){
  if(isset($_POST['edit_profile_submit'])):
    if($_POST['edit_name'] != "" && $_POST['edit_phone'] != ""){
      $no_space_name = str_replace(" ", "", $_POST['edit_name']);
      $name = ucfirst(strtolower(filter_var($no_space_name, FILTER_SANITIZE_STRING)));
      //remove all special characters other than numbers from phone number
      $no_space_number = str_replace(" ", "", $_POST['edit_phone']);
      $phone = preg_replace("/[^0-9,.]/", "", $no_space_number);

      $timezone = $_POST['edit_timezone_offset'];
      //check if phone number is the right length
      if(strlen($phone)== 10){
          //check if there is a duplicate phone number

          //get an array of the current list of users
          $users_sql = "SELECT * FROM users;";
          $users_result = mysqli_query($connection, $users_sql);
          $users_array = mysqli_fetch_all($users_result, MYSQLI_ASSOC);

          $i = 0;
          foreach($users_array as $user):
            //add one to the counter if the number entered matches another user's number who isn't the logged in user
          if($phone == $user['phone'] && $user['id'] != $_SESSION['id']){
              $i+=1;
            }
          endforeach;
          if($i == 0){
            if($name != $specific_user['name']){
              $edit_name_sql = "UPDATE users SET name = '".$name."' WHERE id = ".$_SESSION['id'].";";
              $edit_name_result = mysqli_query($connection, $edit_name_sql);
            }
            if($phone != $specific_user['phone']){
              $edit_phone_sql = "UPDATE users SET phone = '".$phone."' WHERE id = ".$_SESSION['id'].";";
              $edit_phone_result = mysqli_query($connection, $edit_phone_sql);
            }
            if($timezone != $specific_user['timezone']){
              $edit_timezone_sql = "UPDATE users SET timezone = ".$timezone." WHERE id = ".$_SESSION['id'].";";
              $edit_timezone_result = mysqli_query($connection, $edit_timezone_sql);
            }

            header('Location: '.$_SERVER['REQUEST_URI']);
          } else{
            echo "<p id='edit_profile_error'>Another user with this phone number already exists.</p>";
          }

      } else if(strlen($phone)== 7){
        echo "<p id='edit_profile_error'>Did you include your area code?</p>";
      }else{
      echo "<p id='edit_profile_error'>The format for the phone number entered is invalid.</p>";
      }
    } else{
      echo "<p id='edit_profile_error'>An input is still empty.</p>";
    }
  endif;
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
  return $array_user[0];
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
//get array of users from database
  $users_sql = "SELECT * FROM users;";
  $users_result = mysqli_query($connection, $users_sql);
  $array_users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);
//get array of actions from database
  $actions_sql = "SELECT * FROM actions;";
  $actions_result = mysqli_query($connection, $actions_sql);
  $array_actions = mysqli_fetch_all($actions_result, MYSQLI_ASSOC);

//if today is over value for the day in yesterday, update yesterday
//and give each user a new random # for their action
  foreach($array_users as $user):
    //get current seconds since jan 1 1970 in the users time zone and convert to nearest whole days.
    $user_curr_time = time() + 3600 * ($user['timezone']/100);
    $user_days = ($user_curr_time/86400) - ($user_curr_time%86400)/86400;
    //if the number of days since 1970 for the users timezone is greater than the value for yesterday for that user, update their actions and set days as the new value for yesterday
    if($user_days > $user['yesterday']):
      $day_sql = "UPDATE users SET yesterday = ".$user_days." WHERE id = ".$user['id'].";";
      $day_result = mysqli_query($connection, $day_sql);
      //get a random action that isn't the one they just had
        do{
          $rand = mt_rand(0,(count($array_actions)-1));
        }while($rand == $user['curr_action']);

        $user_action_sql = "UPDATE users SET curr_action = ".$rand." WHERE id=".$user['id'].";";
        $user_action_result = mysqli_query($connection, $user_action_sql);
        $user_texted_sql = "UPDATE users SET texted = 0 WHERE id=".$user['id'].";";
        $user_texted_result = mysqli_query($connection, $user_texted_sql);
        //see if user completed the action that day
        $completed_yesterday = $user['completed_action'];
        //set new completed yesterday for user accordingly
        $completed_yesterday_sql = "UPDATE users SET completed_yesterday = ".$completed_yesterday." WHERE id=".$user['id'].";";
        $completed_yesterday_result = mysqli_query($connection, $completed_yesterday_sql);
        //set completed_action to false
        $completed_action_sql = "UPDATE users SET completed_action = FALSE WHERE id=".$user['id'].";";
        $completed_action_result = mysqli_query($connection, $completed_action_sql);
      endif;
    endforeach;
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
