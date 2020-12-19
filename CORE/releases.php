<?php
include('admin/class.lib.php');
include($header);

$go = isset($_GET['go']) ? $_GET['go'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;

if (empty($login)) {
    header("Location: account.php?do=login");
}

$user = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `type`='Releases' AND `subtitle`='($date)'");

if (empty($go)) {
    if ($logChk['subtitle'] == "(".$date.")") {
        echo '<h1>Update Pulls ('.$date.') : Halt!</h1>
        <p>You have already pulled cards from this update! If you missed your pulls, here they are:</p>
        <center>';
        $logs = $database->query("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `subtitle`='($date)'");
        while($row=mysqli_fetch_assoc($logs)) {
            echo '<b>'.$row['title'].' '.$row['subtitle'].':</b> '.$row['rewards'].'<br />';
        }
        echo '</center>';
    } else {
    $get = $database->query("SELECT * FROM `tcg_blog` WHERE `timestamp`='$date'");
    while ($row = mysqli_fetch_assoc($get)) {
        echo '<h1>Update Pulls : '.$row['timestamp'].'</h1>
        <li>You can grab a total worth of <b>'.$row['amount'].'</b> cards from this release but not more than <b>2</b> cards per deck.</li>
        <li><u>You can only submit your pulls once</u>. Be sure of your choices before submitting.</li>
        <li>Your choices are added to your activity log and cannot be changed.</li>
        <li>Do not forget to comment with what you have taken!</li>
        <h2>Decks Released</h2>
        <center>';
        $decks = $row['decks'];
        $array = explode(', ',$decks);
        $array_count = count($array);
        for($i=0; $i<=($array_count -1); $i++) {
            $digits = rand(01,20);
            if ($digits < 10) { $digit = "0".$digits; }
            else { $digit = $digits; }
            echo "<a href=\"/cards.php?view=released&deck=$array[$i]\"><img src=\"$tcgcards";
            echo "$array[$i]$digit";
            echo ".png\" border=\"0\" /></a>\n";
        }
        echo '</center>';
    }
    $get2 = $database->query("SELECT * FROM `tcg_blog` WHERE `timestamp`='$date'");
    $pull = mysqli_fetch_assoc($get2);
    echo '<center>Select the decks you want to pull for this release below:</br />
    <form method="post" action="/releases.php?date='.$date.'&go=pulled">
    <input type="hidden" value="'.$user['name'].'">
    <table width="100%" cellspacing="3" class="border">
        <tr><td class="headLine" width="25%" valign="top">Regular Pulls:</td><td class="tableBody" width="75%">';
        for($i=1; $i<=$pull['amount']; $i++) {
            echo "<select name=\"pull$i\" style=\"width:85%;\">\n";
            echo "<option value=\"\">---</option>\n";
            $query = $database->query("SELECT * FROM `tcg_cards` WHERE status='Active' AND released='$date' ORDER BY filename ASC");
            while($row=mysqli_fetch_assoc($query)) {
                $filename=stripslashes($row['filename']);
                echo "<option value=\"$filename\">$row[deckname] ($filename)</option>\n";
            }
            echo "</select> <input type=\"text\" name=\"pullnum$i\" placeholder=\"00\" size=\"1\" maxlength=\"2\" /><br />";
        }
        echo '</td></tr>
    </table>';
        // CHECK FOR DONATIONS AND CREATIONS
        $dname = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `released`='$date' AND `donator`='$player'");
        $mname = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `released`='$date' AND `maker`='$player'");
            if ($dname['donator'] == $player) {
                echo '<p>Select your extra pulls for the decks you have donated below:<br />';
                $check1 = $database->num_rows("SELECT * FROM `tcg_cards` WHERE `released`='$date' AND `donator`='$player'");
                echo '<input type="hidden" name="donator_amount" value="'.$check1.'" />
                <table width="100%" cellspacing="3" class="border">';
                echo '<tr><td class="headLine" valign="top">Donator Pulls:</td><td class="tableBody"><center>Take only <b>one card</b> from each <u>donated decks</u>:</center>';
                for($i=1; $i<=$check1; $i++) {
                    echo "<select name=\"donator$i\" style=\"width:85%;\">\n";
                    echo "<option value=\"\">---</option>\n";
                    $query = $database->query("SELECT * FROM `tcg_cards` WHERE `released`='$date' AND `donator`='$player' ORDER BY `filename` ASC");
                    while($row=mysqli_fetch_assoc($query)) {
                        $filename=stripslashes($row['filename']);
                        echo "<option value=\"$filename\">$row[deckname] ($filename)</option>\n";
                    }
                    echo "</select> <input type=\"text\" name=\"donatornum$i\" placeholder=\"00\" size=\"1\" maxlength=\"2\" /><br />";
                }
                echo '</td></tr>
                </table></p>';
            }

            if ($mname['maker'] == $player) {
                echo '<p>Select your extra pulls for the decks you have made below:<br />';
                $check2 = $database->num_rows("SELECT * FROM `tcg_cards` WHERE `released`='$date' AND `maker`='$player'");
                echo '<input type="hidden" name="maker_amount" value="'.$check2.'" />
                <table width="100%" cellspacing="3" class="border">';
                $check = $database->num_rows("SELECT * FROM `tcg_cards` WHERE `released`='$date' AND `maker`='$player'");
                echo '<tr><td class="headLine" valign="top">Maker Pulls:</td><td class="tableBody"><center>Take only <b>one card</b> from each <u>decks made</u>:</center>';
                for($i=1; $i<=$check2; $i++) {
                    echo "<select name=\"maker$i\" style=\"width:85%;\">\n";
                    echo "<option value=\"\">---</option>\n";
                    $query = $database->query("SELECT * FROM `tcg_cards` WHERE `released`='$date' AND `maker`='$player' ORDER BY `filename` ASC");
                    while($row=mysqli_fetch_assoc($query)) {
                        $filename=stripslashes($row['filename']);
                        echo "<option value=\"$filename\">$row[deckname] ($filename)</option>\n";
                    }
                    echo "</select> <input type=\"text\" name=\"makernum$i\" placeholder=\"00\" size=\"1\" maxlength=\"2\" /><br />";
                }
                echo '</td></tr>
                </table></p>';
            }
        
    echo '<input type="submit" name="submit" class="btn-success" value="Claim Pulls" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" />
    </form>
    </center>';
    }
} else if ($go == "pulled") {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
    else {
        $check->Value();
        $today = date("Y-m-d", strtotime("now"));
        $mamt = $sanitize->for_db($_POST['maker_amount']);
        $damt = $sanitize->for_db($_POST['donator_amount']);
        $get = $database->get_assoc("SELECT * FROM `tcg_blog` WHERE `timestamp`='$date'");
        echo '<h1>Update Pulls ('.$date.')</h1>
        <p>Your pulls has been logged on your permanent logs, make sure to log it on your trade post as well.</p>
        <center>';
        for($i=1; $i<=$get['amount']; $i++) {
            $pcard = "pull$i";
            $pcard2 = "pullnum$i";
            echo "<img src=\"$tcgcards";
            echo $_POST[$pcard];
            echo $_POST[$pcard2];
            echo ".png\" />\n";
        }
        for($i=1; $i<=$get['amount']; $i++) {
            $ppcard = "pull$i";
            $ppcard2 = "pullnum$i";
            $pulled .= $_POST[$ppcard].$_POST[$ppcard2].", ";
        }
        $pulled = substr_replace($pulled,"",-2);
        echo '<br /><strong>Deck Release ('.$date.'):</strong> '.$pulled;
        $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','Releases','Deck Release','($date)','$pulled','$today')");
        $database->query("UPDATE `user_items` SET `cards`=cards+'".$get['amount']."' WHERE `name`='$player'");

        // CHECK DONATOR & MAKER
        if (!empty($_POST['donator1'])) {
            echo '<br /><br />';
            for($i=1; $i<=$damt; $i++) {
                $dcard = "donator$i";
                $dcard2 = "donatornum$i";
                echo "<img src=\"$tcgcards";
                echo $_POST[$dcard];
                echo $_POST[$dcard2];
                echo ".png\" />\n";
            }
            for($i=1; $i<=$damt; $i++) {
                $ddcard = "donator$i";
                $ddcard2 = "donatornum$i";
                $donated .= $_POST[$ddcard].$_POST[$ddcard2].", ";
            }
            $donated = substr_replace($donated,"",-2);
            echo '<br /><strong>Donator Pull ('.$date.'):</strong> '.$donated;
            $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','Pulls','Donator Pull','($date)','$donated','$today')");
            $database->query("UPDATE `user_items` SET `cards`=cards+'$damt' WHERE `name`='$player'");
        }

        if (!empty($_POST['maker1'])) {
            echo '<br /><br />';
            for($i=1; $i<=$mamt; $i++) {
                $mcard = "maker$i";
                $mcard2 = "makernum$i";
                echo "<img src=\"$tcgcards";
                echo $_POST[$mcard];
                echo $_POST[$mcard2];
                echo ".png\" />\n";
            }
            for($i=1; $i<=$mamt; $i++) {
                $mmcard = "maker$i";
                $mmcard2 = "makernum$i";
                $made .= $_POST[$mmcard].$_POST[$mmcard2].", ";
            }
            $made = substr_replace($made,"",-2);
            echo '<br /><strong>Maker Pull ('.$date.'):</strong> '.$made;
            $database->query("INSERT INTO `logs_$player` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','Pulls','Maker Pull','($date)','$made','$today')");
            $database->query("UPDATE `user_items` SET `cards`=cards+'$mamt' WHERE `name`='$player'");
        }
        echo '</center>';
    }
}

include($footer);
?>