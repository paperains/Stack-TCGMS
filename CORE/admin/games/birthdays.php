<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Birthdays' AND `timestamp` >= '".$range['timestamp']."'");

if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>Birthdays : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
} else {
    $date = date("m", strtotime("now"));
    $bday = date("m", strtotime($row['birthday']));

    if($bday != $date) {
        echo '<h1>GAME SET HERE - Birthdays</h1>
        <!-- CHANGE THE BLURBS -->
        <p><img src="/admin/games/images/birthdays.jpg" align="left" width="200" style="border-radius:150px;margin-right: 20px;" />Please don\'t make us sadder than sad as we can\'t be fooled easily! It is obviously not your birthday month yet... We know that everyone is excited for their own birthday presents, but you are a century early! Please hibernate for the mean time and check back in on your birthday month, which is this coming <i>'.date("F", strtotime($row['birthday'])).'</i>.</p>
        <p>If you\'re looking for more cards, you can still play some games if you haven\'t yet or trade with fellow traders. Because right now isn\'t the right time to get your presents, sorry!</p>
        <p>&nbsp;</p>';
    } else {
        if (empty($go)) {
?>
<h1>GAME SET HERE - Birthdays</h1>
<img src="/admin/games/rounds/birthdays.gif" align="left" style="margin-right: 20px;" />
<!-- CHANGE THE BLURBS -->
<p>Since this is the month of your birthday, we are very happy to celebrate it with you! As delighted as you are, we have these special presents only just for you.</p>
<p>Also take note that you have until the end of the month to get your presents, and if you have taken yours, do not forget to log it! Lastly, don't forget to include your <u>birthday milestone</u> badge's image below. It will be up on your gallery once made!</a></p>
<center>
<form method="post" action="/games.php?play=birthdays&go=presents">
<input type="hidden" name="name" value="<?php echo $row['name']; ?>" />
<input type="hidden" name="email" value="<?php echo $row['email']; ?>" />
<table width="55%" class="border" cellspacing="3">
    <tr>
        <td class="headLine" width="25%">Choice 1:</td>
        <td class="tableBody" width="75%">
            <select name="choice1" style="width:82%;">
                <option>-----</option>
                <?php
                $query = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' ORDER BY `filename` ASC");
                while($row = mysqli_fetch_assoc($query)) {
                    echo '<option value="'.$row['filename'].'">'.$row['deckname'].'</option>';
                }
                ?>
            </select><input type="text" placeholder="00" name="num1" size="1" />
        </td>
    </tr>
    <tr>
        <td class="headLine" width="25%">Choice 2:</td>
        <td class="tableBody" width="75%">
            <select name="choice2" style="width:82%;">
                <option>-----</option>
                <?php
                $query = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' ORDER BY `filename` ASC");
                while($row = mysqli_fetch_assoc($query)) {
                    echo '<option value="'.$row['filename'].'">'.$row['deckname'].'</option>';
                }
                ?>
            </select><input type="text" placeholder="00" name="num2" size="1" />
        </td>
    </tr>
    <tr>
        <td class="headLine">Image URL:</td>
        <td class="tableBody"><input type="text" name="image" placeholder="Link to your image" style="width:90%;" /></td>
    </tr>
    <tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" value="   Claim Presents!   " /></td></tr>
</table>
</form>
</center>
<?php
        } else {
            if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
                exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
            } else {
                $c1 = $sanitize->for_db($_POST['choice1']);
                $c2 = $sanitize->for_db($_POST['choice2']);
                $n1 = $sanitize->for_db($_POST['num1']);
                $n2 = $sanitize->for_db($_POST['num2']);
                $img = $sanitize->for_db($_POST['image']);
                $email = $sanitize->for_db($_POST['email']);
                $name = $sanitize->for_db($_POST['name']);

                $to = "$tcgemail";
                $subject = "Birthday Milestone!";
                $message = "$name just sent you their image to use for their birthday milestone badge!\n";
                $message .= "Image URL: $img\n\n";
                $message .= "Make sure to make their badge before the month ends!";
                $headers = "From: $name <$email>\n";
                $headers .= "Reply-To: $name <$email>";

                $choice1 = "$c1$n1";
                $choice2 = "$c2$n2";

                if (mail($to,$subject,$message,$headers)) {
                    echo '<h1>Birthdays - Presents</h1>
                    <center><img src="'.$tcgcards.''.$choice1.'.png"><img src="'.$tcgcards.''.$choice2.'.png">';
                    // CHECK FOR DOUBLE REWARDS
                    $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
                    if ($getWish['set'] == "GAME SET HERE") { $rand = 12; $cur1 = 20; $cur2 = 10; $total = 14; }
                    else { $rand = 6; $cur1 = 10; $cur2 = 5; $total = 8; }
                    // SPIT OUT REWARDS
                    $query = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active'");
                    $min = 1; $max = mysqli_num_rows($query); $rewards = null;
                    for($i=0; $i<$rand; $i++) {
                        mysqli_data_seek($query,rand($min,$max)-1);
                        $row = mysqli_fetch_assoc($query);
                        $digits = rand(01,$row['count']);
                        if ($digits < 10) { $_digits = "0$digits"; }
                        else { $_digits = $digits; }
                        $card = "$row[filename]$_digits";
                        echo '<img src="/images/cards/'.$card.'.png" border="0" /> ';
                        $rewards .= $card.", ";
                    }
                    $rewards = substr_replace($rewards,"",-2);
                    echo '<img src="/images/cur1.png"> [x'.$cur1.'] <img src="/images/cur2.png"> [x'.$cur2.']';
                    echo "<p><strong>Birthdays:</strong> $choice1, $choice2, $rewards, +$cur1 currency01, +$cur2 currency02</p></center>";
                    $today = date("Y-m-d", strtotime("now"));
                    $newSet = $choice1.', '.$choice2.', '.$rewards.', +'.$cur1.' currency01, +'.$cur2.' currency02';
                    $database->query("UPDATE `user_list` SET `cur1`=cur1+'$cur1', `cur2`=cur2+'$cur2', `cards`=cards+'$total' WHERE `email`='$login'");
                    $database->query("INSERT INTO `logs_$name` (`name`,`type`,`title`,`rewards`,`timestamp`) VALUES ('$name','GAME SET HERE','Birthdays','$newSet','$today')");
                } else {
                    echo '<p>There was an error processing your form and the mail was not sent.</p>';
                }
            }
        }
    }
}
?>
