<?php
######################################
########## Edit Form Action ##########
######################################
if ( isset($_POST['submit']) ) {
    $check->Value();
    $id = $sanitize->for_db($_POST['id']);
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
    $series = $sanitize->for_db($_POST['series']);
    $masterable = $sanitize->for_db($_POST['masterable']);
    $masters = $sanitize->for_db($_POST['masters']);
    $status = $sanitize->for_db($_POST['status']);
    
    $update = $database->query("UPDATE `tcg_cards` SET `filename`='$filename', `deckname`='$deckname', donator='$donator', maker='$maker', `color`='$color', `description`='$description', `series`='$series', `category`='$category', `count`='$count', `worth`='$worth', `masterable`='$masterable', `masters`='$masters', `status`='$status' WHERE `id`='$id'");
    
    if ($update == TRUE) { $success[] = "The card deck was successfully updated in the database."; }
    else { $error[] = "Sorry, there was an error and the card deck was not updated. ".mysqli_error().""; }
}

if (empty($id)) {
    echo '<h1>Upcoming Decks <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
    <p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
    $sql = $database->query("SELECT * FROM `tcg_cards` WHERE id='$id'");
    while($row = mysqli_fetch_assoc($sql)) {
        echo '<h1>Upcoming Decks <span class="fas fa-angle-right" aria-hidden="true"></span> Edit a Card Deck</h1>
        <p>Use this form to edit a card deck in the database. Use the <a href="index.php?action=add&page=cards">add</a> form to add new upcoming decks.</p><center>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        echo '</center>
        <form method="post" action="index.php?action=edit&page=upcoming&id='.$id.'">
        <input type="hidden" name="id" value="'.$id.'" />
        <table width="100%" cellpadding="5" cellspacing="3" border="0">
        <tr>
            <td width="10%" class="headSub">Filename:</td><td width="40%" valign="middle"><input type="text" name="filename" value="'.$row['filename'].'" style="width: 90%;" /></td>
            <td width="10%" class="headSub">Deck Name:</td><td width="40%" valign="middle"><input type="text" name="deckname" value="'.$row['deckname'].'" style="width: 90%;" /></td>
        </tr>
        <tr>
            <td class="headSub">Deck Info:</td><td valign="middle"><input type="text" name="maker" value="'.$row['maker'].'" style="width: 40%;" /> <input type="text" name="donator" value="'.$row['donator'].'" style="width: 40%;" /></td>
            <td class="headSub">Color:</td><td valign="middle"><input type="text" name="color" value="'.$row['color'].'" style="width: 90%;" /></td>
        </tr>
        <tr><td class="headSub">Description:</td><td colspan="3" valign="middle"><textarea name="description" rows="4" style="width: 96%;" />'.$row['description'].'</textarea></td></tr>
        <tr>
            <td class="headSub">Set:</td><td valign="middle"><select name="set" style="width: 95%;" />
                <option value="'.$row['series'].'">Current: '.$row['series'].'</option>
                <option value="Curse of the Black Pearl">Curse of the Black Pearl</option>
                <option value="Dead Man\'s Chest">Dead Man\'s Chest</option>
                <option value="At World\'s End">At World\'s End</option>
                <option value="On Stranger Tides">On Stranger Tides</option>
                <option value="Dead Men Tell No Tales">Dead Men Tell No Tales</option>
                <option value="Various">Various</option>
                <option value="None">None</option>
            </select></td>
            <td class="headSub">Category:</td>
            <td valign="middle"><select name="category" style="width: 95%;">';
                $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='".$row['category']."'");
                echo '<option value="'.$row['category'].'">Current: '.$cat['name'].'</option>';
                $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                for($i=1; $i<=$c; $i++) {
                    $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
                    echo '<option value="'.$i.'">'.$cat['name']."</option>\n";
                }
                echo '</select>
            </td>
        </tr>
        <tr>
            <td class="headSub">Count:</td><td valign="middle"><input type="text" name="count" value="'.$row['count'].'" style="width: 90%;" /></td>
            <td class="headSub">Worth:</td><td valign="middle"><input type="text" name="worth" value="'.$row['worth'].'" style="width: 90%;" /></td>
        </tr>
        <tr>
            <td class="headSub">Puzzle?</td>
            <td valign="middle">';
                if ($row['puzzle'] == "Yes") {
                    echo '<input type="radio" value="Yes" name="puzzle" checked> Yes &nbsp;&nbsp;&nbsp; <input type="radio" value="No" name="puzzle"> No';
                } else {
                    echo '<input type="radio" value="Yes" name="puzzle"> Yes &nbsp;&nbsp;&nbsp; <input type="radio" value="No" name="puzzle" checked> No';
                }
            echo '</td>
            <td class="headSub">Masterable?</td>
            <td valign="middle">';
                if ($row['masterable'] == "Yes") {
                    echo '<input type="radio" value="Yes" name="masterable" checked> Yes &nbsp;&nbsp;&nbsp; 
                    <input type="radio" value="No" name="masterable"> No';
                } else {
                    echo '<input type="radio" value="Yes" name="masterable"> Yes &nbsp;&nbsp;&nbsp; 
                    <input type="radio" value="No" name="masterable" checked> No';
                }
            echo '</td>
        </tr>
        <tr><td class="headSub">Masters:</td><td colspan="3" valign="middle"><textarea name="masters" rows="5" style="width: 96%;">'.$row['masters'].'</textarea></td></tr>
        <tr>
            <td class="headSub">Status:</td>
            <td valign="middle">';
                if ($row['status'] == "Upcoming") {
                    echo '<input type="radio" value="Upcoming" name="status" checked> Upcoming &nbsp;&nbsp;&nbsp; 
                    <input type="radio" value="Active" name="status"> Active';
                } else {
                    echo '<input type="radio" value="Upcoming" name="status"> Upcoming &nbsp;&nbsp;&nbsp; 
                    <input type="radio" value="Active" name="status" checked> Active';
                }
            echo '</td>
            <td valign="middle" colspan="4" align="center"><input type="submit" name="submit" class="btn-success" value="Edit Deck" /></td></tr>
        </table>
        </form>';
    }
}
?>