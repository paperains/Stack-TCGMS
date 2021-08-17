<?php
include("admin/class.lib.php");
include($header);

echo '<h1>Randomizers</h1>
<p>These randomizers are for staff use only! Do not take from these unless you are told to do so.</p>
<center><h2>Melting Pot</h2>';
$query1 = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active'");
$min = 1; $max = mysqli_num_rows($query1); $rewards = null;
for($i=0; $i<20; $i++) {
    mysqli_data_seek($query1,rand($min,$max)-1);
    $row = mysqli_fetch_assoc($query1);
    $digits = rand(01,$row['card_count']);
    if ($digits < 10) { $_digits = "0$digits"; }
    else { $_digits = $digits; }
    $card = "$row[card_filename]$_digits";
    echo '<img src="/images/cards/'.$card.'.png" border="0" /> ';
    $rewards .= "('".$card."'),";
}
$rewards = substr_replace($rewards,"",-1);
echo '<p><b>Melting Pot:</b> '.$rewards.'</p>';



echo '<h2>Card Claim</h2>';
$query2 = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active'");
$min = 1; $max = mysqli_num_rows($query2); $rewards = null;
for($i=0; $i<60; $i++) {
    mysqli_data_seek($query2,rand($min,$max)-1);
    $row = mysqli_fetch_assoc($query2);
    $digits = rand(01,$row['card_count']);
    if ($digits < 10) { $_digits = "0$digits"; }
    else { $_digits = $digits; }
    $card = "$row[card_filename]$_digits";
    echo '<img src="/images/cards/'.$card.'.png" border="0" /> ';
    $rewards .= "('".$card."'),";
}
$rewards = substr_replace($rewards,"",-1);
echo '<p><b>Card Claim:</b> '.$rewards.'</p>';



echo '<h2>Higher or Lower</h2>';
$query2 = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active'");
$min = 1; $max = mysqli_num_rows($query2); $rewards = null;
for($i=0; $i<1; $i++) {
    mysqli_data_seek($query2,rand($min,$max)-1);
    $row = mysqli_fetch_assoc($query2);
    $digits = rand(01,$row['card_count']);
    if ($digits < 10) { $_digits = "0$digits"; }
    else { $_digits = $digits; }
    $card = "$row[card_filename]$_digits";
    echo '<img src="/images/cards/'.$card.'.png" border="0" /> ';
    $rewards .= "('".$card."'),";
}
$rewards = substr_replace($rewards,"",-1);
echo '<p><b>Higher or Lower:</b> '.$rewards.'</p>';



echo '<h2>Lucky Match</h2>';
$query2 = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active'");
$min = 1; $max = mysqli_num_rows($query2); $rewards = null;
for($i=0; $i<5; $i++) {
    mysqli_data_seek($query2,rand($min,$max)-1);
    $row = mysqli_fetch_assoc($query2);
    $digits = rand(01,$row['card_count']);
    if ($digits < 10) { $_digits = "0$digits"; }
    else { $_digits = $digits; }
    $card = "$row[card_filename]$_digits";
    echo '<img src="/images/cards/'.$card.'.png" border="0" /> ';
    $rewards .= $card.", ";
}
$rewards = substr_replace($rewards,"",-1);
echo '<p><b>Lucky Match:</b> '.$rewards.'</p>';



echo '<h2>New Decks</h2>';
$query3 = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active' ORDER BY `card_released` DESC LIMIT 12");
$min = 1; $max = mysqli_num_rows($query3); $rewards = null;
for($i=0; $i<50; $i++) {
    mysqli_data_seek($query3,rand($min,$max)-1);
    $row = mysqli_fetch_assoc($query3);
    $digits = rand(01,$row['card_count']);
    if ($digits < 10) { $_digits = "0$digits"; }
    else { $_digits = $digits; }
    $card = "$row[card_filename]$_digits";
    $rewards .= $card.", ";
}
$rewards = substr_replace($rewards,"",-2);
echo '<p><b>New Decks</b> '.$rewards.'</p></center>';

include($footer);
?>