<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Memory' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Memory : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Memory</h1>
<p>Click each of the blank images below to reveal a picture! Once you do, try to find its matching pair by clicking the other blank images to reveal what pictures are hiding behind them. You will be automatically redirected to your prize once you've matched up all the pictures!</p>
<center>
    <div align="center" class="m-wrap">
        <div class="m-game"></div>
    </div>
    <script language="javascript" src="/admin/games/js/memory.js" type="text/javascript"></script>
</center>

<?php
    }
} else if ($go == "prize") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        /* Blurb can be changed through the class.call.php file */
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Memory - Prize Pickup</h1>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need:
         * ('GAME SET HERE','Memory','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
         */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Memory','','8','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Memory','','4','0','2','0','0'); }
    }
}
?>
