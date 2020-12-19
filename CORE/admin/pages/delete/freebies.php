<?php
########################################
########## Delete Form Action ##########
########################################
if ( isset($_POST['submit']) ) {
    $id = $_POST['id'];
	$delete = $database->query("DELETE FROM `user_freebies` WHERE id='$id'");

	if ( !$delete ) { $error[] = "Sorry, there was an error and the freebie was not deleted. ".mysqli_error().""; }
    else { $success[] = "The freebie has been deleted from the database!"; }
}

if (empty($id)) {
	echo '<h1>Freebies <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	$getdata = $database->query("SELECT * FROM `user_freebies` WHERE id='$id'");
	echo '<h1>Freebies <span class="fas fa-angle-right" aria-hidden="true"></span> Delete a Freebie?</h1>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	echo '<form method="post" action="index.php?action=delete&page=freebies">
	<input type="hidden" name="id" value="'.$id.'" />
	Are you sure you want to delete this freebie? <b>This action can not be undone!</b><br />
    Click on the button below to delete the freebie:<br />
	<input type="submit" name="submit" class="btn-warning" value="Delete">
	</form>';
}
?>