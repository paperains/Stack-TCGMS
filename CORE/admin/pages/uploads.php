<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$img2 = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

if ( isset($_POST['submit']) ) {
    if(!empty($img)) {
        $imgtype = $sanitize->for_db($_POST['upload']);
        $img_desc = $uploads->reArrayFiles($img);
        foreach($img_desc as $val) {
            $newname = $file['name'];
            if ($imgtype=="cards") { $path = "/home/reijou/public_html/sites/potc/images/cards/"; }
            else if ($imgtype=="badges") { $path = "/home/reijou/public_html/sites/potc/images/badges/"; }
            else if ($imgtype=="affiliates") { $path = "/home/reijou/public_html/sites/potc/images/aff/"; }
            else if ($imgtype=="images") { $path = "/home/reijou/public_html/sites/potc/images/"; }
            else if ($imgtype=="game-rounds") { $path = "/home/reijou/public_html/sites/potc/admin/games/rounds/"; }
            else if ($imgtype=="game-answer") { $path = "/home/reijou/public_html/sites/potc/admin/games/answer/"; }
            else if (empty($imgtype)) { echo '<h1>Error</h1><p>You did not select what type of custom image you are trying to upload.</p>'; }
            move_uploaded_file($val['tmp_name'],$path.$val['name']);
        }
        $success[] = "Your image has been successfully uploaded to the website.";
    } else {
        $error[] = "Sorry, there was an error and the image was not uploaded. ".mysqli_error()."";
    }
}

echo '<h1>Main Uploads</h1>
<p>This page consists of sub categories regarding images that needs to be uploaded (either one image or per batch up to 20 files). Kindly choose which type of custom image you\'d like to upload or replace.</p>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
echo '<form method="post" action="index.php?page=uploads" multipart="" enctype="multipart/form-data">
<table width="100%" cellspacing="3" border="0">
<tr>
	<td class="headSub" width="15%">Upload Image(s):</td><td valign="middle" width="35%"><input type="file" name="img[]" multiple></td>
	<td class="headSub" width="15%">Custom Type(s):</td><td valign="middle" width="35%"><select name="upload" style="width:95%;">
		<option>----- Choose custom image type -----</option>
		<option value="cards">Cards / Mastery Badges</option>
		<option value="badges">Level Badges</option>
        <option value="affiliates">Affiliates</option>
		<option value="game-rounds">Game Clues</option>
        <option value="game-answer">Game Answers</option>
		<option value="images">General Images</option>
	</select></td>
</tr>
<tr><td valign="middle" align="center" colspan="4"><input type="submit" name="submit" class="btn-success" value="Upload Custom Image(s)" /></td></tr>
</table>
</form>';

$uploads->reArrayFiles($file);
?>