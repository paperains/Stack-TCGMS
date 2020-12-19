<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Upcoming Vote' AND `timestamp` >= '".$range['timestamp']."'");

if (empty($go)) {
    if ($logChk['timestamp'] >= $range['timestamp']) {
        echo '<h1>Upcoming Vote : Halt!</h1>
        <p>You have already played this game! If you missed your rewards, here they are:</p>
        <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
    } else {
?>

<h1>GAME SET HERE - Upcoming Vote</h1>
<p>Everyone will be given the opportunity to vote for the decks they want to be released on the next update! However, please keep in mind that we do not guarantee that your voted deck will be released immediately as we will prioritize to release the decks with the most number of votes when choosing what decks to release. Before submitting your votes, kindly please take a moment to read on the reminders below:</p>
<ul>
    <li>Only decks on the <a href="/cards.php?view=upcoming">upcoming list</a> can be voted for since these are the decks that we've already made and are ready to be released.</li>
    <li>The voting form is listed as categories. This is to make sure that you will have the chance to choose <b>1</b> deck from each categories and to avoid voting for the same deck.</li>
    <li>You are not required to submit all votes, you can vote for one category or two, whichever you like. However, we encourage you to fill in these fields to maximize the votes unless the category ran out of stock pile.</li>
    <li>You can only vote once per week! So make sure to double check your choices before submitting the form.</li>
    <li>The voting form will be open every time the Weekly games are updated. Everyone will have a week period before a new round opens, so make sure to submit your votes in time.</li>
</ul>
<form method="post" action="/games.php?play=upcoming-vote&go=prize">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
    <center><table width="70%" cellpadding="0" cellspacing="3" class="border">
    <?php
    for($i=1; $i<=11; $i++) {
        $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
        for($i=1; $i<=$c; $i++) {
            $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
            $select = $database->query("SELECT * FROM `tcg_cards` WHERE `category`='$i' AND `status`='Upcoming' ORDER BY `set` ASC, `filename` ASC");
            echo '<tr><td width="30%" class="headLine">'.$cat['name'].':</td>
                <td width="70%" class="tableBody">
                    <select name="vote'.$i.'" style="width: 98%;">
                        <option value="">---</option>';
            while($row=mysqli_fetch_assoc($select)) {
                echo '<option value="'.$row['filename'].'">'.$row['deckname'].'</option>';
            }
            echo '</select>
                </td></tr>';
        }
    }
    ?>
        <tr>
            <td align="center" colspan="2" class="tableBody"><input type="submit" name="submit" class="btn-success" value="Send Votes"> <input type="reset" name="reset" class="btn-cancel" value="Reset Votes"></td>
        </tr>
    </table></center>
</form>

<?php
    }
} else {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
        echo '<p>You did not press the submit button; this page should not be accessed directly.</p>';
    } else {
        for($i=1; $i<=11; $i++) {
            $card = "vote$i";
            $id = $sanitize->for_db($_POST['id']);
            $vote = $sanitize->for_db($_POST[$card]);
            $update = $database->query("UPDATE `tcg_cards` SET votes=votes+'1' WHERE filename='$vote'");
        }
        if ($update == TRUE) {
            echo '<h1>Upcoming Vote : Prize Pickup</h1>';
            echo '<p>Thank you for voting decks for the future releases! This will help us decide which deck to release next week! Please take everything you see below:</p>
            <center>';
            /* CHECK FOR DOUBLE REWARDS
             * Change amount of rewards you need:
             * ('GAME SET HERE','Upcoming Vote','-subtitle-','-random-','-choice-','-currency01-','-currency02-','-currency03-')
             */
            $getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
            if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Upcoming Vote','','4','0','0','0','0'); }
            else { $general->gamePrize('GAME SET HERE','Upcoming Vote','','2','0','0','0','0'); }
        } else {
            echo '<h1>Upcoming Vote : Error!</h1>
            <p>There was an error while processing your votes, please send us an email at <a href="mailto:'.$tcgemail.'">'.$tcgemail.'</a> as soon as possible. We apologize for the inconvenience!</p>';
        }
    }
}

?>
