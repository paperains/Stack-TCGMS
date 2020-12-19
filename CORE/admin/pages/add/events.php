<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

#####################################
########## Add Form Action ##########
#####################################
if ( isset($_POST['submit']) ) {
    $filename = $sanitize->for_db($_POST['filename']);
    $title = $sanitize->for_db($_POST['title']);
    $year = $sanitize->for_db($_POST['year']);
    $month = $sanitize->for_db($_POST['month']);
    $day = $sanitize->for_db($_POST['day']);
    $released = $year."-".$month."-".$day;

    $img_desc = $uploads->reArrayFiles($img);
    $uploads->folderPath('images','cards');

    $insert = $database->query("INSERT INTO `tcg_cards_event` (`filename`,`title`,`released`) VALUES ('$filename','$title','$released')");

    if ($insert == TRUE) { $success[] = "The event card was successfully added to the database!"; }
    else { $error[] = "Sorry, there was an error and the event card was not added. ".mysqli_error().""; }
}

echo '<h1>Event Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Add a Card</h1>
<p>Use this form to add an event card to the database. Use the <a href="index.php?page=events">edit</a> form to update information for existing event cards.</p>
<center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
$current_month = date("F");
$current_date = date("d");
$current_year = date("Y");
$cur_month = date("m");
echo '</center>
<form method="post" action="index.php?action=add&page=events" multipart="" enctype="multipart/form-data">
<table width="100%" cellpadding="5" cellspacing="3" border="0">
    <tr>
        <td width="10%" class="headSub">Title:</td><td width="40%" valign="middle"><input type="text" name="title" style="width: 92%;" /></td>
        <td width="10%" class="headSub">Filename:</td><td width="40%" valign="middle"><input type="text" name="filename" style="width: 92%;" /></td>
    </tr>
    <tr>
        <td class="headSub">Released:</td><td valign="middle"><select name="month" style="width: 45%;">
        <option value="'.$cur_month.'">'.$current_month.'</option>';
        for($m=1; $m<=12; $m++) {
            if ($m < 10) { $_mon = "0$m"; }
            else { $_mon = $m; }
            echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
        }
        echo '</select> <input type="text" name="day" size="2" value="'.$current_date.'" /> ';
        //get the current year
        $start=date('Y'); $end=$start-10;
        // set start and end year range i.e the start year
        $yearArray = range($start,$end);
        // here you displaying the dropdown list
        echo '<select name="year">
        <option value="'.$current_year.'">'.$current_year.'</option>';
        foreach ($yearArray as $year) {
        // this allows you to select a particular year
        $selected = ($year == $start) ? 'selected' : '';
        echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
        }
        echo '</select></td>
        <td class="headSub">Upload a Card:</td><td valign="middle"><input type="file" name="img[]"></td>
    </tr>
    <tr>
        <td colspan="4" valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Add Deck" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td>
    </tr>
</table>
</form>';

$uploads->reArrayFiles($file);
?>
