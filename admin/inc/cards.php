<?php
/********************************************************
 * Sub Page:        Upcoming Deck
 * Description:     Show page for the upcoming deck list
 */
if( $sub == "upcoming-decks" ) {
    if( empty($act) ) {
        if( isset($_POST['mass-release']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $date = date('Y-m-d');
                $release = $database->query("UPDATE `tcg_cards` SET `card_status`='Active', `card_released`='$date', `card_votes`='0' WHERE `card_id`='$id'");
    
                // Set activity record
                $row = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `card_id`='$id'");
                $activity = '<span class="fas fa-paper-plane" aria-hidden="true"></span> <a href="'.$tcgurl.'/members.php?id='.$player.'">'.$player.'</a> released the <a href="'.$tcgurl.'/cards.php?view=released&deck='.$row['card_filename'].'">'.$row['card_deckname'].'</a> deck.';
                $database->query("INSERT INTO `tcg_activities` (`act_name`,`act_rec`,`act_date`) VALUES ('$player','$activity','$date')");
            }
    
            if( !$release ) { $error[] = "Sorry, there was an error and the card decks were not released. ".mysqli_error().""; }
            else { $success[] = "The card decks were released successfully!"; }
        }
        
        if( isset($_POST['mass-delete']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $delete = $database->query("DELETE FROM `tcg_cards` WHERE `card_id`='$id'");
    
                // Delete activity log
                $sql = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `card_id`='$id'");
                $activity = $database->query("DELETE FROM `tcg_activities` WHERE `act_type`='upcoming' AND `act_slug`='".$sql['card_filename']."'");
            }
    
            if( !$delete ) { $error[] = "Sorry, there was an error and the card decks were not deleted. ".mysqli_error().""; }
            else { $success[] = "The card decks were deleted successfully!"; }
        }

        $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Upcoming' ORDER BY `card_votes` DESC");
        $count = mysqli_num_rows($sql);
    
        echo '<h1>Upcoming Deck Administration</h1>
        <p>Do you want to <a href="'.$PHP_SELF.'?mod=cards&action=add-upcoming">add an upcoming deck</a>?</p>
        
        <center>';
        if( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }
        echo '</center>';

        if( $count == 0 ) {
            echo "<p>There are currently no upcoming decks.</p>\n";
        } else {
            echo '<form method="post" action="'.$PHP_SELF.'?mod=cards&sub=upcoming-decks">
            <table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <td width="5%"></td>
                    <td width="5%">ID</td>
                    <td width="45%">Filename</td>
                    <td width="10%">Category</td>
                    <td width="5%">Votes</td>
                    <td width="15%">Action</td>
                </tr>
            </thead>
            <tbody>';
            while( $row = mysqli_fetch_assoc($sql) ) {
                $c = $row['card_cat'];
                $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$c'");
                echo '<tr>
                    <td align="center"><input type="checkbox" name="id[]" value="'.$row['card_id'].'" /></td>
                    <td align="center">'.$row['card_id'].'</td>
                    <td align="center">'.$row['card_deckname'].' ('.$row['card_filename'].')</td>
                    <td align="center">'.$cat['cat_name'].'</td>
                    <td align="center">'.$row['card_votes'].'</td>
                    <td align="center">
                        <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&sub=upcoming-decks&action=release&id='.$row['card_id'].'\';" class="btn-default"><span class="fas fa-check" aria-hidden="true"></span></button> 
                        <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&sub=upcoming-decks&action=edit&id='.$row['card_id'].'\';" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button>
                        <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&sub=upcoming-decks&action=delete&id='.$row['card_id'].'\';" class="btn-cancel"><span class="fas fa-trash-alt" aria-hidden="true"></span></button>
                    </td>
                </tr>';
            }
            echo '<tr>
                <td align="center"><span class="arrow-right">↳</span></td>
                <td colspan="5">With selected: 
                    <input type="submit" name="mass-release" value="Release" class="btn-default" />
                    <input type="submit" name="mass-delete" value="Delete" class="btn-cancel" />
                </td>
            <tr></tbody>
            </table>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Delete Upcoming Decks
     * Description:     Show page of deleting an upcoming deck
     */
    if( $act == "delete" ) {
        if ( isset($_POST['delete']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $delete = $database->query("DELETE FROM `tcg_cards` WHERE `card_id`='$id'");
            if( !$delete ) { $error[] = "Sorry, there was an error and the card deck was not deleted. ".mysqli_error().""; }
            else { $success[] = "The card was successfully deleted!"; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            echo '<h1>Delete an Upcoming Deck</h1>
            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>
            <form method="post" action="'.$PHP_SELF.'?mod=cards&sub=upcoming-decks&action=delete&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <p>Are you sure you want to delete this card deck? <b>This action can not be undone!</b><br />
            Click on the button below to delete the card deck:<br />
            <input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Edit Upcoming Decks
     * Description:     Show page of editing an upcoming deck
     */
    if( $act == "edit" ) {
        if( isset($_POST['update']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $filename = $sanitize->for_db($_POST['filename']);
            $deckname = $sanitize->for_db($_POST['deckname']);
            $donator = $sanitize->for_db($_POST['donator']);
            $maker = $sanitize->for_db($_POST['maker']);
            $color = $sanitize->for_db($_POST['color']);
            $puzzle = $sanitize->for_db($_POST['puzzle']);
            $cat = $sanitize->for_db($_POST['category']);
            $count = $sanitize->for_db($_POST['count']);
            $worth = $sanitize->for_db($_POST['worth']);
            $break = $sanitize->for_db($_POST['break']);
            $mast = $sanitize->for_db($_POST['masterable']);
            $masters = $sanitize->for_db($_POST['masters']);
            $status = $sanitize->for_db($_POST['status']);
            $desc = $_POST['entry'];
            $set = $_POST['set'];
            $desc = nl2br($desc);
            $desc = str_replace("'","\'",$desc);
            $set = str_replace("'","\'",$set);

            $update = $database->query("UPDATE `tcg_cards` SET `card_filename`='$filename', `card_deckname`='$deckname', `card_donator`='$donator', `card_maker`='$maker', `card_color`='$color', `card_desc`='$desc', `card_set`='$set', `card_cat`='$cat', `card_count`='$count', `card_worth`='$worth', `card_break`='$break', `card_mast`='$mast', `card_puzzle`='$puzzle', `card_masters`='$masters', `card_status`='$status' WHERE `card_id`='$id'");

            if( !$update ) { $error[] = "Sorry, there was an error and the card deck was not updated. ".mysqli_error().""; }
            else { $success[] = "The card deck was successfully updated in the database."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $row = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `card_id`='$id'");
            echo '<h1>Edit an Upcoming Deck</h1>
            <p>Use this form to edit a card deck in the database.<br />
            Use the <a href="'.$PHP_SELF.'?mod=cards&action=add-upcoming">add</a> form to add new upcoming decks.</p>

            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }

            echo '<form method="post" action="'.$PHP_SELF.'?mod=cards&sub=upcoming-decks&action=edit&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <table width="100%" cellpadding="5" cellspacing="0" border="0">
            <tr>
                <td width="68%" valign="top">
                    <b>Deck Name:</b><br />
                    <input type="text" name="deckname" value="'.$row['card_deckname'].'" style="width:96%;" /><br /><br />

                    <b>File Name:</b><br />
                    <input type="text" name="filename" value="'.$row['card_filename'].'" style="width:96%;" /><br /><br />

                    <b>Deck Information:</b><br />
                    <input type="text" name="maker" value="'.$row['card_maker'].'" style="width:46%;" />
                    <input type="text" name="donator" value="'.$row['card_donator'].'" style="width:46%;" /><br /><br />

                    <b>Description:</b><br />';
                    include('theme/text-editor.php');
                    echo '<textarea name="entry" id="entry" class="textEditor" rows="10" style="width:96%;" />'.$row['card_desc'].'</textarea><br />
                    <small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to.</i></small><br /><br />

                    <b>Masters:</b><br />
                    <input type="text" name="masters" value="'.$row['card_masters'].'" style="width:96%;" />
                </td>

                <td width="2%">&nbsp;</td>

                <td width="30%" valign="top">
                    <b>Status:</b><br />';
                    if( $row['card_status'] == "Upcoming" ) {
                        echo '<input type="radio" value="Upcoming" name="status" checked> Upcoming &nbsp;&nbsp;&nbsp; 
                        <input type="radio" value="Active" name="status"> Active';
                    } else {
                        echo '<input type="radio" value="Upcoming" name="status"> Upcoming &nbsp;&nbsp;&nbsp; 
                        <input type="radio" value="Active" name="status" checked> Active';
                    }
                    echo '<br /><br />

                    <b>Category:</b><br />
                    <select name="category" style="width:93%;">';
                    $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='".$row['card_cat']."'");
                    echo '<option value="'.$row['card_cat'].'">Current: '.$cat['cat_name'].'</option>';
                    $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                    for($i=1; $i<=$c; $i++) {
                        $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
                        echo '<option value="'.$i.'">'.$cat['cat_name']."</option>\n";
                    }
                    echo '</select><br /><br />

                    <b>Set / Series:</b><br />
                    <select name="set" style="width:93%;">';
                    $set = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_name`='".$row['card_set']."'");
                    echo '<option value="'.$set['set_name'].'">Current: '.$row['card_set'].'</option>';
                    $s = $database->num_rows("SELECT * FROM `tcg_cards_set`");
                    for( $i=1; $i<=$s; $i++ ) {
                        $set = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_id`='$i'");
                        echo '<option value="'.$set['set_name'].'">'.$set['set_name']."</option>\n";
                    }
                    echo '</select><br /><br />

                    <b>Count / Worth / Break:</b><br />
                    <input type="text" name="count" value="'.$row['card_count'].'" style="width:23%;" />
                    <input type="text" name="worth" value="'.$row['card_worth'].'" style="width:23%;" />
                    <input type="text" name="break" value="'.$row['card_break'].'" style="width:23%;" /><br /><br />

                    <b>Deck Color:</b><br />
                    <input type="text" name="color" value="'.$row['card_color'].'" style="width:86%;" /><br /><br />';

                    if ($row['card_puzzle'] == "Yes") {
                        echo '<b>Puzzle?</b> <input type="radio" value="Yes" name="puzzle" checked> Yes 
                        <input type="radio" value="No" name="puzzle"> No';
                    } else {
                        echo '<b>Puzzle?</b> <input type="radio" value="Yes" name="puzzle"> Yes 
                        <input type="radio" value="No" name="puzzle" checked> No';
                    }

                    echo '<br />';

                    if ($row['card_mast'] == "Yes") {
                        echo '<b>Masterable?</b> <input type="radio" value="Yes" name="masterable" checked> Yes 
                        <input type="radio" value="No" name="masterable"> No';
                    } else {
                        echo '<b>Masterable?</b> <input type="radio" value="Yes" name="masterable"> Yes 
                        <input type="radio" value="No" name="masterable" checked> No';
                    }

                    echo '<div align="right" style="margin-top:20px;">
                        <input type="submit" name="update" class="btn-success" value="Edit Deck" /> 
                        <input type="reset" name="reset" class="btn-cancel" value="Reset" />
                    </div>
                </td>
            </tr>
            </table>
            </form>
            </center>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Release Upcoming Decks
     * Description:     Process form of releasing an upcoming deck
     */
    if( $act == "release" ) {
        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $released = date('Y-m-d');
            $update = $database->query("UPDATE `tcg_cards` SET `card_status`='Active', `card_released`='$released', `card_votes`='0' WHERE `card_id`='$id'");
            $row = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `card_id`='$id'");

            if( !$update ) {
                echo '<h1>Release a Deck</h1><p>Sorry, there was an error and the card deck was not released.</p>';
                die("Error:". mysqli_error());
            } else {
                $activity = '<span class="fas fa-paper-plane" aria-hidden="true"></span> <a href="'.$tcgurl.'/members.php?id='.$player.'">'.$player.'</a> released the <a href="'.$tcgurl.'/cards.php?view=released&deck='.$row['card_filename'].'">'.$row['card_deckname'].'</a> deck.';
                $database->query("INSERT INTO `tcg_activities` (`act_name`,`act_rec`,`act_date`) VALUES ('$player','$activity','$released')");
                echo '<h1>Release a Deck</h1><p>The card deck was successfully added to the released decks and was deleted from the upcoming list.<br />
                Want to <a href="'.$PHP_SELF.'?mod=cards&sub=upcoming-decks">release</a> more decks?</p>';
            }
        }
    }
} // end sub page (upcoming)



/********************************************************
 * Sub Page:        Donated Decks
 * Description:     Show page for the donated deck list
 */
else if( $sub == "donated-decks" ) {
    if( empty($act) ) {
        if( isset($_POST['mass-claim']) ) {
            $getID = $_POST['id'];
            $maker = $_POST['maker'];

            foreach( $getID as $id ) {
                $claim = $database->query("UPDATE `tcg_donations` SET `deck_maker`='$maker' WHERE `deck_id`='$id'");
            }

            if( !$claim ) { $error[] = "Sorry, there was an error and the deck was not updated. ".mysqli_error().""; }
            else { $success[] = "You have claimed to make the deck!"; }
        }
        
        if( isset($_POST['mass-delete']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $delete = $database->query("DELETE FROM `tcg_donations` WHERE `deck_id`='$id'");
            }

            if( !$delete ) { $error[] = "Sorry, there was an error and the donated deck was not deleted. ".mysqli_error().""; }
            else { $success[] = "The donated deck was successfully deleted."; }
        }
        
        echo '<h1>Donated Decks</h1>
        <center>';
        if( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }
        echo '</center>';
        
        $sql = $database->query("SELECT * FROM `tcg_donations` ORDER BY `deck_date` ASC");
        $count = mysqli_num_rows($sql);
        if( $count == 0 ) {
            echo "<p>There are currently no donated decks.</p>\n";
        } else {
            echo '<form method="post" action="'.$PHP_SELF.'?mod=cards&sub=donated-decks">
            <input type="hidden" name="maker" value="'.$player.'" />
            <table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
            <thead>
            <tr>
                <td width="5%"></td>
                <td width="25%">Filename</td>
                <td width="8%">Maker</td>
                <td width="10%">Category</td>
                <td width="17%">Set/Series</td>
                <td width="10%">Date</td>
                <td width="18%">Action</td>
            </tr>
            </thead>
            <tbody>';
            while( $row = mysqli_fetch_assoc($sql) ) {
                $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='".$row['deck_cat']."'");
                echo '<tr>
                <td align="center"><input type="checkbox" name="id[]" value="'.$row['id'].'" /></td>
                <td align="center">'.$row['deck_filename'].'</td>
                <td align="center">'.$row['deck_maker'].'</td>
                <td align="center">'.$cat['cat_name'].'</td>
                <td align="center">'.$row['deck_set'].'</td>
                <td align="center">'.$row['deck_date'].'</td>
                <td align="center">
                    <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&sub=donated-decks&action=claim&id='.$row['deck_id'].'\';" class="btn-default"><span class="fas fa-user-tag" aria-hidden="true"></span></button> 
                    <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&sub=donated-decks&action=edit&id='.$row['deck_id'].'\';" class="btn-success"><span class="fas fa-cogs" aria-hidden="true"></span></button> 
                    <button type="button" onClick="window.location.href=\''.$row['deck_url'].'\';" target="_blank" class="btn-primary"><span class="fas fa-download" aria-hidden="true"></span></button>
                    <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&sub=donated-decks&action=delete&id='.$row['deck_id'].'\';" class="btn-cancel"><span class="fas fa-times" aria-hidden="true"></span></button>
                </td>
                </tr>';
            }
            echo '<tr>
                <td align="center"><span class="arrow-right">↳</span></td>
                <td colspan="6">With selected: 
                    <input type="submit" name="mass-claim" value="Claim" class="btn-default" />
                    <input type="submit" name="mass-delete" value="Delete" class="btn-cancel" />
                </td>
            <tr></tbody>
            </table>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Claim Donated Decks
     * Description:     Show page for claiming a donated deck
     */
    if( $act == "claim" ) {
        if( isset($_POST['claim']) ) {
            $id = $_POST['id'];
            $maker = $_POST['maker'];
            $update = $database->query("UPDATE `tcg_donations` SET `deck_maker`='$maker' WHERE `deck_id`='$id'");
            if( !$update ) { $error[] = "Sorry, there was an error and the deck was not updated. ".mysqli_error().""; }
            else { $success[] = "You have claimed to make the deck!"; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $sql = $database->get_assoc("SELECT * FROM `tcg_donations` WHERE `deck_id`='$id'");
            echo '<p>If you are a deck maker, use this form to claim a card deck to make from the database.</p>

            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=cards&sub=donated-decks&action=claim&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <input type="hidden" name="maker" value="'.$player.'" />
            <p>Are you sure you want to claim the <b>'.$sql['deck_name'].'</b> deck? <b>This action can not be undone!</b><br />
            Click on the button below to claim the card deck:<br />
            <input type="submit" name="claim" class="btn-success" value="Yes, I\'m making this deck!" /></p>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Delete Donated Decks
     * Description:     Show page for deleting a donated deck
     */
    if( $act == "delete" ) {
        if( isset($_POST['delete']) ) {
            $id = $_POST['id'];
            $delete = $database->query("DELETE FROM `tcg_donations` WHERE `deck_id`='$id'");
            if( !$delete ) { $error[] = "Sorry, there was an error and the donated deck hasn't been deleted. ".mysqli_error().""; }
            else { $success[] = "The donated deck was successfully deleted."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            echo '<center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=cards&sub=donated-decks&action=delete&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <p>Are you sure you want to delete this donated deck? <b>This action can not be undone!</b><br />
            Click on the button below to delete the card deck:<br />
            <input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Edit Donated Decks
     * Description:     Show page for editing a donated deck
     */
    if( $act == "edit" ) {
        if( isset($_POST['update']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $donator = $sanitize->for_db($_POST['donator']);
            $maker = $sanitize->for_db($_POST['maker']);
            $filename = $sanitize->for_db($_POST['filename']);
            $deckname = $sanitize->for_db($_POST['deckname']);
            $cat = $sanitize->for_db($_POST['category']);
            $set = $sanitize->for_db($_POST['set']);
            $url = $sanitize->for_db($_POST['url']);

            $update = $database->query("UPDATE `tcg_donations` SET `deck_donator`='$donator', `deck_maker`='$maker', `deck_filename`='$filename', `deck_feature`='$deckname', `deck_cat`='$cat', `deck_set`='$set', `deck_url`='$url' WHERE `deck_id`='$id'");

            if( !$update ) { $error[] = "Sorry, there was an error and the donated deck was not updated. ".mysqli_error().""; }
            else { $success[] = "The donated deck has been updated from the database!"; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $row = $database->get_assoc("SELECT * FROM `tcg_donations` WHERE `deck_id`='$id'");
            echo '<p>Use this form to edit an existing donated deck.<br />
            If you want to claim a donated deck to make, use the <a href="'.$PHP_SELF.'?mod=donated">claim form</a> instead.</p>

            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }

            echo '<form method="post" action="'.$PHP_SELF.'?mod=cards&sub=donated-decks&action=edit&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <table width="100%" cellspacing="4" cellpadding="0">
            <tr>
                <td width="15%"><b>File Name:</b></td>
                <td width="85%"><input type="text" name="filename" value="'.$row['deck_filename'].'" size="45" /></td>
            </tr>
            <tr>
                <td><b>Deck Name:</b></td>
                <td><input type="text" name="deckname" value="'.$row['deck_feature'].'" size="45" /></td>
            </tr>
            <tr>
                <td><b>Download URL:</b></td>
                <td><input type="text" name="url" value="'.$row['deck_url'].'" size="45" /></td>
            </tr>
            <tr>
                <td><b>Set/Series:</b></td>
                <td><select name="set" style="width:40%;">';
                $s = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_name`='".$row['deck_set']."'");
                echo '<option value="'.$s['set_name'].'">Current: '.$row['deck_set'].'</option>';
                $set = $database->num_rows("SELECT * FROM `tcg_cards_set`");
                for( $i=1; $i<=$set; $i++ ) {
                    $get = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_id`='$i'");
                    echo '<option value="'.$get['set_name'].'">'.$get['set_name'].'</option>';
                }
                echo '</select></td>
            </tr>
            <tr>
                <td><b>Category:</b></td>
                <td><select name="category" style="width:40%;">';
                    $c = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='".$row['deck_cat']."'");
                    echo '<option value="'.$c['cat_id'].'">Current: '.$c['cat_name'].'</option>';
                    $cat = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                    for( $i=1; $i<=$cat; $i++ ) {
                        $get = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
                        echo '<option value="'.$get['cat_id'].'">'.$get['cat_name'].'</option>';
                    }
                    echo '</select>
                </td>
            </tr>
            <tr>
                <td><b>Donator:</b></td>
                <td><input type="text" name="donator" value="'.$row['deck_donator'].'" size="45" readonly /></td>
            </tr>
            <tr>
                <td><b>Maker:</b></td>
                <td><input type="text" name="maker" value="'.$row['deck_maker'].'" size="45" readonly /></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="update" class="btn-success" value="Update Donation" /> 
                    <input type="reset" name="reset" class="btn-cancel" value="Reset" />
                </td>
            </tr>
            </table>
            </form>
            </center>';
        }
    }
} // end sub page (donated)




/********************************************************
 * Sub Page:        Event Cards
 * Description:     Show main page of event cards list
 */
else if( $sub == "event-cards" ) {
    if( empty($act) ) {
        if( isset($_POST['mass-delete']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $delete = $database->query("DELETE FROM `tcg_cards_event` WHERE `event_id`='$id'");
            }
            if( !$delete ) { $error[] = "Sorry, there was an error and the event cards were not deleted. ".mysqli_error().""; }
            else { $success[] = "The event cards were deleted successfully!"; }
        }
        
        echo '<h1>Event Cards</h1>
        <p>Do you want to <a href="'.$PHP_SELF.'?mod=cards&sub=event-cards&action=add">add an event card</a>?</p>';
        $sql = $database->query("SELECT * FROM `tcg_cards_event` ORDER BY `event_date` DESC");
        $count = $database->num_rows("SELECT * FROM `tcg_cards_event`");
        if( $count == 0 ) {
            echo "<p>There are currently no event cards added.</p>\n";
        } else {
            echo '<form method="post" action="'.$PHP_SELF.'?mod=cards&sub=event-cards">
            <table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
            <thead>
            <tr>
                <td width="5%"></td>
                <td width="5%">ID</td>
                <td width="30%">Filename</td>
                <td width="10%">Group</td>
                <td width="10%">Released</td>
                <td width="10%">Action</td>
            </tr>
            </thead>
            <tbody>';
            while( $row = mysqli_fetch_assoc($sql) ) {
                echo '<tr>
                <td align="center"><input type="checkbox" name="id[]" value="'.$row['event_id'].'" /></td>
                <td align="center">'.$row['event_id'].'</td>
                <td align="center">'.$row['event_title'].' ('.$row['event_filename'].')</td>
                <td align="center">'.$row['event_group'].'</td>
                <td align="center">'.$row['event_date'].'</td>
                <td align="center">
                    <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&sub=event-cards&action=edit&id='.$row['event_id'].'\';" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
                    <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&sub=event-cards&action=delete&id='.$row['event_id'].'\';" class="btn-cancel"><span class="fas fa-times" aria-hidden="true"></span></button>
                </td>
                </tr>';
            }
            echo '<tr>
                <td align="center"><span class="arrow-right">↳</span></td>
                <td colspan="5">With selected: <input type="submit" name="mass-delete" value="Delete" class="btn-cancel" /></td>
            <tr></tbody>
            </table>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Delete Event Cards
     * Description:     Show page for deleting event cards
     */
    if( $act == "delete" ) {
        if ( isset($_POST['delete']) ) {
            $id = $_POST['id'];
            $delete = $database->query("DELETE FROM `tcg_cards_event` WHERE `event_id`='$id'");
            if( !$delete ) { $error[] = "Sorry, there was an error and the event card was not deleted. ".mysqli_error().""; }
            else { $success[] = "The event card was successfully deleted."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            echo '<center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>
            <form method="post" action="'.$PHP_SELF.'?mod=cards&sub=event-cards&action=delete&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <p>Are you sure you want to delete this event card? <b>This action can not be undone!</b><br />
            Click on the button below to delete the event card:<br />
            <input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Add Event Cards
     * Description:     Show page for adding event cards
     */
    if( $act == "add" ) {
        $img = (isset($_FILES['img']) ? $_FILES['img'] : null);
        $file = (isset($_GET['name']) ? $_GET['name'] : null);

        if( isset($_POST['add']) ) {
            $filename = $sanitize->for_db($_POST['filename']);
            $title = $sanitize->for_db($_POST['title']);
            $group = $sanitize->for_db($_POST['group']);
            $released = $_POST['year']."-".$_POST['month']."-".$_POST['day'];

            $img_desc = $uploads->reArrayFiles($img);
            $uploads->folderPath('images','cards');

            $insert = $database->query("INSERT INTO `tcg_cards_event` (`event_filename`,`event_title`,`event_group`,`event_date`) VALUES ('$filename','$title','$group','$released')");

            if( !$insert ) { $error[] = "Sorry, there was an error and the event card was not added. ".mysqli_error().""; }
            else { $success[] = "The event card was successfully added to the database!"; }
        }

        $current_month = date("F");
        $current_date = date("d");
        $current_year = date("Y");
        $cur_month = date("m");

        echo '<h1>Add an Event Card</h1>
        <p>Use this form to add an event card to the database.<br />
        Use the <a href="'.$PHP_SELF.'?mod=cards&sub=event-cards">edit</a> form to update information for existing event cards.</p>

        <center>';
        if ( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if ( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }

        echo '<form method="post" action="'.$PHP_SELF.'?mod=cards&sub=event-cards&action=add" multipart="" enctype="multipart/form-data">
        <table width="100%" cellpadding="8" cellspacing="0" border="0">
        <tr>
            <td width="17%" valign="middle"><b>Title:</b></td>
            <td width="83%"><input type="text" name="title" placeholder="e.g. Halloween 2021" size="30" /></td>
        </tr>
        <tr>
            <td valign="middle"><b>File Name:</b></td>
            <td><input type="text" name="filename" placeholder="e.g. ec-halloween2021" size="30" /></td>
        </tr>
        <tr>
            <td valign="middle"><b>Group:</b></td>
            <td>
                <select name="group" style="width:35%;" />
                    <option>--- Select Group ---</option>
                    <option value="Events & Holidays">Events & Holidays</option>
                    <option value="Layouts">Layouts</option>
                    <option value="Milestones">Milestones</option>
                    <option value="Monthly">Monthly</option>
                    <option value="Seasons">Seasons</option>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="middle"><b>Release Date:</b></td>
            <td>
                <select name="month" style="width:19%;">
                    <option value="'.$cur_month.'">'.$current_month.'</option>';
                    for($m=1; $m<=12; $m++) {
                        if ($m < 10) { $_mon = "0$m"; }
                        else { $_mon = $m; }
                        echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                    }
                echo '</select> 
                <input type="text" name="day" size="1" value="'.$current_date.'" /> ';
                $start = date('Y');
                $end = $start-10;
                $yearArray = range($start,$end);
                echo '<select name="year">
                <option value="'.$current_year.'">'.$current_year.'</option>';
                    foreach ($yearArray as $year) {
                        $selected = ($year == $start) ? 'selected' : '';
                        echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                    }
                echo '</select>
            </td>
        </tr>
        <tr>
            <td valign="middle"><b>Upload Card:</b></td>
            <td><input type="file" name="img[]" size="45"></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" name="add" class="btn-success" value="Add Event Card" /> 
                <input type="reset" name="reset" class="btn-cancel" value="Reset" />
            </td>
        </tr>
        </table>
        </form>
        </center>';
    }
    
    
    
    /********************************************************
     * Action:          Edit Event Cards
     * Description:     Show page for editing event cards
     */
    if( $act == "edit" ) {
        if( isset($_POST['update']) ) {
            $check->Value();
            $id = $sanitize->for_db($_POST['id']);
            $filename = $sanitize->for_db($_POST['filename']);
            $title = $sanitize->for_db($_POST['title']);
            $group = $sanitize->for_db($_POST['group']);
            $released = $_POST['year']."-".$_POST['month']."-".$_POST['day'];

            $update = $database->query("UPDATE `tcg_cards_event` SET `event_title`='$title', `event_filename`='$filename', `event_group`='$group', `event_date`='$released' WHERE `event_id`='$id'");

            if( !$update ) { $error[] = "Sorry, there was an error and the event card was not updated. ".mysqli_error().""; }
            else { $success[] = "The event card has been updated successfully!"; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $row = $database->get_assoc("SELECT * FROM `tcg_cards_event` WHERE `event_id`='$id'");
            $day = date("d", strtotime($row['event_date']));
            $mon = date("m", strtotime($row['event_date']));
            $mon2 = date("F", strtotime($row['event_date']));
            $year = date("Y", strtotime($row['event_date']));
            echo '<h1>Edit an Event Card</h1>
            <p>Use this form to edit an event card in the database.<br />
            Use the <a href="'.$PHP_SELF.'?mod=events&action=add">add</a> form to add a new event card.</p>

            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }

            echo '<form method="post" action="'.$PHP_SELF.'?mod=cards&sub=event-cards&action=edit&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <table width="100%" cellpadding="5" cellspacing="0" border="0">
            <tr>
                <td width="15%" valign="middle"><b>Title:</b></td>
                <td width="2%">&nbsp;</td>
                <td width="83%"><input type="text" name="title" value="'.$row['event_title'].'" size="39" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>File Name:</b></td>
                <td>&nbsp;</td>
                <td><input type="text" name="filename" value="'.$row['event_filename'].'" size="39" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>Group:</b></td>
                <td>&nbsp;</td>
                <td>
                    <select name="group" style="width:37%;" />
                        <option value="'.$row['event_group'].'">'.$row['event_group'].'</option>
                        <option value="Events & Holidays">Events & Holidays</option>
                        <option value="Layouts">Layouts</option>
                        <option value="Milestones">Milestones</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Seasons">Seasons</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td valign="middle"><b>Release Date:</b></td>
                <td>&nbsp;</td>
                <td>
                    <select name="month" style="width:19%;">
                        <option value="'.$mon.'">'.$mon2.'</option>';
                        for($m=1; $m<=12; $m++) {
                            if ($m < 10) { $_mon = "0$m"; }
                            else { $_mon = $m; }
                            echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                        }
                    echo '</select> 
                    <input type="text" name="day" size="3" value="'.$day.'" /> ';
                    $start = date('Y');
                    $end = $start-10;
                    $yearArray = range($start,$end);
                    echo '<select name="year">
                        <option value="'.$year.'">'.$year.'</option>';
                        foreach ($yearArray as $year) {
                            $selected = ($year == $start) ? 'selected' : '';
                            echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                        }
                    echo '</select>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <input type="submit" name="update" class="btn-success" value="Edit Event Card" /> 
                    <input type="reset" name="reset" class="btn-cancel" value="Reset" />
                </td>
            </tr>
            </table>
            </form>
            </center>';
        }
    }
} // end sub page (event cards)




/********************************************************
 * Moderation:      Cards
 * Description:     Show main page of cards list
 */
else {
    if( empty($act) ) {
        if( isset($_POST['withhold-deck']) ) {
            $getId = $_POST['id'];
            foreach( $getID as $id ) {
                $withhold = $database->query("UPDATE `tcg_cards` SET `card_status`='Upcoming' WHERE `card_id`='$id'");

                // Delete activity log
                $sql = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `card_id`='$id'");
                $activity = $database->query("DELETE FROM `tcg_activities` WHERE `act_type`='release' AND `act_slug`='".$sql['card_filename']."'");
            }
            if( !$withhold && !$activity ) {
                $error[] = "Sorry, there was an error and the card deck(s) was not moved back to the upcoming list. ".mysqli_error()."";
            } else {
                $success[] = "The card deck(s) was successfully moved back to the upcoming list!";
            }
        }
        
        if( isset($_POST['delete-deck']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $delete = $database->query("DELETE FROM `tcg_cards` WHERE `card_id`='$id'");

                // Delete activity log
                $sql = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `card_id`='$id'");
                $activity = $database->query("DELETE FROM `tcg_activities` WHERE `act_type`='upcoming' AND `act_slug`='".$sql['card_filename']."'");
            }
            if( !$delete ) { $error[] = "Sorry, there was an error and the card deck(s) was not deleted. ".mysqli_error().""; }
            else { $success[] = "The card deck(s) was successfully deleted from the database!"; }
        }

        echo '<h1>Cards Administration</h1>
        <p>Do you want to <a href="'.$PHP_SELF.'?mod=cards&action=add-upcoming">add an upcoming deck</a>?</p>
        <center><a href="'.$PHP_SELF.'?mod=cards&sub=upcoming-decks">View Upcoming Decks?</a> | <a href="'.$PHP_SELF.'?mod=cards&sub=donated-decks">View Donated Decks?</a>';
        if ( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if ( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }
        echo '</center>';

        $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
        for($i=1; $i<=$c; $i++) {
            $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `card_cat`='$i' AND `card_status`='Active' ORDER BY `card_filename`");
            $count = $database->num_rows("SELECT * FROM `tcg_cards` WHERE `card_cat`='$i' AND `card_status`='Active' ORDER BY `card_filename`");
            $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
            echo '<h2>'.$cat['cat_name']."</h2>\n";

            if( $count == 0 ) {
                echo "<p>There are currently no card decks in this category.</p>\n";
            } else {
                echo '<p>Please be careful when using the buttons below for mass withhold and mass delete when the checkboxes are selected. <b>This action can not be undone!</b></p>
                <form method="post" action="'.$PHP_SELF.'?mod=cards">
                <table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <td width="5%"></td>
                    <td width="5%">ID</td>
                    <td width="30%">Filename</td>
                    <td width="10%">Made/Donated by</td>
                    <td width="10%">Released</td>
                    <td width="5%"># / $</td>
                    <td width="10%">Action</td>
                </tr>
                </thead>
                <tbody>';
                while( $row = mysqli_fetch_assoc($sql) ) {
                    echo '<tr>
                    <td align="center"><input type="checkbox" name="id[]" value="'.$row['card_id'].'" /></td>
                    <td align="center">'.$row['card_id'].'</td>
                    <td align="center">'.$row['card_deckname'].' ('.$row['card_filename'].')</td>
                    <td align="center">'.$row['card_maker'].' / '.$row['card_donator'].'</td>
                    <td align="center">'.$row['card_released'].'</td>
                    <td align="center">'.$row['card_count'].'/'.$row['card_worth'].'</td>
                    <td align="center">
                        <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&action=edit&id='.$row['card_id'].'\';" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
                        <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=cards&action=delete&id='.$row['card_id'].'\';" class="btn-cancel"><span class="fas fa-times" aria-hidden="true"></span></button>
                    </td>
                    </tr>';
                }
                echo '<tr>
                    <td align="center"><span class="arrow-right">↳</span></td>
                    <td colspan="6">With selected: 
                        <input type="submit" name="withhold-deck" value="Withhold" class="btn-default" />
                        <input type="submit" name="delete-deck" value="Delete" class="btn-cancel" />
                    </td>
                </tr></tbody>
                </table>
                </form>';
            }
        }
    }



    /********************************************************
     * Action:          Add Cards
     * Description:     Show page for adding card decks
     */
    if( $act == "add-upcoming" ) {
        if( isset($_POST['add']) ) {
            $filename = $sanitize->for_db($_POST['filename']);
            $deckname = $sanitize->for_db($_POST['deckname']);
            $donator = $sanitize->for_db($_POST['donator']);
            $maker = $sanitize->for_db($_POST['maker']);
            $color = $sanitize->for_db($_POST['color']);
            $puzzle = $sanitize->for_db($_POST['puzzle']);
            $cat = $sanitize->for_db($_POST['category']);
            $count = $sanitize->for_db($_POST['count']);
            $worth = $sanitize->for_db($_POST['worth']);
            $break = $sanitize->for_db($_POST['break']);
            $mast = $sanitize->for_db($_POST['masterable']);
            $set = $_POST['set'];
            $desc = $_POST['entry'];
            $desc = nl2br($desc);

            $desc = str_replace("'","\'",$desc);
            $set = str_replace("'","\'",$set);

            $date = date("Y-m-d", strtotime("now"));

            $insert = $database->query("INSERT INTO `tcg_cards` (`card_filename`,`card_deckname`,`card_color`,`card_puzzle`,`card_desc`,`card_maker`,`card_donator`,`card_cat`,`card_set`,`card_count`,`card_worth`,`card_break`,`card_mast`,`card_masters`,`card_status`) VALUES ('$filename','$deckname','$color','$puzzle','$desc','$maker','$donator','$cat','$set','$count','$worth','$break','$mast','None','Upcoming')");

            // Insert acquited data if all queries are correct
            if( !$insert ) {
                $error[] = "Sorry, there was an error and the card deck was not added. ".mysqli_error()."";
            } else {
                $date = date("Y-m-d", strtotime("now"));
                $activity = '<span class="fas fa-plus-circle" aria-hidden="true"></span> <a href="'.$tcgurl.'/members.php?id='.$maker.'">'.$maker.'</a> added <a href="'.$tcgurl.'/cards.php?view=upcoming&deck='.$filename.'">'.$deckname.'</a> to the upcoming list.';
                $database->query("DELETE FROM `tcg_donations` WHERE `deck_filename`='$filename'");
                $database->query("INSERT INTO `tcg_activities` (`act_name`,`act_rec`,`act_date`) VALUES ('$maker','$activity','$date')");
                $database->query("INSERT INTO `user_rewards` (`rwd_name`,`rwd_type`,`rwd_subtitle`,`rwd_mcard`,`rwd_cards`,`rwd_currency`,`rwd_date`) VALUES ('$maker','Paycheck','(Deck Making: $filename)','No','".$settings->getValue('prize_deckmaker_reg')."','".$settings->getValue('prize_deckmaker_cur')."','$date')");

                $uploads->cards();
                $success[] = "The deck has been successfully added!";
            }
        }

        echo '<h1>Add an Upcoming Deck</h1>
        <p>Use this form to add an upcoming deck to the database. Use the <a href="'.$PHP_SELF.'?mod=cards">edit</a> form to update information for existing card decks.</p>
        <ul><li>Please make sure to zip all the cards of the deck you\'re going to add first.</li>
        <li>DO NOT put the cards into a folder before zipping! Otherwise it will become a sub folder in the <code>images/cards/</code> directory and the cards will not be displayed properly.</li></ul>

        <center>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        
        echo '<form method="post" action="'.$PHP_SELF.'?mod=cards&action=add-upcoming" multipart="" enctype="multipart/form-data">
        <table width="100%" cellpadding="5" cellspacing="0" border="0">
        <tr>
            <td width="68%" valign="top">
                <b>Deck Name:</b><br />
                <input type="text" name="deckname" style="width:96%;" /><br /><br />

                <b>File Name:</b><br />
                <input type="text" name="filename" style="width:96%;" /><br /><br />

                <b>Deck Information:</b><br />
                <input type="text" name="maker" style="width:46%;" placeholder="Maker" />
                <input type="text" name="donator" style="width:46%;" placeholder="Donator" /><br /><br />

                <b>Description:</b><br />';
                include('theme/text-editor.php');
                echo '<textarea name="entry" id="entry" class="textEditor" style="width:96%;" rows="10" /></textarea><br />
                <small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to.</i></small>
                </div>
            </td>

            <td width="2%">&nbsp;</td>

            <td width="30%" valign="top">
                <b>Category:</b><br />
                <select name="category" style="width:97%;">';
                $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                for($i=1; $i<=$c; $i++) {
                    $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
                    echo '<option value="'.$i.'">'.$cat['cat_name']."</option>\n";
                }
                echo '</select><br /><br />

                <b>Set / Series:</b><br />
                <select name="set" style="width:97%;">';
                $s = $database->num_rows("SELECT * FROM `tcg_cards_set`");
                for($i=1; $i<=$s; $i++) {
                    $set = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_id`='$i'");
                    echo '<option value="'.$set['set_name'].'">'.$set['set_name']."</option>\n";
                }
                echo '</select><br /><br />

                <b>Count / Worth / Break:</b><br />
                <input type="text" name="count" placeholder="count" style="width:23%;" />
                <input type="text" name="worth" placeholder="worth" style="width:23%;" />
                <input type="text" name="break" placeholder="break" style="width:23%;" /><br /><br />

                <b>Deck Color:</b><br />
                <input type="text" name="color" placeholder="e.g. DarkGoldenrod" style="width:90%;" /><br /><br />

                <b>Puzzle?</b> <input type="radio" name="puzzle" value="Yes" /> Yes 
                <input type="radio" value="No" name="puzzle" checked> No<br />

                <b>Masterable?</b> <input type="radio" value="Yes" name="masterable" checked> Yes 
                <input type="radio" value="No" name="masterable"> No<br /><br />

                <b>Upload Cards:</b><br />
                <input type="file" name="file" style="width:90%;" />

                <div align="right" style="margin-top:20px;">
                    <input type="submit" name="add" class="btn-success" value="Add Deck" /> 
                    <input type="reset" name="reset" class="btn-cancel" value="Reset" />
                </div>
            </td>
        </tr>
        </table>
        </form>
        </center>';
    }



    /********************************************************
     * Action:          Delete Cards
     * Description:     Show page for deleting card decks
     */
    if( $act == "delete" ) {
        if ( isset($_POST['delete']) ) {
            $id = $_POST['id'];
            $delete = $database->query("DELETE FROM `tcg_cards` WHERE `card_id`='$id'");
            if( !$delete ) { $error[] = "Sorry, there was an error and the card deck was not deleted. ".mysqli_error().""; }
            else { $success[] = "The card deck was successfully deleted."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            echo '<h1>Delete a Deck</h1>
            <center>';
            if ( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if ( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=cards&action=delete&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <p>Are you sure you want to delete this card deck? <b>This action can not be undone!</b><br />
            Click on the button below to delete the card deck:<br />
            <input type="submit" name="delete" class="btn-cancel" value="Delete">
            </form>';
        }
    }



    /********************************************************
     * Action:          Edit Cards
     * Description:     Show page for editing card decks
     */
    if( $act == "edit" ) {
        if ( isset($_POST['edit']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $filename = $sanitize->for_db($_POST['filename']);
            $deckname = $sanitize->for_db($_POST['deckname']);
            $donator = $sanitize->for_db($_POST['donator']);
            $maker = $sanitize->for_db($_POST['maker']);
            $color = $sanitize->for_db($_POST['color']);
            $puzzle = $sanitize->for_db($_POST['puzzle']);
            $cat = $sanitize->for_db($_POST['category']);
            $count = $sanitize->for_db($_POST['count']);
            $worth = $sanitize->for_db($_POST['worth']);
            $break = $sanitize->for_db($_POST['break']);
            $mast = $sanitize->for_db($_POST['masterable']);
            $masters = $sanitize->for_db($_POST['masters']);
            $status = $sanitize->for_db($_POST['status']);
            $set = $_POST['set'];
            $desc = $_POST['entry'];
            $desc = nl2br($desc);

            $desc = str_replace("'","\'",$desc);
            $set = str_replace("'","\'",$set);

            $update = $database->query("UPDATE `tcg_cards` SET `card_filename`='$filename', `card_deckname`='$deckname', `card_donator`='$donator', `card_maker`='$maker', `card_color`='$color', `card_puzzle`='$puzzle', `card_desc`='$desc', `card_set`='$set', `card_cat`='$category', `card_count`='$count', `card_worth`='$worth', `card_break`='$break', `card_mast`='$mast', `card_masters`='$masters', `card_status`='$status' WHERE `card_id`='$id'");

            if( !$update ) { $error[] = "Sorry, there was an error and the card deck was not updated. ".mysqli_error().""; }
            else { $success[] = "The card deck was successfully updated in the database."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $row = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `card_id`='$id'");
            echo '<h1>Edit a Deck</h1>
            <p>Use this form to edit a card deck in the database.<br />
            Use the <a href="'.$PHP_SELF.'?mod=cards&action=add">add</a> form to add new card decks.</p>

            <center>';
            if ( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if ( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }

            echo '<form method="post" action="'.$PHP_SELF.'?mod=cards&action=edit&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <table width="100%" cellpadding="5" cellspacing="0" border="0">
            <tr>
                <td width="68%" valign="top">
                    <b>Deck Name:</b><br />
                    <input type="text" name="deckname" value="'.$row['card_deckname'].'" style="width:96%;" /><br /><br />

                    <b>File Name:</b><br />
                    <input type="text" name="filename" value="'.$row['card_filename'].'" style="width:96%;" /><br /><br />

                    <b>Deck Information:</b><br />
                    <input type="text" name="maker" value="'.$row['card_maker'].'" style="width:46%;" />
                    <input type="text" name="donator" value="'.$row['card_donator'].'" style="width:46%;" /><br /><br />

                    <b>Description:</b><br />';
                    include('theme/text-editor.php');
                    echo '<textarea name="entry" id="entry" class="textEditor" rows="10" style="width:96%;" />'.$row['card_desc'].'</textarea><br />
                    <small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to.</i></small><br /><br />

                    <b>Masters:</b><br />
                    <input type="text" name="masters" value="'.$row['card_masters'].'" style="width:96%;" />
                    </div>
                </td>

                <td width="2%">&nbsp;</td>

                <td width="30%" valign="top">
                    <b>Status:</b><br />';
                    if( $row['card_status'] == "Active" ) {
                        echo '<input type="radio" value="Upcoming" name="status"> Upcoming &nbsp;&nbsp;&nbsp; 
                        <input type="radio" value="Active" name="status" checked> Active';
                    } else {
                        echo '<input type="radio" value="Upcoming" name="status" checked> Upcoming &nbsp;&nbsp;&nbsp; 
                        <input type="radio" value="Active" name="status"> Active';
                    }
                    echo '<br /><br />

                    <b>Category:</b><br />
                    <select name="category" style="width:90%;">';
                    $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='".$row['card_cat']."'");
                    echo '<option value="'.$row['card_cat'].'">Current: '.$cat['cat_name'].'</option>';
                    $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                    for($i=1; $i<=$c; $i++) {
                        $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
                        echo '<option value="'.$i.'">'.$cat['cat_name']."</option>\n";
                    }
                    echo '</select><br /><br />

                    <b>Set / Series:</b><br />
                    <select name="set" style="width:90%;">';
                    $set = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_name`='".$row['card_set']."'");
                    echo '<option value="'.$row['card_set'].'">Current: '.$set['set_name'].'</option>';
                    $s = $database->num_rows("SELECT * FROM `tcg_cards_set`");
                    for($i=1; $i<=$s; $i++) {
                        $set = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_id`='$i'");
                        echo '<option value="'.$set['set_name'].'">'.$set['set_name']."</option>\n";
                    }
                    echo '</select><br /><br />

                    <b>Count / Worth / Break:</b><br />
                    <input type="text" name="count" value="'.$row['card_count'].'" style="width:23%;" />
                    <input type="text" name="worth" value="'.$row['card_worth'].'" style="width:23%;" />
                    <input type="text" name="break" value="'.$row['card_break'].'" style="width:23%;" /><br /><br />

                    <b>Deck Color:</b><br />
                    <input type="text" name="color" value="'.$row['card_color'].'" style="width:90%;" /><br /><br />';

                    if ($row['card_puzzle'] == "Yes") {
                        echo 'Puzzle? <input type="radio" value="Yes" name="puzzle" checked> Yes 
                        <input type="radio" value="No" name="puzzle"> No';
                    } else {
                        echo 'Puzzle? <input type="radio" value="Yes" name="puzzle"> Yes 
                        <input type="radio" value="No" name="puzzle" checked> No';
                    }

                    echo '&nbsp;&nbsp;&nbsp;';

                    if ($row['card_mast'] == "Yes") {
                        echo 'Masterable? <input type="radio" value="Yes" name="masterable" checked> Yes 
                        <input type="radio" value="No" name="masterable"> No';
                    } else {
                        echo 'Masterable? <input type="radio" value="Yes" name="masterable"> Yes 
                        <input type="radio" value="No" name="masterable" checked> No';
                    }

                    echo '<div align="right" style="margin-top:20px;">
                        <input type="submit" name="edit" class="btn-success" value="Edit Deck" /> 
                        <input type="reset" name="reset" class="btn-cancel" value="Reset" />
                    </div>
                </td>
            </tr>
            </table>
            </form>
            </center>';
        }
    }
}
?>