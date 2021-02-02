<?php
include("admin/class.lib.php");
include($header);

if (empty($login)) {
    header("Location: account.php?do=login");
}

if (empty($form)) {
    header("Location: account.php");
}

$result = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active'") or die("Unable to select from database.");

###############################
########## DO CLAIMS ##########
###############################
if ($form == "deck-claims") {
    $month = date("m", strtotime("now"));
    $max = $database->num_rows("SELECT count(*) FROM `tcg_donations` WHERE `name`='$player' AND `date`='$month' GROUP BY `date`");
    if ($max < 5) {
        if ( isset($_POST['submit']) ) {
            foreach ($_POST as $key => $value) {
                $num_chk = $database->num_rows("SELECT * FROM `tcg_donations` WHERE `deckname`='".$_POST['deckname']."'");
                $value = trim($value);
                if (empty($value)) {
                    exit("<h1>Error</h1>\n<p>All fields are required. Please go back and complete the form.</p>");
                } elseif ($num_chk!=0) {
                    exit("<h1>Error</h1>\n<p>Someone has already claimed that deck! Please choose another nature-related subject to claim, thank you!</p>");
                }
                $_POST[$key] = stripslashes(strip_tags($value));
            }
            $name = $sanitize->for_db($_POST['name']);
            $cat = $sanitize->for_db($_POST['category']);
            $deck = $sanitize->for_db($_POST['deckname']);
            $feat = $sanitize->for_db($_POST['feature']);
            $pass = $sanitize->for_db($_POST['pass']);
            $series = $sanitize->for_db($_POST['series']);
            $date = date("Y-m-d", strtotime("now"));
            
            $insert = $database->query("INSERT INTO `tcg_donations` (`name`,`category`,`deckname`,`feature`,`series`,`type`,`status`,`pass`,`date`) VALUES ('$name','$cat','$deck','$feat','$series','Claims','Pending','$pass','$date')");
            
            if ($insert == TRUE) { $success[] = "Your deck claim has been added to the database."; }
            else { $error[] = "There was an error while processing your donations.<br />Kindly send us your donation details instead at <u>".$tcgemail."</u>. ".mysqli_error().""; }
        } // END PROCESS

        echo '<h1>Deck Claims</h1>
        <p>Use the form below to submit your claims. Please make sure that the deck you\'re about to claim hasn\'t been claimed by anyone else. All claims are password-protected by the claimant, so don\'t forget to provide any dummy password that you can use when you\'re going to <a href="/services.php?form=deck-donations">send your donations</a>.</p><center>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        echo '</center><form method="post" action="/services.php?form=deck-claims">
        <table width="100%" cellspacing="3" class="border">
            <tr>
                <td class="headLine" width="15%">Name:</td><td class="tableBody"><input type="text" name="name" value="'.$player.'" readonly style="width:90%;"></td>
                <td class="headLine" width="15%">Password:</td><td class="tableBody"><input type="text" name="pass" placeholder="for donation purposes" style="width:90%;"></td>
            </tr>
            <tr>
                <td class="headLine" width="15%">Category:</td><td class="tableBody" width="35%"><select name="category" style="width:97%;">
                <option value="">-----</option>';
                $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                for($i=1; $i<=$c; $i++) {
                    $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
                    echo '<option value="'.$cat['name'].'">'.$cat['name'].'</option>';
                }
                echo '</td>
                <td class="headLine" width="15%">File Name:</td><td class="tableBody" width="35%"><input type="text" name="deckname" style="width:90%;" placeholder="e.g. blackcats"></td>
            </tr>
            <tr>
                <td class="headLine">Feature:</td><td class="tableBody"><input type="text" name="feature" placeholder="e.g. Black Cats" style="width:90%;"></td>
                <td class="headLine">Series:</td><td class="tableBody"><input type="text" name="series" placeholder="e.g. IDOLiSH7" style="width:90%;"></td>
            </tr>
            <tr><td class="tableBody" align="center" colspan="4"><input type="submit" name="submit" id="submit" class="btn-success" value="Send Claims" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
        </table>
        </form>';
    } else {
        echo '<h1>Claims Limit!</h1>
        <p>You\'ve already reached the maximum number of claims for this month! Kindly wait until next month to make your claims, thank you!</p>';
    } // END DONATION FORM
}

##################################
########## DO DONATIONS ##########
##################################
else if ($form == "deck-donations") {
    $date = date("Y-m-d", strtotime("now"));
    $max = $database->num_rows("SELECT * FROM `tcg_donations` WHERE `name`='$player' AND `date`='$date' GROUP BY `date`");
    if ($max < 5) {
        if ( isset($_POST['submit']) ) {
            foreach ($_POST as $key => $value) {
                $value = trim($value);
                if (empty($value)) {
                    exit("<h1>Error</h1>\nAll fields are required. Please go back and complete the form.");
                }
                $_POST[$key] = stripslashes(strip_tags($value));
            }
            $deck = $sanitize->for_db($_POST['deckname']);
            $name = $sanitize->for_db($_POST['name']);
            $pass = $sanitize->for_db($_POST['pass']);
            $url = $sanitize->for_db($_POST['url']);
            $date = date("Y-m-d", strtotime("now"));
            
            $row = $database->get_assoc("SELECT * FROM `tcg_donations` WHERE `deckname`='$deck'");
            if ($row['pass']!=$pass) { exit('<h1>Deck Donations : Error</h1><p>It seems like the password you\'ve provided is incorrect!</p>'); }
            
            $update = $database->query("UPDATE `tcg_donations` SET url='$url', type='Donation' WHERE deckname='$deck' AND pass='$pass'");
            
            if ($update == TRUE) {
                if ($row['category'] == "Puzzle") {
                    $database->query("INSERT INTO `user_rewards` (`name`,`type`,`subtitle`,`mcard`,`cards`,`x1`,`x2`,`timestamp`) VALUES ('$name','Donations','($deck)','No','1','2','0','$date')");
                } else {
                    $database->query("INSERT INTO `user_rewards` (`name`,`type`,`subtitle`,`mcard`,`cards`,`x1`,`x2`,`timestamp`) VALUES ('$name','Donations','($deck)','No','3','2','0','$date')");
                }
                $success[] = "Your deck donation has been received and your rewards has been sent!<br />Please standby for your deck should there be any necessary image replacements needed.";
            } else {
                $error[] = "Sorry, there was an error while processing your donations.<br />Kindly send us your donation details instead at <u>".$tcgemail."</u>. ".mysqli_error()."";
            }
        } // END PROCESS

        echo '<h1>Deck Donations</h1>
        <p>Use the form below to submit your donations. Please keep in mind the exclusive guidelines before donating any deck.</p>
        <!-- CHANGE TO YOUR OWN DONATION GUIDELINES -->
        <ul><li>Donated images must be in high quality and unedited, preferrably 600x600 pixels up to 1600x1600 pixels.</li>
        <li>Horizontal images are much preferred than vertical ones to avoid the subjects getting cropped just to fit the card template.</li>
        <li>Only images that is related to SUBJECT that will fit to our sets are allowed.</li>
        <li>Donations need at least 25 images, but more is encouraged.</li>
        <li>You can donate up to <b>X decks</b> per month.</li>
        <li>For every <b>individual deck</b> you donate, you will get X random cards and X '.$x1.'s. You\'ll get X random card and X '.$x1.' for <b>puzzle decks</b>.</li></ul><center>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        echo '</center><form method="post" action="/services.php?form=deck-donations">
        <input type="hidden" name="name" id="name" value="'.$row['name'].'">
        <table width="100%" cellspacing="3" class="border">
            <tr>
                <td class="headLine" width="15%">File Name:</td><td class="tableBody"><input type="text" name="deckname" placeholder="e.g. cherryblossoms" style="width:90%;"></td>
                <td class="headLine" width="15%">Password:</td><td class="tableBody"><input type="text" name="pass" placeholder="********" style="width:90%;"></td>
            </tr>
            <tr><td class="headLine">Link:</td><td class="tableBody" colspan="3"><input type="text" name="url" placeholder="Link to download your donation" style="width:96%;"></td></tr>
            <tr><td class="tableBody" align="center" colspan="4"><input type="submit" name="submit" class="btn-success" value="Send Donation" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
        </table>
        </form>';
    } else {
        echo '<h1>Donations Limit!</h1>
        <p>You\'ve already reached the maximum number of donations for this month! Kindly wait until next month to make your claims, thank you!</p>';
    } // END DONATION FORM
}

##################################
########## DO MASTERIES ##########
##################################
else if ($form == "masteries") {
    if ($act=="sent") {
        if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
        else {
            $check->Value();
            $id = $sanitize->for_db($_POST['id']);
            $name = $sanitize->for_db($_POST['name']);
            $email = $sanitize->for_db($_POST['email']);
            $mastered = $sanitize->for_db($_POST['mastered']);
            $new = $sanitize->for_db($_POST['new']);
            
            $rowMas1 = $database->query("SELECT * FROM `user_items` WHERE id='$id'");
            while($rowmas=mysqli_fetch_assoc($rowMas1)) {
                if($rowmas['mastered']!="None") { $mast1="$rowmas[mastered], "; }
                else { $mast1=""; }
            }
            $update = $database->query("UPDATE `user_items` SET mastered='$mast1$mastered' WHERE id='$id'");
            
            $rowMas2 = $database->query("SELECT * FROM `tcg_cards` WHERE filename='$mastered'");
            while($rowmas2=mysqli_fetch_assoc($rowMas2)) {
                if($rowmas2['masters']!="None") { $mast2="$rowmas2[masters], "; }
                else { $mast2=""; }
            }
            $update2 = $database->query("UPDATE `tcg_cards` SET masters='$mast2$name' WHERE filename='$mastered'");
            $mast = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE filename='$mastered'");

            // Do things for activities recording
            $date = date("Y-m-d", strtotime("now"));
            $activity = '<span class="fas fa-flag-checkered" aria-hidden="true"></span> <a href="/members.php?id='.$name.'">'.$name.'</a> mastered the <a href="/cards.php?view=released&deck='.$mastered.'">'.$mast['deckname'].'</a> deck!';
            
            if($update == TRUE && $update2 == TRUE) {
                $database->query("UPDATE `user_list` SET collecting='$new' WHERE id='$id'");
                $database->query("INSERT INTO `tcg_activities` (`name`,`activity`,`type`,`slug`,`date`) VALUES ('$name','$activity','master','$mastered','$date')");

                echo '<h1>Congrats!</h1>
                <p>Congrats on mastering '.$mastered.', '.$name.'! Here are your rewards. If you have mastered more than one deck, please do not use the back button to fill out another form (you will receive the same random cards if you do). A copy of these rewards have been recorded on your on-site permanent activity logs.</p>
                <center>';
                $min=1; $max = mysqli_num_rows($result); $rewards = null; $choices = null;
                for($i=1; $i<=$settings->getValue('cards_master_choice'); $i++) {
                    $card = "choice$i";
                    $card2 = "choicenum$i";
                    echo "<img src=\"$tcgcards";
                    echo $_POST[$card];
                    echo $_POST[$card2];
                    echo ".png\" />\n";
                }
                for($i=0; $i<$settings->getValue('cards_master_reg'); $i++) {
                    mysqli_data_seek($result,rand($min,$max)-1);
                    $row = mysqli_fetch_assoc($result);
                    $digits = rand(01,$row['count']);
                    if ($digits < 10) { $_digits = "0$digits"; }
                    else { $_digits = $digits; }
                    $card = "$row[filename]$_digits";
                    echo "<img src=\"$tcgcards$card.png\" border=\"0\" /> ";
                    $rewards .= $card.", ";
                } echo '<br />';
                for($i=1; $i<=$settings->getValue('cards_master_choice'); $i++) {
                    $card = "choice$i";
                    $card2 = "choicenum$i";
                    $choices .= $_POST[$card].$_POST[$card2].", ";
                }
                echo '<img src="/images/'.$settings->getValue('x1').'" /> [x'.$settings->getValue('master_x1').']';
                echo '<p><strong>Deck Mastery ('.$mastered.'):</strong> '.$choices.''.$rewards.'+'.$settings->getValue('master_x1').' '.$x1.'s';
                echo '</p></center>';
                $today = date("Y-m-d", strtotime("now"));
                $newSet = $choices."".$rewards."+".$settings->getValue('master_x1')." ".$x1."s";
                $total = $settings->getValue('cards_master_choice') + $settings->getValue('cards_master_reg');
                $database->query("INSERT INTO `user_logs` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','Service','Deck Mastery','($mastered)','$newSet','$today')");
                $database->query("UPDATE `user_items` SET `x1`=x1+'".$settings->getValue('master_x1')."', `cards`=cards+'$total' WHERE `name`='$player'");
            } else {
                echo '<h1>Error</h1>
                <p>It looks like there was an error in processing your mastery form. Send the information to '.$tcgemail.' and we will send you your rewards ASAP. Thank you and sorry for the inconvenience.</p>';
            }
        }
    } // END EMPTY STATUS
    else {
        echo '<h1>Master Form</h1>
        <p>Congratulations on collecting enough cards to master a deck! Fill out the form below to receive your rewards.<br /><b>Please fill out one form for each mastered deck!</b></p>
        <form method="post" action="/services.php?form=masteries&action=sent">
        <input type="hidden" name="id" value="'.$row['id'].'" />
        <input type="hidden" name="name" value="'.$row['name'].'" />
        <input type="hidden" name="email" value="'.$row['email'].'" />';
        for($i=1; $i<=$settings->getValue('cards_master_reg'); $i++) {
            echo '<input type="hidden" name="random'.$i.'" value="'; $general->randtype('Active'); echo "\" />\n";
        }
        echo '<center><table cellspacing="3" width="100%" class="border">
        <tr><td class="headLine" width="30%">Mastered:</td><td class="tableBody"><select name="mastered" style="width: 97%;">
        <option value="">-----</option>';
        $mast = $database->query("SELECT * FROM `tcg_cards` WHERE `masterable`='Yes' AND `status`='Active' ORDER BY `filename` ASC");
        while($mas=mysqli_fetch_assoc($mast)) {
            echo '<option value="'.$mas['filename'].'">'.$mas['deckname'].' ('.$mas['filename'].")</option>\n";
        }
        echo '</select></td></tr>
        <tr><td class="headLine">Now Collecting:</td><td class="tableBody"><select name="new" style="width: 97%;">
        <option value="">-----</option>';
        $coll = $database->query("SELECT * FROM `tcg_cards` WHERE `masterable`='Yes' AND `status`='Active' ORDER BY `filename` ASC");
        while($col=mysqli_fetch_assoc($coll)) {
            echo '<option value="'.$col['filename'].'">'.$col['deckname'].' ('.$col['filename'].")</option>\n";
        }
        echo '</select></td></tr>
        <tr><td valign="top" class="headLine">Choice Card(s):</td><td class="tableBody">';
        for($i=1; $i<=$settings->getValue('cards_master_choice'); $i++) {
            echo '<select name="choice'.$i.'" style="width: 83%;">';
            echo '<option value="">---</option>';
            $choice = $database->query("SELECT * FROM `tcg_cards` WHERE `masterable`='Yes' AND `status`='Active' ORDER BY `filename` ASC");
            while($cho=mysqli_fetch_assoc($choice)) {
                $filename=stripslashes($cho['filename']);
                echo '<option value="'.$filename.'">'.$cho['deckname'].' ('.$filename.")</option>\n";
            }
            echo '</select> <input type="text" name="choicenum'.$i.'" placeholder="00" size="1" maxlength="2" /><br />';
        }
        echo "</td></tr>\n";
        echo '<tr><td colspan="2" class="tableBody" align="center"><input type="submit" name="submit" class="btn-success" value="Send Mastery" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
        </table><center>
        </form>';
    }
} // END DO MASTERIES

##################################
########## DO LEVEL UPS ##########
##################################
else if ($form == "level-up") {
    if ($act=="sent") {
        if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
        else {
            $check->Value();
            $id = $sanitize->for_db($_POST['id']);
            $name = $sanitize->for_db($_POST['name']);
            $email = $sanitize->for_db($_POST['email']);
            $level = $sanitize->for_db($_POST['newlevel']);

            // Check level for activity recording
            $date = date("Y-m-d", strtotime("now"));
            $update = $database->query("UPDATE `user_list` SET level='$level' WHERE id='$id'");
            $lvlnow = $database->get_assoc("SELECT * FROM `user_list` WHERE `id`='$id'");
            $lvlNew = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `level`='$level'"); // Fetch new level
            $diff = $lvlnow['level'] - 1;
            $lvlOld = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `level`='$diff'");
            $lvlSlug = $lvlOld['name'] .' > '. $lvlNew['name'];
            $activity = '<span class="fas fa-level-up-alt" aria-hidden="true"></span> <a href="/members.php?id='.$name.'">'.$name.'</a> ranked up from '.$lvlOld['name'].' to '.$lvlNew['name'].'!';

            if($update == TRUE) {
                $database->query("INSERT INTO `tcg_activities` (`name`,`activity`,`type`,`slug`,`date`) VALUES ('$name','$activity','level','$lvlSlug','$date')");
                echo '<h1>Congrats!</h1>
                <p>Congrats on leveling up, '.$name.'! Here are your rewards. If you have leveled up more than once, please do not use the back button to fill out another form (you will receive the same random cards if you do). A copy of these rewards have been recorded on your on-site permanent activity logs.</p>
                <center>';
                $min=1; $max = mysqli_num_rows($result); $rewards = null; $choices = null;
                for($i=1; $i<=$settings->getValue('cards_level_choice'); $i++) {
                    $card = "choice$i"; $card2 = "choicenum$i";
                    echo "<img src=\"$tcgcards";
                    echo $_POST[$card]; echo $_POST[$card2];
                    echo ".png\" />\n";
                }
                for($i=0; $i<$settings->getValue('cards_level_reg'); $i++) {
                    mysqli_data_seek($result,rand($min,$max)-1);
                    $row = mysqli_fetch_assoc($result);
                    $digits = rand(01,$row['count']);
                    if ($digits < 10) { $_digits = "0$digits"; }
                    else { $_digits = $digits; }
                    $card = "$row[filename]$_digits";
                    echo "<img src=\"$tcgcards$card.png\" border=\"0\" /> ";
                    $rewards .= $card.", ";
                } echo '<br />';
                echo '<img src="/images/'.$settings->getValue('x1').'" /> [x'.$settings->getValue('level_x1').'] <img src="/images/'.$settings->getValue('x2').'" /> [x'.$$settings->getValue('level_x2').']';
                echo '<p><strong>Level Up ('.$level.'):</strong> ';
                for($i=1; $i<=$settings->getValue('cards_level_choice'); $i++) {
                    $card = "choice$i"; $card2 = "choicenum$i";
                    $choices .= $_POST[$card].$_POST[$card2].", ";
                }
                echo $choices.''.$rewards.'+'.$settings->getValue('level_x1').' '.$x1.'s, +'.$settings->getValue('level_x2').' '.$x2.'s</p></center>';
                $today = date("Y-m-d", strtotime("now"));
                $total = $settings->getValue('cards_level_choice') + $settings->getValue('cards_level_reg');
                $newSet = $choices."".$rewards."+".$settings->getValue('level_x1')." ".$x1."s, +".$settings->getValue('level_x2')." ".$x2."s";
                $database->query("INSERT INTO `user_logs` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','Service','Level Up','(".$level.". ".$lvlNew['name'].")','$newSet','$today')");
                $database->query("UPDATE `user_items` SET `x1`=x1+'".$settings->getValue('level_x1')."', `x2`=x2+'".$settings->getValue('level_x2')."', `cards`=cards+'$total' WHERE `name`='$player'");
            } else {
                echo '<h1>Error</h1>
                <p>It looks like there was an error in processing your level up form. Send the information to '.$tcgemail.' and we will send you your rewards ASAP. Thank you and sorry for the inconvenience.</p>';
            }
        }
    } // END DO ACTION SENT
    else {
        $select = $database->query("SELECT * FROM `user_list` WHERE email='$login'");
        while($row=mysqli_fetch_assoc($select)) {
            echo '<h1>Level Up Form</h1>
            <p>Congratulations on collecting enough cards to level up! Fill out the form below to receive your rewards. <b>Please fill out one form for each level up!</b></p>
            <form method="post" action="/services.php?form=level-up&action=sent">
            <input type="hidden" name="id" value="'.$row['id'].'" />
            <input type="hidden" name="name" value="'.$row['name'].'" />
            <input type="hidden" name="email" value="'.$row['email'].'" />';
            for($i=1; $i<=$settings->getValue('cards_level_reg'); $i++) {
                echo "<input type=\"hidden\" name=\"random$i\" value=\""; $general->randtype('Active'); echo "\" />\n";
            }
            echo '<center><table cellspacing="3" width="100%" class="border">
            <tr><td class="headLine" width="30%">New Level:</td><td class="tableBody"><select name="newlevel" style="width: 97%;">';
            $l = $database->num_rows("SELECT * FROM `tcg_levels`");
            $cur = $row['level'] + 1;
            for($i=$cur; $i<=$l; $i++) {
                $lvl = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `level`='$i'");
                echo '<option value="'.$i.'">'.$lvl['name'].' (Level '.$i.')</option>';
            }
            echo '</select></td></tr>
            <tr><td valign="top" class="headLine">Choice Card(s):</td><td class="tableBody">';
            for($i=1; $i<=$settings->getValue('cards_level_choice'); $i++) {
                echo "<select name=\"choice$i\" style=\"width: 83%;\">\n";
                echo "<option value=\"\">---</option>\n";
                $query = $database->query("SELECT * FROM `tcg_cards` WHERE masterable='Yes' AND status='Active' ORDER BY filename ASC");
                while($row2=mysqli_fetch_assoc($query)) {
                    $filename=stripslashes($row2['filename']);
                    echo "<option value=\"$filename\">$row2[deckname] ($filename)</option>\n";
                }
                echo "</select> <input type=\"text\" name=\"choicenum$i\" value=\"00\" size=\"1\" maxlength=\"2\" /><br />";
            }
            echo "</td></tr>\n";
            echo '<tr><td colspan="2" class="tableBody" align="center"><input type="submit" name="submit" class="btn-success" value="Level Up" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
            </table><center>
            </form>';
        } // END WHILE
    }
} // END DO LEVEL UPS

########################################
########## DO TRADING REWARDS ##########
########################################
else if ($form == "trading-rewards") {
    if ($act == "add-trades") {
        if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
        else {
            $name = $sanitize->for_db($_POST['name']);
            $out = htmlspecialchars(strip_tags($_POST['out']));
            $inc = htmlspecialchars(strip_tags($_POST['inc']));
            $to = htmlspecialchars(strip_tags($_POST['to']));
            $date = $_POST['year']."-".$_POST['month']."-".$_POST['day'];

            $total = explode(",", $out);
            $total = count($total);

            $logs = "<b>".$date.":</b> Traded ".$to." my ".$out." for ".$inc."<br />\n";

            $row = $database->get_assoc("SELECT * FROM `trades` WHERE name='$name'");
            if (!get_magic_quotes_gpc()) { $row['trade'] = addslashes($row['trade']); }
            $newlog = $logs.''.$row['trade'];

            $result = $database->query("INSERT INTO `user_trades` (`name`,`trader`,`outgoing`,`incoming`,`timestamp`) VALUES ('$name','$to','$out','$inc','$date')") or print("Can't insert into table trades_$name.<br />" . $result . "<br />Error:" . mysqli_connect_error($result));

            if ($result != false) {
                $database->query("UPDATE `trades` SET `points`=points+'$total', `updated`='$date' WHERE `name`='$name'");
                echo '<h1>Trade Logs Added</h1>
                <p>Your external trading logs has been added to the database!</p>';
            } else {
                echo '<h1>Trade Logs Error</h1>
                <p>It seems that there was a problem processing your trade logs form. Kindly send your information to <a href="mailto:'.$tcgemail.'">'.$tcgemail.'</a> or through our Discord server. Thank you and we apologize for the inconvenience.</p>';
            }
        }
    } else if ($act == "redeem") {
        if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
        else {
            $check->Value();
            $sets = htmlspecialchars(strip_tags($_POST['sets']));
            $name = $sanitize->for_db($_POST['name']);
            $diff = 25*$sets;

            $update = $database->query("UPDATE `trades` SET `points`=points-'$diff' WHERE `name`='$name'") or print("Can't insert into table user_trades.<br />" . $update . "<br />Error:" . mysqli_connect_error($update));

            if ($update != false) {
                echo '<h1>Redeem Rewards</h1>
                <p>Get your redeemed rewards for '.$sets.' set of trades below!</p><center>';
                $min=1; $max = mysqli_num_rows($result); $rewards = null; $total = 4*$sets; $cur1 = $settings->getValue('trade_x1')*$sets;
                for($i=0; $i<$total; $i++) {
                    mysqli_data_seek($result,rand($min,$max)-1);
                    $row = mysqli_fetch_assoc($result);
                    $digits = rand(01,$row['count']);
                    if ($digits < 10) { $_digits = "0$digits"; }
                    else { $_digits = $digits; }
                    $card = "$row[filename]$_digits";
                    echo "<img src=\"$tcgcards$card.png\" border=\"0\" /> ";
                    $rewards .= $card.", ";
                }
                $rewards = substr_replace($rewards,"",-2);
                echo '<img src="/images/'.$settings->getValue('x1').'" /> [x'.$cur1.']';
                echo '<p><strong>Trade Points (x'.$sets.'):</strong> '.$rewards.', +'.$cur1.' '.$x1.'s</p></center>';
                $today = date("Y-m-d", strtotime("now"));
                $newSet = $rewards.' +'.$cur1.' '.$x1.'s';
                $database->query("INSERT INTO `user_logs` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$name','Service','Trade Points','(x".$sets.")','$newSet','$today')");
                $database->query("UPDATE `user_items` SET `x1`=x1+'$cur1', `cards`=cards+'$total' WHERE `name`='$name'");
            } else {
                echo '<h1>Trading Rewards: Error</h1>
                <p>It seems that there was a problem processing your trade logs form. Kindly send your information to <a href="mailto:'.$tcgemail.'">'.$tcgemail.'</a> or through our Discord server. Thank you and we apologize for the inconvenience.</p>';
            }
        }
    } else {
        $chk = $database->get_assoc("SELECT * FROM `trades` WHERE `name`='$player'");
        if($chk['points'] < 25) {
            $current_month = date("F");
            $current_date = date("d");
            $current_year = date("Y");
            $cur_month = date("m");
            echo '<h1>Add Trade Logs</h1>
            <p>You don\'t have enough cards traded on your on-site trade logs! Kindly add your external trade logs first before claiming a new set of rewards by using the form below.</p>
            <p>- Make sure to <u>add ONLY the logs that you haven\'t turned in yet.</u></p>
            <center><div class="box-info">You now have a total worth of <b>'.$chk['points'].'</b> cards traded on your record.</div></center><br />
            <form method="post" action="/services.php?form=trading-rewards&action=add-trades">
            <input type="hidden" name="name" value="'.$row['name'].'" />
            <center><table width="100%" cellspacing="3" class="border">
            <tr>
                <td class="headLine" width="15%">Date:</td><td class="tableBody" width="45%"><select name="month" style="width:40%;">
                <option value="'.$cur_month.'">'.$current_month.'</option>';
                for($m=1; $m<=12; $m++) {
                    if ($m < 10) { $_mon = "0$m"; }
                    else { $_mon = $m; }
                    echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                }
                echo '</select> <input type="text" name="day" id="day" size="2" value="'.$current_date.'" /> ';
                //get the current year
                $start=date('Y');
                $end=$start-10;
                // set start and end year range i.e the start year
                $yearArray = range($start,$end);
                // here you displaying the dropdown list
                echo '<select name="year">
                <option value="">-----</option>';
                foreach ($yearArray as $year) {
                // this allows you to select a particular year
                $selected = ($year == $start) ? 'selected' : '';
                echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                }
            echo '</select></td>
                <td class="headLine" width="15%">Traded With:</td><td class="tableBody" width="25%"><select name="to" style="width: 97%;" />';
                $mem = $database->query("SELECT * FROM `user_list` ORDER BY name ASC");
                while($row1=mysqli_fetch_assoc($mem)) {
                    $name=stripslashes($row1['name']);
                    echo "<option value=\"$name\">$name</option>\n";
                }
                echo '</select></td>
            </tr>
            <tr>
                <td class="headLine">Outgoing:</td><td class="tableBody" colspan="3"><input type="text" name="out" placeholder="e.g. blackcats04, rubies10, mc-'.$row['name'].'" style="width:95%;" /></td>
            </tr>
            <tr>
                <td class="headLine">Incoming:</td><td class="tableBody" colspan="3"><input type="text" name="inc" placeholder="e.g. tigers11, winter17, mc-Player" style="width:95%;" /></td>
            </tr>
            <tr>
                <td class="tableBody" colspan="4" align="center"><input type="submit" name="submit" class="btn-success" value="Send Logs" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td>
            </tr>
            </table></center>
            </form>';
        } else {
            echo '<h1>Trading Rewards</h1>
            <p>You now have a total number of '.$chk['points'].' cards traded on your record. Please keep in mind that the form automatically counts 25 trades from the data you currently have, hence you can\'t change how many sets you\'ll be redeeming. Once you\'re all set, use the form below to redeem your trading rewards:</p>
            <form method="post" action="/services.php?form=trading-rewards&action=redeem">
            <input type="hidden" name="name" value="'.$row['name'].'" />
            <center><table width="100%" cellspacing="3" class="border">
            <tr>
                <td class="headLine" width="15%">Sets:</td>
                <td class="tableBody" width="35%"><input type="text" name="sets" style="width:90%" value="1" readonly /></td>
                <td class="headLine" width="15%">Redeem?</td>
                <td class="tableBody" width="35%" align="center"><input type="submit" name="submit" class="btn-success" value="Yes, please!" /></td>
            </tr>
            </table></center>
            </form>';
        }
    }
} // END TRADING REWARDS

######################################
########## DO CONTACT ADMIN ##########
######################################
else if ($form == "contact") {
    if ( isset($_POST['submit']) ) {
        $from = $sanitize->for_db($_POST['sender']);
        $to = $sanitize->for_db($_POST['recipient']);
        $message = $_POST['message'];
        $date = date("Y-m-d H:i:s", strtotime("now"));

        $message = nl2br($message);

        if (!get_magic_quotes_gpc()) {
            $message = addslashes($message);
        }

        $insert = $database->query("INSERT INTO `user_mbox` (`subject`,`message`,`sender`,`recipient`,`mbox_from`,`mbox_to`,`read_from`,`read_to`,`del_from`,`del_to`,`origin`,`timestamp`) VALUES ('General Contact','$message','$from','$to','Out','In','0','1','0','0','','$date')");

        if ( $insert == TRUE ) {
            $database->query("UPDATE `user_mbox` SET origin=LAST_INSERT_ID() WHERE id=LAST_INSERT_ID()");
            $success[] = "Thank you for sending in a contact form! I will try to get back to you within the next few days.<br />If you don\'t hear from me within a week, please poke me via Discord (Aki#6429).";
        } else {
            $error[] = "Sorry, there was an error while processing your form.<br />Send the information to ".$tcgemail." and we will send you a reply ASAP. ".mysqli_error()."";
        }
    }

    echo '<h1>General Contact</h1>
    <p>If you have any inquiries regarding '.$tcgname.' that you wish to ask or share to the administrator, please use the form below to keep in touch with them. Kindly give them at least 48 hours to get back to you! If for any reason that you haven\'t heard from them after the given time, you can poke them directly on our Discord server as chances are your email was missed or it didn\'t reach them at all.</p>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
    echo '<form method="post" action="/services.php?form=contact">
    <input type="hidden" name="sender" value="'.$row['name'].'" />
    <input type="hidden" name="recipient" value="'.$tcgowner.'" />
    <center><table width="100%" class="border" cellspacing="3">
    <tr><td valign="top" class="headLine">Message:</td><td class="tableBody"><textarea name="message" rows="5" style="width:95%;">Type your message here.</textarea></td></tr>
    <tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Send Inquiry" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
    </table></center>
    </form>';
} // END CONTACT ADMIN

#########################################
########## DO DOUBLES EXCHANGE ##########
# Use this if you're not using Yuuchin. #
#########################################
else if ($form == "doubles") {
    if($act=="sent") {
        if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
            exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
        }
        else {
            $from = $sanitize->for_db($_POST['sender']);
            $to = $sanitize->for_db($_POST['recipient']);
            $cards = htmlspecialchars(strip_tags($_POST['cards']));
            $date = date("Y-m-d H:i:s", strtotime("now"));
            
            $total = explode(",", $cards);
            $total = count($total);

            $message = "Hello, ".$tcgowner."! I have exchanged the following doubles for ".$total." random cards:\n".$cards."\nMany thanks!";

            $insert = $database->query("INSERT INTO `user_mbox` (`subject`,`message`,`sender`,`recipient`,`mbox_from`,`mbox_to`,`read_from`,`read_to`,`del_from`,`del_to`,`origin`,`timestamp`) VALUES ('Doubles Exchange','$message','$from','$to','Out','In','0','1','0','0','','$date')");

            if ( $insert == TRUE ) {
                echo '<h1>Doubles Exchange : Pick Up</h1>
                <p>Thanks for trading in your double cards. Below are your cards! Don\'t forget to take down your doubled cards and log them.</p>
                <center>';
                $min = 1; $max = mysqli_num_rows($result); $rewards = null;
                for($i=0; $i<$total; $i++) {
                    mysqli_data_seek($result,rand($min,$max)-1);
                    $row = mysqli_fetch_assoc($result);
                    $digits = rand(01,$row['count']);
                    if ($digits < 10) { $_digits = "0$digits"; }
                    else { $_digits = $digits; }
                    $card = "$row[filename]$_digits";
                    echo "<img src=\"$tcgcards$card.png\" border=\"0\" /> ";
                    $rewards .= $card.", ";
                }
                $rewards = substr_replace($rewards,"",-2);
                echo '<p><strong>Doubles Exchange:</strong> '.$rewards.'</p></center>';
                $today = date("Y-m-d", strtotime("now"));
                $database->query("INSERT INTO `user_logs` (`name`,`type`,`title`,`rewards`,`timestamp`) VALUES ('$name','Service','Doubles Exchange','$rewards','$today')");
            } else {
                echo '<h1>Doubles Exchange : Error</h1>
                <p>It looks like there was an error in processing your doubles form. Send the information to '.$tcgemail.' and we will send you your doubles ASAP. Thank you and sorry for the inconvenience.</p>';
            }
        }
    } else {
        echo '<h1>Doubles Exchange</h1>
        <p>Use this form to get rid of your doubles!<br />
        Do keep in mind that <u>only duplicates on your trade pile count</u> and separate your cards with a comma.</p>
        <blockquote>For example, if I have 2 copies of card01 from my trade pile, I can exchange one copy of it. However, if I have 2 copies of card01, 1 from my keeping and 1 from my trade pile, it won\'t be eligible for an exchange since I can trade it out easily.</blockquote>
        <form method="post" action="/services.php?form=doubles&action=sent">
        <input type="hidden" name="sender" value="'.$row['name'].'" />
        <input type="hidden" name="recipient" value="'.$tcgowner.'" />
        <table width="100%" cellspacing="3">
            <tr>
                <td width="30%" class="headLine">List your doubles:</td>
                <td width="70%" class="tableBody">
                    <input type="text" name="cards" placeholder="e.g. card04, card13, card20" style="width:50%;" /> 
                    <input type="submit" name="submit" class="btn-success" value="Exchange" />
                </td>
            </tr>
        </table>
        </form>';
    }
}
include ($footer);
?>
