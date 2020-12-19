<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

if ($stat == "added") {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
    else {
        $img_desc = $uploads->reArrayFiles($img);
        $uploads->folderPath('images','cards');

        echo '<h1>Success</h1>';
        echo '<p>Want to <a href=\"index.php?action=add&page=cards\">add more</a> decks?</p>';
    }
} else {
    echo '<h1>Add Remaining Cards</h1>
    <p>Use this form to upload the remaining cards from your submitted deck.</p>
    <form method="post" action="index.php?page=complete-deck&stat=added" multipart="" enctype="multipart/form-data">
	<table width="100%" cellpadding="5" cellspacing="3" border="0">
    <tr>
        <td class="headSub" width="15%">Upload Cards:</td><td valign="middle" width="35%"><input type="file" name="img[]" multiple></td>
        <td class="headSub" width="15%">Proceed?</td><td valign="middle" width="35%" align="center"><input type="submit" name="submit" class="btn-success" value="Complete Deck" /></td></tr>
	</table>
	</form>';
} // END SHOW ADD FORM

$uploads->reArrayFiles($file);
?>