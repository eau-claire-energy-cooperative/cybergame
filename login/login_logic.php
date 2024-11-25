<?php
require('../private/initialize.php');
require('ldap_logic.php');
session_start();  // Start the session

//Check the entered username and password against expected
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    //Check if the username exists in active directory
    if (user_authorization($username, $password)) {
        $count = user_exists($db, $username);
        //Start the session
        startSession($username);

        if($count > 0){
            getUserFromDatabase($db, $username);
            header("Location: ../pick_passcode");
        }else{
            header("Location: ../registration_page");
        }
        exit();
    } else {
        // Store the error message in the session
        $_SESSION['error'] = "Invalid credentials!";

        // Redirect back to the login page
        header("Location: ../login");
    }
}
?>
