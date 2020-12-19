<?php
########################################
########## Delete Form Action ##########
########################################
$sql = $database->query("SELECT * FROM `tcg_donations` WHERE id='$id'");
if ( isset($_POST['delete']) ) {
    $id = $_POST['id'];
    $delete = $database->query("DELETE FROM `tcg_donations` WHERE id='$id'");
    if ($delete == TRUE) { $success[] = "The donated deck was successfully deleted."; }
    else { $error[] = "Sorry, there was an error and the donated deck hasn't been deleted. ".mysqli_error().""; }
}

if (empty($id)) {
    echo '<h1>Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
    <p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
    echo '<h1>Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Delete a Donated Deck?</h1>
    <center>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
    echo '</center>
    <form method="post" action="index.php?action=delete&page=donated&id='.$id.'">
    <input type="hidden" name="id" value="'.$id.'" />
    Are you sure you want to delete this donated deck? <b>This action can not be undone!</b><br />
    Click on the button below to delete the card deck:<br />
    <input type="submit" name="delete" class="btn-warning" value="Delete">
    </form>';
}
?>