<?php
#####################################
########## Add Form Action ##########
#####################################
if ( isset($_POST['submit']) ) {
    $month = htmlspecialchars(strip_tags($_POST['month']));
    $date = htmlspecialchars(strip_tags($_POST['date']));
    $year = htmlspecialchars(strip_tags($_POST['year']));
    $title = htmlspecialchars(strip_tags($_POST['title']));
    $mem = htmlspecialchars(strip_tags($_POST['members']));
    $lvl = htmlspecialchars(strip_tags($_POST['levels']));
    $mas = htmlspecialchars(strip_tags($_POST['masters']));
    $decks = htmlspecialchars(strip_tags($_POST['decks']));
    $wish = htmlspecialchars(strip_tags($_POST['wish']));
    $card = htmlspecialchars(strip_tags($_POST['amount']));
    $stat = htmlspecialchars(strip_tags($_POST['status']));
    $auth = htmlspecialchars(strip_tags($_POST['author']));
    $icon = htmlspecialchars(strip_tags($_POST['icon']));
    $game = htmlspecialchars(strip_tags($_POST['games']));
    $aff = $_POST['affiliates'];
    $refer = $_POST['referrals'];
    $entry = $_POST['entry'];
    
    $timestamp = $year . "-" . $month . "-" . $date;
    $entry = nl2br($entry);
    
    if (!get_magic_quotes_gpc()) {
        $title = addslashes($title);
        $entry = addslashes($entry);
        $aff = addslashes($aff);
    }
        
    $result = $database->query("INSERT INTO `tcg_blog` (`timestamp`,`title`,`author`,`icon`,`members`,`masters`,`levels`,`affiliates`,`games`,`referrals`,`decks`,`status`,`wish`,`amount`,`entry`) VALUES ('$timestamp','$title','$auth','$icon','$mem','$mas','$lvl','$aff','$game','$refer','$decks','$stat','$wish','$card','$entry')") or print("Can't insert into table tcg_blog.<br />" . $result . "<br />Error:" . mysqli_connect_error());
    if ( !$result ) { $error[] = "Sorry, there was an error and your blog entry was not added. ".mysqli_error().""; }
    else { $success[] = "Your blog entry has successfully been entered into the database."; }
} // END BLOG PROCESS

date_default_timezone_set('Asia/Manila');
$current_month = date("F");
$current_date = date("d");
$current_year = date("Y");
$cur_month = date("m");

echo '<h1>Blog <span class="fas fa-angle-right" aria-hidden="true"></span> Add a Blog</h1>
<p>Use the form below to create a new blog post for your TCG\'s weekly update. Use the <a href="index.php?page=blog">edit</a> form to update the information for existing blog posts.</p><center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
echo '<form method="post" action="index.php?action=add&page=blog">
<input type="hidden" name="author" value="'.$player.'" />
<table width="100%" cellpadding="5" cellspacing="3" border="0">
<tr>
    <td width="10%" class="headSub">Date:</td><td width="30%" valign="middle"><select name="month" id="month" style="width: 45%;">
    <option value="'.$cur_month.'">'.$current_month.'</option>';
    for($m=1; $m<=12; $m++) {
        if ($m < 10) { $_mon = "0$m"; }
        else { $_mon = $m; }
        echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
    }
    echo '</select>
    <input type="text" name="date" id="date" size="1" value="'.$current_date.'" />';
    //get the current year
    $start=date('Y');
    $end=$start-40;
    // set start and end year range i.e the start year
    $yearArray = range($start,$end);
    // here you displaying the dropdown list
    echo ' <select name="year" id="year">
    <option value="'.$current_year.'">'.$current_year.'</option>';
    foreach ($yearArray as $year) {
        // this allows you to select a particular year
        $selected = ($year == $start) ? 'selected' : '';
        echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
    }
    echo '</select></td>
    <td width="10%" class="headSub">Title:</td><td width="30%" valign="middle"><input type="text" name="title" style="width: 90%;" /></td>
</tr>
<tr>
    <td class="headSub">Icon:</td><td valign="middle"><input type="text" name="icon" value="" style="width: 90%;" /></td>
    <td class="headSub">Games:</td><td valign="middle"><input type="text" name="games" value="" style="width: 90%;" /></td>
</tr>
<tr>
    <td class="headSub">Members:</td><td valign="middle"><input type="text" name="members" value="" style="width: 90%;" /></td>
    <td class="headSub">Referrals:</td><td valign="middle"><input type="text" name="referrals" value="" style="width: 90%;" /></td>
</tr>
<tr>
    <td class="headSub">New Decks:</td><td valign="middle"><input type="text" name="decks" value="" style="width: 90%;" /></td>
    <td class="headSub">Card Amount:</td><td valign="middle"><input type="text" name="amount" value="" style="width: 90%;" /></td>
</tr>
<tr>
    <td class="headSub">Affiliates:</td><td valign="middle"><input type="text" name="affiliates" value="" style="width: 90%;" /></td>
    <td class="headSub">Wishes:</td><td valign="middle"><select name="wish" id="wish" style="width:95%;">
    <option>----- With Wish? -----</option>
    <option value="Yes">Yes</option>
    <option value="None">None</option>
    </select></td>
</tr>
<tr>
    <td class="headSub">Masteries:</td><td valign="middle"><textarea name="masters" style="width: 90%;" rows="4" /></textarea></td>
    <td class="headSub">Level Ups:</td><td valign="middle"><textarea name="levels" style="width: 90%;" rows="4" /></textarea></td>
</tr>
<tr><td class="headSub">Main Update:</td><td colspan="3" valign="middle"><textarea name="entry" style="width: 95%;" rows="6" /></textarea></td></tr>
<tr>
    <td class="headSub">Status:</td><td valign="middle"><select name="status" style="width: 95%;">
    <option value="Draft">Draft</option>
    <option value="Published">Publish</option>
    <option value="Scheduled">Schedule</option>
    </select></td>
    <td class="headSub">Proceed?</td><td valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Add Blog" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td>
</tr>
</table>
</form>';
?>