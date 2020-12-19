<?php
#########################################
########## Approve Form Action ##########
#########################################
if (empty($id)) {
	echo '<h1">Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	$sql = $database->query("SELECT * FROM `tcg_affiliates` WHERE `id`=$id");
	while($row = mysqli_fetch_assoc($sql)) {
		$update = $database->query("UPDATE `tcg_affiliates` SET `status`='Active' WHERE `id`='$row[id]'");
		$recipient = "$row[email]";
		$subject = "$tcgname: Affiliate Approved!";
		
		$message = "Thank you for affiliating with $tcgname! Your application has been approved.\n\n";
		$message .= "-- $tcgowner\n";
		$message .= "$tcgname: $tcgurl\n";
		
		$headers = "From: $tcgname <$tcgemail> \n";
		$headers .= "Reply-To: $tcgname <$tcgemail>";
		
		if (mail($recipient,$subject,$message,$headers)) {
			if($update == TRUE) {
                $activity = '<span class="fas fa-globe" aria-hidden="true"></span> <a href="'.$row['url'].'" target="_blank">'.$row['subject'].' TCG</a> has been added as Shizen\'s new affiliate.';
                $date = date("Y-m-d", strtotime("now"));
                $database->query("INSERT INTO `tcg_activities` (`name`,`activity`,`date`) VALUES ('$name','$activity','$date')");
				echo '<h1">Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
				echo '<p>'.$row['name'].' has been successfully emailed and has been updated in the database.</p>';
			}
			else {
				echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
				echo '<p>'.$row['name'].' has been successfully emailed but has not be updated in the database. Please use the edit form from the <a href="index.php?page=affiliates">affiliates</a> page to update their status.</p>';
			}
		}
		else {
			if($update == TRUE) {
				echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
				echo '<p>'.$row['name'].' has been updated in the database but has not be emailed. Please send them an email to let them know they have been approved.</p>';
			}
			else {
				echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
				echo '<p>Sorry, there was an error and the email could not be sent to '.$row['name'].' @ '.$row['email'].'. They also were not updated in the database. Please send them an email to let them know they have been approved and use the edit form from the <a href="index.php?page=affiliates">affiliates</a> page to update their status.</p>';
			}
		}
	}
}
?>