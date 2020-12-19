<?php
include("admin/class.lib.php");
include($header);

if (empty($login)) {
    header("Location: account.php?do=login");
}

// GET USER ITEMS
$item = $database->get_assoc("SELECT * FROM `user_items` WHERE `name`='$player'");

if (empty($_SERVER['QUERY_STRING'])) {
    echo '<h1>Cake Shoppe</h1>
    <p>Welcome to the shop, '.$player.'! Here you can buy card packs that we are currently offering. Choose the product you want to purchase using your gained cakes and tickets!</p>
    <blockquote class="wish"><center>';
    if ($item['cake'] <= 0) { echo 'You don\'t have enough cakes or tickets to spend! Please play more games to earn more currencies.'; }
    else { echo 'You currently have <b>'.$item['cake'].' cakes</b> and <b>'.$item['ticket'].' tickets</b> to spend!'; }
    echo '</center></blockquote>
    <center><table width="100%" cellspacing="5">
        <tr>
            <td width="50%" valign="top" align="center" class="tableBody">
                <h2>Random Pack</h2>
                1 random pack consists of 5 cards<br />Buy a pack for <b>50 cakes</b>!<br />
                <form method="post" action="/shoppe.php?random">
                <b>How many packs?</b> <input type="text" name="random" value="1" style="width:25%;"> <input type="submit" name="submit" class="btn-success" value="Buy" />
                </form>
            </td>
            <td width="50%" valign="top" align="center" class="tableBody">
                <h2>Chance Pack</h2>
                1 chance pack consists of 3 cards from recent release<br />Buy a pack for <b>50 cakes</b>!<br />
                <form method="post" action="/shoppe.php?chance">
                <b>How many packs?</b> <input type="text" name="chance" value="1" style="width:25%;"> <input type="submit" name="submit" class="btn-success" value="Buy " />
                </form>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center" class="tableBody">
                <h2>Exchange Tickets</h2>
                Use the form below to exchange your tickets to choice cards.<br />
                <form method="post" action="/shoppe.php?ticket">
                <b>How many tickets?</b> <input type="text" name="ticket" value="2" style="width:25%;"> <input type="submit" name="submit" class="btn-success" value="Redeem" />
                </form>
            </td>
        </tr>
    </table></center>';
} // END SHOPPE MAIN

else if ($_SERVER['QUERY_STRING'] == "random") {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
        exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
    } else {
        $random = $sanitize->for_db($_POST['random']);
        $diff = 50*$random;
        $cards = 5*$random;

        // CHECK IF THERE ARE ENOUGH GOLDS
        if ($item['cake'] < $diff) {
            echo '<h1>Shoppe : Halt!</h1>
            <p>Sorry, but it seems like you don\'t have enough cakes on your account to purchase this pack. Play more games to earn more cakes before making a purchase!</p>';
            exit();
        }

        $update = $database->query("UPDATE `user_items` SET cake=cake-'$diff' WHERE name='$player'");

        if ($update == TRUE) {
            echo '<h1>Shoppe : Random Pack</h1>
            <p>Thank you for purchasing '.$random.' random pack(s) from our inventory! A total of <b>'.$diff.'</b> cakes has been deducted from your account.</p>
            <center>';
            $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active'");
            $min=1; $max=mysqli_num_rows($sql); $rewards = null;
            for($i=0; $i<$cards; $i++) {
                mysqli_data_seek($sql,rand($min,$max)-1);
                $row = mysqli_fetch_assoc($sql);
                $digits = rand(01,$row['count']);
                if ($digits < 10) { $_digits = "0$digits"; }
                else { $_digits = $digits; }
                $card = "$row[filename]$_digits";
                echo '<img src="'.$tcgcards.''.$card.'.png" border="0" /> ';
                $rewards .= $card.", ";
            }
            $rewards = substr_replace($rewards,"",-2);
            echo '<p><strong>Random Pack (x'.$random.'):</strong> '.$rewards.'</p></center>';
            $today = date("Y-m-d", strtotime("now"));
            $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','Purchases','Random Pack','($random)','$rewards','$today')");
            $database->query("UPDATE `user_items` SET `cards`=cards+'$cards' WHERE `name`='$player'");
        }
    }
} // END PROCESS RANDOM PACK

else if ($_SERVER['QUERY_STRING'] == "chance") {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
        exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
    } else {
        $chance = $sanitize->for_db($_POST['chance']);
        $diff = 50*$chance;
        $cards = 3*$chance;

        // CHECK IF THERE ARE ENOUGH GOLDS
        if ($item['cake'] < $diff) {
            echo '<h1>Shoppe : Halt!</h1>
            <p>Sorry, but it seems like you don\'t have enough cakes on your account to purchase this pack. Play more games to earn more cakes before making a purchase!</p>';
            exit();
        }

        $update = $database->query("UPDATE `user_items` SET `cake`=cake-'$diff' WHERE `name`='$player'");

        if ($update == TRUE) {
            echo '<h1>Shoppe : Chance Pack</h1>
            <p>Thank you for purchasing '.$chance.' chance pack(s) from our inventory! A total of <b>'.$diff.'</b> cakes has been deducted from your account.</p>
            <center>';
            $range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Weekly' ORDER BY `id` DESC");
            $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' AND `released`='".$range['timestamp']."'");
            $min=1; $max=mysqli_num_rows($sql); $rewards = null;
            for($i=0; $i<$cards; $i++) {
                mysqli_data_seek($sql,rand($min,$max)-1);
                $row = mysqli_fetch_assoc($sql);
                $digits = rand(01,$row['count']);
                if ($digits < 10) { $_digits = "0$digits"; }
                else { $_digits = $digits; }
                $card = "$row[filename]$_digits";
                echo '<img src="'.$tcgcards.''.$card.'.png" border="0" /> ';
                $rewards .= $card.", ";
            }
            $rewards = substr_replace($rewards,"",-2);
            echo '<p><strong>Chance Pack (x'.$random.'):</strong> '.$rewards.'</p></center>';
            $today = date("Y-m-d", strtotime("now"));
            $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','Purchases','Chance Pack','($chance)','$rewards','$today')");
            $database->query("UPDATE `user_items` SET `cards`=cards+'$cards' WHERE `email`='$login'");
        }
    }
} // END PROCESS CHANCE PACK

else {
    if (isset($_POST['exchange'])) {
        $total = $sanitize->for_db($_POST['diff']);
        $subTicket = 2*$total;
        echo '<h1>Shoppe : Exchanged Tickets</h1>
        <p>Thank you for exchanging your tickets into cards! You may now take your choice cards below.</p>
        <center>';
        $database->query("UPDATE `user_items` SET `ticket`=ticket-'$subTicket' WHERE `name`='$player'");
        for($i=1; $i<=$total; $i++) {
            $card = "choice$i";
            $card2 = "num$i";
            echo "<img src=\"$tcgcards";
            echo $_POST[$card];
            echo $_POST[$card2];
            echo ".png\" />\n";
            $choices .= $_POST[$card].$_POST[$card2].", ";
        }
        $choices = substr_replace($choices,"",-2);
        echo '<p><strong>Vial Exchange (x'.$total.'):</strong> -'.$subVial.' vials for '.$choices.'</p></center>';
        $today = date("Y-m-d", strtotime("now"));
        $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','Exchanges','Vial Exchange','(x$total)','-$subVial vials for $choices','$today')");
        $database->query("UPDATE `user_items` SET `cards`=cards+'$total' WHERE `email`='$login'");
    } else {
        $ticket = $sanitize->for_db($_POST['ticket']);
        $diff = (int)$ticket / 2;

        // CHECK IF VIAL IS EVEN OR ODD
        if ($ticket % 2 !== 0) {
            echo '<h1>Shoppe : Halt!</h1>
            <p>You have entered an invalid amount of tickets! Please make sure that you fill in the correct amount of tickets needed for exchanging using <u>even numbers</u> only.</p>';
            exit();
        }

        // CHECK IF THERE ARE ENOUGH VIALS
        if ($item['ticket'] < $ticket) {
            echo '<h1>Shoppe : Halt!</h1>
            <p>Sorry, but it seems like you don\'t have enough tickets on your account to make this exchange. Play more games to earn more tickets before making an exchange!</p>';
            exit();
        }

        echo '<h1>Shoppe : Ticket Exchange</h1>
        <p>Kindly use the form below to get your choice cards! A total of <b>'.$ticket.'</b> tickets will be deducted from your account upon checkout.</p>
        <center><form method="post" action="/shoppe.php?ticket">
        <input type="hidden" name="diff" value="'.$diff.'" />
        <table width="80%" cellspacing="3" class="border">';
        for($i=1; $i<=$diff; $i++) {
            echo '<tr><td class="headLine" width="25%">Choice '.$i.'</td>
                <td class="tableBody" width="75%"><select name="choice'.$i.'" style="width:80%;">';
                $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' ORDER BY `filename` ASC");
                while ($card = mysqli_fetch_assoc($sql)) {
                    echo '<option value="'.$card['filename'].'">'.$card['deckname'].' ('.$card['filename'].')</option>';
                }
                echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1" /></td>
            </tr>';
        }
        echo '<tr><td colspan="2" class="tableBody" align="center"><input type="submit" name="exchange" class="btn-success" value="Checkout" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td></tr></table>
        </form></center>';
    }
} // END PROCESS VIAL EXCHANGE

include($footer);
?>