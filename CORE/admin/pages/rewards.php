<?php
#########################################
########## Rewards Form Action ##########
#########################################
if ( isset($_POST['submit']) ) {
	$id = $sanitize->for_db($_POST['id']);
	$name = $sanitize->for_db($_POST['name']);
	$type = $sanitize->for_db($_POST['type']);
	$cards = $sanitize->for_db($_POST['cards']);
	$cake = $sanitize->for_db($_POST['cake']);
	$ticket = $sanitize->for_db($_POST['ticket']);
    $mcard = $sanitize->for_db($_POST['mcard']);
    $date = $sanitize->for_db($_POST['timestamp']);

    $insert = $database->query("INSERT INTO `user_rewards` (`name`,`type`,`cards`,`mcard`,`cake`,`ticket`,`timestamp`) VALUES ('$name','$type','$cards','$mcard','$cake','$ticket','$date')");
	
	if ($insert == TRUE) { $success[] = "The rewards were successfully sent to $name."; }
	else { $error[] = "Sorry, there was an error and the rewards were not sent. ".mysqli_error().""; }
}

if (empty($id)) {
	echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	$row = $database->get_assoc("SELECT * FROM `user_list` WHERE id='$id'");
    $date = date("Y-m-d", strtotime("now"));
    echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Send Rewards</h1>
    <p>Use the form below to send rewards to a member.</p><center>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
    echo '</center>
    <form method="post" action="/index.php?page=rewards&id='.$id.'">
    <input type="hidden" name="id" id="id" value="'.$id.'" />
    <input type="hidden" name="timestamp" id="timestamp" value="'.$date.'" />
    <table width="100%" cellspacing="3">
        <tr>
            <td width="15%" class="headSub">Name:</td><td width="35%" valign="middle"><input type="text" name="name" id="name" value="'.$row['name'].'" readonly style="width:90%;" /></td>
            <td width="15%" class="headSub">Rewarded for:</td><td width="35%" valign="middle"><select name="type" id="type" style="width:97%;">
                <option value="Daily Bonus">Daily Bonus</option>
                <option value="Donations">Donations</option>
                <option value="Games">Games</option>
                <option value="Gift">Gift</option>
                <option value="Referrals">Referrals</option>
            </select></td>
        </tr>
        <tr>
            <td class="headSub">Member Card:</td><td valign="middle"><input type="radio" name="mcard" id="mcard" value="Yes" /> Yes &nbsp;&nbsp; <input type="radio" name="mcard" id="mcard" value="No" /> No</td>
            <td class="headSub">Cards:</td><td valign="middle"><input type="text" name="cards" id="cards" style="width:90%;" /></td>
        </tr>
        <tr>
            <td class="headSub">Cake:</td><td valign="middle"><input type="text" name="cake" id="cake" style="width:90%;" /></td>
            <td class="headSub">Ticket:</td><td valign="middle"><input type="text" name="ticket" id="ticket" style="width:90%;" /></td>
        </tr>
        <tr>
            <td colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Send Rewards" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td>
        </tr>
    </table>
    </form>';
}
?>