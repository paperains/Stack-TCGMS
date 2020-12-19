<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */
 
$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Telepathy' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
  if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>Telepathy : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
  } else {
?>

<h1>GAME SET HERE - Telepathy</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<p>In the free textbox below, type in your guessed number of seeds and press the "Guess!" button. If you guessed wrong, the box on the left will display a hint. Using those clues, continue guessing until you see a message that says you won!</p>
<center>
  <script language="javascript" src="/admin/games/js/telepathy.js" type="text/javascript"></script>
  <form onSubmit="" NAME="guessquiz">
    <input type="text" name="prompt" value="The number I'm thinking of is 1-100!" size="50" /> <input type="text" size="5" name="guess" /><br/>
    <input type="button" value=" Guess! " onClick="process(guessme)" />
  </form>
</center>

<?php
  }
} else {
  if(!isset($_SERVER['HTTP_REFERER'])){
    echo $ForbiddenAccess;
  } else {
    echo '<h1>Telepathy - Prize Pickup</h1>
    <center><p>Congrats, you found them! Take everything you see blow and don\'t forget to log it!</p>';
    /* CHECK FOR DOUBLE REWARDS
     * Change amount of rewards you need:
     * ('GAME SET HERE','Telepathy','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
     */
    $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
    if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Telepathy','','4','0','6','0','0'); }
    else { $general->gamePrize('GAME SET HERE','Telepathy','','2','0','3','0','0'); }
  }
}
?>
