<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Reaction' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Reaction : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Reaction</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<center>
    <script language="javascript" src="/admin/games/js/reaction.js" type="text/javascript"></script>
    <p class="center">Click the start button and press the correct button.<br />Try to get it right 5 times in a row.</p>
    <form name="ausgabe">
        <table class="form" style='margin: 0 auto;'>
            <tr>
                <td><input type="button" value="Start" name="B3"	onclick="tempo3()" class="center"></td>
                <td><input type="text" name="text" size="10" class="form-control" style="display: inline;" disabled></td>
                <td><input type="text" name="versuche" size="2" class="form-control" style="display: inline;" disabled></td>
            </tr>
            <tr>
                <td colspan="3" class='center'>
                    <input type="button" value="l e f t" name="B1" onclick="gas()" style="width: 45%; display: inline-block;" class="center" >
                    <input type="button" value="r i g h t" name="B2" onclick="bremsen()" class="center" style="width: 45%; display: inline-block;">
                </td>
            </tr>
        </table>
    </form>
</center>

<?php
    }
} else if ($go == "zero") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        /* Blurb can be changed through the class.call.php file */
        echo $ForbiddenAccess;
    } else {
        echo '<center><b>Reaction - Tough Luck!</b>
        <p>Sorry, you didn\'t win! Please try your luck again next round. :D</p></center>';
        $today = date("Y-m-d", strtotime("now"));
        $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','GAME SET HERE','Reaction','(Zero)','You lost this game.','$today')");
    }
}

else if ($go == "one") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Reaction - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need:
         * ('GAME SET','Reaction','-subtitle-','-random-','-choice-','-cake-','-ticket-','-special currency-')
         */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Reaction','(One)','2','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Reaction','(One)','1','0','2','0','0'); }
    }
}

else if ($go == "two") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Reaction - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Reaction','(Two)','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Reaction','(Two)','2','0','2','0','0'); }
    }
}

else if ($go == "three") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Reaction - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Reaction','(Three)','6','0','8','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Reaction','(Three)','3','0','4','0','0'); }
    }
}

else if ($go == "four") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Reaction - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Reaction','(Four)','8','0','8','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Reaction','(Four)','4','0','4','0','0'); }
    }
}

else if ($go == "prize") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Reaction - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Reaction','(Jackpot)','10','0','10','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Reaction','(Jackpot)','5','0','5','0','0'); }
    }
}
?>
