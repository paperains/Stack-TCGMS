<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

#########################################
########## Approve Form Action ##########
#########################################
if (empty($id)) {
	echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	$sql = $database->query("SELECT * FROM `user_list` WHERE `id`='$id'");
	while($row = mysqli_fetch_assoc($sql)) {
		$update = $database->query("UPDATE `user_list` SET `status`='Active', `level`='1', `memcard`='Yes' WHERE `id`='$row[id]'");
		$recipient = "$row[email]";
		$subject = "$tcgname: Approved!";
		
		$message = "Thank you for joining $tcgname! You have been approved by $tcgowner and can now begin to play games and take freebies. You should have received your starter pack already on the site and in your email. You do not need to reply to this email.\n\n";
		$message .= "Now that you are an active member, there are lots of fun things to do. Check out the interactive section for the current game rounds, submit a member card, or post your wishlists!\n\n";
		$message .= "Don't forget that all members should be active at least once per two months. If you need to go on hiatus, please let us know! $tcgname does not delete members from the member list for inactivity, however any member who is inactive for two months will be listed as inactive and must submit an update form to reactive themselves.\n\n";
		$message .= "That should be everything you need to know! Have any questions? Make sure to look through the Information page and if you still can't find the answer, shoot us an email! Thanks again for joining and happy trading!\n\n";
		$message .= "-- $tcgowner\n";
		$message .= "$tcgname: $tcgurl\n";
		
		$headers = "From: $tcgname <$tcgemail> \n";
		$headers .= "Reply-To: $tcgname <$tcgemail>";
		
		if (mail($recipient,$subject,$message,$headers)) {
            $activity = '<span class="fas fa-user" aria-hidden="true"></span> <a href="/members.php?id='.$row['name'].'">'.$row['name'].'</a> became a member of '.$tcgname.'!';
            $date = date("Y-m-d", strtotime("now"));
            $name = $row['name'];
            $database->query("INSERT INTO `tcg_activities` (`name`,`activity`,`date`) VALUES ('$name','$activity','$date')");
            $database->query("INSERT INTO `user_rewards` (`name`,`type`,`mcard`,`cards`,`gold`,`timestamp`) VALUES ('$name','Gift','Yes','5','10','$date')");
			if($update == TRUE) {
				echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
				echo '<p>'.$row['name'].' has been successfully emailed and has been updated in the database.</p>';
			} else {
				echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
				echo '<p>'.$row['name'].' has been successfully emailed but has not be updated in the database. Please use the edit form from the <a href="index.php?page=members">members</a> page to update their status.</p>';
			}
		}
		else {
			if($update == TRUE) {
				echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
				echo '<p>'.$row['name'].' has been updated in the database but has not be emailed. Please send them an email to let them know they have been approved.</p>';
			} else {
				echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
				echo '<p>Sorry, there was an error and the email could not be sent to '.$row['name'].' @ '.$row['email'].'. They also were not updated in the database. Please send them an email to let them know they have been approved and use the edit form from the <a href="index.php?page=members">members</a> page to update their status.</p>';
			}
		}
	}
}
?>