<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->query("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Card Claim' AND `timestamp` >= '".$range['timestamp']."'");
$count = $database->num_rows("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Card Claim' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($count != 0) {
        echo '<h1>Card Claim : Halt!</h1>
        <p>You have already played this game! If you missed the cards you\'ve taken, here they are:</p>';
        while($row=mysqli_fetch_assoc($logChk)) {
            echo '<center><b>'.$row['title'].':</b> '.$row['rewards'].'<br /></center>';
        }
    } else {
?>

<h1>GAME SET HERE - Card Claim</h1>
<p>Use the form below to claim a card from the card inventory below.<br />You can claim <u>2 cards per week</u>, and <b><u>none</u></b> can be from the same deck.</p>
<center><h2>Inventory Pile</h2>
<div style="width: 650px;">
    <?php
    $card = $database->num_rows("SELECT * FROM `game_cclaim_cards`");
    if ($card == 0) {
        echo '<p>There are no more cards in the pile!</p>';
    } else if ($card != 0) {
        echo '<p>There are currently <b>'.$card.' cards</b> available!</p>';
    }
    $cards = $database->query("SELECT * FROM `game_cclaim_cards` ORDER BY cards ASC");
    while($row = mysqli_fetch_assoc($cards)) {
        $name = stripslashes($row['cards']);
        echo '<img src="/images/cards/';
        echo $name;
        echo '.png" />';
    }
    ?>
</div>
<form method="post" action="/games.php?play=cardclaim&go=claimed">
    <input type="hidden" name="name" value="<?php echo $player; ?>" />
    <table border="0" cellspacing="3" width="70%" class="border">
        <tr>
            <td width="30%" class="headLine">Claiming #1:</td>
            <td width="70%" class="tableBody">
                <select name="card1" style="width: 98%;">
                    <option value="">-----</option>
                    <?php
                    $query = $database->query("SELECT * FROM `game_cclaim_cards` ORDER BY cards ASC");
                    while($row = mysqli_fetch_assoc($query)) {
                        $name = stripslashes($row['cards']);
                        echo '<option value="'.$name.'">'.$name."</option>\n";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="headLine">Claiming #2:</td>
            <td class="tableBody">
                <select name="card2" style="width: 98%;">
                    <option value="">-----</option>
                    <?php
                    $query = $database->query("SELECT * FROM `game_cclaim_cards` ORDER BY cards ASC");
                    while($row1 = mysqli_fetch_assoc($query)) {
                        $name = stripslashes($row1['cards']);
                        echo '<option value="'.$name.'">'.$name."</option>\n";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Claim!" /></td></tr>
    </table>
</form>
</center>

<h2>Claim Logs</h2>
<center>
    <div style="border:1px solid #cccccc;border-radius:8px;text-align:left;overflow:auto;padding:10px;width:90%;height:100px;">
    <?php
    $now = $range['timestamp'];
    $next = date("Y-m-d", strtotime("+1 week"));

    $logs = $database->query("SELECT * FROM `game_cclaim_logs` WHERE `timestamp` BETWEEN '$now' AND '$next' ORDER BY `timestamp` DESC");
    $count = $database->num_rows("SELECT * FROM `game_cclaim_logs` WHERE `timestamp` BETWEEN '$now' AND '$next' ORDER BY `timestamp` DESC");

    if ($count == 0) {
        echo 'There are no new logs for this week.';
    } else {
        while($row = mysqli_fetch_assoc($logs)) {
            $name = stripslashes($row['name']);
            $take = stripslashes($row['take']);
            $date = date("Y/m/d", strtotime($row['timestamp']));
            echo "$date - $name claimed the $take cards.<br />";
        }
    }
    ?>
    </div>
</center>

<?php
    }
} else {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
        exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
    } else {
        $c1 = $sanitize->for_db($_POST['card1']);
        $c2 = $sanitize->for_db($_POST['card2']);
        $name = $sanitize->for_db($_POST['name']);
        $date = date("Y-m-d", strtotime("now"));

        $delete = $database->query("DELETE FROM `game_cclaim_cards` WHERE `cards`='$c1' OR `cards`='$c2'");
        if (empty($c2)) { $database->query("INSERT INTO `game_cclaim_logs` (`name`,`take`,`timestamp`) VALUES ('$name','$c1','$date')"); }
        else { $database->query("INSERT INTO `game_cclaim_logs` (`name`,`take`,`timestamp`) VALUES ('$name','$c1 and $c2','$date')"); }

        if($delete == TRUE) {
            if (empty($c2)) {
                $database->query("UPDATE `user_items` SET `cards`=cards+'1' WHERE `name`='$name'");
                $newSet = $c1;
                echo '<h1>Card Claim</h1>
                <center>Here is your card:<br /><img src="/images/cards/'.$c1.'.png"><br />
                <b>Card Claim:</b> Claimed '.$c1.'</center>';
                $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`rewards`,`timestamp`) VALUES ('$name','GAME SET HERE','Card Claim','$newSet','$date')");
            }
            else {
                $database->query("UPDATE `user_items` SET `cards`=cards+'2' WHERE `name`='$name'");
                $newSet = $c1.', '.$c2;
                echo '<h1>Card Claim</h1>
                <center>Here are your cards:<br /><img src="/images/cards/'.$c1.'.png"> <img src="/images/cards/'.$c2.'.png"><br />
                <b>Card Claim:</b> Claimed '.$c1.' and '.$c2.'</center>';
                $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`rewards`,`timestamp`) VALUES ('$name','GAME SET HERE','Card Claim','$newSet','$date')");
            }
        } else {
            echo '<h1>Card Claim : Error!</h1>
            <p>It seems like there was an error while processing your claims, kindly please contact '.$tcgowner.' about this as soon as possible.</p>';
        }
    }
}
?>
