<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Peeptin' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Peeptin : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Peeptin</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<blockquote><b>Mechanics:</b> The goal of the game is to arrange the blocks from 1 to 15 in their numeric order. Click a number next to the empty cell to move it into that cell. The game is won when all the numbers are sorted, and the empty square is in the lower-righthand corner.</blockquote>
<center>
    <script language="javascript" src="/admin/games/js/peeptin.js" type="text/javascript"></script>
    <table border="0" cellspacing="0" cellpadding="1">
        <form>
        <tr>
            <td><input style="width:30px;" type="button" name="0_0" value="1" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="1_0" value="2" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="2_0" value="3" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="3_0" value="4" onclick="press15(this.form, this);"></td>
        </tr>
        <tr>
            <td><input style="width:30px;" type="button" name="0_1" value="5" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="1_1" value="6" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="2_1" value="7" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="3_1" value="8" onclick="press15(this.form, this);"></td>
        </tr>
        <tr>
            <td><input style="width:30px;" type="button" name="0_2" value="9" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="1_2" value="10" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="2_2" value="11" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="3_2" value="12" onclick="press15(this.form, this);"></td>
        </tr>
        <tr>
            <td><input style="width:30px;" type="button" name="0_3" value="13" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="1_3" value="14" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="2_3" value="15" onclick="press15(this.form, this);"></td>
            <td><input style="width:30px;" type="button" name="3_3" value=" " onclick="press15(this.form, this);"></td>
        </tr>
        <tr>
            <td colspan="2">
                <input style="width:62px;" type="button" value="reset" onclick="resetboard15(this.form);">
            </td>
            <td colspan="2">
                <input style="width:62px;" type="button" value="rules" onclick="showrules15();">
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <input style="width:126px;" type="button" value="shuffle" onclick="shuffle15(this.form,150);">
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
        echo '<h1>Peeptin - Prize Pickup</h1>
        <center><p>Congrats, you won the game! Take everything you see blow and don\'t forget to log it!</p>';
        /* CHECK FOR DOUBLE REWARDS
         * Change amount of rewards you need:
	 * ('GAME SET HERE','Peeptin','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
	 */
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Peeptin','','4','0','6','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Peeptin','','2','0','3','0','0'); }
    }
}
?>
