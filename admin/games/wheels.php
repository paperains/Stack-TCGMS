<?php
// Run arrays
$subtitle = explode(", ", $games->gameSub('wheels'));
$choice = explode(", ", $games->gameChoiceArr('wheels'));
$random = explode(", ", $games->gameRandArr('wheels'));
$money = explode(", ", $games->gameCurArr('wheels'));
$array_count = count($subtitle);
$array_count .= count($choice);
$array_count .= count($random);
$array_count .= count($money);
for( $i=0; $i<=($array_count -1); $i++ ) {
    $subtitle[$i];
    $choice[$i];
    $random[$i];
    $money[$i];
}

$range = $database->get_assoc("SELECT * FROM `tcg_games_updater` WHERE `gup_set`='".$games->gameSet('wheels')."'");
$logChk = $database->get_assoc("SELECT * FROM `user_logs` WHERE `log_name`='$player' AND `log_title`='".$games->gameTitle('wheels')."' AND `log_date` >= '".$range['gup_date']."'");

if (empty($go)) {
    if ($logChk['log_date'] >= $range['gup_date']) {
        echo '<h1>'.$games->gameTitle('wheels').' : Halt!</h1>
        <center><p>You have already played this game! If you missed your rewards, here they are:</p>';
        $general->displayRewards('wheels');
        echo '</center>';
    } else {
?>

<h1><?php echo $games->gameSet('wheels'); ?> - <?php echo $games->gameTitle('wheels'); ?></h1>
<?php echo $games->gameBlurb('wheels'); ?>
<script language="javascript" src="/admin/games/js/wheels.js" type="text/javascript"></script>
<center>
<form method="POST" name="wheel">
<select name="wheel2" size="7" style="width:200px;">
    <option value="/games.php?play=wheels&go=red" style="color: #636363;background-color: #fafafa;text-align:center;">Tree of Knowledge</option>
    <option value="/games.php?play=wheels&go=orange" style="color: #636363;background-color: #eaeaea;text-align:center;">Angelic Fruit</option>
    <option value="/games.php?play=wheels&go=yellow" style="color: #636363;background-color: #fafafa;text-align:center;">Love Blossom</option>
    <option value="/games.php?play=wheels&go=green" style="color: #636363;background-color: #eaeaea;text-align:center;">Lif's Sapling</option>
    <option value="/games.php?play=wheels&go=blue" style="color: #636363;background-color: #fafafa;text-align:center;">Wonder Lichen</option>
    <option value="/games.php?play=wheels&go=violet" style="color: #636363;background-color: #eaeaea;text-align:center;">Sprout of Hope</option>
    <option value="/games.php?play=wheels&go=black" style="color: #636363;background-color: #fafafa;text-align:center;">Seed of Doom</option>
</select><br />
<input type="button" value="Spin Reel!" class="btn-primary" name="B1" onClick="spinthewheel()">
</form>
</center>

<?php
    }
} else if ($go == "red") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>'.$games->gameTitle('wheels').' ('.$subtitle[0].')</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_status`='Granted' AND `wish_date`='".$range['gup_date']."' AND `wish_set`='".$games->gameSet('wheels')."'");
        if( $getWish['wish_set'] == $games->gameSet('wheels') ) {
            $cTotal = $choice[0] * 2;
            $rTotal = $random[0] * 2;
            $currency = explode(" | ", $money[0]);
            foreach( $currency as $m ) { $mTotal[] = $m * 2; }
            $mTotal = implode(" | ", $mTotal);
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[0].')',$rTotal,$cTotal,$mTotal);
        }
        else {
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[0].')',$random[0],$choice[0],$money[0]);
        }
    }
}

else if ($go == "orange") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>'.$games->gameTitle('wheels').' ('.$subtitle[1].')</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_status`='Granted' AND `wish_date`='".$range['gup_date']."' AND `wish_set`='".$games->gameSet('wheels')."'");
        if( $getWish['wish_set'] == $games->gameSet('wheels') ) {
            $cTotal = $choice[1] * 2;
            $rTotal = $random[1] * 2;
            $currency = explode(" | ", $money[1]);
            foreach( $currency as $m ) { $mTotal[] = $m * 2; }
            $mTotal = implode(" | ", $mTotal);
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[1].')',$rTotal,$cTotal,$mTotal);
        }
        else {
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[1].')',$random[1],$choice[1],$money[1]);
        }
    }
}

else if ($go == "yellow") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>'.$games->gameTitle('wheels').' ('.$subtitle[2].')</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_status`='Granted' AND `wish_date`='".$range['gup_date']."' AND `wish_set`='".$games->gameSet('wheels')."'");
        if( $getWish['wish_set'] == $games->gameSet('wheels') ) {
            $cTotal = $choice[2] * 2;
            $rTotal = $random[2] * 2;
            $currency = explode(" | ", $money[2]);
            foreach( $currency as $m ) { $mTotal[] = $m * 2; }
            $mTotal = implode(" | ", $mTotal);
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[2].')',$rTotal,$cTotal,$mTotal);
        }
        else {
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[2].')',$random[2],$choice[2],$money[2]);
        }
    }
}

else if ($go == "green") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>'.$games->gameTitle('wheels').' ('.$subtitle[3].')</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_status`='Granted' AND `wish_date`='".$range['gup_date']."' AND `wish_set`='".$games->gameSet('wheels')."'");
        if( $getWish['wish_set'] == $games->gameSet('wheels') ) {
            $cTotal = $choice[3] * 2;
            $rTotal = $random[3] * 2;
            $currency = explode(" | ", $money[3]);
            foreach( $currency as $m ) { $mTotal[] = $m * 2; }
            $mTotal = implode(" | ", $mTotal);
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[3].')',$rTotal,$cTotal,$mTotal);
        }
        else {
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[3].')',$random[3],$choice[3],$money[3]);
        }
    }
}

else if ($go == "blue") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>'.$games->gameTitle('wheels').' ('.$subtitle[4].')</h1><center>';
        echo '<center><p>Good work! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_status`='Granted' AND `wish_date`='".$range['gup_date']."' AND `wish_set`='".$games->gameSet('wheels')."'");
        if( $getWish['wish_set'] == $games->gameSet('wheels') ) {
            $cTotal = $choice[4] * 2;
            $rTotal = $random[4] * 2;
            $currency = explode(" | ", $money[4]);
            foreach( $currency as $m ) { $mTotal[] = $m * 2; }
            $mTotal = implode(" | ", $mTotal);
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[4].')',$rTotal,$cTotal,$mTotal);
        }
        else {
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[4].')',$random[4],$choice[4],$money[4]);
        }
    }
}

else if ($go == "violet") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>'.$games->gameTitle('wheels').' ('.$subtitle[5].')</h1><center>';
        echo '<center><p>That was close! Take everything you see below and don\'t forget to log it!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_status`='Granted' AND `wish_date`='".$range['gup_date']."' AND `wish_set`='".$games->gameSet('wheels')."'");
        if( $getWish['wish_set'] == $games->gameSet('wheels') ) {
            $cTotal = $choice[5] * 2;
            $rTotal = $random[5] * 2;
            $currency = explode(" | ", $money[5]);
            foreach( $currency as $m ) { $mTotal[] = $m * 2; }
            $mTotal = implode(" | ", $mTotal);
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[5].')',$rTotal,$cTotal,$mTotal);
        }
        else {
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[5].')',$random[5],$choice[5],$money[5]);
        }
    }
}

else if ($go == "black") {
    if(!isset($_SERVER['HTTP_REFERER'])){
        echo $ForbiddenAccess;
    } else {
        echo '<h1>'.$games->gameTitle('wheels').' ('.$subtitle[6].')</h1><center>';
        echo '<center><p>Oh shoot! You may not have gained any cards, at least you have CURRENCY TYPE!</p>';
        $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_status`='Granted' AND `wish_date`='".$range['gup_date']."' AND `wish_set`='".$games->gameSet('wheels')."'");
        if( $getWish['wish_set'] == $games->gameSet('wheels') ) {
            $cTotal = $choice[6] * 2;
            $rTotal = $random[6] * 2;
            $currency = explode(" | ", $money[6]);
            foreach( $currency as $m ) { $mTotal[] = $m * 2; }
            $mTotal = implode(" | ", $mTotal);
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[6].')',$rTotal,$cTotal,$mTotal);
        }
        else {
            $general->gamePrize($games->gameSet('wheels'),$games->gameTitle('wheels'),'('.$subtitle[6].')',$random[6],$choice[6],$money[6]);
        }
    }
}
?>