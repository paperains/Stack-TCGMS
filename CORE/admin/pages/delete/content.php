<?php
########################################
########## Delete Form Action ##########
########################################
if ( isset($_POST['submit']) ) {
	$id=$_POST['id'];
	$delete = $database->query("DELETE FROM `tcg_pages` WHERE id='$id'");
	if ($delete == TRUE) { $success[] = "The page has been successfully deleted from the database."; }
	else { $error[] = "Sorry, there was an error and the page was not deleted from the database. ".mysqli_error().""; }
}

if (empty($id)) {
	echo '<h1>Page <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	echo '<h1>Page <span class="fas fa-angle-right" aria-hidden="true"></span> Delete a Page?</h1>
    <center>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
    echo '</center>
	<form method="post" action="index.php?action=delete&page=content&id='.$id.'">
	<input type="hidden" name="id" value="'.$id.'" />
	Are you sure you want to delete this page content? <b>This action can not be undone!</b><br />Click on the button below to delete the page:<br />
	<input type="submit" name="submit" class="btn-warning" value="Delete">
	</form>';
}
?>
