<?php
require('../private/initialize.php');
session_start();
// Get the raw POST data
$inputData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($inputData, true);

// Check if the alias is received
if (isset($data['alias'])) {
    $alias = $data['alias'];

    //update user alias
    $updateUserSQL = "UPDATE users SET alias = '" . $alias . "' WHERE user_id = " . $_SESSION['user_id'];
    $updateResult = mysqli_query($db, $updateUserSQL);

    writeToLog(get_user_name($db), "(" . $alias . ") has registered for Galactic Codebreaker");

    // For this example, we'll just echo the alias back as a response
    echo json_encode(['status' => 'success', 'alias' => $alias]);
} else {
    // Handle error if alias is not sent
    echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
}
?>
