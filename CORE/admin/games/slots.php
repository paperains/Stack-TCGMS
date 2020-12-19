<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Slots' AND `timestamp` >= '".$range['timestamp']."'");

if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>Slots : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
} else {
?>

<h1>GAME SET HERE - Slots</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<center>
<?php
$slot = array("slot01-","slot02-","slot03-","slot04-","slot05-","slot06-");

$one= $slot[array_rand($slot,1)];
$two= $slot[array_rand($slot,1)];
$three= $slot[array_rand($slot,1)];

echo '<img src="/admin/games/images/' . $one .'01.jpg">';
echo '<img src="/admin/games/images/' . $two .'02.jpg">';
echo '<img src="/admin/games/images/' . $three .'03.jpg">';

echo '<br><br><input type="button" value="  Fix the Photos  " onClick="window.location.reload()">';
?>
</center>

<?php
    if ($one == $two && $one == $three) {
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need:
         * ('GAME SET HERE','Slots','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
         */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Slots','','4','0','0','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Slots','','2','0','0','0','0'); }
    } else {
        echo "<center>Your prize will appear here when you have three matching images.</center>";
    }
}
?>
