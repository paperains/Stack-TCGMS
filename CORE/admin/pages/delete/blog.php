<?php
########################################
########## Delete Form Action ##########
########################################
if ( isset($_POST['submit']) ) {
	$id=$_POST['id'];
	$delete = $database->query("DELETE FROM `tcg_blog` WHERE id='$id'");
	if ($delete == TRUE) { $success[] = "The update has been successfully deleted from the database."; }
	else { $error[] = "Sorry, there was an error and the update was not deleted from the database. ".mysqli_error().""; }
}

if (empty($id)) {
	echo '<h1>Blog <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	echo '<h1>Blog <span class="fas fa-angle-right" aria-hidden="true"></span> Delete a Blog?</h1>
    <center>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
    echo '</center>
	<form method="post" action="index.php?action=delete&page=blog&id='.$id.'">
	<input type="hidden" name="id" value="'.$id.'" />
	Are you sure you want to delete this update? <b>This action can not be undone!</b><br />Click on the button below to delete the update:<br />
	<input type="submit" name="submit" class="btn-warning" value="Delete">
	</form>';
}
?>