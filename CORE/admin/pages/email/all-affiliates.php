<?php
###########################################
########## Email All Form Action ##########
###########################################
if ($stat == "sent") {
	if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
	else {
		$check->Value();
		echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Emails</h1>
        <p>Your email was sent to the following:</p>';
		$sql = $database->query("SELECT * FROM `tcg_affiliates` ORDER BY `owner`");
		while($row = mysqli_fetch_assoc($sql)) {
			$recipient = "$row[email]";
			$subject = "$tcgname: Affiliate Contact Form";
			$message = "$tcgowner at $tcgname has sent you the following message: \n";
			$message .= "{$_POST['message']} \n\n";
			$message .= "-- $tcgowner\n";
			$message .= "$tcgname: $tcgurl\n";
			$headers = "From: $tcgname <$tcgemail> \n";
			$headers .= "Reply-To: $tcgname <$tcgemail>";
			
			if (mail($recipient,$subject,$message,$headers)) { echo "Success: $row[name] ($row[tcgname]) @ $row[email]<br />\n"; }
			else { echo "Failed: $row[name] ($row[tcgname]) @ $row[email]<br />\n"; }
		}
	}
} else {
	echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Email All Affiliates</h1>
    <p>Need to contact all of '.$tcgname.'\'s affiliates? Use this form. If you need to email one affiliate, please use the contact form from <a href="index.php?action=email&page=affiliates">this page</a>.</p>
	<form method="post" action="index.php?action=email&page=all-affiliates&stat=sent">
	<table width="90%" cellspacing="3">
	<tr><td valign="top" class="headSub">Message:</td><td valign="middle"><textarea name="message" rows="5" cols="50"></textarea></td></tr>
	<tr><td class="headSub">Proceed?</td><td valign="middle"><input type="submit" name="submit" class="btn-success" value="Send" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td></tr>
	</table>
	</form>';
}
?>