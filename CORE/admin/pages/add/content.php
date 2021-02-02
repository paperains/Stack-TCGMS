<?php
#####################################
########## Add Form Action ##########
#####################################
if ( isset($_POST['submit']) ) {
    $month = htmlspecialchars(strip_tags($_POST['month']));
    $date = htmlspecialchars(strip_tags($_POST['date']));
    $year = htmlspecialchars(strip_tags($_POST['year']));
    $title = htmlspecialchars(strip_tags($_POST['title']));
    $slug = htmlspecialchars(strip_tags($_POST['slug']));
    $parent = htmlspecialchars(strip_tags($_POST['parent']));
    $status = htmlspecialchars(strip_tags($_POST['status']));
    $content = $_POST['content'];
    
    $timestamp = $year . "-" . $month . "-" . $date;
    $content = nl2br($content);
    
    if (!get_magic_quotes_gpc()) {
        $content = addslashes($content);
    }
        
    $result = $database->query("INSERT INTO `tcg_pages` (`post_title`,`post_slug`,`parent_id`,`post_content`,`post_status`,`timestamp`) VALUES ('$title','$slug','$parent','$content','$status','$timestamp')") or print("Can't insert into table tcg_pages.<br />" . $result . "<br />Error:" . mysqli_connect_error());
    if ( !$result ) { $error[] = "Sorry, there was an error and your page content was not added. ".mysqli_error().""; }
    else { $success[] = "Your page content has successfully been entered into the database."; }
} // END BLOG PROCESS

date_default_timezone_set('Asia/Manila');
$current_month = date("F");
$current_date = date("d");
$current_year = date("Y");
$cur_month = date("m");

echo '<h1>Page <span class="fas fa-angle-right" aria-hidden="true"></span> Add a Page</h1>
<p>Use the form below to create a new page content for your TCG. Use the <a href="index.php?page=content">edit</a> form to update the information for existing pages.</p><center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
echo '<form method="post" action="index.php?action=add&page=content">
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
    <td width="10%" class="headSub">Title:</td><td width="30%" valign="middle"><input type="text" name="title" placeholder="Information" style="width: 90%;" /></td>
</tr>
<tr>
    <td class="headSub">Parent:</td><td valign="middle"><select name="parent" style="width: 95%;">';
    $count = $database->num_rows("SELECT * FROM `tcg_pages` WHERE `parent_id`='0'");
    if ($count == 0) { echo '<option value="0">None</option>'; }
    else {
        echo '<option value="0">None</option>';
        for($i=1;$i<=$count;$i++) {
            $sql = $database->get_assoc("SELECT * FROM `tcg_pages` WHERE `parent_id`='0' ORDER BY `id`");
            echo '<option value='.$sql['id'].'">'.$sql['post_title'].'</option>';
        }
    }
    echo '</select></td>
    <td class="headSub">Slug:</td><td valign="middle"><input type="text" name="slug" placeholder="information" style="width: 90%;" /></td>
</tr>
<tr><td class="headSub">Content:</td><td colspan="3" valign="middle"><textarea name="content" style="width: 95%;" rows="10" /></textarea></td></tr>
<tr>
    <td class="headSub">Status:</td><td valign="middle"><select name="status" style="width: 95%;">
    <option value="Draft">Draft</option>
    <option value="Published">Publish</option>
    </select></td>
    <td class="headSub">Proceed?</td><td valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Add Page" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td>
</tr>
</table>
</form>';
?>
