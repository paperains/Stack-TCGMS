<?php
######################################
########## Edit Form Action ##########
######################################
if ( isset($_POST['update']) ) {
    $id = htmlspecialchars(strip_tags($_POST['id']));
    $title = htmlspecialchars(strip_tags($_POST['title']));
    $slug = htmlspecialchars(strip_tags($_POST['slug']));
    $parent = htmlspecialchars(strip_tags($_POST['parent']));
    $status = htmlspecialchars(strip_tags($_POST['status']));
    $content = $_POST['content'];
    $month = $_POST['month'];
    $date = $_POST['date'];
    $year = $_POST['year'];
	$timestamp = date('Y-m-d', strtotime($year . "-" . $month . "-" . $date));
	
	$content = nl2br($content);
	
	if (!get_magic_quotes_gpc()) { $content = addslashes($content); }
	
	$update = $database->query("UPDATE `tcg_pages` SET timestamp='$timestamp', post_title='$title', post_slug='$slug', parent_id='$parent', post_content='$content', post_status='$status' WHERE id='$id' LIMIT 1") or print ("Can't update page content.<br />" . mysqli_connect_error());

    if ( !$update ) { $error[] = "Sorry, there was an error and the page content was not updated. ".mysqli_error().""; }
	else { $success[] = "The page content has been updated successfully!"; }
}

if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid page ID."); }
else { $id = (int)$_GET['id']; }
$sql = $database->query("SELECT * FROM `tcg_pages` WHERE id='$id'") or print ("Can't select page.<br />" . $sql . "<br />" . mysqli_connect_error());
while($row = mysqli_fetch_array($sql)) {
    $old_timestamp = $row['timestamp'];
    $old_title = stripslashes($row['post_title']);
    $old_slug = stripslashes($row['post_slug']);
    $old_parent = stripslashes($row['parent_id']);
    $old_status = stripslashes($row['post_status']);
    $old_content = stripslashes($row['post_content']);
    $old_title = str_replace('"','\'',$old_title);

    $old_month = date("F", strtotime($old_timestamp));
    $old_date = date("d", strtotime($old_timestamp));
    $old_year = date("Y", strtotime($old_timestamp));
    $oldm = date("m", strtotime($old_timestamp));
    $oldy = date("Y", strtotime($old_timestamp));
}
    
echo '<h1>Page <span class="fas fa-angle-right" aria-hidden="true"></span> Edit Page Content</h1>
<center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
echo '</center>
<form method="post" action="index.php?action=edit&page=content&id='.$id.'">
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
    <td class="headSub">Parent:</td><td valign="middle"><select name="parent" style="width: 95%;">';
    $old = $database->get_assoc("SELECT * FROM `tcg_pages` WHERE `id`='$old_parent' ORDER BY `id`");
    if ($old_parent == 0) { echo '<option value="0">None</option>'; }
    else { echo '<option value='.$old_parent.'">'.$old['post_title'].'</option>'; }
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
    <td class="headSub">Slug:</td><td valign="middle"><input type="text" name="slug" id="slug" value="'.$old_slug.'" style="width:90%;" /></td>
</tr>
<tr><td class="headSub">Content:</td><td valign="middle" colspan="3"><textarea style="width: 97%" rows="15" name="content" id="content">'.$old_content.'</textarea></td></tr>
<tr>
    <td class="headSub">Status:</td><td valign="middle"><select name="status" id="status" style="width:95%;">
        <option value="'.$old_status.'">'.$old_status.'</option>
        <option>-----</option>
        <option value="Draft">Draft</option>
        <option value="Published">Publish</option>
    </select></td>
    <td class="headSub">Proceed?</td><td valign="middle" align="center"><input type="submit" name="update" id="update" class="btn-success" value="Update"></td>
</tr>
</table>
</form>';
?>
