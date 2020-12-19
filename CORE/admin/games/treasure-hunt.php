<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */
 
$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Treasure Hunt' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
  if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>Treasure Hunt : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
  } else {
?>

<h1>GAME SET HERE - Treasure Hunt</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

<!-- Create your own map image! -->
<center>
  <img id="map" width="400" height="350" src="/admin/games/images/treasure-hunt.png">
  <p id="distance"></p>
</center>

<script language="javascript" src="/admin/games/js/treasure-hunt.js" type="text/javascript"></script>

<?php
  }
} else if ($go == "prize") {
  if(!isset($_SERVER['HTTP_REFERER'])){
    echo $ForbiddenAccess;
  } else {
    echo '<h1>Treasure Hunt - Prize Pickup</h1>';
    echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
    /* CHECK FOR DOUBLE REWARDS
     * Change amount of rewards you need:
     * ('GAME SET HERE','Treasure Hunt','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
     */
    $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
    if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Treasure Hunt','','4','0','0','0','0'); }
    else { $general->gamePrize('GAME SET HERE','Treasure Hunt','','2','0','0','0','0'); }
  }
}
?>
