<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Lucky Match' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Lucky Match : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Lucky Match</h1>
<p>There are <b>5</b> cards below that were chosen at random. If you have a card on your trade post that matches one of the cards displayed, simply click the right amount of matches that you have. The more matches you make, the greater your reward will be! In case that you can't find a match, you don't have to worry as we have prepared something for you as well.</p>
<p align="center">
<?php
    echo '<img src="/images/cards/'.str_replace(", ", ".png\" title=\"\"> <img src=\"/images/cards/", $LuckyMatch['cards']).'.png"><br />
    ('.$LuckyMatch['cards'].')<br /><br />
    <b>Last round:</b> '.$LuckyMatch['last'];
?>
</p>
<p align="center"><b>How many matches did you find?</b></p>
<center>
    <button onclick="window.location.href='/games.php?play=lucky-match&go=zero'">&nbsp; 0 &nbsp;</button>
    <button onclick="window.location.href='/games.php?play=lucky-match&go=one'">&nbsp; 1 &nbsp;</button>
    <button onclick="window.location.href='/games.php?play=lucky-match&go=two'">&nbsp; 2 &nbsp;</button><br />
    <button onclick="window.location.href='/games.php?play=lucky-match&go=three'">&nbsp; 3 &nbsp;</button>
    <button onclick="window.location.href='/games.php?play=lucky-match&go=four'">&nbsp; 4 &nbsp;</button>
    <button onclick="window.location.href='/games.php?play=lucky-match&go=five'">&nbsp; 5 &nbsp;</button>
</center>

<?php
    }
} else if ($go == "zero") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        /* Blurb can be changed through the class.call.php file */
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Lucky Match - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lucky Match','(Zero)','2','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Lucky Match','(Zero)','1','0','2','0','0'); }
    }
}

else if ($go == "one") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Lucky Match - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lucky Match','(One)','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Lucky Match','(One)','2','0','2','0','0'); }
    }
}

else if ($go == "two") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Lucky Match - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lucky Match','(Two)','6','0','8','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Lucky Match','(Two)','3','0','4','0','0'); }
    }
}

else if ($go == "three") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Lucky Match - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lucky Match','(Three)','8','0','12','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Lucky Match','(Three)','4','0','6','0','0'); }
    }
}

else if ($go == "four") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Lucky Match - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lucky Match','(Four)','10','0','16','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Lucky Match','(Four)','5','0','8','0','0'); }
    }
}

else if ($go == "five") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>Lucky Match - Prize Pickup</h1><center><p>Take everything you see below and don\'t forget to log it.</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Lucky Match','(Five)','12','0','20','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Lucky Match','(Five)','6','0','10','0','0'); }
    }
}
?>
