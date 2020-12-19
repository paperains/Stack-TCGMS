<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Hangman (Image)' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
  if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>Hangman (Image) : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
  } else {
?>

<h1>GAME SET HERE - Hangman (Image)</h1>
<p>Use the alphabet below to guess the word, or click hint to get a clue.</p>
<script language="javascript" src="<?php echo $tcgurl; ?>admin/games/js/hangman-img.js" type="text/javascript"></script>
<center>
  <img src="/admin/games/images/hmstart.png" height="170" width="170" name="hm"><br><br>
  <form name="game">
    <table width="300">
    <tr><td width="100" align="right"><strong>Word</strong></td><td width="200"><input type="text" name="displayWord"></td></tr>
    <tr><td width="100" align="right"><strong>Used</strong></td><td width="200"><input type="text" name="usedLetters"></td></tr>
    </table>
  </form>

  <input type="button" onClick="javascript:selectLetter('1');" value="1">
  <input type="button" onClick="javascript:selectLetter('2');" value="2">
  <input type="button" onClick="javascript:selectLetter('3');" value="3">
  <input type="button" onClick="javascript:selectLetter('4');" value="4">
  <input type="button" onClick="javascript:selectLetter('5');" value="5">
  <input type="button" onClick="javascript:selectLetter('6');" value="6">
  <input type="button" onClick="javascript:selectLetter('7');" value="7">
  <input type="button" onClick="javascript:selectLetter('8');" value="8">
  <input type="button" onClick="javascript:selectLetter('9');" value="9">
  <input type="button" onClick="javascript:selectLetter('0');" value="0"><br />

  <input type="button" onClick="javascript:selectLetter('A');" value="A">
  <input type="button" onClick="javascript:selectLetter('B');" value="B">
  <input type="button" onClick="javascript:selectLetter('C');" value="C">
  <input type="button" onClick="javascript:selectLetter('D');" value="D">
  <input type="button" onClick="javascript:selectLetter('E');" value="E">
  <input type="button" onClick="javascript:selectLetter('F');" value="F">
  <input type="button" onClick="javascript:selectLetter('G');" value="G">
  <input type="button" onClick="javascript:selectLetter('H');" value="H">
  <input type="button" onClick="javascript:selectLetter('I');" value="I">
  <input type="button" onClick="javascript:selectLetter('J');" value="J">
  <input type="button" onClick="javascript:selectLetter('K');" value="K">
  <input type="button" onClick="javascript:selectLetter('L');" value="L">
  <input type="button" onClick="javascript:selectLetter('M');" value="M"><br />

  <input type="button" onClick="javascript:selectLetter('N');" value="N">
  <input type="button" onClick="javascript:selectLetter('O');" value="O">
  <input type="button" onClick="javascript:selectLetter('P');" value="P">
  <input type="button" onClick="javascript:selectLetter('Q');" value="Q">
  <input type="button" onClick="javascript:selectLetter('R');" value="R">
  <input type="button" onClick="javascript:selectLetter('S');" value="S">
  <input type="button" onClick="javascript:selectLetter('T');" value="T">
  <input type="button" onClick="javascript:selectLetter('U');" value="U">
  <input type="button" onClick="javascript:selectLetter('V');" value="V">
  <input type="button" onClick="javascript:selectLetter('W');" value="W">
  <input type="button" onClick="javascript:selectLetter('X');" value="X">
  <input type="button" onClick="javascript:selectLetter('Y');" value="Y">
  <input type="button" onClick="javascript:selectLetter('Z');" value="Z"><br />

  <input type="button" onClick="javascript:selectLetter(' ');" value="SPACE"> <input type="button" onClick="javascript:reset();" value="RESET GAME">
</center>

<?php
  }
} else if ($go == "nicesave") {
  if(!isset($_SERVER['HTTP_REFERER'])){
    echo $ForbiddenAccess;
  } else {
    echo '<h1>Hangman (Image) - Prize Pickup</h1>';
    echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';

    /* CHECK FOR DOUBLE REWARDS
     * Change amount of rewards you need:
     * ('GAME SET HERE','Hangman (Image)','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-') **/
    $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
    if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Hangman (Image)','','8','0','2','0','0'); }
    else { $general->gamePrize('GAME SET HERE','Hangman (Image)','','4','0','1','0','0'); }
  }
}
?>
