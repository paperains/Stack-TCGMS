<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Toggler' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Toggler : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Toggler</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<blockquote><b>Mechanics:</b> The goal of the game is to turn all the buttons from [X] to [.]. This is done by clicking buttons. When a button is clicked, its state is toggeled, but so is the state of four buttons around it, so plan carefully!</blockquote>
<center>
    <script language="javascript" src="/admin/games/js/toggler.js" type="text/javascript"></script>
    <table class='form' style='margin: 0 auto;'>
        <form>
        <tr>
            <td><input class="center" style="width:50px;" type="button" name="0_0" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="1_0" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="2_0" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="3_0" value="X" onclick="press(this.form, this);"></td>
        </tr>
        <tr>
            <td><input class="center" style="width:50px;" type="button" name="0_1" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="1_1" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="2_1" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="3_1" value="X" onclick="press(this.form, this);"></td>
        </tr>
        <tr>
            <td><input class="center" style="width:50px;" type="button" name="0_2" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="1_2" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="2_2" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="3_2" value="X" onclick="press(this.form, this);"></td>
        </tr>
        <tr>
            <td><input class="center" style="width:50px;" type="button" name="0_3" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="1_3" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="2_3" value="X" onclick="press(this.form, this);"></td>
            <td><input class="center" style="width:50px;" type="button" name="3_3" value="X" onclick="press(this.form, this);"></td>
        </tr>
        <tr>
            <td colspan="2">
                <input class="center" type="button" value="reset" onclick="resetboard(this.form);">
            </td>
            <td colspan="2">
                <input class="center" type="button" value="rules" onclick="showrules();">
            </td>
        </tr>
        </form>
    </table>
</center>

<?php
    }
} else {
    if(!isset($_SERVER['HTTP_REFERER'])){
        /* Blurb can be changed through the class.call.php file */
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Toggler - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need:
	 * ('GAME SET HERE','Toggler','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
	 */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Toggler','','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Toggler','','2','0','2','0','0'); }
    }
}
?>
