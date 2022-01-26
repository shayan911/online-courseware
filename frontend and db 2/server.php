<?php
//session temporarily holds data until session ends
session_start();

// initializing variables

$firstname = "";
$lastname    = "";
$username = "";
$phonenumber = "";
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'usersinfo');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $phonenumber = mysqli_real_escape_string($db, $_POST['phonenumber']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM customer WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  //mysqli_fetch_assoc returns one array with only name key (not numeric key) stored in user
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    //selects attribute username from array user having multiple attributes(columns) for verification
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    //selects attribute email from array user having multiple attributes(columns) for verification

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO customer (firstname, lastname, username, phonenumber, email, password) 
  			  VALUES( '$firstname', '$lastname', '$username', '$phonenumber', '$email', '$password')";
  	mysqli_query($db, $query);



  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM customer WHERE username='$username' AND password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index.php');
  	}else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }
}

?>