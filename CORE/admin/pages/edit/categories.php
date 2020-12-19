<?php
######################################
########## Edit Form Action ##########
######################################
if ($stat == "edited") {
	if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
	else {
		$id = $sanitize->for_db($_POST['id']);
		$cat = $sanitize->for_db($_POST['category']);
		$name = $sanitize->for_db($_POST['name']);
		
		$update = $database->query("UPDATE `tcg_cards_cat` SET category='$cat', name='$name' WHERE id='$id'");
		
		if ($update == TRUE) {
			echo '<h1>Categories <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
			echo '<p>The category was successfully updated.</p>';
		}
		else {
			echo '<h1>Categories <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
			echo '<p>Sorry, there was an error and the category was not updated.</p>';
			die("Error:". mysqli_connect_error());
		}
	}
}

else {
	if (empty($id)) {
		echo '<h1>Categories <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
		<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		$sql = $database->query("SELECT * FROM `tcg_cards_cat` WHERE id='$id'");
		while($row = mysqli_fetch_assoc($sql)) {
			echo '<h1>Categories <span class="fas fa-angle-right" aria-hidden="true"></span> Edit a Category</h1>
			<p>Use this form to edit an existing category in the database. Use the <a href="index.php?action=add&page=categories">add</a> form to add a new category for your card decks.</p>
			<form method="post" action="index.php?action=edit&page=categories&id='.$id.'&stat=edited">
			<input type="hidden" name="id" value="'.$id.'" />
			<table width="100%" cellspacing="3">
			<tr>
        <td class="headSub" width="15%">Category:</td><td valign="middle" width="35%"><input type="text" name="category" value="'.$row['category'].'" style="width:90%;" /></td>
        <td class="headSub" width="15%">Name:</td><td valign="middle" width="35%"><input type="text" name="name" value="'.$row['name'].'" style="width:90%;" /></td>
      </tr>
			<tr><td valign="middle" colspan="4" align="center"><input type="submit" name="submit" class="btn-success" value="Edit" /></td></tr>
			</table>
			</form>';
		}
	}
}
?>
