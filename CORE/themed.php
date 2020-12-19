<?php
include("admin/class.lib.php");
include($header);

if (empty($login)) {
    header("Location:account.php?do=login");
}

if ( isset($_POST['new-themed']) ) {
    $deck = $sanitize->for_db($_POST['deck']);
    $cards = $sanitize->for_db($_POST['cards']);
    $donator = $sanitize->for_db($_POST['donator']);
    $image = $sanitize->for_db($_POST['image']);
    $count = intval($_POST['count']);
    $break = intval($_POST['break']);
    $deadline = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
    if ( $cards == 'cards (01, 02, 03)' ) { $cards = ''; }
    if ( $deck === '' || $deck == 'deck' ) { $error[] = 'Deck name must be defined.'; }
    else if ( $count === '' || $deck == 'count' ) { $error[] = 'Card count must be defined.'; }
    else if ( $break === '' || $deck == 'break' ) { $error[] = 'Break field must be defined. Set it to 0 if you don\'t want line breaks.'; }
    else {
        if ( !isset($error) ) {
            $result = $database->query("INSERT INTO `tcg_cards_themed` (`deck`,`count`,`break`,`deadline`) VALUE ('$deck','$count','$break','$deadline')");
            if ( !$result ) { $error[] = "Failed to add the themed deck."; }
            else { $success[] = "The new themed deck has been added."; }
        }
    }
}

if ( isset($_POST['submit']) ) {
    $catid = intval($_POST['id']);
    $card = $sanitize->for_db($_POST['card']);
    $donator = $sanitize->for_db($_POST['donator']);
    $image = $sanitize->for_db($_POST['image']);

    $deckinfo = $database->get_assoc("SELECT * FROM `tcg_cards_themed` WHERE `id`='$catid'");
    $deck = $deckinfo['deck'];

    $date = date("Y-m-d", strtotime("now"));

    if ( $card !== '' ) {
        $card = explode(',',$card);
        $donator = explode(',',$donator);
        $image = explode(',',$image);
        function adddeck(&$value,$key) {
            $value = trim($value);
            $value = ''.$value.'';
        }
        array_walk($card,'adddeck');
        if (empty($deckinfo['cards']) && empty($deckinfo['donator']) && empty($deckinfo['image'])) {
            $c = implode(', ',$card);
            $d = implode(', ',$donator);
            $i = implode(', ',$image);
        } else {
            $card = implode(', ',$card);
            $donator = implode(', ',$donator);
            $image = implode(', ',$image);
            $c = $deckinfo['cards'].', '.$card;
            $d = $deckinfo['donator'].', '.$donator;
            $i = $deckinfo['image'].', '.$image;
        }
    }

    $result = $database->query("UPDATE `tcg_cards_themed` SET `cards`='$c',`donator`='$d',`image`='$i' WHERE `id`='$catid' LIMIT 1");
	if ( !$result ) { $error[] = "Failed to update the deck. ".mysqli_error().""; }
	else {
        $database->query("INSERT INTO `user_rewards` (`name`,`type`,`subtitle`,`mcard`,`cards`,`cur1`,`cur2`,`timestamp`) VALUES ('$donator','Themed Deck','($deck)','No','1','1','0','$date')");
        $success[] = "The deck has been updated and your rewards has been sent!";
    }
}

if ( $_GET['action'] == "delete" && isset($_GET['cat']) ) {
    $catid = intval($_GET['cat']);	
    $exists = $database->num_rows("SELECT * FROM `tcg_cards_themed` WHERE `id`='$catid'");

    if ( $exists === 1 ) {
        $result  = $database->query("DELETE FROM `tcg_cards_themed` WHERE `id` = '$catid' LIMIT 1");
        if ( !$result ) { $error[] = "There was an error while attempting to remove the themed deck. ".mysqli_error().""; }
        else { $success[] = "The themed deck and containing cards have been removed."; }
    }
    else { $error[] = "The set no longer exists."; }
}

echo '<h1>Themed Deck</h1>
<p>Here is the current list of the active or on-going themed deck that is due for donations in two weeks. Any members of the TCG can donate an image to complete an available themed deck</p>
<p>Within the duration of the donation period, members can donate <u>only <b>1</b> image</u> per available deck. Which means, a member can only add their second image if the deck was not completed until its deadline. Kindly please wait for an official announcement via Discord or Twitter if the themed deck in question is open for your second image.</p>
<p>This is a first-come, first-served basis of donation. So please do not attempt to donate an image to an already claimed card number or overwrite the data. Also make sure to fill up the card number correctly, exluding any zeros from numbers below 10 (e.g. <i>2</i> instead of <i>02</i>).</p>
<p>Rewards which will consist of <u>X random card and X currency01</u> will be automatically sent to you on your rewards chest after submitting the form. If you didn\'t get the rewards or if there\'s an error while processing your form, kindly let '.$tcgowner.' know via Discord as soon as possible.</p>
<center>';
if ($row['role'] == "Admin") {
    echo '<button id="s1" class="btn-success">  Add New Themed Deck  </button>
    <span id="l1" class="slideable">
    <form action="themed.php" method="post">
        <table width="100%" cellspacing="3" class="border">
            <tr>
                <td width="10%" class="headLine">Deck Name:</td>
                <td width="90%" class="tableBody" colspan="5"><input name="deck" id="deck" type="text" style="width:97%;" /></td>
            </tr>
            <tr>
                <td width="10%" class="headLine">Count:</td>
                <td width="12%" class="tableBody"><input name="count" id="count" type="number" value="20" style="width:50%;"></td>
                <td width="10%" class="headLine">Break:</td>
                <td width="12%" class="tableBody"><input name="break" id="break" type="number" value="5" style="width:50%;"></td>
                <td width="10%" class="headLine">Deadline:</td>
                <td width="46%" class="tableBody"><select name="month">';
                for($m=1; $m<=12; $m++) {
                    if ($m < 10) { $_mon = "0$m"; }
                    else { $_mon = $m; }
                    echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                }
                echo '</select>&nbsp;<select name="day">';
                for($i=1; $i<=31; $i++) {
                    if ($i < 10) { $_days = "0$i"; }
                    else { $_days = $i; }
                    echo '<option value="'.$_days.'">'.$_days.'</option>';
                }
                echo '</select>&nbsp;';
                    $start=date('Y');
                    $end=$start-40;
                    $yearArray = range($start,$end);
                    echo '<select name="year">';
                    foreach ($yearArray as $year) {
                        $selected = ($year == $start) ? 'selected' : '';
                        echo '<option value="'.$year.'">'.$year.'</option>';
                    }
                echo '</select></td>
            </tr>
            <tr>
                <td width="15%" class="headLine">Cards:</td>
                <td width="35%" class="tableBody" colspan="5"><input name="cards" id="cards" type="text" placeholder="1, 2, 3" style="width:95%;" /></td>
            </tr>
            <tr><td class="tableBody" align="center" colspan="6"><input type="submit" name="new-themed" id="new-themed" class="btn-success" value="Add Deck" /> <input type="reset" name="reset" id="reset" class="btn-warning" value="Reset" /></td></tr>
        </table>
    </form>
    </span>';
}

echo '<center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div>'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div>'; } }

function trim_value(&$value) { $value = trim($value); }
$res = $database->query("SELECT * FROM `tcg_cards_themed` WHERE `completed`='0' ORDER BY `deck`");
while ($col = mysqli_fetch_assoc($res)) {
    $data = array();

    if ($col['cards'] != ''){
        $cards = explode(',', $col['cards']);
        array_walk($cards, 'trim_value');
        $count = count($cards);

        $donator = explode(',', $col['donator']);
        $image = explode(',', $col['image']);
        foreach ($cards as $key => $card) {
            $data[$card] = array(
                'user' => trim($donator[$key]),
                'img' => trim($image[$key])
            );
        }
    }

    echo '<h2>'.$col['deck'].' ('; if(empty($col['cards'])) { echo '0'; } else { echo $count; } echo ' / '.$col['count'].')</h2>
    <p>Donation period will end on <b>'.date("F d, Y", strtotime($col['deadline'])).'</b> at <b>11:59PM PHT</b>!</p>
    <table width="625" cellspacing="0" cellpadding="0" border="0"><tr>';
    for ( $i = 1; $i <= $col['count']; $i++ ) {
        if ( in_array($i, $cards) ) {
            echo '<td width="125" align="center" height="105" background="/images/cards/filler.png">'.$i.'<br ><a href="'.$data[$i]['img'].'" target="_blank">'.$data[$i]['user'].'</a></td>';
        } else {
            echo '<td width="125" align="center" height="105" background="/images/cards/filler.png">00</td>';
        }

        if ( $col['break'] !== '0' && $i % $col['break'] == 0 )
            echo '</tr>';
    }
    echo '</table><br />
    <form action="/themed.php" method="post">
    <input name="id" type="hidden" value="'.$col['id'].'">
    <input type="hidden" name="donator" id="donator" value="'.$row['name'].'" />
    <table width="100%" cellspacing="3" class="border">
        <tr>
            <td width="10%" class="headLine">Card #:</td>
            <td width="20%" class="tableBody"><input name="card" id="card" type="number" placeholder="1, not 01" style="width:85%;"></td>
            <td width="15%" class="headLine">Image URL:</td>
            <td width="45%" class="tableBody"><input name="image" id="image" type="text" placeholder="http://" style="width:93%;"></td>
        </tr>
        <tr>
            <td width="10%" class="tableBody" colspan="4" align="center">
                <input type="submit" name="submit" id="submit" class="btn-success" value="Donate" />';
                if ($row['role'] == "Admin") { echo ' <a class="btn-warning" onclick="location.href=\'themed.php?action=delete&cat='.$col['id'].'\'">Delete</a>'; }
            echo '</td>
        </tr>
    </table>
    </form></center>';
}

include($footer);
?>
