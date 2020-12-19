<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

######################################
########## Edit Form Action ##########
######################################
if ( isset($_POST['submit']) ) {
    $id = $sanitize->for_db($_POST['id']);
    $filename = $sanitize->for_db($_POST['filename']);
    $title = $sanitize->for_db($_POST['title']);
    $maker = $sanitize->for_db($_POST['maker']);
    $year = $sanitize->for_db($_POST['year']);
    $month = $sanitize->for_db($_POST['month']);
    $day = $sanitize->for_db($_POST['day']);
    $released = $year."-".$month."-".$day;

    $img_desc = $uploads->reArrayFiles($img);
    $uploads->folderPath('images','cards');

    $update = $database->query("UPDATE `tcg_cards_event` SET `filename`='$filename', `title`='$title', `maker`='$maker', `released`='$released' WHERE id='$id'");

	if ($update == TRUE) { $success[] = "The event card was successfully updated in the database."; }
	else { $error[] = "Sorry, there was an error and the event card was not updated. ".mysqli_error().""; }
}

if (empty($id)) {
	echo '<h1>Event Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
	$row = $database->get_assoc("SELECT * FROM `tcg_cards_event` WHERE id='$id'");
	echo '<h1>Event Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Edit an Event Card</h1>
	<p>Use this form to edit an event card in the database. Use the <a href="index.php?action=add&page=events">add</a> form to add a new event card.</p><center>';
    if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
    if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	echo '</center>
    <form method="post" action="index.php?action=edit&page=events&id='.$id.'">
	<input type="hidden" name="id" value="'.$id.'" />
    <table width="100%" cellpadding="5" cellspacing="3" border="0">
        <tr>
            <td width="10%" class="headSub">Title:</td><td width="40%" valign="middle"><input type="text" name="title" value="'.$row['title'].'" style="width: 92%;" /></td>
            <td width="10%" class="headSub">Filename:</td><td width="40%" valign="middle"><input type="text" name="filename" value="'.$row['filename'].'" style="width: 92%;" /></td>
        </tr>
        <tr>
            <td class="headSub">Maker:</td><td valign="middle"><input type="text" name="maker" value="'.$row['maker'].'" style="width: 92%;" placeholder="Maker" /></td>
            <td class="headSub">Released:</td><td valign="middle"><select name="month" style="width: 45%;">
            <option value="'.date('m', strtotime($row['released'])).'">'.date('F', strtotime($row['released'])).'</option>';
            for($m=1; $m<=12; $m++) {
                if ($m < 10) { $_mon = "0$m"; }
                else { $_mon = $m; }
                echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
            }
            echo '</select> <select name="day">
            <option value="'.date('d', strtotime($row['released'])).'">'.date('d', strtotime($row['released'])).'</option>';
            for($i=1; $i<=31; $i++) {
                if ($i < 10) { $_days = "0$i"; }
                else { $_days = $i; }
                echo '<option value="'.$_days.'">'.$_days.'</option>';
            }
            echo '</select> ';
            //get the current year
            $start=date('Y'); $end=$start-10;
            // set start and end year range i.e the start year
            $yearArray = range($start,$end);
            // here you displaying the dropdown list
            echo '<select name="year">
            <option selected value="'.date('Y', strtotime($row['released'])).'">'.date('Y', strtotime($row['released'])).'</option>';
            foreach ($yearArray as $year) {
            // this allows you to select a particular year
            $selected = ($year == $start) ? 'selected' : '';
            echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
            }
            echo '</select></td>
        </tr>
        <tr>
            <td valign="middle" align="center" colspan="4"><input type="submit" name="submit" class="btn-success" value="Edit Event Card" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td>
        </tr>
    </table>
    </form>';
}
?>