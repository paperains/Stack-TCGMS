<?php
date_default_timezone_set("Asia/Manila"); // Timezone you're using

$firstofmonth = date("m-d");
$thisday = date('Y-m-d');

if($firstofmonth == '01') {
    $MonthlyUpdate = $thisday;
} else {
    $MonthlyUpdate = date("Y-m-d", strtotime("first day of -1 month"));
}

#################################
########## BEGIN GAMES ##########
#################################
/* These are usually password gate games.
 * Change the game variable to your own game name (e.g $Game01 = $CharacterGuess)
 */
switch (date('Y-m')) {
    // MONTHLY SETS
    case '2021-01' :
        $Game01 = "ANSWER";
        $Game02 = array( "clue" => "TEXT OR IMAGE", "pass" => "ANSWER", "last" => "LAST ROUND ANSWER TEXT", "img" => "LAST ROUND ANSWER IMG" );
    break;

    case '2021-02' :
        $Game01 = "ANSWER";
        $Game02 = array( "clue" => "zoology02.jpg", "pass" => "africanwilddog", "last" => "Wasp", "img" => "zoology01.jpg" );
    break;

    /* Copy the case line for more monthly rounds here */

    default: // What it'll show in case your rounds run out 
	$Game01 = "FREE PLAY";
        $Game02 = array( "clue" => "TEXT OR IMAGE", "pass" => "ANSWER", "last" => "LAST ROUND ANSWER TEXT", "img" => "LAST ROUND ANSWER IMG" );
}
?>
