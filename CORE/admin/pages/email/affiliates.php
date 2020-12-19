<?php
#######################################
########## Email Form Action ##########
#######################################
if ($stat == "sent") {
	if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
	else {
		$sql = $database->query("SELECT * FROM `tcg_affiliates` WHERE `id`=$_POST[id]");
		while($row = mysqli_fetch_assoc($sql)) {
			$recipient = "$row[email]";
			$subject = "$tcgname: Affiliate Contact Form";
			
			$message = "$tcgowner at $tcgname has sent you the following message: \n";
			$message .= "{$_POST['message']} \n\n";
			$message .= "-- $tcgowner\n";
			$message .= "$tcgname: $tcgurl\n";
			
			$headers = "From: $tcgname <$tcgemail> \n";
			$headers .= "Reply-To: $tcgname <$tcgemail>";
			
			if (mail($recipient,$subject,$message,$headers)) {
				echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
				echo '<p>Your message to '.$row['name'].' @ '.$row['email'].' from '.$row['tcgname'].' has been successfully sent!</p>';
			}
			else {
				echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
				echo '<p>Sorry, there was an error and the email could not be sent to '.$row['name'].' @ '.$row['email'].' from '.$row['tcgname'].'.';
			}
		}
	}
} else {
	if (empty($id)) {
		echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
		<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		$sql = $database->query("SELECT * FROM `tcg_affiliates` WHERE id='$id'");
		while($row = mysqli_fetch_assoc($sql)) {
			echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Email '.$row['owner'].' at '.$row['subject'].'</h1>
            <p>Use this form to send an email to '.$row['owner'].', owner of '.$row['subject'].'. <b>This is not the form for sending an email to all affiliates.</b> If you need to send an email to all of the affiliates of '.$tcgname.', please use <a href="index.php?action=email&page=all-affiliates">this form</a>.</p>
			<form method="post" action="index.php?action=email&page=affiliates&id='.$id.'&stat=sent">
			<input type="hidden" name="id" value="'.$id.'" />
			<table width="100%" cellspacing="3">
			<tr><td class="headSub" width="15%">To:</td><td valign="middle">'.$row['owner'].' @ '.$row['subject'].'</td></tr>
			<tr><td class="headSub" valign="top">Message:</td><td valign="middle"><textarea name="message" rows="10" style="width:97%;"></textarea></td></tr>
			<tr><td valign="middle" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Send" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td></tr>
			</table>
			</form>';
		}
	}
}
?>