<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Black Jack' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Black Jack : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>
<h1>GAME SET HERE - Black Jack</h1>
<p>Click the "Deal" button to start your game and receive your first card, click "Hit" to be dealt another card, or click "Stand" if you want to stay at the current total you have and continue to play as many rounds as you need to until you win or make the dealer bust!</p>
<center>
    <script language="javascript" src="/admin/games/js/blackjack.js" type="text/javascript"></script>
    <form name="display">
        <table border="0" cellspacing="0" cellpadding="3">
            <tr>
                <td><center>Score: <input type=text name="numgames" size="3" value="0"></center></td>
                <td><center>Dealer</center></td>
                <td><center><input type=text name="dealer" size="2"></center></td>
                <td><center>Card(s):  <input type=text name="say1" size="18" value=""></center></td>
            </tr>
            <tr>
                <td><center></center></td>
                <td><center>Player</center></td>
                <td><center><input type=text name="you" size="2"></center></td>
                <td><center>Card(s):  <input type=text name="say2" size="18" value=""></center></td>
            </tr>
            <tr>
                <td><center><input type=button value="Deal" onClick="NewHand(this.form)" style="width:100px;"></center></td>
                <td colspan=3><center>
                <input type=button value="Stand" onClick="Dealer(this.form);LookAtHands(this.form);"  style="width:135px;">
                <input type=button value=" Hit " onClick="User(this.form)"  style="width:135px;"></center></td>
            </tr>
        </table>
    </form>
</center>
<?php
    }
} else if ($go == "winner") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Black Jack - Prize Pickup</h1>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need: 
         * ('GAME SET HERE','Black Jack','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
         */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Black Jack','','8','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Black Jack','','4','0','2','0','0'); }
    }
}
?>
