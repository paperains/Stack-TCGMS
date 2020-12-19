<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 * Also create your own slot machine items!
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Slot Machine' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
  if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>Slot Machine : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
  } else {
?>

<h1>GAME SET HERE - Slot Machine</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<p>Place your bet on the box below the tokens then hit "Spin". Continue to spin the slot machine until you gain more than <b>30 tokens</b> then hit "Redeem" to claim your winnings.</p>
<script language="javascript" src="/admin/games/js/slot-machine.js" type="text/javascript"></script>
<center>
  <form name=slots onsubmit="rollem(); return false;">
    <table width="100%" cellspacing="3" border="0">
      <tr>
        <td width="50%" valign="top" align="center">
          <table border="0" cellpadding="0" cellspacing="5" width="400" class="border">
            <tr><td class="headLine">Tokens:</td><td align="center" class="tableBody"><input type=box size="5" name=cake READONLY value="25"></td></tr>
            <tr><td class="headLine">Your bet:</td><td align="center" class="tableBody"><input type=box size="5" name=bet></td></tr>
            <tr><td colspan="2" align="center" class="tableBody"><input type="submit" value=" Spin "> <input type="button" value=" Redeem " onclick="stopplay();"></td></tr>
            <tr><td colspan="2" align="center" class="tableBody">
              <p><img src="/admin/games/images/sm01.gif" name="slot1">
              <img src="/admin/games/images/sm02.gif" name="slot2">
              <img src="/admin/games/images/sm03.gif" name="slot3"><br />
              <input type=text readonly size="33" name=banner></p>
            </td></tr>
          </table>
        </td>
        <td width="50%" valign="top" align="center">
          <table width="400" border="0" class="border">
            <tr><td colspan="3" class="headLine">Payouts</td></tr>
            <tr>
              <td class="headLine">3 of a kind</td>
              <td class="tableBody" align="center"><img src="/admin/games/images/sm02.gif"><img src="/admin/games/images/sm02.gif"><img src="/admin/games/images/sm02.gif"></td>
              <td class="tableBody" align="center">10x your bet</td>
            </tr>
            <tr>
              <td class="headLine">A pair</td>
              <td class="tableBody" align="center"><img src="/admin/games/images/sm05.gif"><img src="/admin/games/images/sm05.gif"><img src="/admin/games/images/sm01.gif"></td>
              <td class="tableBody" align="center">2x your bet</td>
            </tr>
            <tr>
              <td class="headLine">No match</td>
              <td class="tableBody" align="center"><img src="/admin/games/images/sm03.gif"><img src="/admin/games/images/sm04.gif"><img src="/admin/games/images/sm06.gif"></td>
              <td class="tableBody" align="center">You lose</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </form>
</center>

<?php
  }
} else if ($go == 0 || $go <= 29) {
  echo '<h1>Slot Machine - No Enough Tokens</h1>
  <p>It seems that you didn\'t gain any tokens or you haven\'t played the slot machine, please go back and earn some tokens.</p>';
}

else if ($go >= 30) {
  if(!isset($_SERVER['HTTP_REFERER'])){
    /* Blurb can be changed through the class.call.php file */
    echo $ForbiddenAccess;
  } else {
    echo '<h1>Slot Machine - Prize Pickup</h1>';
    echo '<center><p>Congrats, claim your reward for winning more than 30 tokens! Take everything you see below:</p>';
    $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
    if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Slot Machine','','4','0','0','0','0'); }
    else { $general->gamePrize('GAME SET HERE','Slot Machine','','2','0','0','0','0'); }
  }
}

else {
  echo '<h1>Slot Machine - Try Again</h1>
  <p>Sorry, you didn\'t gain enough tokens to receive a reward. Please go back and try again.</p>';
}
?>
