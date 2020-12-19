<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Lottery' AND `timestamp` >= '".$range['timestamp']."'");

$_POST[$Lottery['g1']] = null;
$_POST[$Lottery['g2']] = null;
$_POST[$Lottery['g3']] = null;
$_POST[$Lottery['g4']] = null;
$_POST[$Lottery['g5']] = null;

$g1 = $_POST[$Lottery['g1']];
$g2 = $_POST[$Lottery['g2']];
$g3 = $_POST[$Lottery['g3']];
$g4 = $_POST[$Lottery['g4']];
$g5 = $_POST[$Lottery['g5']];

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Lottery : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Lottery</h1>
<!-- CHANGE THE BLURBS -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

<p>Select 5 random numbers from the lottery ticket below by checking the box according to your chosen numbers.<br />
You will be directed to your prize if you're lucky to get at least 1 number correctly.</p>
<center>
    <form action="/games.php?play=lottery&go=prize" method="post">
        <table class="table-border" width="30%" cellspacing="3">  
            <tr><td width="5%" class="tableBody" align="center"><input type="checkbox" name="a" value="1"><br/>01</td> 
            <td width="5%" class="tableBody" align="center"><input type="checkbox" name="b" value="1"><br/>02</td> 
            <td width="5%" class="tableBody" align="center"><input type="checkbox" name="c" value="1"><br/>03</td> 
            <td width="5%" class="tableBody" align="center"><input type="checkbox" name="d" value="1"><br/>04</td> 
            <td width="5%" class="tableBody" align="center"><input type="checkbox" name="e" value="1"><br/>05</td></tr> 
            <tr><td class="tableBody" align="center"><input type="checkbox" name="f" value="1"><br/>06</td> 
            <td class="tableBody" align="center"><input type="checkbox" name="g" value="1"><br/>07</td> 
            <td class="tableBody" align="center"><input type="checkbox" name="h" value="1"><br/>08</td> 
            <td class="tableBody" align="center"><input type="checkbox" name="i" value="1"><br/>09</td> 
            <td class="tableBody" align="center"><input type="checkbox" name="j" value="1"><br/>10</td></tr> 
            <tr><td class="tableBody" align="center"><input type="checkbox" name="k" value="1"><br/>11</td>
            <td class="tableBody" align="center"><input type="checkbox" name="l" value="1"><br/>12</td> 
            <td class="tableBody" align="center"><input type="checkbox" name="m" value="1"><br/>13</td> 
            <td class="tableBody" align="center"><input type="checkbox" name="n" value="1"><br/>14</td> 
            <td class="tableBody" align="center"><input type="checkbox" name="o" value="1"><br/>15</td></tr> 
            <tr><td class="tableBody" colspan="5" align="center"> <input type="submit" class="btn-success" value="Draw"> <input type="reset" class="btn-cancel" value="Reset"> </td> </tr> 
        </table>
    </form>
</center>

<?php
    }
} else {
    $N = $g1 + $g2 + $g3 + $g4 + $g5;
    if(!isset($_SERVER['HTTP_REFERER'])){
       echo $ForbiddenAccess;
    } else {
        if ($N == "0") {
            echo '<h1>Lottery - Try Again!</h1><center>Oh no! You didn\'t get any numbers correctly. Please try your luck again in the next round!</center><br/>';
            $today = date("Y-m-d");
            $database->query("INSERT INTO `user_logs` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','GAME SET HERE','Lottery','(0 Number)','You lost this game.','$today')");
        }
        if ($N == "1") {
            echo '<h1>Lottery - Prize Pickup</h1>
            <p>Congrats, you got <em>1 number</em> correctly! Claim your rewards below and don\'t forget to log them:</p><center>';
            /* CHECK FOR DOUBLE REWARDS
             * Change amount of rewards you need:
             * ('GAME SET HERE','Lottery','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
             */
            $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
            if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lottery','1 Number','4','0','0','0','0'); }
            else { $general->gamePrize('GAME SET HERE','Lottery','1 Number','2','0','0','0','0'); }
        }
        if ($N == "2") {
            echo '<h1>Lottery - Prize Pickup</h1>
            <p>Congrats, you got <em>2 numbers</em> correctly! Claim your rewards below and don\'t forget to log them:</p><center>';
            $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
            if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lottery','2 Numbers','6','0','4','0','0'); }
            else { $general->gamePrize('GAME SET HERE','Lottery','2 Numbers','3','0','2','0','0'); }
        }
        if ($N == "3") {
            echo '<h1>Lottery - Prize Pickup</h1>
            <p>Congrats, you got <em>3 numbers</em> correctly! Claim your rewards below and don\'t forget to log them:</p><center>';
            $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
            if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lottery','3 Numbers','8','0','4','0','0'); }
            else { $general->gamePrize('GAME SET HERE','Lottery','3 Numbers','4','0','2','0','0'); }
        }
        if ($N == "4") {
            echo '<h1>Lottery - Prize Pickup</h1>
            <p>Congrats, you got <em>4 numbers</em> correctly! Claim your rewards below and don\'t forget to log them:</p><center>';
            $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
            if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lottery','4 Numbers','10','0','8','0','0'); }
            else { $general->gamePrize('GAME SET HERE','Lottery','4 Numbers','5','0','4','0','0'); }
        }
        if ($N == "5") {
            echo '<h1>Lottery - Prize Pickup</h1>
            <p>Congrats, you got <em>5 numbers</em> correctly! Claim your rewards below and don\'t forget to log them:</p><center>';
            $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
            if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lottery','Jackpot','12','0','8','0','0'); }
            else { $general->gamePrize('GAME SET HERE','Lottery','Jackpot','6','0','4','0','0'); }
        }
        if ($N > "5") {
            echo '<h1>Lottery - Halt!</h1>
            <p>It seems like you\'ve selected more than 5 lottery numbers! Please go back and make sure to check 5 numbers.</p>';
        }
    }
}
?>
