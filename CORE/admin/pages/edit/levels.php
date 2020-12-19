<?php
######################################
########## Edit Form Action ##########
######################################
if ($stat == "edited") {
	if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
	else {
		$id = $sanitize->for_db($_POST['id']);
		$level = $sanitize->for_db($_POST['level']);
		$name = $sanitize->for_db($_POST['name']);
		$cards = $sanitize->for_db($_POST['cards']);
		
    $update = $database->query("UPDATE `tcg_levels` SET level='$level', name='$name', cards='$cards' WHERE id='$id'");
		
		if ($update == TRUE) {
			echo '<h1>Levels <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
			echo '<p>The level was successfully updated.</p>';
		}
		else {
			echo '<h1>Levels <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
			echo '<p>Sorry, there was an error and the level was not updated.</p>';
			die("Error:". mysqli_connect_error());
		}
	}
}

else {
	if (empty($id)) {
		echo '<h1>Levels <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
		<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		$sql = $database->query("SELECT * FROM `tcg_levels` WHERE id='$id'");
		while($row = mysqli_fetch_assoc($sql)) {
			echo '<h1>Levels <span class="fas fa-angle-right" aria-hidden="true"></span> Edit a Level</h1>
			<p>Use this form to edit an existing level in the database. Use the <a href="index.php?action=add&page=levels">add</a> form to add new level.</p>
			<form method="post" action="index.php?action=edit&page=levels&id='.$id.'&stat=edited">
			<input type="hidden" name="id" value="'.$id.'" />
			<table width="100%" cellspacing="3">
			<tr>
        <td class="headSub" width="15%">Level:</td><td valign="middle" width="35%"><input type="text" name="level" value="'.$row['level'].'" style="width:90%;" /></td>
        <td class="headSub" width="15%">Name:</td><td valign="middle" width="35%"><input type="text" name="name" value="'.$row['name'].'" style="width:90%;" /></td>
      </tr>
			<tr>
        <td class="headSub">Cards:</td><td valign="middle"><input type="text" name="cards" value="'.$row['cards'].'" style="width:90%;" /></td>
        <td class="headSub">Proceed?</td><td valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Edit" /></td>
      </tr>
			</table>
			</form>';
		}
	}
}
?>
