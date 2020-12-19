<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Hangman (Text)' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
  if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>Hangman (Text) : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
  } else {
?>

<h1>GAME SET HERE - Hangman (Text)</h1>
<p>Use the alphabet below to guess the word, or click hint to get a clue.</p>
<script language="javascript" src="<?php echo $tcgurl; ?>admin/games/js/hangman-txt.js" type="text/javascript"></script>
<center>
  <form name="f">
    <table class="border" cellspacing="3" border="0">
      <tr>
        <td align="RIGHT" class="tableBody">
          Score : <input type="TEXT" name="score" value="0" onfocus="score.blur();" size="2"><br>
          Fails (6): <input type="TEXT" name="lives" value="0" onfocus="lives.blur();" size="2">  
        </td>
        <td align="CENTER" class="tableBody">
          <input type="TEXT" name="word" value="----- Hangman -----" onfocus="word.blur();" size="25"> <br>
          <input type="TEXT" name="tried" value="Click GO to get a word." onfocus="tried.blur();" size="25">  
        </td>
        <td align="CENTER" class="tableBody"><input type="BUTTON" onclick="new_word(this.form);" value=" GO ">   </td>
      </tr>
      <tr>
        <td colspan="3" align="CENTER" class="tableBody">
          <input type="BUTTON" value=" A " onclick="seek('A');">
          <input type="BUTTON" value=" B " onclick="seek('B');">
          <input type="BUTTON" value=" C " onclick="seek('C');">
          <input type="BUTTON" value=" D " onclick="seek('D');">
          <input type="BUTTON" value=" E " onclick="seek('E');">
          <input type="BUTTON" value=" F " onclick="seek('F');">
          <input type="BUTTON" value=" G " onclick="seek('G');">
          <input type="BUTTON" value=" H " onclick="seek('H');">
          <input type="BUTTON" value=" I " onclick="seek('I');">
          <input type="BUTTON" value=" J " onclick="seek('J');">
          <input type="BUTTON" value=" K " onclick="seek('K');">
          <input type="BUTTON" value=" L " onclick="seek('L');">
          <input type="BUTTON" value=" M " onclick="seek('M');">
        </td>
      </tr>
      <tr>
        <td colspan="3" align="CENTER" class="tableBody">
          <input type="BUTTON" value=" N " onclick="seek('N');">
          <input type="BUTTON" value=" O " onclick="seek('O');">
          <input type="BUTTON" value=" P " onclick="seek('P');">
          <input type="BUTTON" value=" Q " onclick="seek('Q');">
          <input type="BUTTON" value=" R " onclick="seek('R');">
          <input type="BUTTON" value=" S " onclick="seek('S');">
          <input type="BUTTON" value=" T " onclick="seek('T');">
          <input type="BUTTON" value=" U " onclick="seek('U');">
          <input type="BUTTON" value=" V " onclick="seek('V');">
          <input type="BUTTON" value=" W " onclick="seek('W');">
          <input type="BUTTON" value=" X " onclick="seek('X');">
          <input type="BUTTON" value=" Y " onclick="seek('Y');">
          <input type="BUTTON" value=" Z " onclick="seek('Z');">
        </td>
      </tr>
    </table>
  </form>
</center>

<?php
  }
} else if ($go == "nicesave") {
  if(!isset($_SERVER['HTTP_REFERER'])){
    echo $ForbiddenAccess;
  } else {
    echo '<h1>Hangman (Text) - Prize Pickup</h1>';
    echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';

    /* CHECK FOR DOUBLE REWARDS
     * Change amount of rewards you need:
     * ('GAME SET HERE','Hangman (Text)','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-') **/
    $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
    if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Hangman (Text)','','8','0','2','0','0'); }
    else { $general->gamePrize('GAME SET HERE','Hangman (Text)','','4','0','1','0','0'); }
  }
}
?>
