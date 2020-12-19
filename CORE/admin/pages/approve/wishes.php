<?php
#########################################
########## Approve Form Action ##########
#########################################
if (empty($id)) {
	echo "<h1>Error</h1>
	This page shouldn't be accessed directly! Please go back and try something else.";
} else {
    date_default_timezone_set('Asia/Manila');
	$timestamp = date('Y-m-d');

	$sql = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `id`='$id'");
	$update = "UPDATE `user_wishes` SET `timestamp`='$timestamp', `status`='Granted' WHERE `id`='$sql[id]'";
	if( $update == TRUE ) {
		echo '<h1>Wishes <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
		echo '<p>You have just granted a wish submitted by '.$row['player'].'.';
	} else {
		echo '<h1>Wishes <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
		echo '<p>Sorry, there was an error and the wish was not granted.</p>';
	}
}
?>