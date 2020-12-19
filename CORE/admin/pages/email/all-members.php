<?php
###########################################
########## Email All Form Action ##########
###########################################
if ( isset($_POST['submit']) ) {
	echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Emails</h1>
	<p>Your email was sent to the following:</p>';
	$row = $database->get_assoc("SELECT * FROM `user_list` ORDER BY `name`");
	
	$recipient = "$row[email]";
	$subject = "$tcgname: Contact Form";
	$message = "$tcgowner at $tcgname has sent you the following message: \n";
	$message .= "{$_POST['message']} \n\n";
	$message .= "-- $tcgowner\n";
	$message .= "$tcgname: $tcgurl\n";
	$headers = "From: $tcgname <$tcgemail> \n";
	$headers .= "Reply-To: $tcgname <$tcgemail>";
	
	if (mail($recipient,$subject,$message,$headers)) { echo "Success: $row[name] @ $row[email]<br />\n"; }
	else { echo "Failed: $row[name] @ $row[email]<br />\n"; }
}

echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Email All</h1>
<p>Need to contact all of '.$tcgname.'\'s members? Use this form. If you need to email one member, please use the contact form from <a href="index.php?page=members">this page</a>.</p>
<form method="post" action="index.php?action=email&page=members-all">
<table width="100%" cellspacing="3">
<tr><td class="headSub" valign="top" width="20%">Message:</td><td valign="middle"><textarea name="message" rows="5" style="width:95%;"></textarea></td></tr>
<tr><td class="headSub">Proceed?</td><td valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Send" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td></tr>
</table>
</form>';
?>