<nav id="nav_tag">
  <div id="main_links">
    <h3><a href="index.php">Home</a></h3>
    <h3><a href="about_us.php">About</a></h3>

  </div>
  <h3 id="ecommunity_title"><a href="index.php">Ecommunity</a></h3>

      <?php
      $specific_user = getTheUser($conn);
        if(!isset($_SESSION['id'])){
          echo '<h3 id="login_link"><a href="login_page.php">Login</a></h3>';
        } else if (isset($_SESSION['id'])){
          echo '
          <h3 id="hello_user">Hello, '.$specific_user[0]['name'].'</h3>
          <div id="user_links">
            <div id="user_links_content">
              <ul>
              <li>
                <a href="user_page.php">Dashboard</a>
              </li>
              ';
              if($specific_user[0]['admin'] == 1){
                echo '
                <li>
                  <a href="admin.php">Admin</a>
                </li>
                ';
              }
              echo '
              <li>
                <form method="POST" action="'.logOut().'">
                  <button type="submit" name="logout">Log Out</button>
                </form>
              </li>
              <li>
                <button id="delete_link">Delete Account</button>
              </li>
              </ul>
            </div>
          </div>
          <div id="delete_div">
            <div id="delete_div_content">
              <h3>Are you sure you want to delete your account?</h3>
              <form method="POST" action="'.deleteAccount($conn).'">
                <div id="cancel_delete" class="delete_confirmation">Cancel</div>
                <button class="delete_confirmation" type="submit" name="delete_account">Yes, Delete Account</button>
              </form>
            </div>
          </div>

          ';
        }
      ?>

</nav>
