<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='War' AND `timestamp` >= '".$range['timestamp']."'");
$query = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active'");

if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>War : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
} else {
?>

<h1>GAME SET HERE - War</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<?php 
    $min = 1; $max = mysqli_num_rows($query);
    for($i=0; $i<1; $i++) {
        mysqli_data_seek($query,rand($min,$max)-1);
        $row = mysqli_fetch_assoc($query);
        $digits = rand(01,$row['count']);
        if ($digits < 10) { $_digits = "0$digits"; }
        else { $_digits = $digits; }
        $computer = "$row[filename]$_digits";
    }
    $min = 1; $max = mysqli_num_rows($query);
    for($i=0; $i<1; $i++) {
        mysqli_data_seek($query,rand($min,$max)-1);
        $row3 = mysqli_fetch_assoc($query);
        $digits3 = rand(01,$row3['count']);
        if ($digits3 < 10) { $_digits3 = "0$digits3"; }
        else { $_digits3 = $digits3; }
        $you = "$row3[filename]$_digits3";
    }
    echo '<center>
    <table width="40%" class="border" cellspacing="3">
    <tr><td width="20%" align="center"><img src="/admin/games/images/computer.gif"></td><td width="20%" align="center"><img src="/admin/games/images/player.gif"></td></tr>
    <tr><td class="headLine">Computer</td><td class="headLine">You</td></tr>
    <tr>
        <td class="tableGame" align="center"><img src="'.$tcgcards.''.$computer.'.png" border="0" /><br /><b>'.$digits.'</b></td>
        <td class="tableGame" align="center"><img src="'.$tcgcards.''.$you.'.png" border="0" /><br /><b>'.$digits3.'</b></td>
    </tr>
    </table></center><br />';
    if ($digits3 <= $digits) {
        echo '<center><b>War - Tough Luck!</b>
        <p>Sorry, you didn\'t win! Please try your luck again next week. :D</p></center>';
        $today = date("Y-m-d", strtotime("now"));
        $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`rewards`,`timestamp`) VALUES ('$player','GAME SET HERE','War','You lost this game.','$today')");
    }
     
    if ($digits3 > $digits) {
        echo '<center><b>War - Prize Pickup</b>
        <p>Congratulations, you won the game! Take everything you see below and don\'t forget to log it!</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need: 
         * ('GAME SET HERE','Freebies','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
         */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','War','','4','0','0','0','0'); }
        else { $general->gamePrize('GAME SET HERE','War','','2','0','0','0','0'); }
    }
}
?>
