<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

######################################
########## Edit Form Action ##########
######################################
if ($stat == "edited") {
	if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
	else {
		$id = $sanitize->for_db($_POST['id']);
		$name = $sanitize->for_db($_POST['name']);
    $set = $sanitize->for_db($_POST['set']);
    $levels = $sanitize->for_db($_POST['levels']);
    $height = $sanitize->for_db($_POST['height']);
    $width = $sanitize->for_db($_POST['width']);
		
		$img_desc = $uploads->reArrayFiles($img);
    $uploads->folderPath('images','badges');
		
		$update = $database->query("UPDATE `tcg_levels_badge` SET `donator`='$name', `set`='$set', `levels`='$levels', `width`='$width', `height`='$height' WHERE id='$id'");
		
		if ($update == TRUE) {
			echo '<h1>Level Bagdes <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
			echo '<p>The affiliate was successfully updated.</p>';
		}
		else {
			echo '<h1>Level Badges <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
			echo '<p>Sorry, there was an error and the affiliate was not updated.</p>';
			die("Error:". mysqli_connect_error());
		}
	}
} else {
	if (empty($id)) {
		echo '<h1>Level Badges <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
		<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		$sql = $database->query("SELECT * FROM `tcg_levels_badge` WHERE id='$id'");
		while($row = mysqli_fetch_assoc($sql)) {
			echo '<h1>Level Badges <span class="fas fa-angle-right" aria-hidden="true"></span> Edit a Level Badge</h1>
			<p>Use this form to edit a level badge in the database. Use the <a href="index.php?action=add&page=bagdes">add</a> form to add new level badges.</p>
			<form method="post" action="index.php?action=edit&page=badges&id='.$id.'&stat=edited" multipart="" enctype="multipart/form-data">
			<input type="hidden" name="id" value="'.$id.'" />
			<table width="100%" cellspacing="3">
			<tr>
          <td class="headSub" width="15%">Donator:</td><td valign="middle" width="35%"><input type="text" name="name" value="'.$row['name'].'" style="width:90%;" /></td>
          <td class="headSub" width="15%">Set:</td><td valign="middle" width="35%"><input type="text" name="set" value="'.$row['set'].'" style="width:90%;" /></td>
      </tr>
			<tr>
          <td class="headSub">Height:</td><td valign="middle"><input type="text" name="height" value="'.$row['height'].'" style="width:90%;" /></td>
          <td class="headSub">Width:</td><td valign="middle"><input type="text" name="width" value="'.$row['width'].'" style="width:90%;" /></td>
      </tr>
			<tr>
			    <td class="headSub">Levels:</td><td valign="middle"><input type="text" name="levels" value="'.$row['levels'].'" style="width: 92%;" /></td>
          <td class="headSub">Upload Badges:</td><td valign="middle"><input type="file" name="img" multiple /></td>
      </tr>
			<tr><td valign="middle" colspan="4" align="center"><input type="submit" name="submit" class="btn-success" value="Edit" /></td></tr>
			</table>
			</form>';
		}
	}
}
$uploads->reArrayFiles($file);
?>
