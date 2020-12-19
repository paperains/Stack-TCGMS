<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

#####################################
########## Add Form Action ##########
#####################################
if ($stat == "added") {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
    else {
        $filename = $sanitize->for_db($_POST['filename']);
        $deckname = $sanitize->for_db($_POST['deckname']);
        $donator = $sanitize->for_db($_POST['donator']);
        $maker = $sanitize->for_db($_POST['maker']);
        $color = $sanitize->for_db($_POST['color']);
        $puzzle = $sanitize->for_db($_POST['puzzle']);
        $description = $sanitize->for_db($_POST['description']);
        $category = $sanitize->for_db($_POST['category']);
        $count = $sanitize->for_db($_POST['count']);
        $worth = $sanitize->for_db($_POST['worth']);
        $masterable = $sanitize->for_db($_POST['masterable']);
        $series = $sanitize->for_db($_POST['series']);
        
        $img_desc = $uploads->reArrayFiles($img);
        $uploads->folderPath('images','cards');
        
        $date = date("Y-m-d", strtotime("now"));
        
        $insert = $database->query("INSERT INTO `tcg_cards` (`filename`,`deckname`,`color`,`puzzle`,`description`,`maker`,`donator`,`category`,`series`,`count`,`worth`,`masterable`,`masters`,`status`) VALUES ('$filename','$deckname','$color','$puzzle','$description','$maker','$donator','$category','$series','$count','$worth','$masterable','None','Upcoming')");
        
        if ($insert == TRUE) {
            $date = date("Y-m-d", strtotime("now"));
            $activity = '<span class="fas fa-plus-circle" aria-hidden="true"></span> <a href="/members.php?id='.$maker.'">'.$maker.'</a> added <a href="/cards.php?view=upcoming&deck='.$filename.'">'.$deckname.'</a> to the upcoming list.';
            $database->query("DELETE FROM `tcg_donations` WHERE `deckname`='$filename'");
            $database->query("INSERT INTO `tcg_activities` (`name`,`activity`,`date`) VALUES ('$maker','$activity','$date')");
            $database->query("INSERT INTO `user_rewards` (`name`,`type`,`subtitle`,`mcard`,`cards`,`shilling`,`pence`,`timestamp`) VALUES ('$maker','Paycheck','(Deck Making: $filename)','No','2','0','0','$date')");
            echo '<h1>Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
            echo '<p>The card deck was successfully added to the database!<br />Before adding a new upcoming deck, <a href="index.php?page=complete-deck">use this form to upload the remaining cards</a> the deck have (e.g. a filler or mastery badge).</p>';
        }
        else {
            echo '<h1>Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
            echo '<p>Sorry, there was an error and the card deck was not added.</p>';
            die("Error:". mysqli_connect_error());
        }
    }
} // END ADDED STATUS
else {
    echo '<h1>Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Add an Upcoming Deck</h1>
    <p>Use this form to add an upcoming deck to the database. Use the <a href="index.php?page=cards">edit</a> form to update information for existing card decks.</p>
    <p><font color="red">*</font> Please take note that you can only upload a total of 20 files. You will be instructed to upload the remaining files after submitting this form.</p>
    <form method="post" action="index.php?action=add&page=cards&stat=added" multipart="" enctype="multipart/form-data">
    <table width="100%" cellpadding="5" cellspacing="3" border="0">
    <tr>
        <td width="10%" class="headSub">Filename:</td><td width="40%" valign="middle"><input type="text" name="filename" style="width: 92%;" /></td>
        <td width="10%" class="headSub">Deck Name:</td><td width="40%" valign="middle"><input type="text" name="deckname" style="width: 92%;" /></td>
    </tr>
    <tr>
        <td class="headSub">Deck Info:</td><td valign="middle"><input type="text" name="maker" style="width: 41%;" placeholder="Maker" /> <input type="text" name="donator" style="width: 41%;" placeholder="Donator" /></td>
        <td class="headSub">Color:</td><td valign="middle"><input type="text" name="color" style="width: 92%;" /></td>
    </tr>
    <tr><td class="headSub">Description:</td><td colspan="3" valign="middle"><textarea name="description" style="width: 97%;" rows="4" /></textarea></td></tr>
    <tr>
        <td class="headSub">Count:</td><td valign="middle"><input type="text" name="count" value="" style="width: 92%;" /></td>
        <td class="headSub">Worth:</td><td valign="middle"><input type="text" name="worth" value="" style="width: 92%;" /></td>
    </tr>
    <tr>
        <td class="headSub">Puzzle?</td>
        <td valign="middle">
            <input type="radio" value="Yes" name="puzzle"> Yes &nbsp;&nbsp;&nbsp; <input type="radio" value="No" name="puzzle" checked> No
        </td>
        <td class="headSub">Masterable?</td>
        <td valign="middle">
            <input type="radio" value="Yes" name="masterable" checked> Yes &nbsp;&nbsp;&nbsp; <input type="radio" value="No" name="masterable"> No
        </td>
    </tr>
    <tr>
        <td class="headSub">Category:</td><td valign="middle"><select name="category" style="width: 98%;">';
            $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
            for($i=1; $i<=$c; $i++) {
                $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
                echo '<option value="'.$i.'">'.$cat['name']."</option>\n";
            }
        echo '</select></td>
        <td class="headSub">Series:</td>
        <td valign="middle"><input type="text" name="series" value="" style="width: 92%;" /></td>
    </tr>
    <tr><td class="headSub">Upload Cards:</td><td valign="middle"><input type="file" name="img[]" multiple></td><td class="headSub">Proceed?</td><td valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Add Deck" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td></tr>
    </table>
    </form>';
} // END SHOW ADD FORM
$uploads->reArrayFiles($file);
?>