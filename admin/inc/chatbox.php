<?php
/********************************************************
 * Moderation:		Chat Box
 * Description:		Show main page of members chat
 */
if( empty($act) ) {
	if( isset($_POST['mass-delete']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$delete = $database->query("DELETE FROM `tcg_chatbox` WHERE `chat_id`='$id'");
		}
		if( !$delete ) { $error[] = "Sorry, there was an error and the chat messages were not deleted from the database. ".mysqli_error().""; }
		else { $success[] = "The chat messages has been deleted from the database!"; }
	}

	$sql = $database->query("SELECT * FROM `tcg_chatbox` ORDER BY `chat_date`");
	echo '<h1>Chat Box</h1>
	<p>Manage your members chat messages here. You can delete inapproriate messages or edit a specific message to filter out the chat box from public view.</p>

	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	echo '</center>

	<form method="post" action="'.$PHP_SELF.'?mod=chatbox">
	<table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
	<thead>
	<tr>
		<td width="5%"></td>
		<td width="5%">ID</td>
		<td width="15%">From</td>
		<td width="45%">Message</td>
		<td width="15%">Date</td>
		<td width="15%">Action</td>
	</tr>
	</thead>
	<tbody>';
	while( $row = mysqli_fetch_assoc($sql) ) {
		echo '<tr>
		<td align="center"><input type="checkbox" name="id[]" value="'.$row['chat_id'].'"></td>
		<td align="center">'.$row['chat_id'].'</td>
		<td align="center">'.$row['chat_name'].'</td>
		<td align="center">'.$row['chat_msg'].'</td>
		<td align="center">'.$row['chat_date'].'</td>
		<td align="center">
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=chatbox&action=edit&id='.$row['chat_id'].'\';" class="btn-success">Edit</button> 
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=chatbox&action=delete&id='.$row['chat_id'].'\';" class="btn-cancel">Delete</button>
		</td>
		</tr>';
	}
	echo '<tr>
		<td align="center"><span class="arrow-right">↳</span></td>
		<td colspan="5">With selected: <input type="submit" name="mass-delete" class="btn-cancel" value="Delete" /></td>
	<tr>
	</tbody>
	</table>
	</form>';
}




/********************************************************
 * Action:			Delete Chat Messages
 * Description:		Show page for deleting chat messages
 */
else if( $act == "delete" ) {
	if ( isset($_POST['delete']) ) {
		$id = $_POST['id'];
		$delete = $database->query("DELETE FROM `tcg_chatbox` WHERE `chat_id`='$id'");

		if( !$delete ) { $error[] = "Sorry, there was an error and the chat message was not deleted. ".mysqli_error().""; }
		else { $success[] = "The chat message has been deleted from the database!"; }
	}

	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		$getdata = $database->query("SELECT * FROM `tcg_chatbox` WHERE `chat_id`='$id'");
		echo '<h1>Delete a Chat Message</h1>
		<center>';
		if( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}
		if( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}
		echo '</center>
		
		<form method="post" action="'.$PHP_SELF.'?mod=chatbox&action=delete">
		<input type="hidden" name="id" value="'.$id.'" />
		<p>Are you sure you want to delete this chat message? <b>This action can not be undone!</b><br />
		Click on the button below to delete the chat message:<br />
		<input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
		</form>';
	}
}




/********************************************************
 * Action:			Edit Chat Messages
 * Description:		Show page for editing chat messages
 */
if( $act == "edit" ) {
	if( isset($_POST['update']) ) {
		$id = $sanitize->for_db($_POST['id']);
		$name = $sanitize->for_db($_POST['name']);
		$url = $sanitize->for_db($_POST['url']);
		$msg = $_POST['message'];
		$msg = str_replace("'", "\'", $msg);
		
		$result = $database->query("UPDATE `tcg_chatbox` SET `chat_name`='$name', `chat_url`='$url', `chat_msg`='$msg' WHERE `chat_id`='$id'") or print ("Can't update freebies.<br />" . mysqli_connect_error());

		if( !$result ) { $error[] = "Sorry, there was an error and the chat message was not updated. ".mysqli_error().""; }
		else { $success[] = "You have successfully updated the chat message!"; }
	}

	if ( !isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id']) ) { die("Invalid freebie ID."); }
	else { $id = (int)$_GET['id']; }

	$row = $database->get_assoc("SELECT * FROM `tcg_chat` WHERE `chat_id`='$id'") or print ("Can't select chat message.<br />" . $row . "<br />" . mysqli_connect_error());
	$old_name = stripslashes($row['chat_name']);
	$old_url = stripslashes($row['chat_url']);
	$old_msg = stripslashes($row['chat_msg']);

	echo '<h1>Edit a Chat Message</h1>
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	echo '</center>
	
	<form method="post" action="'.$PHP_SELF.'?mod=chatbox&action=edit&id='.$id.'">
	<input type="hidden" name="id" value="'.$id.'" />
	<table width="100%" cellspacing="0" cellpadding="5">
	<tr>
		<td width="20%" valign="middle"><b>Member Name:</b></td>
		<td width="2%">&nbsp;</td>
		<td width="78%"><input type="text" name="name" value="'.$old_name.'" size="45" /></td>
	</tr>
	<tr>
		<td valign="middle"><b>Trade Post:</b></td>
		<td>&nbsp;</td>
		<td><input type="text" name="url" value="'.$old_url.'" size="45" /></td>
	</tr>
	<tr>
		<td valign="middle"><b>Message:</b></td>
		<td>&nbsp;</td>
		<td><textarea name="message" cols="50" rows="4">'.$old_url.'</textarea></td>
	</tr>
	<tr>
		<td colspan="2"></td>
		<td>
			<input type="submit" name="update" id="update" class="btn-success" value="Edit Message" /> 
			<input type="reset" name="reset" id="reset" class="btn-cancel" value="Reset" />
		</td>
	</tr>
	</table>
	</form>
	</center>';
}
?>