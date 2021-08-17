<?php
/********************************************************
 * Moderation:		Freebies
 * Description:		Show main page of freebies list
 */
if( empty($act) ) {
    if( isset($_POST['mass-delete']) ) {
        $getID = $_POST['id'];
        foreach( $getID as $id ) {
            $delete = $database->query("DELETE FROM `user_freebies` WHERE `free_id`='$id'");
        }
		if( !$delete ) { $error[] = "Sorry, there was an error and the freebies were not deleted from the database. ".mysqli_error().""; }
		else { $success[] = "The freebies has been deleted from the database!"; }
    }
    
	$select = $database->query("SELECT * FROM `user_freebies` ORDER BY `free_date`");

	echo '<h1>Freebies</h1>
	<p>This page gives you, as an Admin of your TCG, an opportunity to give out freebies to your players which is different from the user wishes. Although it has the same structure as the user wishes, at least this one is personally from you.</p>
	<p>&raquo; Do you want to <a href="'.$PHP_SELF.'?mod=freebies&action=add">add a freebie</a>?</p>
	
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	echo '</center>

	<form method="post" action="'.$PHP_SELF.'?mod=freebies">
	<table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
	<thead>
	<tr>
		<td width="5%"></td>
		<td width="10%">Type</td>
		<td width="10%">Word</td>
		<td width="10%">Category</td>
		<td width="10%">Amount</td>
		<td width="10%">Date</td>
		<td width="10%">Action</td>
	</tr>
	</thead>
	<tbody>';
	while( $row = mysqli_fetch_assoc($select) ) {
		echo '<tr>
		<td align="center"><input type="checkbox" name="id[]" value="'.$row['free_id'].'" /></td>
        <td align="center">'.$row['free_type'].'</td>
		<td align="center">'.$row['free_word'].'</td>
		<td align="center">'.$row['free_cat'].'</td>
		<td align="center">'.$row['free_amount'].'</td>
		<td align="center">'.$row['free_date'].'</td>
		<td align="center">
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=freebies&action=edit&id='.$row['free_id'].'\';" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=freebies&action=delete&id='.$row['free_id'].'\';" class="btn-cancel"><span class="fas fa-trash-alt" aria-hidden="true"></span></button>
		</td>
		</tr>';
	}
	echo '<tr>
		<td align="center"><span class="arrow-right">↳</span></td>
		<td colspan="6">With selected: <input type="submit" name="mass-delete" class="btn-cancel" value="Delete" /></td>
	<tr>
	</tbody>
	</table>
	</form>';
}



/********************************************************
 * Action:			Add Freebies
 * Description:		Show page for adding freebies
 */
if( $act == "add" ) {
	if ( isset($_POST['add']) ) {
		$type = $sanitize->for_db($_POST['type']);
		$word = $sanitize->for_db($_POST['word']);
		$amnt = $sanitize->for_db($_POST['amount']);
		$cat = $sanitize->for_db($_POST['category']);
		$date = date('Y-m-d', strtotime("now"));

		$result = $database->query("INSERT INTO `user_freebies` (`free_type`,`free_word`,`free_amount`,`free_cat`,`free_date`) VALUES ('$type','$word','$amnt','$cat','$date')") or print ("Can't update freebies.<br />" . mysqli_connect_error());

		if ( !$result ) { $error[] = "Sorry, there was an error and the freebie was not added to the database. ".mysqli_error().""; }
		else { $success[] = "You have successfully added a freebie!"; }
	}

	echo '<h1>Add a Freebie</h1>
	<p>Make sure to only fill out the fields you need (e.g. spell word field for spell choice).</p>
	
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=freebies&action=add">
	<table width="100%" cellspacing="0" cellpadding="5">
	<tr>
		<td width="20%" valign="middle"><b>Wish Type:</b></td>
		<td width="2%">&nbsp;</td>
		<td width="78%">
			<select name="type" id="type" style="width:43%;" />
				<option value="1">Spell Choice</option>
				<option value="2">Choice Pack</option>
				<option value="3">Random Pack</option>
				<option value="4">Category Choice</option>
			</select>
		</td>
	</tr>
	<tr>
        <td valign="middle"><b>Spell Word:</b></td>
        <td>&nbsp;</td>
        <td><input type="text" name="word" id="word" size="43" placeholder="SUMMER2020" /></td>
	</tr>
	<tr>
		<td valign="middle"><b>Category Choice:</b></td>
		<td>&nbsp;</td>
		<td>
			<select name="category" id="category" style="width:43%;">
				<option value="0">Not applicable</option>';
				$c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
				for($i=1; $i<=$c; $i++) {
					$cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_name`='$i'");
					echo '<option value="'.$i.'">'.$cat['cat_name']."</option>\n";
				}
			echo '</select>
		</td>
	</tr>
	<tr>
        <td valign="middle"><b>Choice/Random Amount:</b></td>
        <td>&nbsp;</td>
        <td><input type="text" name="amount" id="amount" size="43" placeholder="0" /></td>
	</tr>
	<tr>
        <td colspan="3">
            <input type="submit" name="add" id="add" class="btn-success" value="Add Freebie" /> 
			<input type="reset" name="reset" id="reset" class="btn-cancel" value="Reset" />
		</td>
	</tr>
	</table>
	</form>
	</center>';
}



/********************************************************
 * Action:			Delete Freebies
 * Description:		Show page for deleting freebies
 */
if( $act == "delete" ) {
	if ( isset($_POST['delete']) ) {
		$id = $_POST['id'];
		$delete = $database->query("DELETE FROM `user_freebies` WHERE `free_id`='$id'");

		if( !$delete ) { $error[] = "Sorry, there was an error and the freebie was not deleted. ".mysqli_error().""; }
		else { $success[] = "The freebie has been deleted from the database!"; }
	}

	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		$getdata = $database->query("SELECT * FROM `user_freebies` WHERE `free_id`='$id'");
		echo '<h1>Delete a Freebie</h1>
		<center>';
		if( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}
		if( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}
		echo '</center>
		
		<form method="post" action="'.$PHP_SELF.'?mod=freebies&action=delete">
		<input type="hidden" name="id" value="'.$id.'" />
		<p>Are you sure you want to delete this freebie? <b>This action can not be undone!</b><br />
		Click on the button below to delete the freebie:<br />
		<input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
		</form>';
	}
}



/********************************************************
 * Action:			Edit Freebies
 * Description:		Show page for editing freebies
 */
if( $act == "edit" ) {
	if( isset($_POST['update']) ) {
		$type = $sanitize->for_db($_POST['type']);
		$word = $sanitize->for_db($_POST['word']);
		$amnt = $sanitize->for_db($_POST['amount']);
		$cat = $sanitize->for_db($_POST['category']);
		$id = $sanitize->for_db($_POST['id']);
		
		$result = $database->query("UPDATE `user_freebies` SET `free_name`='$name', `free_type`='$type', `free_word`='$word', `free_amount`='$amnt', `free_cat`='$cat' WHERE `free_id`='$id'") or print ("Can't update freebies.<br />" . mysqli_connect_error());

		if( !$result ) { $error[] = "Sorry, there was an error and the freebie was not updated. ".mysqli_error().""; }
		else { $success[] = "You have successfully updated the freebie!"; }
	}

	if ( !isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id']) ) { die("Invalid freebie ID."); }
	else { $id = (int)$_GET['id']; }

	$row = $database->get_assoc("SELECT * FROM `user_freebies` WHERE `free_id`='$id'") or print ("Can't select freebie.<br />" . $row . "<br />" . mysqli_connect_error());
	$old_type = stripslashes($row['free_type']);
	$old_word = stripslashes($row['free_word']);
	$old_amnt = stripslashes($row['free_amount']);
	$old_cat = stripslashes($row['free_cat']);

	echo '<h1>Edit a Freebie</h1>
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=freebies&action=edit&id='.$id.'">
	<input type="hidden" name="id" value="'.$id.'" />
	<table width="100%" cellspacing="0" cellpadding="5">
	<tr>
		<td width="20%" valign="middle"><b>Wish Type:</b></td>
		<td width="2%">&nbsp;</td>
		<td width="78%">
			<select name="type" id="type" style="width:43%;" />
				<option value="'.$old_type.'">Current: '.$old_type.'</option>
				<option value="1">Spell Choice</option>
				<option value="2">Choice Pack</option>
				<option value="3">Random Pack</option>
				<option value="4">Category Choice</option>
			</select>
		</td>
	</tr>
	<tr>
        <td valign="middle"><b>Spell Word:</b></td>
        <td>&nbsp;</td>
        <td><input type="text" name="word" id="word" size="40" value="'.$old_word.'" /></td>
	</tr>
	<tr>
		<td valign="middle"><b>Category Choice:</b></td>
		<td>&nbsp;</td>
		<td>
			<select name="category" id="category" style="width:43%;" />';
			if( $old_color == 0 ) { echo '<option value="0">Current: None</option>'; }
			else {
				$get = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='".$old_cat."'");
				echo '<option value="'.$get['cat_id'].'">Current: '.$get['cat_name'].'</option>';
			}
			$c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
			for($i=1; $i<=$c; $i++) {
				$cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
				echo '<option value="'.$i.'">'.$cat['cat_name']."</option>\n";
			}
			echo '</select>
		</td>
	</tr>
	<tr>
        <td valign="middle"><b>Choice/Random Amount:</b></td>
        <td>&nbsp;</td>
        <td><input type="text" name="amount" id="amount" size="40" value="'.$old_amnt.'" /></td>
	</tr>
	<tr>
        <td colspan="3">
            <input type="submit" name="update" id="update" class="btn-success" value="Edit Freebie" /> 
			<input type="reset" name="reset" id="reset" class="btn-cancel" value="Reset" />
		</td>
	</tr>
	</table>
	</form>
	</center>';
}
?>