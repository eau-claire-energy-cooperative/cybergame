<?php

// Require database credentials to allow for easy logins
require_once('credentials.php');

// Get connected to the database based on the login infomration
function db_connect(){
  $connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
  confirm_db_connect();
  return $connection;
}

// Disconnect from the database when finished using it
function db_disconnect($connection){
  if(isset($connection)){
    mysqli_close($connection);
  }
}

// Confirm whether the database is connected or not
function confirm_db_connect(){
  if(mysqli_connect_errno()){
    $msg = "Database connection failed: ";
    $msg .= mysqli_connect_error();
    $msg .= " (" . mysqli_connect_errno() . ")";
    exit($msg);
  }
}

// Get the pattern for the user that is logged in
function get_user_id($connection, $username){
  //SQL Query to get information from the database table
  $getPatternSQL = "SELECT user_id FROM users WHERE uname='" . $username . "'";

  //Get the passcode infomration
  $pattern_set = mysqli_query($connection, $getPatternSQL);
  $passcode_info = mysqli_fetch_assoc($pattern_set);

  $userID = $passcode_info['user_id'];

  return $userID;
}

// Check whether the pattern has been solved or not
function pattern_solved($connection, $patternID){
    //SQL Query to get the list of patterns with a specific id
    $patternSQL = "SELECT * FROM guesses WHERE correct_pattern = " . $patternID;
    $patternSQL .= " AND user_guessing = " . $_SESSION['user_id'];
    $patternSQL .= " AND correct_guess = 1";

    //Get the passcode infomration
    $pattern_set = mysqli_query($connection, $patternSQL);
    $count = mysqli_num_rows($pattern_set);

    return $count;
}

// Check whether the pattern id exists in the Database
function pattern_exists($connection, $patternID){
    //SQL Query to get the list of patterns with a specific id
    $patternSQL = "SELECT * FROM patterns WHERE pattern_id = '" . $patternID . "'";

    //Get the passcode infomration
    $pattern_set = mysqli_query($connection, $patternSQL);
    $count = mysqli_num_rows($pattern_set);

    return $count;
}

// Check whether the pattern id exists in the Database
function user_exists($connection, $userName){
    //SQL Query to get the list of patterns with a specific id
    $patternSQL = "SELECT * FROM users WHERE uname = '" . $userName . "'";

    //Get the passcode infomration
    $pattern_set = mysqli_query($connection, $patternSQL);
    $count = mysqli_num_rows($pattern_set);

    return $count;
}

// Get the pattern for the user that is logged in
function get_user_pattern($connection){
  //SQL Query to get information from the database table
  $getPatternSQL = "SELECT user_pattern FROM users WHERE user_id=" . $_SESSION['user_id'];

  //Get the passcode infomration
  $pattern_set = mysqli_query($connection, $getPatternSQL);
  $passcode_info = mysqli_fetch_assoc($pattern_set);

  $patternID = $passcode_info['user_pattern'];

  return $patternID;
}

// Get the pattern difficulty for the user
function get_user_pattern_difficulty($connection){
  $getDifficultySQL = "SELECT difficulty FROM patterns WHERE pattern_id=" . get_user_pattern($connection);

  //get the difficulty information
  $difficulty_set = mysqli_query($connection, $getDifficultySQL);
  $difficulty_info = mysqli_fetch_assoc($difficulty_set);

  $difficulty = $difficulty_info['difficulty'];

  return $difficulty;
}

// SQL Query to show data based on a specific id (used for the view_passcode page)
function view_passcode_queries($connection){
  //SQL Query to get information from the database table
  $patternSQL = "SELECT * FROM patterns WHERE pattern_id=". get_user_pattern($connection);

  //Get the passcode infomration
  $pattern_set = mysqli_query($connection, $patternSQL);
  $passcode_info = mysqli_fetch_assoc($pattern_set);

  //remove the passcode id and difficulty from the list
  unset($passcode_info['pattern_id']);
  unset($passcode_info['difficulty']);

  //get the guess info using the get_guess_info function
  $myPatternInfo = get_guess_info($connection);

  foreach ($myPatternInfo as $key => $value) {
    $passcode_info[$key] = $value;
  }

  return $passcode_info;
}

// SQL Query to show data based on a specific id (used for the view_passcode page)
function view_passcode($connection, $patternID){
  //SQL Query to get information from the database table
  $patternSQL = "SELECT * FROM patterns WHERE pattern_id=". $patternID;

  //Get the passcode infomration
  $pattern_set = mysqli_query($connection, $patternSQL);
  $passcode_info = mysqli_fetch_assoc($pattern_set);

  //remove the passcode id and difficulty from the list
  unset($passcode_info['pattern_id']);
  unset($passcode_info['difficulty']);

  foreach ($myPatternInfo as $key => $value) {
    $passcode_info[$key] = $value;
  }

  return $passcode_info;
}

// Get the information about the users pattern (total guesses and correct guesses)
function get_guess_info($connection){
  $guessesSQL = "SELECT * FROM guesses WHERE correct_pattern=". get_user_pattern($connection);
  $correctGuessesSQL = $guessesSQL. " AND correct_guess=1";

  //Get the number of guesses on this pattern
  $guesses_set = mysqli_query($connection, $guessesSQL);
  $guesses = mysqli_num_rows($guesses_set);

  //get the correct number of guesses on this pattern
  $guesses_set = mysqli_query($connection, $correctGuessesSQL);
  $correctGuesses = mysqli_num_rows($guesses_set);

  //Add the values from above to the info array
  $guessInfo['guesses'] = $guesses;
  $guessInfo['correct_guesses'] = $correctGuesses;

  return $guessInfo;
}

// Get the information about the patterns that you can guess (used for the pick_passcode page)
function get_available_patterns($connection){
  $getPatternSQL = "SELECT pattern_id, difficulty FROM patterns WHERE difficulty IS NOT NULL";
  $pattern_set = mysqli_query($connection, $getPatternSQL);

  while($pattern = mysqli_fetch_assoc($pattern_set)){
    //Figure out how many guesses a user made on a specific pattern
    $sqlPasscodes = "SELECT correct_guess FROM guesses WHERE user_guessing=". $_SESSION['user_id']. " AND correct_pattern=". $pattern['pattern_id'];
    $guess_set = mysqli_query($connection, $sqlPasscodes);

    $guessCount = mysqli_num_rows($guess_set);
    $status = 'In Progress';

    //Cycle through the guesses and see if any ore correct
    while($correctGuess = mysqli_fetch_assoc($guess_set)){
      if ($correctGuess['correct_guess'] == 1){
        $status = 'Complete';
      }
    }
    if($guessCount == 0){
      $status = 'Not Started';
    }

    //Add the information found to the passcodes array to be passed to the page
    $passcodes[] = [
      'id' => $pattern['pattern_id'],
      'difficulty' => $pattern['difficulty'],
      'status' => $status,
      'guess_count' => $guessCount
    ];
  }

  usort($passcodes, function($a, $b) {
        // Define the order for status: "Complete" should always be last
        $statusOrder = ['Not Started' => 0, 'In Progress' => 0, 'Complete' => 2];

        // First, compare status: "Not Started" or "In Progress" should come before "Complete"
        if ($statusOrder[$a['status']] < $statusOrder[$b['status']]) {
            return -1; // $a comes before $b
        } elseif ($statusOrder[$a['status']] > $statusOrder[$b['status']]) {
            return 1; // $b comes before $a
        }

        // If statuses are the same, compare difficulty for non-complete patterns
        if ($statusOrder[$a['status']] < 2 && $statusOrder[$b['status']] < 2) {
            // Define the order for difficulty: Easy < Medium < Hard
            $difficultyOrder = ['Easy' => 0, 'Medium' => 1, 'Hard' => 2];

            if ($difficultyOrder[$a['difficulty']] < $difficultyOrder[$b['difficulty']]) {
                return -1; // $a comes before $b
            } elseif ($difficultyOrder[$a['difficulty']] > $difficultyOrder[$b['difficulty']]) {
                return 1; // $b comes before $a
            }
        }

        // If both status and difficulty are the same, keep the order unchanged
        return 0;
    });
  return $passcodes;
}

// Get the user alias from the pattern that was passed in
function get_user_alias_from_pattern($connection, $patternID){
  // Connect the pattern id to a user
  $userPatternSQL = "SELECT alias FROM users WHERE user_pattern=". $patternID;
  $user_set = mysqli_query($connection, $userPatternSQL);

  $user = mysqli_fetch_assoc($user_set);
  $userAlias = $user['alias'];

  if ($userAlias === null){
    return "Game Generated " . $patternID;
  }else{
    return $userAlias;
  }
}

// Get the alias of the user that is signed in
function get_user_alias($connection){
  // Connect the pattern id to a user
  $userPatternSQL = "SELECT alias FROM users WHERE user_id=". $_SESSION['user_id'];
  $user_set = mysqli_query($connection, $userPatternSQL);

  $user = mysqli_fetch_assoc($user_set);
  $userAlias = $user['alias'];

  if ($userAlias === null){
    return "Game Generated " . $patternID;
  }else{
    return $userAlias;
  }
}

// Get the alias of the user that is signed in
function get_user_name($connection){
  // Connect the pattern id to a user
  $userNameSQL = "SELECT uname FROM users WHERE user_id=". $_SESSION['user_id'];
  $user_set = mysqli_query($connection, $userNameSQL);

  $user = mysqli_fetch_assoc($user_set);
  $userName = $user['uname'];

  if ($userName === null){
    return "Game Generated " . $patternID;
  }else{
    return $userName;
  }
}

//Get the signed in user's score
function get_user_score($connection){
  //Get the user score
  $userScoreSQL = "SELECT score FROM users WHERE user_id=". $_SESSION['user_id'];
  $user_set = mysqli_query($connection, $userScoreSQL);
  $user = mysqli_fetch_assoc($user_set);

  //Set initial values
  $score = $user['score'];

  return $score;
}

// This function will update the user score after a pattern is solved
function update_score($connection, $pattern){
  //Set initial values
  $score = get_user_score($connection);
  $difficultyMultiplyer = 1;

  //Get incorrect guesses
  $sqlPasscodes = "SELECT correct_guess FROM guesses WHERE user_guessing=". $_SESSION['user_id']. " AND correct_pattern=". $pattern. " AND correct_guess = 0";
  $guess_set = mysqli_query($connection, $sqlPasscodes);

  $incorrectGuesses = mysqli_num_rows($guess_set);

  //Get the pattern difficulty
  $getDifficultySQL = "SELECT difficulty FROM patterns WHERE pattern_id='". $pattern. "'";
  $difficulty_set = mysqli_query($connection, $getDifficultySQL);
  $difficultyArray = mysqli_fetch_assoc($difficulty_set);

  $difficulty = $difficultyArray['difficulty'];

  if ($difficulty === "Easy"){
    $difficultyMultiplyer = 1;
  }elseif($difficulty === "Medium"){
    $difficultyMultiplyer = 2;
  }else{
    $difficultyMultiplyer = 4;
  }

  //Update score
  $score += ($difficultyMultiplyer * (100 - ($incorrectGuesses * 5)));
  $updateUserSQL = "UPDATE users SET score = '". $score ."' WHERE user_id = " . $_SESSION['user_id'];

  $updateResult = mysqli_query($connection, $updateUserSQL);
}

//Calculate a passcodes score based on the ID number
function calculate_passcode_score($connection, $patternID, $difficulty){
  //Figure out how many correct guesses were made on a specific pattern
  $correctGuessesSQL = "SELECT correct_guess FROM guesses WHERE correct_pattern=". $patternID . " AND correct_guess=1";
  $correct_guess_set = mysqli_query($connection, $correctGuessesSQL);

  //Figure out how many guesses were made on a specific pattern
  $getGuessesSQL = "SELECT correct_guess FROM guesses WHERE correct_pattern=". $patternID;
  $guess_set = mysqli_query($connection, $getGuessesSQL);
  $score = 0;

  // Get the pattern base score
  if($difficulty === "Easy"){
    $score = 250;
  }elseif($difficulty === "Medium"){
    $score = 500;
  }else{
    $score = 1000;
  }

  // Get the count of the guesses
  $score += (mysqli_num_rows($guess_set) * 100);

  // Get the count of the correct guesses
  $score -= (mysqli_num_rows($correct_guess_set) * 250);

  return $score;
}

// This function will get the list of the top passcodes
function get_top_passcodes($connection){
  // Setup the ranking array
  $rankedArray = [];

  $getPatternSQL = "SELECT pattern_id, difficulty FROM patterns WHERE difficulty IS NOT NULL";
  $pattern_set = mysqli_query($connection, $getPatternSQL);

  while($pattern = mysqli_fetch_assoc($pattern_set)){
    $alias = get_user_alias_from_pattern($connection, $pattern['pattern_id']);
    // Get passcode score
    $score = calculate_passcode_score($connection, $pattern['pattern_id'], $pattern['difficulty']);

    // Check if $alias does not contain "Game Generated"
    if (strpos($alias, 'Game Generated') === false) {
        // Add to rankedArray only if alias does not contain "Game Generated"
        $rankedArray[] = [
            'score' => $score,
            'passcode' => $alias
        ];
    }
  }

  usort($rankedArray, function($a, $b) {
        // Sort by score in descending order
        if ($a['score'] == $b['score']) {
            // If scores are equal, sort by passcode alphabetically
            return strcmp($a['passcode'], $b['passcode']);
        }
        return $b['score'] - $a['score']; // Descending score order
    });

    // Reduce array to just 10 values
    $topTenArray = array_slice($rankedArray, 0, 10);

    // Add rank field to each element
    $rank = 1;
    foreach ($topTenArray as &$item) {
        $item['rank'] = $rank++;
    }

  return $topTenArray;
}

// This function will get the list of top users
function get_top_users($connection){
  //setup the ranked array
  $rankedArray = [];

  $getUsersSQL = "SELECT alias, score FROM users";
  $pattern_set = mysqli_query($connection, $getUsersSQL);

  while($user = mysqli_fetch_assoc($pattern_set)){
    $rankedArray[] = [
      'score' => $user['score'],
      'alias' => $user['alias']
    ];
  }

  usort($rankedArray, function($a, $b) {
        // Sort by score in descending order
        if ($a['score'] == $b['score']) {
            // If scores are equal, sort by passcode alphabetically
            return strcmp($a['alias'], $b['alias']);
        }
        return $b['score'] - $a['score']; // Descending score order
    });

    // Reduce array to just 10 values
    $topTenArray = array_slice($rankedArray, 0, 10);

    // Add rank field to each element
    $rank = 1;
    foreach ($topTenArray as &$item) {
        $item['rank'] = $rank++;
    }

  return $topTenArray;
}

// This function generates a percentage of solved patterns for the user to see
function generate_patterns_solved($connection){
    //Get total pattern count
    $getPatternSQL = "SELECT * FROM patterns WHERE difficulty IS NOT NULL";
    $pattern_set = mysqli_query($connection, $getPatternSQL);
    $totalPatterns = mysqli_num_rows($pattern_set) - 1; // This is -1 because you can't solve your own passcode

    //Get correct patterns
    $getSolvedPatternSQL = "SELECT * FROM guesses WHERE user_guessing = '" . $_SESSION['user_id'] . "' AND correct_guess = 1";
    $solved_pattern_set = mysqli_query($connection, $getSolvedPatternSQL);
    $solvedPatterns = mysqli_num_rows($solved_pattern_set);

    //Generate the percentage of solved patterns
    $percentage = ($totalPatterns > 0) ? ($solvedPatterns / $totalPatterns) * 100 : 0;
    $patternString = number_format($percentage) . "%";
    return $patternString;
}

// FIXME Function to generate guesses per pattern
function generate_guesses_per_pattern($connection){
  //Get total guesses count
  $totalGuessesSQL = "SELECT * FROM guesses WHERE user_guessing = " . $_SESSION['user_id'];
  $total_guesses_set = mysqli_query($connection, $totalGuessesSQL);
  $totalGuesses = mysqli_num_rows($total_guesses_set);

  //Get correct guesses count
  $correctGuessesSQL = "SELECT * FROM guesses WHERE user_guessing = '" . $_SESSION['user_id'] . "' AND correct_guess = 1";
  $correct_guesses_set = mysqli_query($connection, $correctGuessesSQL);
  $correctGuesses = mysqli_num_rows($correct_guesses_set);

  //Generate the guesses per passcode value
  $patternGuessRatio = ($correctGuesses > 0) ? ($totalGuesses/$correctGuesses) : 0;
  $guessesPerPattern = number_format($patternGuessRatio, 2);

  return $guessesPerPattern;
}
?>
