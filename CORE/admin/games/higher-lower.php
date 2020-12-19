<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$count = $database->num_rows("SELECT * FROM `game_hol_logs` WHERE `name`='".$row['name']."' AND `timestamp` >= '".$range['timestamp']."'");
$week = date("Y-m-d", strtotime("+6 days", strtotime($range['timestamp'])));
$now = $range['timestamp'];

if (empty($go)) {
?>

<h1>GAME SET HERE - Higher or Lower</h1>
<p>Higher or Lower is a simple game where you have to guess whether the card that's randomly chosen next week will be higher or lower than this week's card! A new round will be posted every week, so once a new round is posted the previous round will close and can not be played anymore so make sure you post your guess on time!<br />
<li>Fill your guess on the form below whether next week's card will be <u>higher</u> or <u>lower</u> than this week's card!</li>
<li>Make sure to type your answer in lowercase (e.g. <code>higher</code> instead of <code>Higher</code>).</li>
<li>Once you've submitted your guess, you may not go back later and change it!</li>
<li>When the week is over and you have guessed it correctly, you will be rewarded with 2 random cards and 1 gold.</li></p>
<center><h2>This week's card is...</h2>
<?php
$get = $database->get_assoc("SELECT * FROM `game_hol_cards` WHERE `timestamp`='$now'");
echo '<img src="/images/cards/'.$get['filename'].''.$get['number'].'.png" /><br />'.$get['filename'].'<b>'.$get['number'].'</b><br /><br />
<b><u>This week ends on <i>'.date("F d, Y", strtotime("+6 days", strtotime($get['timestamp']))).'</i> at <i>11:59PM PHT</i>!</u></b>';
if ($count == 1) {
    echo '<p>You have already played this game! Please wait until next week to receive your rewards.</p>';
} else {
?>
<p><form method="post" action="/games.php?play=higherlower&go=guess">
<input type="hidden" name="name" value="<?php echo $row['name']; ?>" />
<input type="text" name="guess" placeholder="higher / lower" size="25" /><input type="submit" name="submit" class="btn-success" value="Guess!" />
</form></p>
<?php
}
?>
<h2>Logs</h2>
<div style="border:1px solid #cccccc;border-radius:8px;text-align:left;overflow:auto;padding:10px;width:50%;height:100px;">
<?php
$sql = $database->query("SELECT * FROM `game_hol_logs` WHERE `timestamp` BETWEEN '$now' AND '$week' ORDER BY `timestamp` DESC");
while ($row = mysqli_fetch_assoc($sql)) {
    echo '<b>'.$row['timestamp'].':</b> '.$row['name'].' guessed it to be <u>'.$row['guess'].'</u>.<br />';
}
?>
</div>
</center>

<?php
} else {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
	exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
    } else {
        $name = $sanitize->for_db($_POST['name']);
        $guess = $sanitize->for_db($_POST['guess']);
        $date = date("Y-m-d", strtotime("now"));

        $insert = $database->query("INSERT INTO `game_hol_logs` (`name`,`guess`,`timestamp`) VALUES ('$name','$guess','$date')");

        if ($insert == TRUE) {
            echo '<h1>Higher or Lower</h1>
            <p>Your guess has been saved into the Higher or Lower\'s logs. Kindly please wait until next week for your rewards as you will automatically receive it once a new round starts.</p>';
        } else {
            echo '<h1>Higher or Lower : Error!</h1>
            <p>It seems like there was an error while processing your form, kindly please contact '.$tcgowner.' about this as soon as possible.</p>';
        }
    }
}
?>
