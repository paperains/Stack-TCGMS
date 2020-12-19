<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Vacation' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Vacation : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Vacation</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<script language="javascript" src="/admin/games/js/vacation.js" type="text/javascript"></script>
<center><form name="vacation">
<table width="50%" class="border" cellspacing="3">
<tr><td class="headLine"><input type="text" name="hint" placeholder="Enter your guess below and click Guess!" readonly style="width:95%;" /></td></tr>
<tr><td class="tableBody"><input type="text" name="answer" title="Enter your guess here." style="width:95%;" /></td></tr>
<tr><td class="tableBody" align="center">
    <input type="button" value=" Guess! " onClick="guessit()" title="Click here to get a hint or check your guess." />
    <input type="button" value=" Clear " onClick="clearBox()" title="Click here to clear the text box." />
</td></tr>
</table>
</form></center>

<?php
    }
} else if ($go == "prize") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        /* Blurb can be changed through the class.call.php file */
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Vacation - Prize Pickup</h1>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need:
         * ('GAME SET HERE','Vacation','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
         */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Vacation','','4','0','0','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Vacation','','2','0','0','0','0'); }
    }
} else {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Vacation - Tough Luck!</h1>';
        echo '<center><p>Shoot, you guessed it wrong! Try again on the next round.</p></center>';
        $today = date("Y-m-d", strtotime("now"));
        $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`rewards`,`timestamp`) VALUES ('$player','GAME SET HERE','Vacation,'You lost this game.','$today')");
    }
}
?>
