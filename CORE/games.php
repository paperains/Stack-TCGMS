<?php
include('admin/class.lib.php');
include($header);

include('admin/games/func.monthly.php');
include('admin/games/func.updater.php');

if (empty($login)) {
    header("Location:account.php?do=login");
}

$query = "SELECT * FROM `tcg_cards` WHERE `status`='Active'";

switch($play) {
    case "war" : include('admin/games/war.php'); break;
    case "hangman-txt" : include('admin/games/hangman-txt.php'); break; // TEXT TYPE
    case "hangman-img" : include('admin/games/hangman-img.php'); break; // IMAGE TYPE
    case "puzzle" : include('admin/games/puzzle.php'); break; // SINGLE, DIV TYPE
    case "wheels" : include('admin/games/wheels.php'); break;
    case "telepathy" : include('admin/games/telepathy.php'); break;
    case "treasure-hunt" : include('admin/games/treasure-hunt.php'); break;

    case "memory" : include('admin/games/memory.php'); break;
    case "tic-tac-toe" : include('admin/games/tic-tac-toe.php'); break;
    case "lucky-match" : include('admin/games/lucky-match.php'); break;
    case "slots" : include('admin/games/slots.php'); break; // IMAGE TYPE
    case "slot-machine" : include('admin/games/slot-machine.php'); break; // TOKEN TYPE
    case "black-jack" : include('admin/games/black-jack.php'); break;
    case "vacation" : include('admin/games/vacation.php'); break;

    case "higher-lower" : include('admin/games/higher-lower.php'); break;
    case "melting-pot" : include('admin/games/melting-pot.php'); break;
    case "card-claim" : include('admin/games/card-claim.php'); break;
    case "upcoming-vote" : include('admin/games/upcoming-vote.php'); break;
    case "freebies" : include('admin/games/freebies.php'); break;
    case "lottery" : include('admin/games/lottery.php'); break;
    
    /* To add your password gate games, just copy the example case line and paste it below.
     * Make sure to change the following according to yours:
     * GAME-NAME - e.g. guess-the-character
     */
    case "GAME-NAME" : include('admin/games/GAME-NAME.php'); break;
    
    default:
        echo '<h1>Interactive</h1>
        <p>These are the sets of games available here at '.$tcgname.', each games has their own game plays but before proceeding, please read the guidelines first. All of the games below will be automatically logged on your permanent logs once you play and receive your rewards. Also, <u>DO NOT</u> refresh the reward pages unless stated otherwise.</p>
        <p>Before playing, please take note of these guidelines and game help:</p>
        <ol>
            <li>All passwords gates from the monthly set should be written in lowercase with no spaces, symbols and punctuation.</li>
            <li>Answers should be spelt the same as the decks of '.$tcgname.'.</li>
            <li>Do not type the numbers in words. (i.e. 10 instead of ten)</li>
            <li>All answers are [TCG SUBJECT] related, except for other games which will vary depends on the game problem.</li>
        </ol>
        <p>For some reason and the game help still doesn\'t work but you know you have the correct answer, it\'s time for you to contact us either via <a href="mailto:'.$tcgemail.'">direct mail</a> or <a href="" target="_blank">Discord</a>.</p>';

        if ($row['status']=="Hiatus") {
            echo '<h3>Access Denied</h3>';
            echo '<p>It looks like you haven\'t been active in the past two months and have been placed on the Hiatus list. In order to play games here, kindly contact '.$tcgowner.' to reactivate your account.</p>';
        } else {
            $w = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Weekly' ORDER BY `id` DESC");
            $b1 = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Set A' ORDER BY `id` DESC");
            $b2 = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Set B' ORDER BY `id` DESC");
            $m = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Monthly' ORDER BY `id` DESC");
            $timechk = date("Y-m", strtotime($m['timestamp']));
            $sc = $database->num_rows("SELECT COUNT(*) FROM `tcg_games` WHERE `sets`='Special' AND `timestamp`='$timechk' ORDER BY `id` DESC");
            $s = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Special' ORDER BY `id` DESC");
            
            /* Choose from the included games under the admin folder which you'd like to use for your game sets.
             * Then copy the sample table rows below to list more games for each sets.
             * <tr class="rows"><td align="center"><a href="/games.php?play=GAME-NAME">GAME NAME</a></td><td align="center">short description</td></tr>
             */
            echo '<h2>Weekly Set ('.$w['timestamp'].')</h2>
                <table width="100%" cellspacing="3" class="border">
                    <tr><td class="headLine" width="30%">Link</td><td class="headLine" width="70%">Description</td></tr>
                    <tr class="rows"><td align="center"><a href="/games.php?play=GAME-NAME">GAME NAME</a></td><td align="center">short description</td></tr>
                </table><br />';
                if ($w['timestamp'] == $b1['timestamp']) {
                    echo '<h2>Bi-weekly A Set ('.$b1['timestamp'].')</h2>
                    <table width="100%" cellspacing="3" class="border">
                        <tr><td class="headLine" width="30%">Link</td><td class="headLine" width="70%">Description</td></tr>
                        <tr class="rows"><td align="center"><a href="/games.php?play=GAME-NAME">GAME NAME</a></td><td align="center">short description</td></tr>
                    </table><br />';
                } else {
                    echo '<h2>Bi-weekly B Set ('.$b2['timestamp'].')</h2>
                    <table width="100%" cellspacing="3" class="border">
                        <tr><td class="headLine" width="30%">Link</td><td class="headLine" width="70%">Description</td></tr>
                        <tr class="rows"><td align="center"><a href="/games.php?play=GAME-NAME">GAME NAME</a></td><td align="center">short description</td></tr>
                    </table><br />';
                }
            
            echo '<h2><em>Monthly</em> Set ('.$m['timestamp'].')</h2>
            <table width="100%" class="border">
                <tr><td class="headLine" width="30%">Link</td><td class="headLine" width="70%">Description</td></tr>
                <tr class="rows"><td align="center"><a href="/games.php?play=GAME-NAME">GAME NAME</a></td><td align="center">short description</td></tr>
            </table>';
        } // END LOGIN CHECK
} // END SWITCH
include($footer);
?>
