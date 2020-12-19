<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->query("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Melting Pot' AND `timestamp` >= '".$range['timestamp']."'");
$count = $database->num_rows("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Melting Pot' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($count == 2) {
        echo '<h1>Melting Pot : Halt!</h1>
        <p>You have already played this game! If you missed your trades, here they are:</p>';
        while($row=mysqli_fetch_assoc($logChk)) {
            echo '<center><b>'.$row['title'].':</b> '.$row['rewards'].'<br /></center>';
        }
    } else {
?>

<h1>GAME SET HERE - Melting Pot</h1>
<p>Use the form below to trade a card from the melting pot.<br />You may take 2 cards per week, but there may <b><u>never</u></b> be 2 cards from the same deck.</p>
<center>
    <div style="width: 650px;">
    <?php
        $query = $database->query("SELECT * FROM `game_mpot_cards` ORDER BY card ASC"); 
        $count = $database->num_rows("SELECT * FROM `game_mpot_cards` ORDER BY card ASC");
        if($count==0) {
            echo "There are no more cards. Come back next week.";
        } else {
            while($row = mysqli_fetch_array($query)) {
                echo '<img src="/images/cards/'.$row['card'].'.png" alt="'.$row['card'].'" title="'.$row2['card'].'" style="padding: 2px 0" />';
            }
        }
    ?>
    </div>
</center>

<center>
<form method="post" action="/games.php?play=melting-pot&go=traded">
    <input type="hidden" name="name" value="<?php echo $player; ?>" />
    <table border="0" cellspacing="3" width="100%" class="border">
        <tr>
            <td width="30%" class="headLine">Taking:</td>
            <td width="70%" class="tableBody">
                <select name="card1" style="width: 97%;">
                    <option value="">-----</option>
                    <?php
                    $query = $database->query("SELECT * FROM `game_mpot_cards` ORDER BY `card` ASC");
                    while($row=mysqli_fetch_assoc($query)) {
                        $card=stripslashes($row['card']);
                        echo "<option value=\"$card\">$card</option>\n";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="35%" class="headLine">Giving:</td>
            <td width="75%" class="tableBody">
                <select name="give1" style="width: 84%;">
                    <option value="">-----</option>
                    <?php
                    $query = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' ORDER BY filename");
                    while($row=mysqli_fetch_assoc($query)) {
                        $card=stripslashes($row['filename']);
                        echo "<option value=\"$card\"\>$card</option>\n";
                    }
                    ?>
                </select><input type="text" name="give2" size="1" placeholder="00" />
            </td>
        </tr>
        <tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" value="Trade Cards" class="btn-success" /></td></tr>
    </table>
</form>
</center>

<h2>Trade Logs</h2>
<center>
    <div style="border:1px solid #cccccc;border-radius:8px;text-align:left;overflow:auto;padding:10px;width:90%;height:100px;">
    <?php
    $range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Weekly' ORDER BY `id` DESC");
    $dateToday = date("Y-m-d", strtotime($range['timestamp']));
    $weekAgo = date("Y-m-d", strtotime("+1 week"));
    $logs = $database->query("SELECT * FROM `game_mpot_logs` WHERE `timestamp` BETWEEN '$dateToday' AND '$weekAgo' ORDER BY `timestamp` DESC");
    $count = $database->num_rows("SELECT * FROM `game_mpot_logs` WHERE `timestamp` BETWEEN '$dateToday' AND '$weekAgo' ORDER BY `timestamp` DESC");
    if ($count == 0) {
        echo 'There are no new logs for this week.';
    } else {
        while($row = mysqli_fetch_assoc($logs)) {
            $name = stripslashes($row['name']);
            $take = stripslashes($row['take']);
            $give = stripslashes($row['give']);
            $date = date("Y/m/d", strtotime($row['timestamp']));
            echo "$date - $name exchanged $give for $take<br />";
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
        $name = $sanitize->for_db($_POST['name']);
        $card1 = $sanitize->for_db($_POST['card1']);
        $give1 = $sanitize->for_db($_POST['give1']);
        $give2 = $sanitize->for_db($_POST['give2']);
        $date = date("Y-m-d", strtotime("now"));

        $card2 = "$give1$give2";

        $num = $database->num_rows("SELECT * FROM game_mpot_cards WHERE card = '".$card2."'");
        $num2 = $database->num_rows("SELECT * FROM game_mpot_cards WHERE card LIKE '".$give1."%'");

        if ($num > 0) {
            echo "<h1>Error</h1>";
            echo "<center>That card is already in the pile.</center>";
        } else if ($num2 > 0) {
            echo "<h1>Error</h1>";
            echo "<center>That deck is already in the pile.</center>";
        } else {
            $delete = $database->query("DELETE FROM game_mpot_cards WHERE `card`='$card1' LIMIT 1") or die(mysqli_error());
            $insert = $database->query("INSERT INTO game_mpot_logs (`name`, `take`, `give`, `timestamp`) VALUE ('$name', '$card1', '$card2', '$date')") or die(mysqli_error());
            $insert1 = $database->query("INSERT INTO game_mpot_cards (`card`) VALUE ('$card2')");
            if ($insert1 == TRUE) {
                echo '<h1>Melting Pot</h1>
                <center><img src="/images/cards/'.$card1.'.png"><br>
                <b>Melting Pot:</b> Traded '.$card2.' for '.$card1.'</center>';
                $newSet = 'Traded '.$card2.' for '.$card1;
                $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`rewards`,`timestamp`) VALUES ('$player','GAME SET HERE','Melting Pot','$newSet','$date')");
            } else {
                echo '<h1>Melting Pot : Error!</h1>
                <p>It seems like there was an error while processing your trade, kindly please contact '.$tcgowner.' about this as soon as possible.</p>';
            }
        }
    }
}
?>
