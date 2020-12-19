<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Puzzle' AND `timestamp` >= '".$range['timestamp']."'");

/* Create at least 10 puzzle images to randomize and name them as puzzle01, puzzle02... */
$array = array("01","02","03","04","05","06","07","08","09","10");
$rand = array_rand($array);
$Puzzle = $array[$rand];

if (empty($go)) {
  if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>Puzzle : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
  } else {
?>

<h1>GAME SET HERE - Puzzle</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<center>
  <script language="javascript" src="<?php echo $tcgurl; ?>admin/games/js/puzzle-div.js" type="text/javascript"></script>
  <canvas id="canvas"></canvas>
</center>

<?php
  }
} else if ($go == "prize") {
  if(!isset($_SERVER['HTTP_REFERER'])){
    echo $ForbiddenAccess;
  } else {
    echo '<h1>Puzzle - Prize Pickup</h1><center>
    <p>The puzzle has been fixed! Thank you and please take your rewards below:</p>';
    /* CHECK FOR DOUBLE REWARDS
     * Change amount of rewards you need:
     * ('GAME SET HERE','Puzzle','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
     */
    $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
    if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Puzzle','','4','0','4','0','0'); }
    else { $general->gamePrize('GAME SET HERE','Puzzle','','2','0','2','0','0'); }
  }
}
?>
