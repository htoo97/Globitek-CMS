<?php
  require_once('../private/initialize.php');

  // Set default values for all variables the page needs.
  $firstName = '';
  $lastName = '';
  $email = '';
  $userName = '';
  $errors = [];

  // if this is a POST request, process the form
  // Hint: private/functions.php can help
  // Confirm that POST values are present before accessing them.
  if (is_post_request()) {
    $firstName = isset($_POST['first_name']) ? h($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? h($_POST['last_name']) : '';
    $email = isset($_POST['e_mail']) ? h($_POST['e_mail']) : '';
    $userName = isset($_POST['user_name']) ? h($_POST['user_name']) : '';

    // Perform Validations
    // Hint: Write these in private/validation_functions.php

    // firstName
    if (is_blank($firstName)) {
        $errors[] = "First name cannot be blank.";
    }
    elseif (!has_length($firstName, ['min' => 2, 'max' => 255])) {
        $errors[] = "First name must be between 2 and 255 characters.";
    }
    elseif (preg_match('/\A[A-Za-z\s\-,\.\']+\Z/', $firstName) == 0) {
        $errors[] = "First name has invalid characters. Only letters, spaces, and symbols (-,.') are allowed.";
    }

    // lastName
    if (is_blank($lastName)) {
        $errors[] = "Last name cannot be blank.";
    }
    elseif (!has_length($lastName, ['min' => 2, 'max' => 255])) {
        $errors[] = "Last name must be between 2 and 255 characters.";
    }
    elseif (preg_match('/\A[A-Za-z\s\-,\.\']+\Z/', $lastName) == 0) {
        $errors[] = "Last name has invalid characters. Only letters, spaces, and symbols (-,.') are allowed.";
    }

    // email
    if (is_blank($email)) {
        $errors[] = "Email cannot be blank.";
    }
    elseif (!has_length($email, ['min' => 2, 'max' => 255])) {
        $errors[] = "Email must be between 2 and 255 characters.";
    }
    elseif (!has_valid_email_format($email)) {
        $errors[] = "Invalid email!";
    }
    elseif (preg_match('/\A[A-Za-z0-9_@\.]+\Z/', $email) == 0) {
        $errors[] = "Email has invalid characters. Only letters, numbers, and symbols (_@.) are allowed.";
    }

    //userName
    if (is_blank($userName)) {
        $errors[] = "Username cannot be blank.";
    }
    elseif (!has_length($userName, ['min' => 8, 'max' => 255])) {
        $errors[] = "Username must be at least 8 characters.";
    }
    elseif (preg_match('/\A[A-Za-z0-9_]+\Z/', $userName) == 0) {
        $errors[] = "Username has invalid characters. Only letters, numbers, and symbols (_) are allowed.";
    }

    // if there were no errors, submit data to database
    if (sizeof($errors) == 0) {
        $connection = db_connect();

        $firstName = db_escape($connection, $firstName);
        $lastName = db_escape($connection, $lastName);
        $email = db_escape($connection, $email);
        $userName = db_escape($connection, $userName);
        $date = db_escape($connection,date("Y-m-d H:i:s"));

        $userList = db_query($connection, "SELECT `username` from `users` WHERE `username` like '$userName'");
        if (db_num_rows($userList) > 0) {
            $errors[] = "Username already exists. Choose another.";
            db_free_result($userList);
        }
        else {
            $sql = "INSERT INTO user (first_name, last_name, email, username, created_at)
              VALUES ('$firstName', '$lastName', '$email', '$userName', '$date')";

            // For INSERT statments, $result is just true/false
            $result = db_query($db, $sql);
            if($result) {
                db_close($db);

            //   redirect user to success page
            redirect_to("./registration_success.php");
            }
            else {
                // The SQL INSERT statement failed.
                // Just show the error, not the form
                echo db_error($db);
                db_close($db);
                exit;
            }
        }
    }
  }

?>

<?php $page_title = 'Register'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<div id="main-content">
  <h1>Register</h1>
  <p>Register to become a Globitek Partner.</p>

  <?php
    // display any form errors here
    echo display_errors($errors);

  ?>

  <!-- TODO: HTML form goes here -->
  <html>
  <body>

  <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
  First name:<br>
  <input type="text" name="first_name" value="<?php echo $firstName; ?>" />
  <br>

  Last name:<br>
  <input type="text" name="last_name" value="<?php echo $lastName; ?>" />
  <br>

  Email:<br>
  <input type="text" name="e_mail" value="<?php echo $email; ?>" />
  <br>

  Username:<br>
  <input type="text" name="user_name" value="<?php echo $userName; ?>" />
  <br>
  <br>

  <input type="submit" name="submit" value="Submit">

  </form>

  </body>
  </html>

</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
