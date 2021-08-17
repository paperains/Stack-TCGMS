<?php
include("admin/class.lib.php");
include($header);
include('theme/headers/acct-header.php');

/********************************************************
 * Action:			Login
 * Description:		Process form when user logs in
 */
if ( $do == "login" ) {
	if ( $act == "loggedin" ) {
		$userName = $_POST["username"];
		$password = $_POST["password"];
		$errMsg="";

		if ( $userName != "" && $password != "" ) {
			$encryptPassword = md5($password);
			$authRow = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$userName' AND `usr_pass`='$encryptPassword'");

			$userID = $authRow['usr_id'];
			$userNAME = $authRow['usr_name'];
			$userStatus = $authRow['usr_status'];

			if ( $userID!=0 ) {
				$_SESSION['USER_ID'] = $userID;
				$_SESSION['USR_LOGIN'] = $userName;
				$_SESSION['USR_NAME'] = $userNAME;
				$_SESSION['USR_STATUS'] = $userStatus;

				$log = date('Y-m-d H:i:s', strtotime("now"));
				$log2 = date('Y-m-d', strtotime("now"));

				$database->query("UPDATE `user_list` SET `usr_sess`='$log' WHERE `usr_id`='$userID'");
				$database->query("UPDATE `user_trades_rec` SET `trd_date`='$log2' WHERE `trd_name`='$userNAME'");

				// Set status to active when logged in for inactive members
				if ( $userStatus == 'Inactive' ) {
					$database->query("UPDATE `user_list` SET `usr_status`='Active' WHERE `usr_name`='$userNAME'");
				} // end status update
				header("Location: account.php");
			}
			else { header("Location: account.php?do=login&msg=invalid"); }
		}
		else { header ("Location: account.php?do=login&msg=missing"); }
	}

	else {
		if ( $msg == "invalid" ) {
			echo '<h1>Login : Error</h1>
			<p>Oops, it looks like the email and/or password you entered is not in our database. Check your spelling and try again or contact us at '.$tcgemail.'.</p>';
		}

		else if ( $msg == "missing" ) {
			echo '<h1>Login : Error</h1>
			<p>Oops, it looks like one or more values from the form were not entered. Please go back and try again.</p>';
		}

		else {
			echo '<h1>Login</h1>
			<p>Below is the login form for the member panel here at '.$tcgname.'. <b>This is only for current members</b>. If you would like to join, please click <a href="'.$tcgurl.'members.php?page=join">here</a> to see the rules and join.</p>
			<center>
			<form method="post" action="'.$tcgurl.'account.php?do=login&action=loggedin">
			<table width="100%" class="table table-sliced table-striped">
            <tbody><tr>
				<td width="15%"><b>Email:</b></td>
				<td width="85%"><input type="text" name="username" placeholder="username@domain.tld" style="width:90%;" /></td>
			</tr>
			<tr>
				<td><b>Password:</b></td>
				<td><input type="password" name="password" placeholder="********" style="width:90%;" /></td>
			</tr></tbody>
            </table>
            <input type="submit" name="submit" class="btn-success" value="Login"> 
			<button onclick="window.location.href=\'account.php?do=lostpass\'" class="btn-primary">Lost Password?</a>
			</form>
			</center>';
		}
	}
} // end login




/********************************************************
 * Action:			Lost Password
 * Description:		Process form for lost password
 */
else if ( $do == "lostpass" ) {
	if ( isset($_POST['submit']) ) {
		$check->Value();
		if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,6})$/",strtolower($_POST['email']))) {
			exit("<h1>Error</h1>\nThat e-mail address is not valid, please use another.<br /><br />");
		}
		$password = substr(md5(date("c")), 0, 8);
		$email = $sanitize->for_db($_POST['email']);
		$scrampass = md5($password);

		$num = $database->num_rows("SELECT * FROM `user_list` WHERE `usr_email`='$email'");
		$sql = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$email'");
		if ( $num === 0 ) {
			exit("<h1>Error</h1>\nThat email address does not exist in our database. Please go back and check your spelling and try again.");
		}

		else {
			$id = $sql['usr_id'];
			$recipient = "$email";
			$subject = "$tcgname: Reset Your Password";
			$message = "Your password has been reset to $password. Please log in and change it.\n";
			$headers = "From: $tcgname <$tcgemail> \n";
			$headers .= "Reply-To: $tcgname <$tcgemail>";
			if ( mail($recipient,$subject,$message,$headers) ) {
				$database->query("UPDATE `user_list` SET `usr_pass`='$scrampass' WHERE `usr_id`='$id'");
				$success[] = "Your password has been reset and sent to the email you have provided.<br />Please log in and change your password once you have checked your email.";
			}

			else {
				$error[] = "Sorry, there was an error and your password was not updated. ".mysqli_error()."";
			}
		}
	}

	echo '<h1>Lost Password</h1>
	<p>If you have forgotten your password, please feel free to use the reset password form below.</p>
	<center>';
	if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
	if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	echo '</center>
	<form method="post" action="'.$tcgurl.'account.php?do=lostpass">
	<center><table width="70%" class="border" cellspacing="3">
	<tr>
		<td class="headLine">Email:</td>
		<td class="tableBody">
			<input type="text" name="email" value="" style="width:62%;" /> 
			<input type="submit" name="submit" class="btn-success" value="Reset Password" />
		</td>
	</tr>
	</table></center>
	</form>';
} //end lost password




/********************************************************
 * Action:			Redirection
 * Description:		Redirect to login page if not loggedin
 */
else if ( empty($login) ) {
	header ( "Location: account.php?do=login" );
}




/********************************************************
 * Action:			Logout
 * Description:		Process form when user logs out
 */
else if ( $do == "logout" ) {
	$_SESSION = array();
	session_destroy();

	// Redirect to following page after logout
	header("Location: account.php?do=login");
} // end logout





/********************************************************
 * Action:			Reset Password
 * Description:		Process form for resetting password
 */
else if ( $do == "reset-password" ) {
	if ( isset($_POST['submit']) ) {
		$check->Password();
		$id = $sanitize->for_db($_POST['id']);
		$email = $sanitize->for_db($_POST['email']);
		$password = $sanitize->for_db($_POST['password']);
		$scrampass = md5($password);

		$recipient = "$email";
		$subject = "$tcgname: Changed Your Password";
		$message = "Your password has been changed to $password. Please keep this email in a safe place, as we cannot recover lost passwords.\n";
		$headers = "From: $tcgname <$tcgemail> \n";
		$headers .= "Reply-To: $tcgname <$tcgemail>";
		$update = $database->query("UPDATE `user_list` SET `usr_pass`='$scrampass' WHERE `usr_id`='$id'");

		if ( mail($recipient,$subject,$message,$headers) ) {
			if( $update === TRUE ) {
				session_destroy();
				header("Location: account.php?do=login");
			}
			$success[] = "Your password has been changed and has been sent to your email.";
		}

		else { $error[] = "Sorry, there was an error and your password was not updated. ".mysqli_error().""; }
	}

	$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$login'");
	echo '<h1>Change Your Password</h1>
	<p>Use this form to change your password. <b>You will be logged out after this change</b>. Make sure you have any card activity logged before this.</p>
	<center>';
	if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
	if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	echo '</center>
	<form method="post" action="'.$tcgurl.'account.php?do=reset-password">
	<input type="hidden" name="id" value="'.$row['usr_id'].'" />
	<input type="hidden" name="email" value="'.$row['usr_email'].'" />
	<table width="100%" class="table table-sliced table-striped">
    <tbody>
		<tr>
			<td width="25%"><b>Current Password:</b></td>
			<td width="75%"><input type="password" name="current" value="" style="width:95%;" /></td>
		</tr>
		<tr>
			<td><b>New Password:</b></td>
			<td>
				<input type="password" name="password" placeholder="********" style="width:44%;" /> 
				<input type="password" name="password2" placeholder="Retype password for verification" style="width:45%;" />
			</td>
		</tr>
    </tbody></table>
    <input type="submit" name="submit" class="btn-success" value="Change Password" /> 
	<input type="reset" name="reset" class="btn-danger" value="Reset" />
	</form>';
} // end reset password




/********************************************************
 * Action:			Quit TCG
 * Description:		Process form when user quits the TCG
 */
else if ( $do == "quit" ) {
	if ( isset($_POST['submit']) ) {
		$from = $sanitize->for_db($_POST['sender']);
		$to = $sanitize->for_db($_POST['recipient']);
		$date = date("Y-m-d H:i:s", strtotime("now"));
		$date2 = date("Y-m-d", strtotime("now"));
		$message = $_POST['message'];
		$message = nl2br($message);
		$message = str_replace("'", "\'", $message);

		$insert = $database->query("INSERT INTO `user_mbox` (`msg_subject`,`msg_text`,`msg_sender`,`msg_recipient`,`msg_box_from`,`msg_box_to`,`msg_see_from`,`msg_see_to`,`msg_del_from`,`msg_del_to`,`msg_origin`,`msg_date`) VALUES ('Quitting','$message','$from','$to','Out','In','0','1','0','0','','$date')");

		if( !$insert ) {
			$error[] = "Sorry, there was an error in processing your form.<br />
			Send the information to ".$tcgemail." and we will send you a reply ASAP. ".mysqli_error()."";
		}

		else {
			$get = $database->get_assoc("SELECT `usr_reg` FROM `user_list` WHERE `usr_name`='$from'");
			$database->query("UPDATE `user_mbox` SET `msg_origin`=LAST_INSERT_ID() WHERE `msg_id`=LAST_INSERT_ID()");
			$database->query("INSERT INTO `user_list_quit` (`usr_name`,`usr_mcard`,`usr_joined`,`usr_quit`) VALUES ('$from','mc-$from','".$get['usr_reg']."','$date2')");
			$database->query("DELETE FROM `user_list` WHERE `usr_name`='$from'");
			$success[] = "Sorry to see you leave. Hopefully you change your mind and join us in the future again!";
		}
	}

	echo '<h1>Quit '.$tcgname.'</h1>
	<p>Are you sure about this? ∑(ﾟﾛﾟ〃) If you are&mdash;and we\'re sorry to see you leave, you can use the form below to inform us.</p>
	<center>';
	if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
	if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	echo '</center>
	<form method="post" action="'.$tcgurl.'account.php?do=quit">
	<input type="hidden" name="sender" value="'.$row['usr_name'].'" />
	<input type="hidden" name="recipient" value="'.$tcgowner.'" />
	<textarea name="message" rows="5" style="width:95%;">Please tell us something why you\'re leaving or a farewell message.</textarea><br />
	<input type="submit" name="submit" class="btn-success" value="Send" /> 
	<input type="reset" name="reset" class="btn-danger" value="Reset" />
	</form>';
} // end quit tcg




/********************************************************
 * Action:			Edit Information
 * Description:		Show page for editing user profile
 */
else if ( $do == "edit-information" ) {
	if ( isset($_POST['update']) ) {
		$check->Value();
		$id = $sanitize->for_db($_POST['id']);
		$email = $sanitize->for_db($_POST['email']);
		$url = $sanitize->for_db($_POST['url']);
		$status = $sanitize->for_db($_POST['status']);
		$twitter = $sanitize->for_db($_POST['twitter']);
		$discord = $sanitize->for_db($_POST['discord']);
		$collecting = $sanitize->for_db($_POST['collecting']);
		$random = $sanitize->for_db($_POST['random']);
		$accept = $sanitize->for_db($_POST['accept']);
		$birthday = $_POST['year']."-". $_POST['month']."-".$_POST['day'];
		$about = $_POST['about'];
		$about = nl2br($about);
		$about = str_replace("'", "\'", $about);

		$update = $database->query("UPDATE `user_list` SET `usr_email`='$email', `usr_url`='$url', `usr_bday`='$birthday', `usr_status`='$status', `usr_deck`='$collecting', `usr_bio`='$about', `usr_twitter`='$twitter', `usr_discord`='$discord', `usr_rand_trade`='$random', `usr_auto_trade`='$accept' WHERE `usr_id`='$id'");

		if ( !$update ) { $error[] = "Sorry, there was an error and your info was not updated. ".mysqli_error().""; }
		else { $success[] = "Your information has been updated!"; }
	}

	$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$login'");
	$old_bio = stripslashes($row['usr_bio']);
	echo '<h1>Edit Profile</h1>
	<p>Use this form to edit your information in the database. <b>You cannot use this form to change your password.</b> To change it, please click <a href="'.$tcgurl.'account.php?do=reset-password">here</a>.</p>
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}

	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	echo '</center>
	<form method="post" action="'.$tcgurl.'account.php?do=edit-information">
	<input type="hidden" name="id" value="'.$row['usr_id'].'" />
	<table width="100%" class="table table-sliced table-striped">
    <tbody>
    <tr>
		<td width="15%" align="right"><b>Username:</b></td>
		<td width="35%" colspan="3"><input type="text" name="username" value="'.$row['usr_name'].'" style="width: 96%;" readonly /></td>
	</tr>
	<tr>
		<td align="right"><b>Email:</b></td>
		<td colspan="3"><input type="text" name="email" value="'.$row['usr_email'].'" style="width: 96%;" /></td>
	</tr>
    <tr>
		<td align="right"><b>Trade Post:</b></td>
		<td colspan="3"><input type="text" name="url" value="'.$row['usr_url'].'" style="width: 96%;" /></td>
	</tr>
    <tr>
		<td align="right"><b>Birthday:</b></td>
		<td colspan="3">
			<select name="month">
				<option value="'.date("m", strtotime($row['usr_bday'])).'">Current: '.date("F", strtotime($row['usr_bday'])).'</option>';
			for($m=1; $m<=12; $m++) {
				if ($m < 10) { $_mon = "0$m"; }
				else { $_mon = $m; }
				echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
			}
			echo '</select> 
			<select name="day">
				<option value="'.date("d", strtotime($row['usr_bday'])).'">Current: '.date("d", strtotime($row['usr_bday'])).'</option>';
			for($i=1; $i<=31; $i++) {
				if ($i < 10) { $_days = "0$i"; }
				else { $_days = $i; }
				echo '<option value="'.$_days.'">'.$_days.'</option>';
			}
			echo '</select> ';
			$date = date('Y');
			$start = $date - 10;
			$end = $start - 40;
			$yearArray = range($start,$end);
			echo '<select name="year">
				<option selected value="'.date("Y", strtotime($row['usr_bday'])).'">Current: '.date("Y", strtotime($row['usr_bday'])).'</option>';
			foreach ( $yearArray as $year ) {
				$selected = ($year == $start) ? 'selected' : '';
				echo '<option value="'.$year.'">'.$year.'</option>';
			}
			echo '</select>
		</td>
	</tr>
    <tr>
		<td align="right"><b>Collecting:</b></td>
		<td>
			<select name="collecting" style="width: 98%;">
				<option value="'.$row['usr_deck'].'">Current: '.$row['usr_deck'].'</option>';
			$collect = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active' ORDER BY `card_filename` ASC");
			while ( $collecting = mysqli_fetch_assoc($collect) ) {
				echo '<option value="'.$collecting['card_filename'].'">'.$collecting['card_deckname'].' ('.$collecting['card_filename'].')</option>';
			}
			echo '</select>
		</td>
		<td align="right" width="15%"><b>Status:</b></td>
		<td width="35%">
			<select name="status" style="width: 98%;">
				<option value="'.$row['usr_status'].'">Current: '.$row['usr_status'].'</option>
				<option value="Hiatus">Hiatus</option>
				<option value="Active">Active</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><b>Twitter:</b></td>
		<td><input type="text" name="twitter" value="'.$row['usr_twitter'].'" style="width: 90%;" /></td>
		<td align="right"><b>Discord:</b></td>
		<td><input type="text" name="discord" value="'.$row['usr_discord'].'" style="width: 90%;" /></td>
	</tr>
	<tr>
		<td align="right"><b>Accepts Random:</b></td>
		<td>';
		if ( $row['usr_rand_trade'] == "0" ) {
			echo '<input type="radio" name="random" value="1" /> Yes &nbsp;&nbsp; 
			<input type="radio" name="random" value="0" checked /> No';
		}
		else {
			echo '<input type="radio" name="random" value="1" checked /> Yes &nbsp;&nbsp; 
			<input type="radio" name="random" value="0" /> No';
		}
		echo '</td>
		<td align="right"><b>Allows Trade:</b></td>
		<td>';
		if ( $row['usr_auto_trade'] == "0" ) {
			echo '<input type="radio" name="accept" value="1" /> Yes &nbsp;&nbsp; 
			<input type="radio" name="accept" value="0" checked /> No';
		}
		else {
			echo '<input type="radio" name="accept" value="1" checked /> Yes &nbsp;&nbsp;
			<input type="radio" name="accept" value="0" /> No';
		}
		echo '</td>
	</tr>
	<tr>
		<td align="right"><b>Biography:</b></td>
		<td colspan="3">
			<textarea name="about" rows="5" style="width: 96%;">'.$old_bio.'</textarea>
		</td>
	</tr>
    </tbody>
    </table>
    <input type="submit" name="update" class="btn-success" value="Edit Information" />
	</form>';
} // end edit information




/********************************************************
 * Action:			Edit Items
 * Description:		Show page for editing user items
 */
else if ( $do == "edit-items" ) {
	if ( isset($_POST['update']) ) {
		$name = $sanitize->for_db($_POST['name']);
		$mcard = $sanitize->for_db($_POST['mcard']);
		$ecard = $sanitize->for_db($_POST['ecard']);
		$lvlb = $sanitize->for_db($_POST['lvlb']);

		function trim_value(&$value) { $value = trim($value); }
		$mcard = explode(',',$mcard);
		$ecard = explode(',',$ecard);

		array_walk($mcard, 'trim_value');
		array_walk($ecard, 'trim_value');

		usort($mcard, 'strnatcasecmp');
		sort($ecard);

		$mcard = implode(', ',$mcard);
		$ecard = implode(', ',$ecard);

		$update = $database->query("UPDATE `user_items` SET `itm_mcard`='$mcard', `itm_ecard`='$ecard', `itm_badge`='$lvlb' WHERE `itm_name`='$name'");

		if ( !$update ) { $error[] = "Sorry, there was an error and your items was not updated. ".mysqli_error().""; }
		else { $success[] = "Your items has been updated!"; }
	}

	$row = $database->get_assoc("SELECT * FROM `user_items` WHERE `itm_name`='$player'");
	echo '<h1>Edit Your Items</h1>
	<p>Use this form to edit your items in the database. <b>You can only add or remove member and event cards via this form.</b> Please make sure to add only the cards you\'ve gained in a comma-separated format. Your mastered decks and milestones can only be edited by an administrator.</p>
	<center>';
	if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
	if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	echo '</center>
	<form method="post" action="'.$tcgurl.'account.php?do=edit-items">
	<input type="hidden" name="name" value="'.$row['itm_name'].'" />
	<table width="100%" class="table table-sliced table-striped">
    <tbody>
	<tr>
		<td width="20%"><b>Member Cards:</b></td>
		<td width="80%"><textarea name="mcard" rows="5" style="width: 95%;">'.$row['itm_mcard'].'</textarea></td>
	</tr>
	<tr>
		<td><b>Event Cards:</b></td>
		<td><textarea name="ecard" rows="5" style="width: 95%;">'.$row['itm_ecard'].'</textarea></td>
	</tr>
	<tr>
		<td><b>Level Badge:</b></td>
		<td>
			<select name="lvlb" style="width:95%;">';
			if ($row['itm_badge'] == "") {
				echo '<option value="">Select a Level Badge</option>';
			}
			else {
				echo '<option value="'.$row['itm_badge'].'">'.$row['itm_badge'].'</option>';
			}
			$counts = $database->num_rows("SELECT * FROM `tcg_levels_badge`");
			for ($i=1; $i<=$counts; $i++) {
				$lb = $database->get_assoc("SELECT * FROM `tcg_levels_badge` WHERE `badge_id`='$i'");
				echo '<option value="'.$lb['badge_set'].'">'.$lb['badge_name'].' ('.$lb['badge_set'].')</option>';
			}
			echo '</select>
		</td>
    </tr>
    </tbody></table>
    <input type="submit" name="update" class="btn-success" value="Edit Items" />
	</form>';
} // end edit items




/********************************************************
 * Action:			Profile
 * Description:		Show page for user profile
 */
else {
	$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$login'");
	$trd = $database->get_assoc("SELECT * FROM `user_trades_rec` WHERE `trd_name`='".$row['usr_name']."'");
    $lvlName = $database->get_assoc("SELECT `lvl_name` FROM `tcg_levels` WHERE `lvl_id`='".$row['usr_level']."'");
	$about = stripslashes($row['usr_bio']);

    // Explode bombs
    $curValue = explode(' | ', $general->getItem( 'itm_currency' ));
    $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
    foreach( $curValue as $key => $value ) {
        $tn = substr_replace($curName[$key],"",-4);
        if( $curValue[$key] > 1 ) {
            $var = substr($tn, -1);
            if( $var == "y" ) {
                $tn = substr_replace($tn,"ies",-1);
            } else if( $var == "o" ) {
                $tn = substr_replace($tn,"oes",-1);
            }
            else { $tn = $tn.'s'; }
        } else { $tn = $tn; }
        if( empty($curValue[$key]) ) {
            $arrayCell[] = '<img src="/images/'.$curName[$key].'"><br /><b>x0</b> '.$tn.'<br /><br />';
        } else {
            $arrayCell[] = '<img src="/images/'.$curName[$key].'"><br /><b>x'.$curValue[$key].'</b> '.$tn.'<br /><br />';
        }
    }
    // Fix all bombs after explosions
    $arrayCell = implode(" ", $arrayCell);

	echo '<ul class="tabs" data-persist="true">
        <li><a href="#overview">Overview</a></li>
        <li><a href="#activity">Activity Logs</a></li>
        <li><a href="#trade">Trade Logs</a></li>
        <li><a href="#masteries">Mastered Decks</a></li>
        <li><a href="#gallery">Gallery</a></li>
        <li><a href="#donations">Donations</a></li>
    </ul>

    <div class="tabcontents" align="left">
        <div id="overview">
            <h2>Welcome back, '.$row['usr_name'].'!</h2>
            <table width="100%">
            <tr>
                <td width="20%" align="center" valign="top">';
                if ( $row['usr_mcard'] == "Yes" ) { echo '<img src="'.$tcgcards.'mc-'.$row['usr_name'].'.'.$tcgext.'" />'; }
			    else { echo '<img src="'.$tcgcards.'mc-filler.'.$tcgext.'" />'; }
                echo '<br /><br />'.$arrayCell;
                echo '</td>
                <td width="2%"></td>
                <td width="78%" valign="top">
                    <p>Welcome to your member panel, <strong>'.$row['usr_name'].'</strong>! From here you can submit various forms, edit your info, and play all of the games here at '.$tcgname.'!</p>';

                if( $row['usr_status'] == "Pending" ) {
                    echo '<p>It looks like you recently joined '.$tcgname.' and your account hasn\'t been activated yet. You must be approved by an adminstrator before you can fully access the TCG. Your account should be activated soon. If you joined more than 2 weeks ago and haven\'t received your activation email, please email us at <a href="mailto:'.$tcgemail.'">'.$tcgemail.'</a></p>';
                }

                else if( $row['usr_status'] == "Hiatus" ) {
                    echo '<p>It looks like you have set your status to Hiatus. In order to play games here you must reactivate your account. This is self-service, and to do so, go to <a href="'.$tcgurl.'account.php?do=edit-information">Edit Information</a> and set your status to Active.</p>';
                }

                // Check for daily login rewards
                date_default_timezone_set($settings->getValue('tcg_timezone'));
                $logToday = date("Y-m-d", strtotime("now"));
                $logChk = $database->get_assoc("SELECT * FROM `user_logs` WHERE `log_name`='$player' AND `log_date`='$logToday' AND `log_title`='Daily Login'");
                if( $logChk['log_date'] == $logToday ) {
                    echo '<center><div class="box-warning">Your daily login bonus for today has been logged to your permanent activity logs!<br />Below is your copy in case you missed it:<br /><br />';
                    $rewards = explode(', ',$logChk['log_rewards']);
                    $curName = explode(', ', $settings->getValue('tcg_currency'));
                    //Put currency names in an array
                    foreach($curName as $c) {
                        $currencyNames[] = substr($c, 0, -4);
                    }

                    //declare empty strings
                    $imgString = ''; 
                    $txtString = ''; 
                    $curString = ''; 
                    $curImgString = '';

                    //display images for each reward if NOT a currency
                    foreach($rewards as $r) {
                        if(!in_array($r, $currencyNames)) {
                            $imgString .= '<img src="'.$tcgcards.''.$r.'.png" title="'.$r.'"> ';
                            $txtString .= $r.', ';
                        }
                    }

                    //get count of how many of each reward is present
                    $values = array_count_values($rewards);

                    //display currencies that are in rewards and quantity only if exists in rewards
                    foreach($currencyNames as $cn) {
                        if(array_key_exists($cn, $values)) {
                            $curImgString .= '<img src="'.$tcgimg.''.$cn.'.png" title="'.$cn.'"> [x'.$values[$cn].'] ';
                            
                            // Pluralize the currencies if more than 1
                            if( $values[$cn] > 1 ) {
                                $var = substr($cn, -1);
                                if( $var == "y" ) {
                                    $vtn = substr_replace($cn,"ies",-1);
                                } else if( $var == "o" ) {
                                    $vtn = substr_replace($cn,"oes",-1);
                                }
                                else { $vtn = $cn.'s'; }
                            } else { $vtn = $cn; }

                            $curString .= '+'.$values[$cn].' '.$vtn.', ';
                        }
                    }

                    //display images and text of rewards
                    $curString = substr_replace($curString,"",-2);
                    echo $imgString.' '.$curImgString;
                    echo '<br>';
                    echo '<b>'.$logChk['log_title'].' '.$logChk['log_subtitle'].':</b> '.$txtString.' '.$curString.'</div></center>';
                } else {
                    echo '<center><div class="box-success">Here is your daily login bonus for today!<br /><br />';
                    $query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active' AND `card_worth`='1'");
                    $min = 1; $max = mysqli_num_rows($query); $rewards = null; $rW = null;
                    for($i=0; $i<$settings->getValue('prize_daily_reg'); $i++) {
                        mysqli_data_seek($query,rand($min,$max)-1);
                        $cRow = mysqli_fetch_assoc($query);
                        $digits = rand(01,$cRow['card_count']);
                        if ($digits < 10) { $_digits = "0$digits"; }
                        else { $_digits = $digits; }
                        $card = "$cRow[card_filename]$_digits";
                        $card2 = $cRow['card_filename'];
                        echo '<img src="'.$tcgurl.'images/cards/'.$card.'.'.$tcgext.'" border="0" /> ';

                        $rX = $database->get_assoc("SELECT `card_worth` FROM `tcg_cards` WHERE `card_filename`='$card2'");
                        $rW .= $rX['card_worth'].', ';
                        $rewards .= $card.", ";
                    }
                    $rewards = substr_replace($rewards,"",-2);
                    $rW = substr_replace($rW,"",-2);
                    $rArr = explode(", ", $rW);
                    $rSum = 0;
                    foreach( $rArr as $val ) { $rSum += $val; }

                    // Explode all bombs
                    $curValue = explode(' | ', $settings->getValue( 'prize_daily_cur' ));
                    $curItem = explode(' | ', $general->getItem( 'itm_currency' ));
                    $curName = explode(', ', $settings->getValue( 'tcg_currency' ));

                    $curLog = ''; $curImg = ''; $curCln = '';
                    for($i=0; $i<count($curValue); $i++) {
                        $cn = substr_replace($curName[$i],"",-4);

                        // Pluralize the currencies if more than 1
                        if( $curValue[$i] > 1 ) {
                            $var = substr($cn, -1);
                            if( $var == "y" ) {
                                $vtn = substr_replace($cn,"ies",-1);
                            } else if( $var == "o" ) {
                                $vtn = substr_replace($cn,"oes",-1);
                            }
                            else { $vtn = $cn.'s'; }
                        } else { $vtn = $cn; }

                        if( $curValue[$i] != 0 ) {
                            $curLog .= str_repeat(substr_replace($curName[$i],"",-4).', ', $curValue[$i]);
                            $curImg .= '<img src="/images/'.$curName[$i].'"> [x'.$curValue[$i].']';
                            $curCln .= '+'.$curValue[$i].' '.$vtn.', ';
                            $curItem[$i] += $curValue[$i];
                        } else {}
                    }
                    $total = implode(" | ", $curItem);
                    $curCln = substr_replace($curCln,"",-2);
                    $curLog = substr_replace($curLog,"",-2);

                    echo $curImg;
                    echo '<br /><b>Daily Login ('.$logToday.'):</b> '.$rewards.', '.$curCln.'</div></center>';

                    $newSet = $rewards.", ".$curLog;
                    $database->query("INSERT INTO `user_logs` (`log_name`,`log_type`,`log_title`,`log_subtitle`,`log_rewards`,`log_date`) VALUES ('$player','Service','Daily Login','($logToday)','$newSet','$logToday')");
                    $database->query("UPDATE `user_items` SET `itm_currency`='$total', `itm_cards`=itm_cards+'$rSum' WHERE `itm_name`='$player'");
                }

                    echo '<br />

                    <h3>Player Status</h3>
                    <table width="100%" cellspacing="5" border="0" class="table table-sliced table-striped">
                    <tbody>
                        <tr><td width="35%" align="right"><b>Status:</b></td><td width="2%"></td><td width="63%">'.$row['usr_status'].'</td></tr>
                        <tr><td align="right"><b>Rank:</b></td><td></td><td>Level '.$row['usr_level'].' ('.$lvlName['lvl_name'].')</td></tr>
                        <tr><td align="right"><b>Collecting:</b></td><td></td><td><a href="'.$tcgurl.'cards.php?view=released&deck='.$row['usr_deck'].'">'.$row['usr_deck'].'</a></td></tr>
                        <tr><td align="right"><b>Card Worth:</b></td><td></td><td>'.$general->getItem('itm_cards').'</td></tr>
                        <tr><td align="right"><b>Unique Masteries:</b></td><td></td><td>';
                        if( $general->getItem('itm_masteries') == 'None' ) { echo '0'; }
                        else {
                            $exp = explode(', ', $general->getItem('itm_masteries'));
                            echo count(array_unique($exp));
                        }
                        echo '</td></tr>
                        <tr><td align="right"><b>Total Masteries:</b></td><td></td><td>';
                        if( $general->getItem('itm_masteries') == 'None' ) { echo '0'; }
                        else {
                            $arr = explode(', ', $general->getItem('itm_masteries'));
                            echo count($arr);
                        }
                        echo '</td></tr>
                    </tbody>
                    </table><br />

                    <h3>Trading Status</h3>
                    <table width="100%" cellspacing="5" border="0" class="table table-sliced table-striped">
                    <tbody>
                        <tr><td width="35%" align="right"><b>Current Points:</b></td><td width="2%"></td><td width="63%">'.$trd['trd_points'].'</td></tr>
                        <tr><td align="right"><b>Redeemed Points:</b></td><td></td><td>'.$trd['trd_redeems'].'</td></tr>
                        <tr><td align="right"><b>Total Turnins:</b></td><td></td><td>'.$trd['trd_turnins'].'</td></tr>
                    </tbody>
                    </table><br />

                    <h3>Other Information</h3>
                    <table width="100%" cellspacing="5" border="0" class="table table-sliced table-striped">
                    <tbody>
                        <tr><td width="35%" align="right"><b>Birthday:</b></td><td width="2%"></td><td width="63%">'.date("F d", strtotime($row['usr_bday'])).'</td></tr>
                        <tr><td align="right"><b>Registered:</b></td><td></td><td>'.date("F d, Y", strtotime($row['usr_reg'])).'</td></tr>
                        <tr><td align="right"><b>Last Login:</b></td><td></td><td>'.date("F d, Y", strtotime($row['usr_sess'])).' at '.date("h:i A", strtotime($row['usr_sess'])).'</td></tr>
                    </tbody>
                    </table>

                    <h2>About Me</h2>';
                    if( $row['usr_level'] < 10 ) { $level = '0'.$row['usr_level']; } else { $level = $row['usr_level']; }
                    echo '<p><img src="'.$tcgimg.'badges/'.$general->getItem('itm_badge').'-'.$level.'.'.$tcgext.'" title="Level '.$row['usr_level'].'" align="left" style="margin-right: 10px;" />'.$about.'</p>
                </td>
            </tr>
            </table>
        </div><!-- #overview -->

        <div id="activity">';
            $activity = isset($_GET['ld']) ? $_GET['ld'] : null;
            $logs = $database->query("SELECT * FROM `user_logs` WHERE `log_name`='$player' GROUP BY YEAR(log_date), MONTH(log_date) ORDER BY `log_date` DESC");
            if( empty($activity) ) {
                echo '<h2>Activity Logs</h2>
                <p>Welcome to your permanent logs page! This log contains all of your activity during your sessions at '.$tcgname.'. <b>This may not be 100% accurate!</b> If you log out, all of these logs will still be here, but please do not rely solely on this tool to log your cards.</p>

                <div align="center" class="logLink">';
                while( $row = mysqli_fetch_assoc($logs) ) {
                    echo '<a href="'.$tcgurl.'account.php?ld='.date("Y-m", strtotime($row['log_date'])).'">'.date("F Y", strtotime($row['log_date'])).'</a>';
                } // end activity while
                echo '</div>';
            } else {
                $show1 = $database->query("SELECT * FROM `user_logs` WHERE `log_name`='$player' AND `log_date` LIKE '$activity-%' ORDER BY `log_date` DESC");
                echo '<h2>'.date("F Y", strtotime($activity)).'</h2>';
                //Put currency names in an array
                foreach( $settings->getValue('tcg_currency') as $c ) {
                    $currencyNames[] = substr($c, 0, -4);
                }

                $timestamp = '';
                while( $row = mysqli_fetch_assoc($show1) ) {
                    $rewards = explode(', ',$row['log_rewards']);
                    
                    // Declare empty strings
                    $txtString = ''; 
                    $curString = ''; 

                    // Display cards for each reward if NOT a currency
                    foreach( $rewards as $r ) {
                        if( !in_array($r, $currencyNames) ) {
                            $txtString .= $r.', ';
                        }
                    }

                    // Get count of how many of each reward is present
                    $values = array_count_values($rewards);

                    // Display currencies that are in rewards and quantity only if exists in rewards
                    foreach( $currencyNames as $cn ) {
                        if( array_key_exists($cn, $values) ) {
                            // Pluralize the currencies if more than 1
                            if( $values[$cn] > 1 ) {
                                $var = substr($cn, -1);
                                if( $var == "y" ) {
                                    $vtn = substr_replace($cn,"ies",-1);
                                } else if( $var == "o" ) {
                                    $vtn = substr_replace($cn,"oes",-1);
                                }
                                else { $vtn = $cn.'s'; }
                            } else { $vtn = $cn; }
                            $curString .= ', +'.$values[$cn].' '.$vtn;
                        }
                    }

                    // Display text of rewarded cards
                    $txtString = substr_replace($txtString,"",-2);

                    if ( $row['log_date'] != $timestamp ) {
                        echo '<br /><b>'.date('F d, Y', strtotime($row['log_date'])).' -----</b><br/>';
                        $timestamp = $row['log_date'];
                    }
                    echo '<li class="spacer">- <i><b>'.$row['log_title'];
                    if ( empty($row['log_subtitle']) ) {}
                    else { echo ' '.$row['log_subtitle']; }
                    echo ':</b></i> '.$txtString.''.$curString.'</li>';
                } // end while
            }
        echo '</div><!-- #activity -->

        <div id="trade">';
            $trade = isset($_GET['td']) ? $_GET['td'] : null;
            $trades = $database->query("SELECT * FROM `user_trades` WHERE `trd_name`='$player' GROUP BY YEAR(trd_date), MONTH(trd_date) ORDER BY `trd_date` DESC");
            if( empty($trade) ) {
                echo '<h2>Trade Logs</h2>
                <p>These trade logs shows your permanent trading activities with your fellow players, these also counts your current turned in trade points. So if you have a trade log from your trade post that hasn\'t been turned in yet, kindly do so to have it recorded here.</p>

                <div align="center" class="logLink">';
                while( $row = mysqli_fetch_assoc($trades) ) {
                    echo '<a href="'.$tcgurl.'account.php?td='.date("Y-m", strtotime($row['trd_date'])).'">'.date("F Y", strtotime($row['trd_date'])).'</a>';
                } // end trade while
                echo '</div>';
            } else {
                $show2 = $database->query("SELECT * FROM `user_trades` WHERE `trd_name`='$player' AND `trd_date` LIKE '$trade-%' ORDER BY `trd_date` DESC");
                echo '<h2>'.date("F Y", strtotime($trade)).'</h2>';
                $timestamp = '';
                while( $row2 = mysqli_fetch_assoc($show2) ) {
                    if ( $row2['trd_date'] != $timestamp ) {
                        echo '<br /><b>'.date('F d, Y', strtotime($row2['trd_date'])).' -----</b><br/>';
                        $timestamp = $row2['trd_date'];
                    }
                    echo '<li class="spacer">- <i><b>Traded '.$row2['trd_trader'].':</b></i> my '.$row2['trd_out'].' for '.$row2['trd_inc'].'</li>';
                } // end while
            }
        echo '</div><!-- #trade -->

        <div id="masteries">
            <h2>Mastered Decks</h2>
            <center>';
            if ( $general->getItem('itm_masteries') == "None" ) {
				echo '<i>You haven\'t mastered any decks yet.</i>';
			} else {
				echo '<img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $general->getItem('itm_masteries')).'.png">';
			}
            echo '</center>
        </div><!-- #masteries -->

        <div id="gallery">
            <h2>Member Cards</h2>
            <center>';
            if ( $general->getItem('itm_mcard') == "None" ) {
				echo '<i>You haven\'t traded any member cards yet.</i>';
			} else {
				echo '<img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $general->getItem('itm_mcard')).'.png">';
			}
            echo '</center>

            <h2>Event Cards</h2>
            <center>';
            if ( $general->getItem('itm_ecard') == "None" ) {
				echo '<i>You haven\'t pulled any event cards yet.</i>';
			} else {
				echo '<img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $general->getItem('itm_ecard')).'.png">';
			}
            echo '</center>

            <h2>Milestone Cards</h2>
            <center>';
            if ( $general->getItem('itm_milestone') == "None" ) {
				echo '<i>You haven\'t gained any milestone cards yet.</i>';
			} else {
				echo '<img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $general->getItem('itm_milestone')).'.png">';
			}
            echo '</center>
        </div><!-- #gallery -->

        <div id="donations">';
            if( $act == "edit" ) {
                if( isset($_POST['edit']) ) {
                    $id = $_POST['id'];
                    $file = $sanitize->for_db($_POST['filename']);
                    $feat = $sanitize->for_db($_POST['feature']);
                    $link = $sanitize->for_db($_POST['link']);
                    $cat = $sanitize->for_db($_POST['category']);
                    $set = $sanitize->for_db($_POST['set']);
                    $update = $database->query("UPDATE `tcg_donations` SET `deck_filename`='$file', `deck_feature`='$feat', `deck_cat`='$cat', `deck_set`='$set', `deck_url`='$link' WHERE `deck_id`='$id'");
                    if( !$update ) { $error[] = "There was an error while editing the deck you claimed. ".mysqli_error().""; }
                    else { $success[] = "The deck you have claimed has been successfully updated!"; }
                }

                $id = $_GET['id'];
                $sql = $database->get_assoc("SELECT * FROM `tcg_donations` WHERE `deck_id`='$id'");
                echo '<h2>Edit Claims</h2>
                <p>Use the form below if you need to edit your current deck claims such as correcting misspelled filename and such.</p>
                <center>';
                if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
                if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
                echo '</center>
                <form method="post" action="'.$tcgurl.'account.php?action=edit&id='.$id.'">
                <input type="hidden" name="id" value="'.$id.'" />
                <table width="100%" cellspacing="3" class="table table-sliced table-striped">
                <tbody>
                <tr>
                    <td width="15%"><b>Category:</b></td>
                    <td width="35%">
                        <select name="category" style="width:97%;">';
                        $dc = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='".$sql['deck_cat']."'");
                            echo '<option value="'.$sql['deck_cat'].'">Current: '.$dc['cat_name'].'</option>';
                        $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                        for($i=1; $i<=$c; $i++) {
                            $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
                            echo '<option value="'.$cat['cat_id'].'">'.$cat['cat_name'].'</option>';
                        }
                        echo '</select>
                    </td>
                    <td width="15%"><b>File Name:</b></td>
                    <td width="35%"><input type="text" name="filename" style="width:90%;" value="'.$sql['deck_filename'].'"></td>
                </tr>
                <tr>
                    <td><b>Feature:</b></td>
                    <td><input type="text" name="feature" value="'.$sql['deck_feature'].'" style="width:90%;"></td>
                    <td><b>Set/Series:</b></td>
                    <td>
                        <select name="set" style="width:97%;">
                            <option value="'.$sql['deck_set'].'">Current: '.$sql['deck_set'].'</option>';
                        $c = $database->num_rows("SELECT * FROM `tcg_cards_set`");
                        for($i=1; $i<=$c; $i++) {
                            $set = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_id`='$i'");
                            echo '<option value="'.$set['set_name'].'">'.$set['set_name'].'</option>';
                        }
                        echo '</select>
                    </td>
                </tr>';
                if( $sql['deck_type'] == "Donations" ) {
                    echo '<tr><td><b>Download link:</b></td><td colspan="3"><input type="text" name="link" value="'.$sql['deck_url'].'" style="width:96%;"></td></tr>';
                } else { echo '<input type="hidden" name="link" value="">'; }
                echo '</tbody>
                </table>
                <input type="submit" name="edit" class="btn-success" value="Edit Deck Claim" /> 
                <input type="reset" name="reset" class="btn-danger" value="Reset" />
                </form>';
            }

            else if( $act == "donate" ) {
                if( isset($_POST['donate']) ) {
                    $id = $_POST['id'];
                    $link = $sanitize->for_db($_POST['link']);
                    $deck = $sanitize->for_db($_POST['deck']);
                    $date = date("Y-m-d H:i:s", strtotime("now"));
                    $update = $database->query("UPDATE `tcg_donations` SET `deck_url`='$link', `deck_type`='Donations' WHERE `deck_id`='$id'");
                    if( !$update ) { $error[] = "There was an error while submitting your donation. ".mysqli_error().""; }
                    else {
                        $database->query("INSERT INTO `user_rewards` (`rwd_name`,`rwd_type`,`rwd_subtitle`,`rwd_cards`,`rwd_currency`,`rwd_date`) VALUES ('$player','Donations','($deck)','".$settings->getValue('prize_deck_reg')."','".$settings->getValue('prize_deck_cur')."','$date')");
                        $success[] = "The donation link for the deck you have claimed has been successfully added!";
                    }
                }

                $id = $_GET['id'];
                $sql = $database->get_assoc("SELECT * FROM `tcg_donations` WHERE `deck_id`='$id'");

                // Explode bombs
                $curValue = explode(' | ', $settings->getValue( 'prize_deck_cur' ));
                $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
                foreach( $curValue as $key => $value ) {
                    $tn = substr_replace($curName[$key],"",-4);
                    if( $curValue[$key] > 1 ) {
                        $var = substr($tn, -1);
                        if( $var == "y" ) {
                            $tn = substr_replace($tn,"ies",-1);
                        } else if( $var == "o" ) {
                            $tn = substr_replace($tn,"oes",-1);
                        }
                        else { $tn = $tn.'s'; }
                    } else { $tn = $tn; }
                    if( $curValue[$key] == 0 ) {}
                    else {
                        $arrayCur[] = '<li class="spacer">- <b>'.$curValue[$key].'</b> '.$tn.'</li>';
                    }
                }
                // Fix all bombs after explosions
                $arrayCur = implode(" ", $arrayCur);

                echo '<h2>Add Donation Link</h2>
                <p>Use the form below to submit your image donations for the <b>'.$sql['deck_filename'].'</b> deck. Please keep in mind the exclusive guidelines before donating any deck.</p>
                <center>';
                if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
                if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
                echo '</center>
                <ul>
                    <li>Donated images must be in high quality and unedited, preferrably 600x600 pixels up to 1600x1600 pixels.</li>
                    <li>Horizontal images are much preferred than vertical ones to avoid the subjects getting cropped just to fit the card template.</li>
                    <li>Only images that is related to nature that will fit to our sets are allowed.</li>
                    <li>Donations need at least 25 images, but more is encouraged.</li>
                    <li>You can donate up to <b>'.$settings->getValue('xtra_deck_cards').' decks</b> per month.</li>
                </ul>
                <p><b>You will receive the following rewards:</b></p>
                <li class="spacer">- <b>'.$settings->getValue('prize_deck_reg').'</b> random cards</li>';
                echo $arrayCur;
                echo '<br /><form method="post" action="'.$tcgurl.'account.php?action=donate&id='.$id.'">
                <input type="hidden" name="id" value="'.$id.'" />
                <input type="hidden" name="deck" value="'.$sql['deck_filename'].'" />
                <table width="100%" cellspacing="3" class="table table-sliced table-striped">
                <tbody>
                <tr>
                    <td width="20%"><b>Donating For:</b></td>
                    <td width="80%">'.$sql['deck_set'].' - '.$sql['deck_feature'].'</td>
                </tr>
                <tr>
                    <td><b>Donation Link:</b></td>
                    <td><input type="text" name="link" placeholder="e.g. https://site.com/'.$sql['deck_filename'].'.zip" style="width:90%;"></td>
                </tr>
                </tbody>
                </table>
                <input type="submit" name="donate" class="btn-success" value="Send Donations" /> 
                <input type="reset" name="reset" class="btn-danger" value="Reset" />
                </form>';
            }

            else {
                echo '<h2>Donations</h2>
                <p>Here is the list of your claimed decks that hasn\'t been donated yet and donated decks that hasn\'t been made yet. If you think that you are missing a deck here compared to the claims and donations list from the <a href="'.$tcgurl.'cards.php">cards</a> page, kindly please let '.$tcgowner.' know.</p>
                <table width="100%" class="table table-sliced table-striped">
                <thead>
                <tr>
                    <td width="50%"><b>Deck</b></td>
                    <td width="15%" align="center"><b>Type</b></td>
                    <td width="15%" align="center"><b>Maker</b></td>
                    <td width="20%" align="center"><b>Action</b></td>
                </tr>
                </thead>
                <tbody>';
                $decks = $database->query("SELECT * FROM `tcg_donations` WHERE `deck_donator`='$player' ORDER BY `deck_date`");
                while( $deck = mysqli_fetch_assoc($decks) ) {
                    echo '<tr>
                    <td>'.$deck['deck_feature'].' ('.$deck['deck_filename'].')</td>
                    <td align="center">'.$deck['deck_type'].'</td>
                    <td align="center">'.$deck['deck_maker'].'</td>
                    <td align="center">
                        <button onclick="window.location.href=\''.$tcgurl.'account.php?action=edit&id='.$deck['deck_id'].'\';" class="btn-success">Edit</button> ';
                        if( $deck['deck_type'] == "Claims" ) { echo '<button onclick="window.location.href=\''.$tcgurl.'account.php?action=donate&id='.$deck['deck_id'].'\';" class="btn-primary">Donate</button>'; } else {}
                    echo '</td>
                    </tr>';
                }
                echo '</tbody>
                </table>';
            }
        echo '</div><!-- #donations -->
    </div><!-- .tabcontents -->';
} // end show profile

include('theme/headers/acct-footer.php');
include($footer);
?>