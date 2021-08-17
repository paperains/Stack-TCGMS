<?php
/********************************************************
 * Moderation:		Level Badges
 * Description:		Show main page of level badges list
 */
if( empty($act) ) {
	if( isset($_POST['mass-delete']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$delete = $database->query("DELETE FROM `tcg_levels_badge` WHERE `badge_id`='$id'");
		}
		
		if ( !$delete ) { $error[] = "Sorry, there was an error and the level badge wasn't deleted. ".mysqli_error().""; }
		else { $success[] = "The level badge was successfully deleted."; }
	}

	echo '<p>&raquo; Do you want to <a href="'.$PHP_SELF.'?mod=badges&action=add">add a level badge</a>?</p>
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}

	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	echo '</center>

	<form method="post" action="'.$PHP_SELF.'?mod=badges">
	<table width="100%" cellspacing="0" class="table table-bordered table-striped">
	<thead>
	<tr>
		<td width="5%"></td>
		<td width="5%">ID</td>
		<td width="20%">Donator</td>
		<td width="20%">Filename</td>
		<td width="30%">Size</td>
		<td width="20%">Action</td>
	</tr>
	</thead>
	<tbody>';
	$sql = $database->query("SELECT * FROM `tcg_levels_badge` ORDER BY `badge_name`");
	while( $row = mysqli_fetch_assoc($sql) ) {
		echo '<tr>
		<td align="center"><input type="checkbox" name="id[]" value="'.$row['badge_id'].'"></td>
		<td align="center">'.$row['badge_id'].'</td>
		<td align="center">'.$row['badge_name'].'</td>
		<td align="center">'.$row['badge_set'].'</td>
		<td align="center">'.$row['badge_width'].' x '.$row['badge_height'].' pixels</td>
		<td align="center">
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=badges&action=edit&id='.$row['badge_id'].'\';" class="btn-success" />Edit</button> 
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=badges&action=delete&id='.$row['badge_id'].'\';" class="btn-cancel" />Delete</button>
		</td></tr>';
	}
	echo '<tr>
		<td align="center"><span class="arrow-right">↳</span></td>
		<td colspan="3">With selected: <input type="submit" name="mass-delete" class="btn-cancel" value="Delete" /></td>
	<tr>
	</tbody>
	</table>
	</form>';
}



/********************************************************
 * Action:			Add Level Badges
 * Description:		Show page for adding level badges
 */
else if( $act == "add" ) {
	$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
	$file = (isset($_GET['name']) ? $_GET['name'] : null);

	if ( isset($_POST['submit']) ) {
		$name = $sanitize->for_db($_POST['name']);
		$levels = $sanitize->for_db($_POST['levels']);
		$height = $sanitize->for_db($_POST['height']);
		$width = $sanitize->for_db($_POST['width']);
		$feat = $sanitize->for_db($_POST['feature']);
		
		if( $_POST['setnum'] < 10 ) { $num = "0".$_POST['setnum']; }
		else { $num = $_POST['setnum']; }
		$set = $_POST['set'].''.$num;

		$img_desc = $uploads->reArrayFiles($img);
		$uploads->folderPath('images','badges');

		$insert = $database->query("INSERT INTO `tcg_levels_badge` (`badge_name`,`badge_set`,`badge_level`,`badge_width`,`badge_height`,`badge_feature`) VALUES ('$name','$set','$levels','$width','$height','$feat')");

		if( !$insert ) { $error[] = "Sorry, there was an error and the level badge was not added. ".mysqli_error().""; }
		else { $success[] = "The new level badge was successfully added to the database!"; }
	}

	echo '<p>Use this form to add a new level badge to the database.<br />
	Use the <a href="'.$PHP_SELF.'?mod=badges">edit</a> form to update information for an existing level badge.</p>

	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}

	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=badges&action=add" multipart="" enctype="multipart/form-data">
	<table width="100%" cellpadding="8" cellspacing="0" border="0">
	<tr>
		<td width="17%" valign="top"><b>Donator:</b></td>
		<td width="83%">
            <select name="name" style="width:40%;">
                <option>----- Select Player -----</option>';
                $sql = $database->query("SELECT * FROM `user_list` WHERE `usr_status`='Active' ORDER BY `usr_name`");
                while( $row = mysqli_fetch_assoc($sql) ) {
                    echo '<option value="'.$row['usr_name'].'">'.$row['usr_name'].'</option>';
                }
            echo '</select><br />
            <small><i>Badge set which includes the donator\'s name and number of set donated.</i></small><br />
            <select name="set" style="width:30%">
                <option>----- Select Player -----</option>';
                $sql = $database->query("SELECT * FROM `user_list` WHERE `usr_status`='Active' ORDER BY `usr_name`");
                while( $row = mysqli_fetch_assoc($sql) ) {
                    $name = strtolower($row['usr_name']);
                    echo '<option value="'.$name.'">'.$name.'</option>';
                }
            echo '</select> <input type="number" name="setnum" min="1" max="10" placeholder="1" size="5" />
        </td>
    </tr>
    <tr>
        <td valign="middle"><b>Featuring:</b></td>
        <td><input type="text" name="feature" placeholder="e.g. Mugiwara Pirates" size="36" /></td>
    </tr>
    <tr>
        <td valign="top"><b>Badge Size:</b>
        <td>
            <input type="text" name="width" placeholder="width in pixels (e.g. 120)" size="16" />
            <input type="text" name="height" placeholder="height in pixels (e.g. 100)" size="15" />
        </td>
    </tr>
    <tr>
        <td valign="middle"><b>Levels:</b></td>
        <td><input type="text" name="levels" placeholder="max donated level" size="36" /></td>
    </tr>
    <tr>
        <td valign="middle"><b>Upload Badges:</b></td>
        <td><input type="file" name="img[]" multiple></td>
	</tr>
	<tr>
	    <td>&nbsp;</td>
        <td>
            <input type="submit" name="submit" class="btn-success" value="Add Level Badge" /> 
			<input type="reset" name="reset" class="btn-cancel" value="Reset" />
		</td>
	</tr>
	</table>
	</form>
	</center>';
	$uploads->reArrayFiles($file);
}



/********************************************************
 * Action:			Delete Level Badges
 * Description:		Show page for deleting a level badge
 */
else if( $act == "delete" ) {
	if ( isset($_POST['delete']) ) {
		$id = $sanitize->for_db($_POST['id']);
		$delete = $database->query("DELETE FROM `tcg_levels_badge` WHERE `badge_id`='$id'");
		
		if ( !$delete ) { $error[] = "Sorry, there was an error and the level badge wasn't deleted. ".mysqli_error().""; }
		else { $success[] = "The level badge was successfully deleted."; }
	}

	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	}

	else {
		echo '<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}
        if ( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }
		echo '</center>

	    <form method="post" action="'.$PHP_SELF.'?mod=badges&action=delete&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		Are you sure you want to delete this level badge? <b>This action can not be undone!</b><br />
		Click on the button below to delete the level badge:<br />
		<input type="submit" name="delete" class="btn-cancel" value="Delete Badge">
		</form>';
	}
}



/********************************************************
 * Action:			Edit Level Badges
 * Description:		Show page for editing a level badge
 */
else if( $act == "edit" ) {
	if( isset($_POST['edit']) ) {
		$id = $sanitize->for_db($_POST['id']);
		$name = $sanitize->for_db($_POST['name']);
		$levels = $sanitize->for_db($_POST['levels']);
		$height = $sanitize->for_db($_POST['height']);
		$width = $sanitize->for_db($_POST['width']);
		$feat = $sanitize->for_db($_POST['feature']);
        $set = $sanitize->for_db($_POST['set']);

		$update = $database->query("UPDATE `tcg_levels_badge` SET `badge_name`='$name', `badge_set`='$set', `badge_level`='$levels', `badge_width`='$width', `badge_height`='$height', `badge_feature`='$feat' WHERE `badge_id`='$id'");

		if( !$update ) { $error[] = "Sorry, there was an error and the level badge was not updated. ".mysqli_error().""; }
		else { $success[] = "The level badges was successfully updated."; }
	}

	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	}

	else {
		echo '<p>Use this form to edit a level badge in the database.<br />
		Use the <a href="'.$PHP_SELF.'?mod=bagdes&action=add">add</a> form to add new level badges.</p>

		<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}

		if ( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}
		
		echo '<form method="post" action="'.$PHP_SELF.'?mod=badges&action=edit&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />';

		$row = $database->get_assoc("SELECT * FROM `tcg_levels_badge` WHERE `badge_id`='$id'");
		echo '<table width="100%" cellspacing="8" cellpadding="0">
		<tr>
			<td width="17%" valign="top"><b>Donator:</b></td>
			<td width="83%">
                <select name="name" style="width:35%;">
                    <option value="'.$row['badge_name'].'">Current: '.$row['badge_name'].'</option>';
                    $sql = $database->query("SELECT `usr_name` FROM `user_list` WHERE `usr_status`='Active' ORDER BY `usr_name`");
                    while( $row1 = mysqli_fetch_assoc($sql) ) {
                        echo '<option value="'.$row1['usr_name'].'">'.$row1['usr_name'].'</option>';
                    }
                echo '</select><br />
                <small><i>Badge set which includes the donator\'s name and number of set donated.</i></small><br />
                <input type="text" name="set" value="'.$row['badge_set'].'" size="36" />
            </td>
        </tr>
        <tr>
            <td valign="middle"><b>Featuring:</b></td>
            <td><input type="text" name="feature" value="'.$row['badge_feature'].'" size="36" /></td>
        </tr>
        <tr>
            <td valign="top"><b>Badge Size:</b></td>
            <td>
                <input type="text" name="width" value="'.$row['badge_width'].'" size="16" />
                <input type="text" name="height" value="'.$row['badge_height'].'" size="15" />
            </td>
        </tr>
        <tr>
            <td valign="middle"><b>Levels:</b></td>
            <td><input type="text" name="levels" value="'.$row['badge_level'].'" size="36" /></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="edit" class="btn-success" value="Edit Badge" /></td>
		</tr>
		</table>
		</form>
		</center>';
	}
}