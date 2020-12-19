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
		$name = $sanitize->for_db($_POST['owner']);
		$email = $sanitize->for_db($_POST['email']);
		$tcg = $sanitize->for_db($_POST['subject']);
		$url = $sanitize->for_db($_POST['url']);
		$status = $sanitize->for_db($_POST['status']);
		
		$img_desc = $uploads->reArrayFiles($img);
        $uploads->folderPath('images','aff');
		
		$update = $database->query("UPDATE `tcg_affiliates` SET owner='$name', email='$email', subject='$tcg', url='$url', status='$status' WHERE id='$id'");
		
		if ($update == TRUE) {
			echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
			echo '<p>The affiliate was successfully updated.</p>';
		}
		else {
			echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
			echo '<p>Sorry, there was an error and the affiliate was not updated.</p>';
			die("Error:". mysqli_connect_error());
		}
	}
} else {
	if (empty($id)) {
		echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
		<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		$sql = $database->query("SELECT * FROM `tcg_affiliates` WHERE id='$id'");
		while($row = mysqli_fetch_assoc($sql)) {
			echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Edit an Affiliate</h1>
			<p>Use this form to edit an affiliate in the database. Use the <a href="index.php?action=add&page=affiliates">add</a> form to add new affiliates.</p>
			<form method="post" action="index.php?action=edit&page=affiliates&id='.$id.'&stat=edited" multipart="" enctype="multipart/form-data">
			<input type="hidden" name="id" value="'.$id.'" />
			<table width="100%" cellspacing="3">
			<tr>
                <td class="headSub" width="15%">Name:</td><td valign="middle" width="35%"><input type="text" name="owner" value="'.$row['owner'].'" style="width:90%;" /></td>
                <td class="headSub" width="15%">Email:</td><td valign="middle" width="35%"><input type="text" name="email" value="'.$row['email'].'" style="width:90%;" /></td>
            </tr>
			<tr>
                <td class="headSub">Subject:</td><td valign="middle"><input type="text" name="subject" value="'.$row['subject'].'" style="width:90%;" /></td>
                <td class="headSub">URL:</td><td valign="middle"><input type="text" name="url" value="'.$row['url'].'" style="width:90%;" /></td>
            </tr>
			<tr>
                <td class="headSub">Upload Button:</td><td valign="middle"><input type="file" name="img" /></td>
                <td class="headSub">Status:</td><td valign="middle"><select name="status" style="width:95%;">
				<option value="'.$row['status'].'">Current: '.$row['status'].'</option>
				<option value="Pending">Pending</option>
				<option value="Active">Active</option>
				<option value="Hiatus">Hiatus</option>
				</select></td>
            </tr>
			<tr><td valign="middle" colspan="4" align="center"><input type="submit" name="submit" class="btn-success" value="Edit" /></td></tr>
			</table>
			</form>';
		}
	}
}
$uploads->reArrayFiles($file);
?>