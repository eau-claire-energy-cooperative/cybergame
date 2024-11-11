<?php

// Return the url path
function url_for($script_path) {
  // add the leading '/' if not present
  if($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}

// Get the passcode difficulty
function passcode_difficulty($data){
  $difficulty;
  $score = 0;
  // Account for length in difficulty
  $count = count($data['filenames']);
  if ($count < 3){
    $difficulty = "Easy";
    return $difficulty;
  }elseif ($count < 5) {
    $score += 2;
  }else{
    $score += 5;
  }

  //Check the count of unique letters
  $uniqueCharacters = []; // Initialize an empty array to track unique characters
  $uniqueFilenamesCount = 0;
  // Loop through each filename in $data['filenames']
  foreach ($data['filenames'] as $key => $value) {
      $uniqueCharacters[$value] = 1;
  }
  $uniqueCharactersCount = count($uniqueCharacters);
  // Calculate the passcode difficulty
  $score *= $uniqueCharactersCount;
  // Get difficulty from the score generated
  if ($score < 5){
    $difficulty = "Easy";
  }elseif ($score < 11) {
    $difficulty = "Medium";
  }else{
    $difficulty .= "Hard";
  }
  return $difficulty;
}

// Creates the character button used for displaying patterns (on the view_passcode and guess_page)
function createCharacterButton($imagePath, $altText, $class) {
    return "
        <td class='$class' style='text-align: center;'>
            <img src='$imagePath' width='80' alt='$altText' />
        </td>
    ";
}

// Creates the table for the example page for the example output
function createCharacterTable($characters) {
    $rows = "<tr>";

    foreach ($characters as $character) {
        $rows .= createCharacterButton($character['image'], $character['alt'], $character['class']);
    }

    $rows .= "</tr>";

    return "
        <div class='table-responsive'>
            <table class='table caption-top'>
                <tbody>
                    $rows
                </tbody>
            </table>
        </div>
    ";
}

// Creates the table for the example page where the result is all the same
function createCharacterTableOneColor($characters, $classColor) {
    $rows = "<tr>";

    foreach ($characters as $character) {
        $rows .= createCharacterButton($character['image'], $character['alt'], $classColor);
    }

    $rows .= "</tr>";

    return "
        <div class='table-responsive'>
            <table class='table caption-top'>
                <tbody>
                    $rows
                </tbody>
            </table>
        </div>
    ";
}
?>
