<?php
#########################################
########## Release Form Action ##########
#########################################
if (empty($id)) {
	echo '<h1>Upcoming Decks <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	$released = date('Y-m-d');
	$update = $database->query("UPDATE `tcg_cards` SET status='Active', released='$released', votes='0' WHERE id='$id'");
    $row = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE id='$id'");
	if ($update == TRUE) {
        $activity = '<span class="fas fa-paper-plane" aria-hidden="true"></span> <a href="/members.php?id='.$player.'">'.$player.'</a> released the <a href="/cards.php?view=released&deck='.$row['filename'].'">'.$row['deckname'].'</a> deck.';
        $database->query("INSERT INTO `tcg_activities` (`name`,`activity`,`date`) VALUES ('$player','$activity','$released')");
        echo '<h1>Upcoming Decks <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
        echo '<p>The card deck was successfully added to the released cards and was deleted from the upcoming list.<br />
        Want to <a href="index.php?page=upcoming">release</a> more decks?</p>';
	} else {
        echo '<h1>Upcoming Decks <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
        echo '<p>Sorry, there was an error and the card deck was not released.</p>';
        die("Error:". mysqli_error());
	}
}
?>
