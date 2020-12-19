<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Coin Flip' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Coin Flip : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Coin Flip</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<center>
    <script language="javascript" src="/admin/games/js/coinflip.js" type="text/javascript"></script>
    <form name="game">
        <table border="0">
            <tr>
                <td width="100" valign="middle" align="center"><a href="javascript:void(0);" onClick="playGame(1);"><img src="/admin/games/images/heads.png" title="heads" border=0></a><br /><b>Heads</b></td>
                <td width="50" valign="middle" align="center">OR</td>
                <td width="100" valign="middle" align="center"><a href="javascript:void(0);" onClick="playGame(2);"><img src="/admin/games/images/tails.png" title="tails" border=0></a><br /><b>Tails</b></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><input type="text" name="msg" class="btn-success"></td>
            </tr>
        </table>
    </form>
</center>

<?php
    }
} else if ($go == "lost") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<center><b>Coin Flip - Tough Luck!</b>
        <p>Sorry, you didn\'t win! Please try your luck again next round. :D</p></center>';
        $today = date("Y-m-d", strtotime("now"));
        $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`rewards`,`timestamp`) VALUES ('$player','GAME SET HERE','Coin Flip','You lost this game.','$today')");
    }
}

else if ($go == "prize") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Coin Flip - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need:
         * ('GAME SET HERE','Coin Flip','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
         */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Coin Flip','','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Coin Flip','','2','0','2','0','0'); }
    }
}
?>
