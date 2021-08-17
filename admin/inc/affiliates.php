<?php
/********************************************************
 * Moderation:		Affiliates
 * Description:		Show main page of affiliates list
 */
if( empty($act) ) {
	// Mass hiatus affies
	if( isset($_POST['mass-hiatus']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$hiatus = $database->query("UPDATE `tcg_affiliates` SET `aff_status`='Hiatus' WHERE `aff_id`='$id'");
		}
		if( !$hiatus ) { $error[] = "Sorry, there was an error and the selected affiliates were not set to Hiatus. ".mysqli_error().""; }
		else { $success[] = "The selected affiliates has been set to Hiatus!"; }
	}

	// Mass inactive affies
	if( isset($_POST['mass-inactive']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$inactive = $database->query("UPDATE `tcg_affiliates` SET `aff_status`='Inactive' WHERE `aff_id`='$id'");
		}
		if( !$inactive ) { $error[] = "Sorry, there was an error and the selected affiliates were not set to Inactive. ".mysqli_error().""; }
		else { $success[] = "The selected affiliates has been set to Inactive!"; }
	}

	// Mass close affies
	if( isset($_POST['mass-closed']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$closed = $database->query("UPDATE `tcg_affiliates` SET `aff_status`='Closed' WHERE `aff_id`='$id'");
		}
		if( !$closed ) { $error[] = "Sorry, there was an error and the selected affiliates were not set to Closed. ".mysqli_error().""; }
		else { $success[] = "The selected affiliates has been set to Closed!"; }
	}

	// Mass delete affies
	if( isset($_POST['mass-delete']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$delete = $database->query("DELETE FROM `tcg_affiliates` WHERE `aff_id`='$id'");
		}
		if( !$delete ) { $error[] = "Sorry, there was an error and the selected affiliates were not deleted from the database. ".mysqli_error().""; }
		else { $success[] = "The selected affiliates has been deleted successfully!"; }
	}

	// Mass approve affies
	if( isset($_POST['mass-approve']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$row = $database->get_assoc("SELECT * FROM `tcg_affiliates` WHERE `aff_id`=$id");
			$update = $database->query("UPDATE `tcg_affiliates` SET `aff_status`='Active' WHERE `aff_id`='$id'");
			$recipient = "$row[aff_email]";
			$subject = "$tcgname: Affiliate Approved!";
			
			$message = "Thank you for affiliating with $tcgname! Your application has been approved.\n\n";
			$message .= "-- $tcgowner\n";
			$message .= "$tcgname: $tcgurl\n";
			
			$headers = "From: $tcgname <$tcgemail> \n";
			$headers .= "Reply-To: $tcgname <$tcgemail>";

			// Send email if all queries are correct
			if( mail($recipient,$subject,$message,$headers) ) {
				$activity = '<span class="fas fa-globe" aria-hidden="true"></span> <a href="'.$row['aff_url'].'" target="_blank">'.$row['aff_subject'].' TCG</a> has been added as '.$tcgname.'\'s new affiliate.';
				$date = date("Y-m-d", strtotime("now"));
				$database->query("INSERT INTO `tcg_activities` (`act_name`,`act_rec`,`act_date`) VALUES ('$name','$activity','$date')");
			}
		}
		if( !$update ) {
			$error[] = "Sorry, there was an error and the email could not be sent to the selected affiliates. They also were not updated in the database. Please send them an email to let them know they have been approved and use the edit form from the <a href=\"'.$PHP_SELF.'?mod=affiliates\">affiliates</a> page to update their status. ".mysqli_error()."";
		} else {
			$success[] = "The selected affiliates has been successfully emailed and has been updated in the database.";
		}
	}

	echo '<p>&raquo; Need to email <a href="'.$PHP_SELF.'?mod=affiliates&action=all-affiliates">all affiliates</a>?</p>
	
	<ul class="tabs" data-persist="true">
		<li><a href="#active">Active</a></li>
		<li><a href="#pending">Pending</a></li>
		<li><a href="#hiatus">Hiatus</a></li>
		<li><a href="#inactive">Inactive</a></li>
		<li><a href="#closed">Closed</a></li>
	</ul>

	<div class="tabcontents" align="left">
		<div id="active">
			<h2>Active Affiliates</h2>
			<center>';
			if ( isset($error) ) {
				foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
			}
			if ( isset($success) ) {
				foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
			}
			echo '</center>';
			$admin->affiliates('Active');
		echo '</div><!-- #active -->

		<div id="pending">
			<h2>Pending Affiliates</h2>
			<center>';
			if ( isset($error) ) {
				foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
			}
			if ( isset($success) ) {
				foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
			}
			echo '</center>';
			$admin->affiliates('Pending');
		echo '</div><!-- #pending -->

		<div id="hiatus">
			<h2>Hiatus Affiliates</h2>
			<center>';
			if ( isset($error) ) {
				foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
			}
			if ( isset($success) ) {
				foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
			}
			echo '</center>';
			$admin->affiliates('Hiatus');
		echo '</div><!-- #hiatus -->

		<div id="inactive">
			<h2>Inactive Affiliates</h2>
			<center>';
			if ( isset($error) ) {
				foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
			}
			if ( isset($success) ) {
				foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
			}
			echo '</center>';
			$admin->affiliates('Inactive');
		echo '</div><!-- #inactive -->

		<div id="closed">
			<h2>Closed Affiliates</h2>
			<center>';
			if ( isset($error) ) {
				foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
			}
			if ( isset($success) ) {
				foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
			}
			echo '</center>';
			$admin->affiliates('Closed');
		echo '</div><!-- #closed -->
	</div><!-- .tabcontents -->';
}



/********************************************************
 * Action:			Add Affiliates
 * Description:		Show page for adding an affiliate
 */
if( $act == "add" ) {
	if( isset($_POST['add']) ) {
		$uploads->affiliates();
	}

	echo '<p>Use this form to add an affiliate to the database. <b>If they have sent in a request, they are already in the database!</b><br />
	Use the <a href="'.$PHP_SELF.'?mod=affiliates">edit</a> form to edit an affiliate\'s information.</p>

	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}

	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=affiliates&action=add" accept-charset="UTF-8" enctype="multipart/form-data">
	<input type="hidden" name="status" value="Active" />
	<table width="100%" cellspacing="0" cellpadding="8">
	<tr>
		<td width="17%" valign="middle"><b>TCG Owner:</b></td>
		<td width="83%"><input type="text" name="owner" placeholder="Jane Doe" size="40" /></td>
	</tr>
	<tr>
		<td valign="middle"><b>TCG Name:</b></td>
		<td><input type="text" name="subject" placeholder="Name of the TCG" size="40" /></td>
	</tr>
	<tr>
		<td valign="middle"><b>TCG Email:</b></td>
		<td><input type="text" name="email" placeholder="username@domain.tld" size="40" /></td>
	</tr>
	<tr>
		<td valign="middle"><b>TCG Website:</b></td>
		<td><input type="text" name="url" placeholder="http://" size="40" /></td>
	</tr>
	<tr>
		<td valign="middle"><b>Upload Button:</b></td>
		<td><input type="file" name="file"></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="submit" name="add" class="btn-success" value="Add Affiliate" /> 
			<input type="reset" name="reset" class="btn-cancel" value="Reset" />
		</td>
	</tr>
	</table>
	</form>
	</center>';
}



/********************************************************
 * Action:			Approve Affiliates
 * Description:		Show page for approving an affiliate
 */
if( $act == "approve" ) {
	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	}

	else {
		$row = $database->get_assoc("SELECT * FROM `tcg_affiliates` WHERE `aff_id`=$id");
		$update = $database->query("UPDATE `tcg_affiliates` SET `aff_status`='Active' WHERE `aff_id`='$id'");
		$recipient = "$row[aff_email]";
		$subject = "$tcgname: Affiliate Approved!";
		
		$message = "Thank you for affiliating with $tcgname! Your application has been approved.\n\n";
		$message .= "-- $tcgowner\n";
		$message .= "$tcgname: $tcgurl\n";
		
		$headers = "From: $tcgname <$tcgemail> \n";
		$headers .= "Reply-To: $tcgname <$tcgemail>";

		// Send email if all queries are correct
		if( mail($recipient,$subject,$message,$headers) && $update === TRUE ) {
			$activity = '<span class="fas fa-globe" aria-hidden="true"></span> <a href="'.$row['aff_url'].'" target="_blank">'.$row['aff_subject'].' TCG</a> has been added as '.$tcgname.'\'s new affiliate.';
			$date = date("Y-m-d", strtotime("now"));
			$database->query("INSERT INTO `tcg_activities` (`act_name`,`act_rec`,`act_date`) VALUES ('$name','$activity','$date')");
			echo '<p>'.$row['aff_owner'].' has been successfully emailed and has been updated in the database.</p>';
		}

		else {
			echo '<p>Sorry, there was an error and the email could not be sent to '.$row['aff_owner'].' @ '.$row['aff_email'].'. They also were not updated in the database. Please send them an email to let them know they have been approved and use the edit form from the <a href="'.$PHP_SELF.'?mod=affiliates">affiliates</a> page to update their status.</p>';
		}
	}
}



/********************************************************
 * Action:			Delete Affiliates
 * Description:		Show page for deleting an affiliate
 */
if( $act == "delete" ) {
	if( isset($_POST['delete']) ) {
		$id = $sanitize->for_db($_POST['id']);
		$delete = $database->query("DELETE FROM `tcg_affiliates` WHERE `aff_id`='$id'");
		
		if ( !$delete ) { $error[] = "Sorry, there was an error and the affiliate hasn't been deleted. ".mysqli_error().""; }
		else { $success[] = "The affiliate was successfully deleted."; }
	}

	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		echo '<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}
		if ( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}
		echo '</center>

		<form method="post" action="'.$PHP_SELF.'?mod=affiliates&action=delete&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<p>Are you sure you want to delete this affiliate? <b>This action can not be undone!</b><br />
		Click on the button below to delete the affiliate:<br />
		<input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
		</form>';
	}
}



/********************************************************
 * Action:			Edit Affiliates
 * Description:		Show page for editing an affiliate
 */
if( $act == "edit" ) {
	if( isset($_POST['edit']) ) {
		$id = $sanitize->for_db($_POST['id']);
		$name = $sanitize->for_db($_POST['owner']);
		$email = $sanitize->for_db($_POST['email']);
		$tcg = $sanitize->for_db($_POST['subject']);
		$url = $sanitize->for_db($_POST['url']);
		$status = $sanitize->for_db($_POST['status']);

		$update = $database->query("UPDATE `tcg_affiliates` SET `aff_owner`='$name', `aff_email`='$email', `aff_subject`='$tcg', `aff_url`='$url', `aff_status`='$status' WHERE `aff_id`='$id'");

		// Process form if queries are correct
		if( !$update ) { $error[] = "Sorry, there was an error and the affiliate was not updated. ".mysqli_error().""; }
		else { $success[] = "The affiliate was successfully updated!"; }
	}

	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	}

	else {
		$row = $database->get_assoc("SELECT * FROM `tcg_affiliates` WHERE `aff_id`='$id'");
		echo '<p>Use this form to edit an affiliate in the database.<br />
		Use the <a href="'.$PHP_SELF.'?mod=affiliates&action=add">add</a> form to add new affiliates.</p>

		<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}

		if ( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}
		
		echo '<form method="post" action="'.$PHP_SELF.'?mod=affiliates&action=edit&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<table width="100%" border="0" cellspacing="0" cellpadding="8">
		<tr>
			<td width="17%" valign="middle"><b>TCG Owner:</b></td>
			<td width="83%"><input type="text" name="owner" value="'.$row['aff_owner'].'" size="40" /></td>
		</tr>
		<tr>
			<td valign="middle"><b>TCG Name:</b></td>
			<td><input type="text" name="url" value="'.$row['aff_subject'].'" size="40" /></td>
		</tr>
		<tr>
			<td valign="middle"><b>TCG Email:</b></td>
			<td><input type="text" name="email" value="'.$row['aff_email'].'" size="40" /></td>
		</tr>
		<tr>
			<td valign="middle"><b>TCG Website:</b></td>
			<td><input type="text" name="url" value="'.$row['aff_url'].'" size="40" /></td>
		</tr>
		<tr>
			<td valign="middle"><b>Status:</b></td>
			<td>
				<select name="status" style="width:38%;">
					<option value="'.$row['aff_status'].'">Current: '.$row['aff_status'].'</option>
					<option value="Pending">Pending</option>
					<option value="Active">Active</option>
					<option value="Hiatus">Hiatus</option>
				</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" name="edit" class="btn-success" value="Edit Affiliate" /> 
				<input type="reset" name="reset" class="btn-cancel" value="Reset" />
			</td>
		</tr>
		</table>
		</form>
		</center>';
	}
}



/********************************************************
 * Action:			Email Affiliates
 * Description:		Show page for emailing an affiliate
 */
if( $act == "email" ) {
	if( isset($_POST['email']) ) {
		$row = $database->get_assoc("SELECT * FROM `tcg_affiliates` WHERE `aff_id`='".$_POST['id']."'");
		$recipient = "$row[aff_email]";
		$subject = "$tcgname: Affiliate Contact Form";

		$message = "$tcgowner at $tcgname has sent you the following message: \n";
		$message .= "{$_POST['message']} \n\n";
		$message .= "-- $tcgowner\n";
		$message .= "$tcgname: $tcgurl\n";

		$headers = "From: $tcgname <$tcgemail> \n";
		$headers .= "Reply-To: $tcgname <$tcgemail>";

		if( mail($recipient,$subject,$message,$headers) ) { $success[] = "Your message to ".$row['aff_owner']." @ ".$row['aff_email']." from ".$row['aff_subject']." TCG has been successfully sent!"; }
		else { $error[] = "Sorry, there was an error and the email could not be sent to ".$row['aff_owner']." @ ".$row['aff_email']." from ".$row['aff_subject']." TCG. ".mysqli_error().""; }
	}

	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	}

	else {
		$row = $database->get_assoc("SELECT * FROM `tcg_affiliates` WHERE `aff_id`='$id'");
		echo '<p>Use this form to send an email to '.$row['aff_owner'].', owner of '.$row['aff_subject'].'.<b>This is not the form for sending an email to all affiliates.</b><br />
		If you need to send an email to all of the affiliates of '.$tcgname.', please use <a href="'.$PHP_SELF.'?mod=all-affiliates&action=email">this form</a>.</p>

		<form method="post" action="'.$PHP_SELF.'?mod=affiliates&action=email&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<h2>Recipient: '.$row['aff_owner'].' @ '.$row['aff_subject'].'</h2>
		<p><b>Message:</b><br />
		<textarea name="message" rows="10" style="width:97%;"></textarea><br /><br />
		<input type="submit" name="email" class="btn-success" value="Send Message" /> 
		<input type="reset" name="reset" class="btn-cancel" value="Reset" /></p>
		</form>';
	}
}



/********************************************************
 * Action:			Email All Affiliates
 * Description:		Show page for emailing all affiliate
 */
if( $act == "all-affiliates" ) {
	if( isset($_POST['email-all']) ) {
		$check->Value();

		echo '<p>Your email was sent to the following:</p>';
		$sql = $database->query("SELECT * FROM `tcg_affiliates` ORDER BY `aff_owner`");
		while($row = mysqli_fetch_assoc($sql)) {
			$recipient = "$row[aff_email]";
			$subject = "$tcgname: Affiliate Contact Form";
			$message = "$tcgowner at $tcgname has sent you the following message: \n";
			$message .= "{$_POST['message']} \n\n";
			$message .= "-- $tcgowner\n";
			$message .= "$tcgname: $tcgurl\n";
			$headers = "From: $tcgname <$tcgemail> \n";
			$headers .= "Reply-To: $tcgname <$tcgemail>";
			
			if( mail($recipient,$subject,$message,$headers) ) { echo "Success: $row[aff_owner] ($row[aff_subject]) @ $row[aff_email]<br />\n"; }
			else { echo "Failed: $row[aff_owner] ($row[aff_subject]) @ $row[aff_email]<br />\n"; }
		}
		echo '</div>';
	}

	echo '<p>Need to contact all of '.$tcgname.'\'s affiliates? Use this form.<br />
	If you need to email one affiliate, please use the contact form from <a href="'.$PHP_SELF.'?mod=affiliates&action=email">this page</a>.</p>

	<form method="post" action="'.$PHP_SELF.'?mod=all-affiliates&action=email">
	<h2>Message</h2>
	<p><textarea name="message" rows="5" style="width:95%;"></textarea><br /><br />
	<input type="submit" name="email-all" class="btn-success" value="Send Message" /> 
	<input type="reset" name="reset" class="btn-cancel" value="Reset" /></p>
	</form>';
}
?>