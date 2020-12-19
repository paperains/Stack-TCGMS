<?php
######################################
########## Edit Form Action ##########
######################################
if ( isset($_POST['submit']) ) {
	$id = $sanitize->for_db($_POST['id']);
	$name = $sanitize->for_db($_POST['name']);
	$email = $sanitize->for_db($_POST['email']);
	$url = $sanitize->for_db($_POST['url']);
	$refer = $sanitize->for_db($_POST['refer']);
	$status = $sanitize->for_db($_POST['status']);
	$prejoiner = $sanitize->for_db($_POST['prejoiner']);
	$level = $sanitize->for_db($_POST['level']);
	$collecting = $sanitize->for_db($_POST['collecting']);
	$memcard = $sanitize->for_db($_POST['memcard']);
	$mastered = $sanitize->for_db($_POST['mastered']);
	$about = $sanitize->for_db($_POST['about']);
	$mcard = $sanitize->for_db($_POST['mcard']);
	$ecard = $sanitize->for_db($_POST['ecard']);
    $role = $sanitize->for_db($_POST['role']);
	$month = $_POST['month'];
	$day = $_POST['day'];
	$year = $_POST['year'];
	
	$birthday = $year."-".$month."-".$day;

    $update = $database->query("UPDATE `user_list` SET `name`='$name', `email`='$email', `url`='$url', `refer`='$refer', `birthday`='$birthday', `status`='$status', `prejoiner`='$prejoiner', `level`='$level', `collecting`='$collecting', `memcard`='$memcard', `about`='$about', `role`='$role' WHERE `id`='$id'");
	
	if ($update == TRUE) {
		$database->query("UPDATE `user_items` SET `mastered`='$mastered', `mcard`='$mcard', `ecard`='$ecard' WHERE `id`='$id'");
        $success[] = "The member has been successfully updated!";
	}
	else { $error[] = "Sorry, there was an error and the member was not updated. ".mysqli_error().""; }
}

if (empty($id)) {
	echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	$gal = $database->get_assoc("SELECT * FROM `user_items` WHERE `id`='$id'");
	$row = $database->get_assoc("SELECT * FROM `user_list` WHERE id='$id'");
	echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Edit Member</h1>
	<p>Use this form to edit a member in the database. Use the <a href="index.php?page=members&action=add">add</a> form to add new members.</p><center>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	echo '</center><form method="post" action="index.php?action=edit&page=members&id='.$id.'">
	<input type="hidden" name="id" value="'.$id.'" />
	<input type="hidden" name="about" value="'.$row['about'].'" />
	<table width="100%" cellspacing="3">
	<tr>
        <td class="headSub" width="15%">Name:</td><td valign="middle" width="32%"><input type="text" name="name" value="'.$row['name'].'" style="width:90%;" /></td>
        <td class="headSub" width="15%">Email:</td><td valign="middle" width="38%"><input type="text" name="email" value="'.$row['email'].'" style="width:90%;" /></td>
	</tr>
	<tr>
        <td class="headSub">Trade Post:</td><td valign="middle"><input type="text" name="url" value="'.$row['url'].'" style="width:90%;" /></td>
        <td class="headSub">Birthday:</td><td valign="middle"><select name="month" style="width:41%;">
            <option value="'.date('m', strtotime($row['birthday'])).'">'.date('F', strtotime($row['birthday'])).'</option>';
            for($m=1; $m<=12; $m++) {
                if ($m < 10) { $_mon = "0$m"; }
                else { $_mon = $m; }
                echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
            }
            echo '</select> <select name="day">
            <option value="'.date('d', strtotime($row['birthday'])).'">'.date('d', strtotime($row['birthday'])).'</option>';
            for($i=1; $i<=31; $i++) {
                if ($i < 10) { $_days = "0$i"; }
                else { $_days = $i; }
                echo '<option value="'.$_days.'">'.$_days.'</option>';
            }
            echo '</select> ';
            $date=date('Y');
            $start=$date-10;
            $end=$start-40;
            $yearArray = range($start,$end);
            echo '<select name="year">
            <option selected value="'.date('Y', strtotime($row['birthday'])).'">'.date('Y', strtotime($row['birthday'])).'</option>';
            foreach ($yearArray as $year) {
                $selected = ($year == $start) ? 'selected' : '';
                echo '<option value="'.$year.'">'.$year.'</option>';
            }
        echo '</select></td>
	</tr>
	<tr>
        <td class="headSub">Collecting:</td><td valign="middle"><select name="collecting" style="width:95%;">
        <option value="'.$row['collecting'].'">Current: '.$row['collecting'].'</option>';
        $row_collect = $database->query("SELECT * FROM `tcg_cards` ORDER BY `description` ASC");
        while($col = mysqli_fetch_assoc($row_collect)) { echo '<option value="'.$col['filename'].'">'.$col['filename'].'</option>'; }
        echo '</select></td>
        <td class="headSub">Referral:</td><td valign="middle"><select name="refer" style="width:95%;" />
        <option value="'.$row['refer'].'">Current: '.$row['refer'].'</option>';
        $row_mem = $database->query("SELECT * FROM `user_list` ORDER BY name ASC");
        while($mem = mysqli_fetch_assoc($row_mem)) {
            $name=stripslashes($mem['name']);
            echo "<option value=\"$name\">$name</option>\n";
        }
        echo '</select></td>
    </tr>
	<tr>
        <td class="headSub">Status:</td><td valign="middle"><select name="status" style="width:95%;">
            <option value="'.$row['status'].'">Current: '.$row['status'].'</option>
            <option value="Active">Active</option>
            <option value="Pending">Pending</option>
            <option value="Hiatus">Hiatus</option>
            <option value="Inactive">Inactive</option>
            <option value="Retired">Retired</option>
        </select></td>
        <td class="headSub">Prejoiner?</td><td valign="middle"><select name="prejoiner" style="width:95%;">
            <option value="'.$row['prejoiner'].'">Current: '.$row['prejoiner'].'</option>
            <option value="Beta">Beta Tester</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select></td>
	</tr>
	<tr>
        <td class="headSub">Level:</td><td valign="middle"><select name="level" style="width:95%;">';
        $l = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `level`='".$row['level']."'");
        echo '<option value="'.$row['level'].'">Current: Level '.$l['level'].' - '.$l['name'].'</option>';
        $l = $database->num_rows("SELECT * FROM `tcg_levels`");
        for($i=1; $i<=$l; $i++) {
            $lvl = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `level`='$i'");
            echo '<option value="'.$i.'">'.$lvl['level'].' - '.$lvl['name']."</option>\n";
        }
        echo '</select></td>
        <td class="headSub">Member Card?</td><td valign="middle"><select name="memcard" style="width:95%;">
        <option value="'.$row['memcard'].'">Current: '.$row['memcard'].'</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
        </select></td>
	</tr>
	<tr><td class="headSub" valign="top">Mastered:</td><td valign="middle" colspan="3"><textarea name="mastered" rows="5" style="width: 95%;">'.$gal['mastered'].'</textarea></td></tr>
	<tr><td class="headSub" valign="top">Member Cards:</td><td valign="middle" colspan="3"><textarea name="mcard" rows="5" style="width: 95%;">'.$gal['mcard'].'</textarea></td></tr>
	<tr><td class="headSub" valign="top">Event Cards:</td><td valign="middle" colspan="3"><textarea name="ecard" rows="5" style="width: 95%;">'.$gal['ecard'].'</textarea></td></tr>
	<tr>
        <td class="headSub">Role:</td><td valign="middle"><select name="role" style="width:95%;">
        <option value="'.$row['role'].'">Current: '.$row['role'].'</option>
        <option value="Admin">Admin</option>
        <option value="Editor">Editor</option>
        <option value="Deck Maker">Deck Maker</option>
        </select></td>
        <td valign="middle" align="center" colspan="4"><input type="submit" name="submit" class="btn-success" value="Edit" /></td>
    </tr>
	</table>
	</form>';
}
?>