<?php
#######################################
########## Email Form Action ##########
#######################################
if ( isset($_POST['submit']) ) {
    $id = $sanitize->for_db($_POST['id']);
	$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `id`='$id'");
	$recipient = "$row[email]";
	$subject = "$tcgname: Contact Form";
	$message = "$tcgowner at $tcgname has sent you the following message: \n";
	$message .= "{$_POST['message']} \n\n";
	$message .= "-- $tcgowner\n";
	$message .= "$tcgname: $tcgurl\n";
	$headers = "From: $tcgname <$tcgemail> \n";
	$headers .= "Reply-To: $tcgname <$tcgemail>";
	if (mail($recipient,$subject,$message,$headers)) { $success[] = "Your message to ".$row['name']." @ ".$row['email']." has been successfully sent!"; }
	else { $error[] = "Sorry, there was an error and the email could not be sent to ".$row['name']." @ ".$row['email'].". ".mysqli_error().""; }
}

if (empty($id)) {
	echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	$row = $database->get_assoc("SELECT * FROM `user_list` WHERE id='$id'");
	echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Email '.$row['name'].'</h1>
	<p>Use this form to send an email to '.$row['name'].'. <b>This is not the form for sending an email to all members.</b> If you need to send an email to all of the members of '.$tcgname.', please use <a href="index.php?action=email&page=members-all">this form</a>.</p><center>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	echo '</center>
    <form method="post" action="index.php?action=email&page=members&id='.$id.'">
	<input type="hidden" name="id" value="'.$id.'" />
	<table width="100%" cellspacing="3">
	<tr><td class="headSub" width="20%">To:</td><td valign="middle">'.$row['name'].'</td></tr>
	<tr><td class="headSub" valign="top">Message:</td><td valign="middle"><textarea name="message" rows="5" style="width:95%;"></textarea></td></tr>
	<tr><td valign="middle" align="center" colspan="2"><input type="submit" name="submit" class="btn-success" value="Send" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td></tr>
	</table>
	</form>';
}
?>