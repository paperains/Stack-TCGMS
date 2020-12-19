<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Jan-Ken-Pon' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Jan-Ken-Pon : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Jan-Ken-Pon</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<center>
    <script language="javascript" src="/admin/games/js/jan-ken-pon.js" type="text/javascript"></script>
    <form name=game>
        <table class='form' style='margin: 0 auto;'>
            <tr><th colspan=3 class="center">Choose one</td></tr>
            <tr>
                <td width="100" align="center" valign="middle"><a href="javascript:void(0);" onClick="playGame(1);"><img src="/admin/games/images/rock.png" border=0></a><br /><b>Rock</b></td>
                <td width="100" align="center" valign="middle"><a href="javascript:void(0);" onClick="playGame(2);"><img src="/admin/games/images/paper.png"  border=0></a><br /><b>Paper</b></td>
                <td width="100" align="center" valign="middle"><a href="javascript:void(0);" onClick="playGame(3);"><img src="/admin/games/images/scissor.png"  border=0></a><br /><b>Scissors</b></td>
            </tr>
            <tr>
                <td colspan=3 class=center><input style="text-align:center; display: none;" type=text name=msg size=54></td>
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
        echo '<center><b>Jan-Ken-Pon - Tough Luck!</b>
        <p>Sorry, you didn\'t win! Please try your luck again next round. :D</p></center>';
        $today = date("Y-m-d", strtotime("now"));
        $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','GAME SET HERE','Jan-Ken-Pon','(Lost)','You lost this game.','$today')");
    }
}

else if ($go == "draw") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Jan-Ken-Pon - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need:
         * ('GAME SET HERE','Jan-Ken-Pon','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
         */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Jan-Ken-Pon','(Draw)','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Jan-Ken-Pon','(Draw)','2','0','2','0','0'); }
    }
}

else if ($go == "won") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Jan-Ken-Pon - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Jan-Ken-Pon','(Won)','8','0','8','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Jan-Ken-Pon','(Won)','4','0','4','0','0'); }
    }
}
?>
