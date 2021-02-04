<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

#####################################
########## Add Form Action ##########
#####################################
if ( isset($_POST['submit']) ) {
    $name = $sanitize->for_db($_POST['name']);
    $set = $sanitize->for_db($_POST['set']);
    $levels = $sanitize->for_db($_POST['levels']);
    $height = $sanitize->for_db($_POST['height']);
    $width = $sanitize->for_db($_POST['width']);
    
    $img_desc = $uploads->reArrayFiles($img);
    $uploads->folderPath('images','badges');

    $insert = $database->query("INSERT INTO `tcg_levels_badge` (`donator`,`set`,`levels`,`width`,`height`) VALUES ('$name','$set','$levels','$width','$height')");

    if ($insert == TRUE) { $success[] = "The new level badge was successfully added to the database!"; }
    else { $error[] = "Sorry, there was an error and the level badge was not added. ".mysqli_error().""; }
}

echo '<h1>Level Badges <span class="fas fa-angle-right" aria-hidden="true"></span> Add a Level Badge</h1>
<p>Use this form to add a new level badge to the database. Use the <a href="index.php?page=badges">edit</a> form to update information for an existing level badge.</p>
<center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }

echo '</center>
<form method="post" action="index.php?action=add&page=badges" multipart="" enctype="multipart/form-data">
<table width="100%" cellpadding="5" cellspacing="3" border="0">
    <tr>
        <td width="10%" class="headSub">Donator:</td><td width="40%" valign="middle"><input type="text" name="name" placeholder="Jane Doe" style="width: 92%;" /></td>
        <td width="10%" class="headSub">Set:</td><td width="40%" valign="middle"><input type="text" name="set" placeholder="janedoe01" style="width: 92%;" /></td>
    </tr>
    <tr>
        <td class="headSub">Height:</td><td valign="middle"><input type="text" name="height" placeholder="in pixels (e.g. 100)" style="width: 92%;" /></td>
        <td class="headSub">Width:</td><td valign="middle"><input type="text" name="width" placeholder="in pixels (e.g. 120)" style="width: 92%;" /></td>
    </tr>
    <tr>
        <td class="headSub">Levels:</td><td valign="middle"><input type="text" name="levels" placeholder="max donated level" style="width: 92%;" /></td>
        <td class="headSub">Upload Badges:</td><td valign="middle"><input type="file" name="img[]" multiple></td>
    </tr>
    <tr>
        <td colspan="4" valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Add Level Badge" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td>
    </tr>
</table>
</form>';
$uploads->reArrayFiles($file);
?>
