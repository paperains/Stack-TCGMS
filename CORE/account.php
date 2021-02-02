<?php
include("admin/class.lib.php");
include($header);

##############################
########## DO LOGIN ##########
##############################
if ($do == "login") {
    if ($act == "loggedin") {
        $userName = $_POST["username"];
        $password = $_POST["password"];
        $errMsg="";
        if($userName != "" && $password != "") {
            $encryptPassword = md5($password);
            $authRow = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$userName' AND `password`='$encryptPassword'");

            $userID = $authRow['id'];
            $userNAME = $authRow['name'];
            $userStatus = $authRow['status'];
            
            if($userID!=0) {
                $_SESSION['USER_ID'] = $userID;
                $_SESSION['USR_LOGIN'] = $userName;
                $_SESSION['USR_NAME'] = $userNAME;
                $_SESSION['USR_STATUS'] = $userStatus;
                
                $log = date('Y-m-d H:i:s', strtotime("now"));
                $log2 = date('Y-m-d', strtotime("now"));
                
                $database->query("UPDATE `user_list` SET `session`='$log' WHERE id='$userID'");
                $database->query("UPDATE `trades` SET `updated`='$log2' WHERE name='$userNAME'");
                
                /** ADD DAILY REWARDS HERE **/
                $daily = $database->get_assoc("SELECT * FROM `user_list` WHERE `name`='$userNAME'");
                $timestamp = strtotime($daily['daily']);
                $now = time();
                
                if ( ($now - $timestamp) > 86400 ) {
                    $database->query("INSERT INTO `user_rewards` (`name`,`type`,`subtitle`,`mcard`,`cards`,`cake`,`timestamp`) VALUES ('$userNAME','Rewards','(Daily Login)','No','2','2','$log2')");
                    $database->query("UPDATE `user_list` SET `daily`='$log' WHERE `name`='$userNAME'");
                }
		
		/** SET STATUS TO ACTIVE WHEN LOGGED IN FOR INACTIVE MEMBERS **/
		if ($daily['status'] == 'Inactive') {
		    $database->query("UPDATE `user_list` SET `status`='Active' WHERE `name`='$userNAME'");
		}
                
                header("Location: account.php");
            } else { header("Location: account.php?do=login&msg=invalid"); }
        } else { header ("Location: account.php?do=login&msg=missing"); }
    } else {
        if($msg=="invalid") {
            echo '<h1>Login : Error</h1>
            <p>Oops, it looks like the email and/or password you entered is not in our database. Check your spelling and try again or contact us at '.$tcgemail.'.</p>';
        } elseif($msg=="missing") {
            echo '<h1>Login : Error</h1>
            <p>Oops, it looks like one or more values from the form were not entered. Please go back and try again.</p>';
        } else {
            echo '<h1>Login</h1>
            <p>Below is the login form for the member panel here at '.$tcgname.'. <b>This is only for current members</b>. If you would like to join, please click <a href="/members.php?page=join">here</a> to see the rules and join.</p><center>
            <form method="post" action="/account.php?do=login&action=loggedin">
            <table width="100%" class="border" cellspacing="3" border="0">
            <tr><td class="headLine" width="15%">Email:</td><td class="tableBody" width="35%"><input type="text" name="username" placeholder="username@domain.tld" style="width:90%;" /></td></tr>
            <tr><td class="headLine" width="15%">Password:</td><td class="tableBody" width="35%"><input type="password" name="password" placeholder="********" style="width:90%;" /></td></tr>
            <tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Login"> <a  onclick="window.location.href=\'account.php?do=lostpass\'" class="button">Lost Password?</a></td></tr>
            </table>
            </form></center>';
        }
    }
}

######################################
########## DO LOST PASSWORD ##########
######################################
else if ($do == "lostpass") {
    if( isset($_POST['submit']) ) {
        $check->Value();
        if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,6})$/",strtolower($_POST['email']))) { exit("<h1>Error</h1>\nThat e-mail address is not valid, please use another.<br /><br />"); }
        $password = substr(md5(date("c")), 0, 8);
        $email = $sanitize->for_db($_POST['email']);
        $scrampass = md5($password);

        $num = $database->num_rows("SELECT * FROM `user_list` WHERE email='$email'");
        $sql = $database->get_assoc("SELECT * FROM `user_list` WHERE email='$email'");
        if($num===0) { exit("<h1>Error</h1>\nThat email address does not exist in our database. Please go back and check your spelling and try again."); }
        else {
            $id = $sql['id'];
            $recipient = "$email";
            $subject = "$tcgname: Reset Your Password";
            $message = "Your password has been reset to $password. Please log in and change it.\n";
            $headers = "From: $tcgname <$tcgemail> \n";
            $headers .= "Reply-To: $tcgname <$tcgemail>";
            if (mail($recipient,$subject,$message,$headers)) {
                $database->query("UPDATE `user_list` SET password='$scrampass' WHERE id='$id'");
                $success[] = "Your password has been reset and sent to the email you have provided.<br />Please log in and change your password once you have checked your email.";
            } else {
                $error[] = "Sorry, there was an error and your password was not updated. ".mysqli_error()."";
            }
        }
    }

    echo '<h1>Lost Password</h1>
    <p>If you have forgotten your password, please feel free to use the reset password form below.</p>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
    echo '<form method="post" action="/account.php?do=lostpass">
    <center><table width="70%" class="border" cellspacing="3">
    <tr><td class="headLine">Email:</td><td class="tableBody"><input type="text" name="email" value="" style="width:62%;" /> <input type="submit" name="submit" class="btn-success" value="Reset Password" /></td></tr>
    </table></center>
    </form>';
}

else if (empty($login)) {
    header("Location: account.php?do=login");
}

###############################
########## DO LOGOUT ##########
###############################
else if ($do == "logout") {
    $_SESSION = array();
    session_destroy();
    // Redirect to following page after logout
    header("Location: account.php?do=login"); //Change the page according to requirement
}

#######################################
########## DO RESET PASSWORD ##########
#######################################
else if ($do == "reset-password") {
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
        $update = $database->query("UPDATE `user_list` SET password='$scrampass' WHERE id='$id'");
        if (mail($recipient,$subject,$message,$headers)) {
            if($update == TRUE) {
                session_destroy();
                header("Location: account.php?do=login");
            }
            $success[] = "Your password has been changed and has been sent to your email.";
        } else {
            $error[] = "Sorry, there was an error and your password was not updated. ".mysqli_error()."";
        }
    }
    
    $select = $database->query("SELECT * FROM `user_list` WHERE email='$login'");
    while($row=mysqli_fetch_assoc($select)) {
        echo '<h1>Change Your Password</h1>
        <p>Use this form to change your password. <b>You will be logged out after this change</b>. Make sure you have any card activity logged before this.</p>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        echo '<form method="post" action="/account.php?do=reset-password">
        <input type="hidden" name="id" value="'.$row['id'].'" />
        <input type="hidden" name="email" value="'.$row['email'].'" />
        <table width="100%" class="border" cellspacing="3">
        <tr><td width="25%" class="headLine">Current Password:</td><td width="75%" class="tableBody"><input type="password" name="current" value="" style="width:95%;" /></td></tr>
        <tr><td class="headLine">New Password:</td><td class="tableBody"><input type="password" name="password" placeholder="********" style="width:43%;" /> <input type="password" name="password2" placeholder="Retype password for verification" style="width:43%;" /></td></tr>
        <tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Change" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
        </table>
        </form>
        <p align="right"><a href="/account.php">Go back?</a></p>';
    }
}

#################################
########## DO QUIT TCG ##########
#################################
else if ($do == "quit") {
    if ( isset($_POST['submit']) ) {
	$from = $sanitize->for_db($_POST['sender']);
        $to = $sanitize->for_db($_POST['recipient']);
        $message = $_POST['message'];
        $date = date("Y-m-d H:i:s", strtotime("now"));

        $message = nl2br($message);

        if ( !get_magic_quotes_gpc() ) { $message = addslashes($message); }

        $insert = $database->query("INSERT INTO `user_mbox` (`subject`,`message`,`sender`,`recipient`,`mbox_from`,`mbox_to`,`read_from`,`read_to`,`del_from`,`del_to`,`origin`,`timestamp`) VALUES ('Quitting','$message','$from','$to','Out','In','0','1','0','0','','$date')");

        if($insert == TRUE) {
            $database->query("UPDATE `user_mbox` SET origin=LAST_INSERT_ID() WHERE id=LAST_INSERT_ID()");
            $database->query("UPDATE `user_list` SET `password`='', `url`='http://www.domain.tld', `birthday`='0000-00-00', `status`='Retired' WHERE `id`='".$row['id']."' AND `name`='".$row['name']."'");
            $success[] = "Sorry to see you leave. Hopefully you change your mind and join us in the future!";
        } else {
            $error[] = "Sorry, there was an error in processing your form.<br />Send the information to '.$tcgemail.' and we will send you a reply ASAP. ".mysqli_error()."";
        }
    }

    echo '<h1>Quit '.$tcgname.'</h1>
    <p>Are you sure about this? ∑(ﾟﾛﾟ〃) If you are&mdash;and we\'re sorry to see you leave, you can use the form below to inform us.</p>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
    echo '<form method="post" action="/account.php?do=quit">
    <input type="hidden" name="sender" value="'.$row['name'].'" />
    <input type="hidden" name="recipient" value="'.$tcgowner.'" />
    <center><table width="100%" cellspacing="3" class="border">
    <tr>
        <td width="30%" class="headLine">Reason:</td>
        <td width="70%" class="tableBody"><textarea name="message" rows="5" style="width:95%;">Type your message here.</textarea></td>
    </tr>
    <tr><td colspan="2" class="tableBody" align="center"><input type="submit" name="submit" class="btn-success" value="Send" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
    </table></center>
    </form>';
}

#######################################
########## DO PERMANENT LOGS ##########
#######################################
else if ($do == "logs") {
    $date1 = (isset($_GET['td']) ? $_GET['td'] : null);
    $date2 = (isset($_GET['ld']) ? $_GET['ld'] : null);
    $user = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
    $tl1 = $database->query("SELECT * FROM `trades_$player` GROUP BY YEAR(timestamp), MONTH(timestamp) ORDER BY `timestamp` DESC");
    $ll1 = $database->query("SELECT * FROM `logs_$player` GROUP BY YEAR(timestamp), MONTH(timestamp) ORDER BY `timestamp` DESC");
    $dlogs = $database->get_assoc("SELECT * FROM `trades` WHERE `name`='$player'");
    if(empty($date1) && empty($date2)) {
        echo '<h1>Permanent Logs</h1>
        <p>Welcome to your permanent logs page! This log contains all of your activity during your sessions at '.$tcgname.'. <b>This may not be 100% accurate!</b> If you log out, all of these logs will still be here, but please do not rely solely on this tool to log your cards.</p>
        <center><i>Your activity and trade logs was last updated on '.date("F d, Y", strtotime($dlogs['updated'])).'.</i></center>';
        echo '<h2>Activity Logs</h2>';
        echo '<div align="center" class="logLink">';
        while($row=mysqli_fetch_assoc($ll1)) {
            echo '<a href="/account.php?do=logs&ld='.date("Y-m", strtotime($row['timestamp'])).'">'.date("F Y", strtotime($row['timestamp'])).'</a>';
        }
        echo '</div><br />';

        echo '<h2>Trade Logs</h2>';
        echo '<div align="center" class="logLink">';
        while($row=mysqli_fetch_assoc($tl1)) {
            echo '<a href="/account.php?do=logs&td='.date("Y-m", strtotime($row['timestamp'])).'">'.date("F Y", strtotime($row['timestamp'])).'</a>';
        }
        echo '</div>';
    } else if (!empty($date1)) {
        $show = $database->query("SELECT * FROM `trades_$player` WHERE `timestamp` LIKE '$date1-%' ORDER BY `timestamp` DESC");
        echo '<h1>Trade Logs: '.date("F Y", strtotime($date1)).'</h1>';
        $timestamp = '';
        while($row=mysqli_fetch_assoc($show)){
            if ($row['timestamp'] != $timestamp){
                echo '<br /><b>'.date('F d, Y', strtotime($row['timestamp'])).' -----</b><br/>';
                $timestamp = $row['timestamp'];
            }
            echo '- <i>Traded '.$row['trader'];
            echo ':</i> my '.$row['outgoing'].' for '.$row['incoming'].'<br />';
        }
    } else if (!empty($date2)) {
        $show = $database->query("SELECT * FROM `logs_$player` WHERE `timestamp` LIKE '$date2-%' ORDER BY `timestamp` DESC");
        echo '<h1>Activity Logs: '.date("F Y", strtotime($date2)).'</h1>';
        $timestamp = '';
        while($row=mysqli_fetch_assoc($show)){
            if ($row['timestamp'] != $timestamp){
                echo '<br /><b>'.date('F d, Y', strtotime($row['timestamp'])).' -----</b><br/>';
                $timestamp = $row['timestamp'];
            }
            echo '- <i>'.$row['title'];
                if (empty($row['subtitle'])) {}
                else { echo ' '.$row['subtitle']; }
            echo ':</i> '.$row['rewards'].'<br />';
        }
    }
}

#############################################
########## Edit Information Action ##########
#############################################
else if ($do == "edit-information") {
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
        $birthday = date("Y-m-d", strtotime($_POST['year']."-". $_POST['month']."-".$_POST['day']));
        $about = $_POST['about'];
        $about = nl2br($about);
        
        if (!get_magic_quotes_gpc()) { $about = addslashes($about); }
        
        $update = $database->query("UPDATE `user_list` SET email='$email', url='$url', birthday='$birthday', status='$status', collecting='$collecting', about='$about', twitter='$twitter', discord='$discord' WHERE id='$id'");
        if ( !$update ) { $error[] = "Sorry, there was an error and your info was not updated. ".mysqli_error().""; }
	else { $success[] = "Your information has been updated!"; }
    }
    
    $sql = $database->query("SELECT * FROM `user_list` WHERE email='$login'");
    while($row=mysqli_fetch_assoc($sql)) {
        $old_bio = stripslashes($row['about']);
        echo '<h1>Edit Your Information</h1>
        <p>Use this form to edit your information in the database. <b>You cannot use this form to change your password.</b> To change it, please click <a href="/account.php?do=reset-password">here</a>.</p><center>';
        if ( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if ( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }
        echo '<form method="post" action="/account.php?do=edit-information">
        <input type="hidden" name="id" value="'.$row['id'].'" />
        <table width="100%" cellpadding="0" cellspacing="3" border="0" class="border">
        <tr>
            <td class="headLine" width="15%">Email:</td><td class="tableBody" width="35%"><input type="text" name="email" value="'.$row['email'].'" style="width: 90%;" /></td>
            <td class="headLine" width="15%">URL:</td><td class="tableBody" width="35%"><input type="text" name="url" value="'.$row['url'].'" style="width: 90%;" /></td>
        </tr>
        <tr>
            <td class="headLine">Twitter:</td><td class="tableBody"><input type="text" name="twitter" value="'.$row['twitter'].'" style="width: 90%;" /></td>
            <td class="headLine">Discord:</td><td class="tableBody"><input type="text" name="discord" value="'.$row['discord'].'" style="width: 90%;" /></td>
        </tr>
	<tr>
            <td class="headLine">Accepts Random:</td><td class="tableBody">';
            if ($row['random_trade'] == "0") { echo '<input type="radio" name="random" value="1" /> Yes &nbsp;&nbsp; <input type="radio" name="random" value="0" checked /> No'; }
            else { echo '<input type="radio" name="random" value="1" checked /> Yes &nbsp;&nbsp; <input type="radio" name="random" value="0" /> No'; }
            echo '</td>
            <td class="headLine">Allows Trade:</td><td class="tableBody">';
            if ($row['accept_trade'] == "0") { echo '<input type="radio" name="accept" value="1" /> Yes &nbsp;&nbsp; <input type="radio" name="accept" value="0" checked /> No'; }
            else { echo '<input type="radio" name="accept" value="1" checked /> Yes &nbsp;&nbsp; <input type="radio" name="accept" value="0" /> No'; }
            echo '</td>
        </tr>
        <tr><td class="headLine">Birthday:</td><td class="tableBody" colspan="3"><select name="month">
        <option value="'.date("m", strtotime($row['birthday'])).'">Current: '.date("F", strtotime($row['birthday'])).'</option>';
        for($m=1; $m<=12; $m++) {
            if ($m < 10) { $_mon = "0$m"; }
            else { $_mon = $m; }
            echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
        }
        echo '</select>&nbsp;<select name="day">
        <option value="'.date("d", strtotime($row['birthday'])).'">Current: '.date("d", strtotime($row['birthday'])).'</option>';
        for($i=1; $i<=31; $i++) {
            if ($i < 10) { $_days = "0$i"; }
            else { $_days = $i; }
            echo '<option value="'.$_days.'">'.$_days.'</option>';
        }
        echo '</select>&nbsp;';
            $date=date('Y');
            $start=$date-10;
            $end=$start-40;
            $yearArray = range($start,$end);
            echo '<select name="year">
            <option selected value="'.date("Y", strtotime($row['birthday'])).'">Current: '.date("Y", strtotime($row['birthday'])).'</option>';
            foreach ($yearArray as $year) {
                $selected = ($year == $start) ? 'selected' : '';
                echo '<option value="'.$year.'">'.$year.'</option>';
            }
        echo '</select></td></tr>
        <tr>
            <td class="headLine">Collecting:</td><td class="tableBody"><select name="collecting" style="width: 95%;">
            <option value="'.$row['collecting'].'">Current: '.$row['collecting'].'</option>';
            $sql_collect = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' ORDER BY `filename` ASC");
            while($row_collect=mysqli_fetch_assoc($sql_collect)) {
                echo '<option value="'.$row_collect['filename'].'">'.$row_collect['deckname'].' ('.$row_collect['filename'].')</option>';
            }
            echo '</select></td>
            <td class="headLine">Status:</td><td class="tableBody"><select name="status" style="width: 95%;">
            <option value="'.$row['status'].'">Current: '.$row['status'].'</option>
            <option value="Hiatus">Hiatus</option>
	    <option value="Active">Active</option>
            </select></td>
        </tr>
        <tr><td class="headLine" valign="top">Biography:</td><td class="tableBody" colspan="3"><textarea name="about" rows="5" style="width: 95%;">'.$old_bio.'</textarea></td></tr>
        <tr><td class="tableBody" colspan="4" align="center"><input type="submit" name="update" class="btn-success" value="Edit Information" /></td></tr>
        </table>
        </form></center>
        <p align="right"><a href="/account.php">Go back?</a></p>';
    }
}

#########################################
########## Edit Gallery Action ##########
#########################################
else if ($do == "edit-items") {
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
            
        $update = $database->query("UPDATE `user_items` SET `mcard`='$mcard', `ecard`='$ecard', `level_badge`='$lvlb' WHERE `name`='$name'");
        if ( !$update ) { $error[] = "Sorry, there was an error and your items was not updated. ".mysqli_error().""; }
	else { $success[] = "Your items has been updated!"; }
    }

    $sql = $database->query("SELECT * FROM `user_items` WHERE name='$player'");
    while($row=mysqli_fetch_assoc($sql)) {
        echo '<h1>Edit Your Items</h1>
        <p>Use this form to edit your items in the database. <b>You can only add or remove member and event cards via this form.</b> Please make sure to add only the cards you\'ve gained in a comma-separated format. Your mastered decks can only be edited by an administrator.</p><center>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        echo '<form method="post" action="/account.php?do=edit-items">
        <input type="hidden" name="name" value="'.$row['name'].'" />
        <table width="100%" cellpadding="0" cellspacing="3" border="0" class="border">
        <tr>
            <td class="headLine" width="20%">Member Cards:</td><td class="tableBody" width="80%"><textarea name="mcard" rows="5" style="width: 95%;">'.$row['mcard'].'</textarea></td>
        </tr>
        <tr>
            <td class="headLine">Event Cards:</td><td class="tableBody"><textarea name="ecard" rows="5" style="width: 95%;">'.$row['ecard'].'</textarea></td>
        </tr>
        <tr>
	    <td class="headLine">Level Badge:</td><td class="tableBody"><selec name="lvlb" style="width:95%;">
	    <option value="">Select a Level Badge</option>';
	    $count = $database->num_rows("SELECT * FROM `tcg_badges`");
	    for ($i=1; $i<=$count; $i++) {
		$lb = $database->get_assoc("SELECT * FROM `tcg_badges` WHERE `id`='$i');
		echo '<option value="'.$lb['set'].'">'.$lb['donator'].' ('.$lb['set'].')</option>';
	    }
	    echo '</select></td>
	    <td class="headLine">Proceed?</td><td class="tableBody"><input type="submit" name="update" class="btn-success" value="Edit Gallery" /></td></tr>
        </table>
        </form></center>
        <p align="right"><a href="/account.php">Go back?</a></p>';
    }
}

##################################
########## SHOW PROFILE ##########
##################################
else {
    $select = $database->query("SELECT * FROM `user_list` WHERE `email`='$login'");
    while($row=mysqli_fetch_assoc($select)) {
        $items = $database->get_assoc("SELECT * FROM `user_items` WHERE `name`='".$row['name']."'");
        $about = stripslashes($row['about']);
        echo '<h1>Member Panel : '.$row['name'].'</h1>
        <p>Welcome to your member panel, <strong>'.$row['name'].'</strong>! From here you can submit various forms, edit your info, and play all of the games here at '.$tcgname.'!</p>';
        if($row['status']=="Pending") {
            echo '<p>It looks like you recently joined '.$tcgname.' and your account hasn\'t been activated yet. You must be approved by an adminstrator before you can fully access the TCG. Your account should be activated soon. If you joined more than 2 weeks ago and haven\'t received your activation email, please email us at <a href="mailto:'.$tcgemail.'">'.$tcgemail.'</a></p>';
        } elseif($row['status']=="Hiatus") {
            echo '<p>It looks like you have set your status to Hiatus. In order to play games here you must reactivate your account. This is self-service, and to do so, go to <a href="/account.php?do=edit-information">Edit Information</a> and set your status to Active.</p>';
        }
        echo '<div class="statLink">
            <a href="/account.php?do=logs">Permanent Logs</a>
            <a href="/messages.php?id='.$row['name'].'&page=inbox">Mailbox</a>
            <a href="/shoppe.php">Shoppe</a>
        </div>
        <br /><table width="100%" cellpadding="0" cellspacing="3" border="0">
            <tr><td width="15%" valign="top" align="center" rowspan="2">';
                if($row['memcard']=="Yes") { echo '<img src="'.$tcgcards.'mc-'.$row['name'].'.png" />'; }
                else { echo '<img src="'.$tcgcards.'mc-filler.png" />'; }
            echo '</td>
            <td width="15%" class="headLine" height="5%">'.$x1.'s</td>
            <td width="15%" class="headLine" height="5%">'.$x2.'s</td>
            <td width="20%" rowspan="2" align="center">';
            if ($row['level'] != "10") { $level = "0".$row['level']; }
            else { $level = $row['level']; }
                echo '<img src="/images/badges/'.$items['level_badge'].'-'.$level.'.png" title="Level '.$level.'" />
            </td>
            </tr>
            <tr class="rows"><td align="center" class="tableBody"><img src="/images/'.$settings->getValue('x1').'" /> x <b>'.$items['x1'].'</b></td><td align="center" class="tableBody"><img src="/images/'.$settings->getValue('x2').'" /> x <b>'.$items['x2'].'</b></td></tr>
        </table><br />

        <table width="100%" cellspacing="5" border="0" class="table table-bordered table-striped">
            <tbody>
                <tr><td width="33%"><b>Status:</b> '.$row['status'].'</td>
                    <td width="33%"><b>Rank:</b> Level '.$row['level'].'</td>
                    <td width="33%"><b>Collecting:</b> <a href="'.$tcgurl.'cards.php?view=released&deck='.$row['collecting'].'">'.$row['collecting'].'</a></td></tr>
                <tr><td><b>Card Count:</b> '.$items['cards'].'</td>
                    <td><b>Joined:</b> '.date("F d, Y", strtotime($row['regdate'])).'</td>
                    <td><b>Birthday:</b> '.date("F d", strtotime($row['birthday'])).'</td></tr>
                <tr><td colspan="3"><b>Last seen:</b> '.date("F d, Y", strtotime($row['session'])).' at '.date("h:i A", strtotime($row['session'])).'</td></tr>
            </tbody>
        </table>
        
        <h2>About Me</h2>
        <p>'.$row['about'].'</p>
        
        <h2>Services</h2>
        <div class="statLink">
            <a href="/services.php?form=masteries">Masteries</a>
            <a href="/services.php?form=level-up">Level Up</a>
            <a href="/services.php?form=trading-rewards">Trading Rewards</a>
        </div><div class="statLink2">
            <a href="/services.php?form=doubles">Doubles Exchange</a>
            <a href="/services.php?form=deck-claims">Deck Claims</a>
            <a href="/services.php?form=deck-donations">Deck Donations</a>
        </div>
        
        <h2><span class="line-center">Account</span></h2>
        <div class="statLink">
            <a href="/account.php?do=edit-information">Edit Your Information</a>
            <a href="/account.php?do=edit-items">Edit Your Items</a>
            <a href="/account.php?do=reset-password">Reset Password</a>
        </div><div class="statLink2">
            <a href="/services.php?form=contact">Contact Admin</a>
            <a href="/account.php?do=quit">Quit '.$tcgname.'</a>
            <a href="/account.php?do=logout">Logout</a>
        </div>';
    }
}
include($footer);
?>
