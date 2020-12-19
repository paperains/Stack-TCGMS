<?php
######################################
########## Edit Form Action ##########
######################################
if ( isset($_POST['update']) ) {
    $id = htmlspecialchars(strip_tags($_POST['id']));
    $title = htmlspecialchars(strip_tags($_POST['title']));
    $mem = htmlspecialchars(strip_tags($_POST['members']));
    $mas = htmlspecialchars(strip_tags($_POST['masters']));
    $lvl = htmlspecialchars(strip_tags($_POST['levels']));
    $decks = htmlspecialchars(strip_tags($_POST['decks']));
    $wish = htmlspecialchars(strip_tags($_POST['wish']));
    $amount = htmlspecialchars(strip_tags($_POST['amount']));
    $stat = htmlspecialchars(strip_tags($_POST['status']));
    $icon = htmlspecialchars(strip_tags($_POST['icon']));
    $game = htmlspecialchars(strip_tags($_POST['games']));
    $aff = $_POST['affiliates'];
    $refer = $_POST['referrals'];
    $entry = $_POST['entry'];
    $month = $_POST['month'];
    $date = $_POST['date'];
    $year = $_POST['year'];
	$timestamp = date('Y-m-d', strtotime($year . "-" . $month . "-" . $date));
	
	$entry = nl2br($entry);
	
	if (!get_magic_quotes_gpc()) { $entry = addslashes($entry); }
	
	$update = $database->query("UPDATE `tcg_blog` SET timestamp='$timestamp', title='$title', icon='$icon', members='$mem', masters='$mas', levels='$lvl', affiliates='$aff', games='$game', referrals='$refer', decks='$decks', wish='$wish', amount='$amount', entry='$entry', status='$stat' WHERE id='$id' LIMIT 1") or print ("Can't update entry.<br />" . mysqli_connect_error());

    if ( !$update ) { $error[] = "Sorry, there was an error and the blog entry was not updated. ".mysqli_error().""; }
	else { $success[] = "The blog entry has been updated successfully!"; }
}

if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid entry ID."); }
else { $id = (int)$_GET['id']; }
$sql = $database->query("SELECT * FROM `tcg_blog` WHERE id='$id'") or print ("Can't select entry.<br />" . $sql . "<br />" . mysqli_connect_error());
while($row = mysqli_fetch_array($sql)) {
    $old_timestamp = $row['timestamp'];
    $old_title = stripslashes($row['title']);
    $old_mem = stripslashes($row['members']);
    $old_mas = stripslashes($row['masters']);
    $old_lvl = stripslashes($row['levels']);
    $old_aff = stripslashes($row['affiliates']);
    $old_refer = stripslashes($row['referrals']);
    $old_decks = stripslashes($row['decks']);
    $old_wish = stripslashes($row['wish']);
    $old_amount = stripslashes($row['amount']);
    $old_entry = stripslashes($row['entry']);
    $old_stat = stripslashes($row['status']);
    $old_icon = stripslashes($row['icon']);
    $old_game = stripslashes($row['games']);
    $old_title = str_replace('"','\'',$old_title);

    $old_month = date("F", strtotime($old_timestamp));
    $old_date = date("d", strtotime($old_timestamp));
    $old_year = date("Y", strtotime($old_timestamp));
    $oldm = date("m", strtotime($old_timestamp));
    $oldy = date("Y", strtotime($old_timestamp));
}
    
echo '<h1>Blog <span class="fas fa-angle-right" aria-hidden="true"></span> Edit Blog Post</h1>
<center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
echo '</center>
<form method="post" action="index.php?action=edit&page=blog&id='.$id.'">
<input type="hidden" name="id" value="'.$id.'" />
<table width="100%" cellspacing="3">
<tr>
    <td class="headSub" width="14%">Date:</td>
    <td valign="middle" width="35%"><select name="month" id="month">
        <option value="'.$oldm.'">'.$old_month.'</option>';
        for($m=1; $m<=12; $m++) {
            if ($m < 10) { $_mon = "0$m"; }
            else { $_mon = $m; }
            echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
        }
    echo '</select> <input type="text" name="date" id="date" size="1" value="'.$old_date.'" /> ';
    $start=date('Y');
    $end=$start-40;
    $yearArray = range($start,$end);
    echo '<select name="year" id="year">
        <option value="'.$oldy.'">'.$old_year.'</option>';
        foreach ($yearArray as $year) {
            $selected = ($year == $start) ? 'selected' : '';
            echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
        }
    echo '</select></td>
    <td class="headSub" width="14%">Title:</td>
    <td valign="middle" width="35%"><input type="text" name="title" id="title" value="'.$old_title.'" style="width:90%;" /></td>
</tr>
<tr>
    <td class="headSub">Icon:</td><td valign="middle"><input type="text" name="icon" id="icon" value="'.$old_icon.'" style="width:90%;" /></td>
    <td class="headSub">Games:</td><td valign="middle"><input type="text" name="games" id="games" value="'.$old_game.'" style="width:90%;" /></td>
</tr>
<tr>
    <td class="headSub">Members:</td><td valign="middle"><input type="text" name="members" id="members" value="'.$old_mem.'" style="width:90%;" /></td>
    <td class="headSub">Referrals:</td><td valign="middle"><input type="text" name="referrals" id="referrals" value="'.$old_refer.'" style="width:90%;" /></td>
</tr>
<tr>
    <td class="headSub">New Decks:</b></td><td valign="middle"><input type="text" name="decks" id="decks" value="'.$old_decks.'" style="width:90%;" /></td>
    <td class="headSub">Card Amount:</td><td valign="middle"><input type="text" name="amount" id="amount" value="'.$old_amount.'" style="width:90%;" /></td>
</tr>
<tr>
    <td class="headSub">Affiliates:</td><td valign="middle"><textarea name="affiliates" id="affiliates" style="width:90%;">'.$old_aff.'</textarea></td>
    <td class="headSub">Wishes:</td><td valign="middle"><select name="wish" id="wish" style="width:95%;">
        <option value="'.$old_wish.'">'.$old_wish.'</option>
        <option>-----</option>
        <option value="Yes">Yes</option>
        <option value="None">None</option>
    </select></td>
</tr>
<tr>
    <td class="headSub">Masters:</td><td valign="middle"><textarea style="width: 90%" rows="6" name="masters" id="masters">'.$old_mas.'</textarea></td>
    <td class="headSub">Levels:</td><td valign="middle"><textarea style="width: 90%" rows="6" name="levels" id="levels">'.$old_lvl.'</textarea></td>
</tr>
<tr><td class="headSub">Main Update:</td><td valign="middle" colspan="3"><textarea style="width: 97%" rows="15" name="entry" id="entry">'.$old_entry.'</textarea></td></tr>
<tr>
    <td class="headSub">Status:</td><td valign="middle"><select name="status" id="status" style="width:95%;">
        <option value="'.$old_stat.'">'.$old_stat.'</option>
        <option>-----</option>
        <option value="Draft">Draft</option>
        <option value="Published">Publish</option>
        <option value="Scheduled">Schedule</option>
    </select></td>
    <td class="headSub">Proceed?</td><td valign="middle" align="center"><input type="submit" name="update" id="update" class="btn-success" value="Update"></td>
</tr>
</table>
</form>';
?>