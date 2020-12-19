<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 * CURRENCY TYPE = e.g. coins
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Wheels' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Wheels : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].' '.$logChk['subtitle'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Wheels</h1>
<p>All you have to do is hit the "Shake Box!" button below to get your fortune and you will be redirected to the fortune's corresponding reward!</p>
<script language="javascript" src="/admin/games/js/wheels.js" type="text/javascript"></script>
<center>
<form method="POST" name="wheel">
<select name="wheel2" size="7" style="width:200px;">
    <option value="/games.php?play=wheels&go=red" style="color: #636363;background-color: #fafafa;text-align:center;">Red Ball</option>
    <option value="/games.php?play=wheels&go=orange" style="color: #636363;background-color: #eaeaea;text-align:center;">Orange Ball</option>
    <option value="/games.php?play=wheels&go=yellow" style="color: #636363;background-color: #fafafa;text-align:center;">Yellow Ball</option>
    <option value="/games.php?play=wheels&go=green" style="color: #636363;background-color: #eaeaea;text-align:center;">Green Ball</option>
    <option value="/games.php?play=wheels&go=blue" style="color: #636363;background-color: #fafafa;text-align:center;">Blue Ball</option>
    <option value="/games.php?play=wheels&go=violet" style="color: #636363;background-color: #eaeaea;text-align:center;">Violet Ball</option>
    <option value="/games.php?play=wheels&go=black" style="color: #636363;background-color: #fafafa;text-align:center;">Black Ball</option>
</select><br />
<input type="button" value="Spin Reel!" name="B1" onClick="spinthewheel()" style="width:125px;">
</form>
</center>

<?php
    }
} else if ($go == "red") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        /* Blurb can be changed through the class.call.php file */
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Wheels (Red Ball)</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wheels','(Red Ball)','12','0','10','2','0'); }
        else { $general->gamePrize('GAME SET HERE','Wheels','(Red Ball)','6','0','5','1','0'); }
    }
}

else if ($go == "orange") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Wheels (Orange Ball)</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wheels','(Orange Ball)','10','0','8','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wheels','(Orange Ball)','5','0','4','0','0'); }
    }
}

else if ($go == "yellow") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Wheels (Yellow Ball)</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wheels','(Yellow Ball)','8','0','6','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wheels','(Yellow Ball)','4','0','3','0','0'); }
    }
}

else if ($go == "green") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Wheels (Green Ball)</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wheels','(Green Ball)','6','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wheels','(Green Ball)','3','0','2','0','0'); }
    }
}

else if ($go == "blue") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Wheels (Blue Ball)</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wheels','(Blue Ball)','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wheels','(Blue Ball)','2','0','2','0','0'); }
    }
}

else if ($go == "violet") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Wheels (Violet Ball)</h1><center>';
        echo '<center><p>That was close! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wheels','(Violet Ball)','2','0','10','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wheels','(Violet Ball)','1','0','5','0','0'); }
    }
}

else if ($go == "black") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Wheels (Black Ball)</h1><center>';
        echo '<center><p>Oh shoot! You may not have gained any cards, at least you have CURRENCY TYPE!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wheels','(Black Ball)','0','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wheels','(Black Ball)','0','0','2','0','0'); }
    }
}
?>
