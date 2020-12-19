<?php
#######################################
########## Claim Form Action ##########
#######################################
if ( isset($_POST['claim']) ) {
    $id = $sanitize->for_db($_POST['id']);
    $maker = $sanitize->for_db($_POST['maker']);
    $claimed = date("Y-m-d", strtotime("now"));
    $update = $database->query("UPDATE `tcg_donations` SET `maker`='$maker' WHERE `id`='$id'");
    if ( !$update ) { $error[] = "Sorry, there was an error and the deck was not updated. ".mysqli_error().""; }
	else { $success[] = "You have claimed to make the deck!"; }
}

if (empty($id)) {
    echo '<h1>Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
    <p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
    $sql = $database->get_assoc("SELECT * FROM `tcg_donations` WHERE id='$id'");
    echo '<h1>Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Claim a Deck</h1>
    <p>Use this form to claim a card deck to make from the database.</p><center>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
    echo '</center><form method="post" action="index.php?action=add&page=donated&id='.$id.'">
    <input type="hidden" name="id" value="'.$id.'" />
    <input type="hidden" name="maker" value="'.$player.'" />
    <p>Are you sure you want to claim the <b>'.$sql['deckname'].'</b> deck? <b>This action can not be undone!</b><br />
    Click on the button below to claim the card deck:<br />
    <input type="submit" name="claim" class="btn-success" value="Yes, I\'m making this deck!" /></p>
    </form>';
}
?>