<?php
include("admin/class.lib.php");
include($header);

$to = isset($_GET['to']) ? $_GET['to'] : null;

// Check if user is logged in
if ( empty($login) ) {
	header("Location: account.php?do=login");
}

include('theme/headers/msg-header.php');
// Check if user directly accesses a page
if ( empty($id) ) {
	echo '<h1>Oops?</h1>
	<p>It seems like you\'re trying to access a page directly! Please go back and click the correct link.</p>';
}

else {
	// Show create a message page
	if ( $page == "create" ) {
		if ( isset($_POST['submit']) ) {
			$check->Value();
			$id = $sanitize->for_db($_POST['id']);
			$from = $sanitize->for_db($_POST['sender']);
			$to = $sanitize->for_db($_POST['recipient']);
			$subject = $sanitize->for_db($_POST['subject']);
			$date = date("Y-m-d H:i:s", strtotime("now"));
            $message = $_POST['message'];
			$message = nl2br($message);
            $message = str_replace("'", "\'", $message);

			$insert = $database->query("INSERT INTO `user_mbox` (`msg_id`,`msg_subject`,`msg_text`,`msg_sender`,`msg_recipient`,`msg_box_from`,`msg_box_to`,`msg_see_from`,`msg_see_to`,`msg_del_from`,`msg_del_to`,`msg_origin`,`msg_date`) VALUES ('','$subject','$message','$from','$to','Out','In','0','1','0','0','','$date')");

			if ( !$insert ) { $error[] = "Sorry, there was an error and your message was not sent. ".mysqli_error().""; }
            else {
				$database->query("UPDATE `user_mbox` SET `msg_origin`=LAST_INSERT_ID() WHERE `msg_id`=LAST_INSERT_ID()");
				$success[] = "Your message has been sent to ".$to."!<br />They may be able to respond back the next time they logged in.";
			}
		} // end form process

		$sql = $database->get_assoc("SELECT `usr_name` FROM `user_list` WHERE `usr_name`='$to'");
		echo '<h1>Create Message</h1>
		<center>';
		if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
		if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
		echo '</center>
		<form method="post" action="'.$tcgurl.'messages.php?id='.$id.'&page=create">
			<input type="hidden" name="timestamp" id="timestamp" value="'.date("Y-m-d", strtotime("now")).'" />
			<input type="hidden" name="sender" id="sender" value="'.$player.'" />
			<input type="hidden" name="id" id="id" value="'.$id.'" />
			<table width="100%" class="table table-sliced table-striped">
            <tbody>
			<tr>
				<td width="20%"><b>Subject:</b></td>
				<td width="80%"><input type="text" name="subject" id="subject" style="width:95%;" /></td>
			</tr>
			<tr>
				<td><b>Recipient:</b></td>
				<td>';
				if( empty($to) ) {
					echo '<select name="recipient" id="recipient" style="width:99%;" />
						<option>----- Select Recipient -----</option>';
						$sql = $database->query("SELECT `usr_name` FROM `user_list` ORDER BY `usr_name` ASC");
						while ( $row = mysqli_fetch_assoc($sql) ) {
							echo '<option value="'.$row['usr_name'].'">'.$row['usr_name'].'</option>';
						}
					echo '</select>';
				}
				else if ( $to = $sql['usr_name'] ) {
					echo '<input type="text" style="width: 98%;" name="recipient" id="recipient" value="'.$sql['usr_name'].'" readonly />';
				}
				echo '<td>
			</tr>
			<tr>
				<td><b>Message:</b></td>
				<td><textarea name="message" id="message" style="width: 95%;" rows="10" /></textarea></td>
			</tr>
            </tbody></table>
			<input type="submit" name="submit" id="submit" class="btn-success" value="Send" /> 
			<input type="reset" name="reset" id="reset" class="btn-danger" value="Reset" />
		</form>';
	} // end create page

	// Show outbox page
	if ( $page == "outbox" ) {
		$sql = $database->query("SELECT * FROM `user_mbox` WHERE `msg_sender`='".$id."' AND `msg_box_from`='Out' AND `msg_del_from`='0' ORDER BY `msg_id` DESC");
		$counts = mysqli_num_rows($sql);

		if ( empty($view) ) {
			if ( isset($_POST['delete']) ) {
				$id = $sanitize->for_db($_POST['out_id']);
				$del = $sanitize->for_db($_POST['del_from']);

				$update = $database->query("UPDATE `user_mbox` SET `msg_del_from`='$del' WHERE `msg_id`='$id'");

				if ( !$update ) { $error[] = "Sorry, there was an error and your message was not deleted. ".mysqli_error().""; }
                else { $success[] = "Your message has been deleted!"; }
			}

			echo '<h1>Sent Messages</h1>
			<p>Here are the list of the personal messages you\'ve sent to your fellow traders.</p>
			<center>';
			if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
			if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
			echo '</center>
	        <form method="post" action="'.$tcgurl.'messages.php?id='.$id.'&page=outbox">
			<table width="100%" class="table table-bordered table-striped">';
			if ( $counts == 0 ) {
				echo '<tbody><tr><td width="100%"><p>You don\'t have any sent messages.</p></td></tr></tbody>';
			} else {
				echo '<thead><tr><td width="10%" align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
				<td width="90%"><b>Message Information:</b></td></tr></thead>
                <tbody>';
                while ( $msg = mysqli_fetch_assoc($sql) ) {
                    echo '<tr>
					<td align="center">
						<input type="hidden" name="out_id" value="'.$msg['msg_id'].'" />
						<input type="checkbox" name="del_from" value="1" />
					</td>
					<td><a href="'.$tcgurl.'messages.php?id='.$id.'&page=outbox&view='.$msg['msg_id'].'">'.$msg['msg_subject'].'</a><br />
						Sent to: '.$msg['msg_recipient'].' on '.date("F d, Y h:i A", strtotime($msg['msg_date'])).'</td>
					</tr>';
				}
				echo '<tr>
					<td colspan="2">
						<input type="submit" name="delete" id="delete" class="btn-danger" value="Delete" />
					</td>
				</tr></tbody>';
			}
			echo '</table>
			</form>';
		}

		else {
			$mrow = $database->get_assoc("SELECT * FROM `user_mbox` WHERE `msg_id`='$view' AND `msg_sender`='$id' AND `msg_box_from`='Out'");
			$subject = stripslashes($mrow['msg_subject']);
			$sentto = stripslashes($mrow['msg_recipient']);
			$message = stripslashes($mrow['msg_text']);

			$breaks = array("<br />","<br>","<br/>");
			$message = str_ireplace($breaks, "\n", $message);

			echo '<h1>Sent Messages</h1>
			<center>
			<table width="100%" class="table table-sliced table-striped">
            <tbody>
				<tr>
					<td width="20%"><b>Subject:</b></td>
					<td width="80%"><input type="text" name="subject" id="subject" value="'.$subject.'" /></td>
                </tr>
                <tr>
					<td><b>Sent to:</b></td>
					<td><input type="text" name="recipient" id="recipient" value="'.$sentto.'" /></td>
				</tr>
				<tr>
					<td><b>Message:</b></td>
					<td colspan="3"><textarea name="message" id="message" rows="10" style="width:95%;" />'.$message.'</textarea></td>
				</tr>
			</table>
			</center>';
		}
	} // end outbox page

	// Show inbox page
	if ( $page == "inbox" ) {
		$sql = $database->query("SELECT * FROM `user_mbox` WHERE `msg_recipient`='$id' AND `msg_box_to`='In' AND `msg_del_to`='0' ORDER BY `msg_id` DESC");
		$counts = mysqli_num_rows($sql);

		if( empty($view) ) {
			if ( isset($_POST['delete']) ) {
				$getID = $_POST['in_to'];
                foreach( $getID as $id ) {
                    $del = $_POST['del_to'];
                    $update = $database->query("UPDATE `user_mbox` SET `msg_del_to`='$del' WHERE `msg_id`='$id'");
                }

				if ( !$update ) { $error[] = "Sorry, there was an error and your message was not deleted. ".mysqli_error().""; }
                else { $success[] = "Your message has been deleted!"; }
			}

			echo '<h1>My Messages</h1>
			<p>Here are the list of the personal messages that you\'ve received from your fellow traders.</p>
			<center>';
			if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
			if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
			echo '</center>
            <form method="post" action="'.$tcgurl.'messages.php?id='.$id.'&page=inbox">
			<table width="100%" class="table table-bordered table-striped">';
			if ( $counts == 0 ) { echo '<tbody><tr><td width="100%" valign="top" class="tableBody"><p>You don\'t have any messages.</p></td></tr></tbody>'; }
			else {
				echo '<thead><tr><td width="10%" align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
				<td width="90%"><b>Message Information:</b></td></tr></thead>
                <tbody>';
				while ( $msg = mysqli_fetch_assoc($sql) ) {
					if ( $msg['msg_see_to'] == "1" ) {
						echo '<tr>
						<td align="center">
							<input type="hidden" name="in_to[]" value="'.$msg['msg_id'].'" />
							<input type="checkbox" name="del_to" value="1" />
						</td>
						<td>
							<a href="'.$tcgurl.'messages.php?id='.$id.'&page=inbox&view='.$msg['msg_id'].'"><b>'.$msg['msg_subject'].'</b></a><br />
							From: <b>'.$msg['msg_sender'].'</b> on <b>'.date("F d, Y h:i A", strtotime($msg['msg_date'])).'</b>
						</td>
						</tr>';
					}
					else {
						echo '<tr>
						<td align="center">
							<input type="hidden" name="in_to[]" value="'.$msg['msg_id'].'" />
							<input type="checkbox" name="del_to" id="del_to" value="1" />
						</td>
						<td>
							<a href="'.$tcgurl.'messages.php?id='.$id.'&page=inbox&view='.$msg['msg_id'].'">'.$msg['msg_subject'].'</a><br />
							From: '.$msg['msg_sender'].' on '.date("F d, Y h:i A", strtotime($msg['msg_date'])).'</td>
						</tr>';
					}
				} // end while
                echo '<tr>
                    <td colspan="2">
                        <input type="submit" name="delete" id="delete" class="btn-danger" value="Delete" />
                    </td>
                </tr>
                </tbody>';
		    }
			echo '</table>
            </form>';
	    } // end empty view

		else {
			if ( isset($_POST['submit']) ) {
				$from = $sanitize->for_db($_POST['sender']);
				$to = $sanitize->for_db($_POST['recipient']);
				$subject = $sanitize->for_db($_POST['subject']);
				$date = $sanitize->for_db($_POST['timestamp']);
				$origin = $sanitize->for_db($_POST['origin']);
				$message = $_POST['message'];
				$message = nl2br($message);
                $message = str_replace("'", "\'", $message);

				$insert = $database->query("INSERT INTO `user_mbox` (`msg_subject`,`msg_text`,`msg_sender`,`msg_recipient`,`msg_box_from`,`msg_box_to`,`msg_see_from`,`msg_see_to`,`msg_del_from`,`msg_del_to`,`msg_origin`,`msg_date`) VALUES ('$subject','$message','$from','$to','Out','In','0','1','0','0','$origin','$date')");

				if ( !$insert ) { $error[] = "Sorry, there was an error and your message was not sent. ".mysqli_error().""; }
                else { $success[] = "Your message has been sent to ".$to."!"; }
			}

			$mrow = $database->get_assoc("SELECT * FROM `user_mbox` WHERE `msg_id`='$view' AND `msg_recipient`='$id' AND `msg_box_to`='In'");
			$mid = $mrow['msg_id'];
			$subject = stripslashes($mrow['msg_subject']);
			$sentby = stripslashes($mrow['msg_sender']);
			$message = stripslashes($mrow['msg_text']);
			$origin = stripslashes($mrow['msg_origin']);
			$timestamp = date("F d, Y h:i:s", strtotime($mrow['msg_date']));
			$date = date("Y-m-d H:i:s", strtotime("now"));

			$breaks = array("<br />","<br>","<br/>");
			$message = str_ireplace($breaks, " ", $message);

			$update = $database->query("UPDATE `user_mbox` SET `msg_see_to`='0' WHERE `msg_id`='$mid' AND `msg_recipient`='$id' AND `msg_box_to`='In'");

			echo '<h1>My Messages</h1>
			<center>';
			if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
			if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
			echo '</center>
			<form method="post" action="'.$tcgurl.'messages.php?id='.$id.'&page=inbox&view='.$view.'">
				<input type="hidden" name="origin" id="origin" value="'.$origin.'" />
				<input type="hidden" name="timestamp" id="timestamp" value="'.$date.'" />
				<input type="hidden" name="sender" id="sender" value="'.$id.'" />
				<table width="100%" class="table table-sliced table-striped">
                <tbody>
				<tr>
					<td width="20%"><b>Subject:</b></td>
					<td width="80%"><input type="text" name="subject" id="subject" value="RE: '.$subject.'" style="width: 95%;" /></td>
                </tr>
                <tr>
					<td><b>Reply to:</b></td>
					<td><input type="text" name="recipient" id="recipient" value="'.$sentby.'" readonly style="width: 95%;" /></td>
				</tr>
				<tr>
					<td><b>Message:</b></td>
					<td><textarea name="message" id="message" rows="10" style="width:95%;">

--------------------
'.$timestamp.'
'.$message.'
					</textarea></td>
				</tr>
                </tbody></table>
				<input type="submit" name="submit" id="submit" class="btn-success" value="Send" /> 
				<input type="reset" name="reset" id="reset" class="btn-danger" value="Reset" />
			</form>';
		}
	} // end inbox page
}

include('theme/headers/msg-footer.php');
include($footer);
?>