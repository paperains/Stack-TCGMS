<?php
/********************************************************
 * Moderation:		Blog Posts
 * Description:		Show main page of blog post list
 */
if( empty($act) ) {
	// Mass delete blog posts
	if( isset($_POST['mass-delete']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$delete = $database->query("DELETE FROM `tcg_blog` WHERE `post_id`='$id'");
		}
		if ( !$delete ) { $error[] = "Sorry, there was an error and the selected blog posts were not deleted from the database. ".mysqli_error().""; }
		else { $success[] = "The selected blog posts has been successfully deleted from the database."; }
	}

	// Mass draft blog posts
	if( isset($_POST['mass-draft']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$draft = $database->query("UPDATE `tcg_blog` SET `post_status`='Draft' WHERE `post_id`='$id'");
		}
		if ( !$draft ) { $error[] = "Sorry, there was an error and the selected blog posts were not drafted. ".mysqli_error().""; }
		else { $success[] = "The selected blog posts has been successfully drafted from the database."; }
	}

	// Mass archive blog posts
	if( isset($_POST['mass-archive']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$archive = $database->query("UPDATE `tcg_blog` SET `post_status`='Archived' WHERE `post_id`='$id'");
		}
		if ( !$archive ) { $error[] = "Sorry, there was an error and the selected blog posts were not archived. ".mysqli_error().""; }
		else { $success[] = "The selected blog posts has been successfully archived from the database."; }
	}

	echo '<h1>Blog Posts</h1>
	<p>&raquo; Do you want to <a href="'.$PHP_SELF.'?mod=blog&action=add">add an update</a>?</p>
	
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	echo '</center>

	<form method="post" action="'.$PHP_SELF.'?mod=blog">
	<table width="100%" cellspacing="0" class="table table-bordered table-striped">
	<thead>
	<tr>
		<td width="5%"></td>
		<td width="5%">ID</td>
		<td width="42%">Title</td>
		<td width="20%">Posted on</td>
		<td width="10%">Status</td>
		<td width="17%">Action</td>
	</tr>
	</thead>
	<tbody>';
	$sql = $database->query("SELECT * FROM `tcg_blog` ORDER BY `post_id` DESC");
	while( $row = mysqli_fetch_array($sql) ) {
		$date  = date("F d, Y", strtotime($row['post_date']));
		$id = $row['post_id'];
		$title = strip_tags(stripslashes($row['post_title']));
		if (mb_strlen($title) >= 30) {
			$title = substr($title, 0, 30);
			$title = $title . "...";
		}
		echo '<tr>
		<td align="center"><input type="checkbox" name="id[]" value="'.$row['id'].'" /></td>
		<td align="center">'.$row['post_id'].'</td>
		<td>'.$title.'</td>
		<td align="center">'.$date.'</td>
		<td align="center">'.$row['post_status'].'</td>
		<td align="center">
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=blog&action=edit&id='.$id.'\';" class="btn-success" />Edit</button> 
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=blog&action=delete&id='.$id.'\';" class="btn-cancel" />Delete</button>
		</td>
		</tr>';
	}
	echo '<tr>
		<td align="center"><span class="arrow-right">↳</span></td>
		<td colspan="5">With selected: 
			<input type="submit" name="mass-draft" class="btn-default" value="Draft" />
			<input type="submit" name="mass-archive" class="btn-warning" value="Archive" />
			<input type="submit" name="mass-delete" class="btn-cancel" value="Delete" />
		</td>
	<tr>
	</tbody>
	</table>
	</form>';
}



/********************************************************
 * Action:			Add Blog Posts
 * Description:		Show page for adding a blog post
 */
if( $act == "add" ) {
	if ( isset($_POST['add']) ) {
		$mem = $sanitize->for_db($_POST['members']);
		$lvl = $sanitize->for_db($_POST['levels']);
		$mas = $sanitize->for_db($_POST['masters']);
		$decks = $sanitize->for_db($_POST['decks']);
		$wish = $sanitize->for_db($_POST['wish']);
		$card = $sanitize->for_db($_POST['amount']);
		$stat = $sanitize->for_db($_POST['status']);
		$auth = $sanitize->for_db($_POST['author']);
		$icon = $sanitize->for_db($_POST['icon']);
		$game = $sanitize->for_db($_POST['games']);
		$refer = $sanitize->for_db($_POST['referrals']);
		$aff = $_POST['affiliates'];
		$title = $_POST['title'];
		$entry = $_POST['entry'];

		$entry = str_replace("'","\'",$entry);
		$title = str_replace("'","\'",$title);

		$timestamp = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['date'];
		$entry = nl2br($entry);

		$result = $database->query("INSERT INTO `tcg_blog` (`post_date`,`post_title`,`post_auth`,`post_icon`,`post_member`,`post_master`,`post_level`,`post_affiliate`,`post_game`,`post_referral`,`post_deck`,`post_status`,`post_wish`,`post_amount`,`post_entry`) VALUES ('$timestamp','$title','$auth','$icon','$mem','$mas','$lvl','$aff','$game','$refer','$decks','$stat','$wish','$card','$entry')") or print("Can't insert into table tcg_blog.<br />" . $result . "<br />Error:" . mysqli_connect_error());
		if ( !$result ) {
			$error[] = "Sorry, there was an error and your blog entry was not added. ".mysqli_error()."";
		} else {
			$success[] = "Your blog entry has successfully been entered into the database.";
		}
	}

	$current_month = date("F");
	$current_date = date("d");
	$current_year = date("Y");
	$cur_month = date("m");

	echo '<h1>Add a Blog Post</h1>
	<p>Use the form below to create a new blog post for your TCG\'s weekly update.<br />
	If you want to update the information for an existing blog post, kindly use the <a href="'.$PHP_SELF.'?mod=blog">edit form</a> instead.</p>
	
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=blog&action=add">
	<input type="hidden" name="author" value="'.$player.'" />
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="68%" valign="top">
			<table width="100%">
			<tr>
				<td width="49%">
					<b>Title:</b><br />
					<input type="text" name="title" style="width: 93%;" />
				</td>
				<td width="2%"></td>
				<td width="49%">
					<b>Icon:</b> <i>(Optional)</i><br />
					<input type="text" name="icon" placeholder="image file (e.g. icon.png)" style="width: 93%;" />
				</td>
			</tr>
			</table><br />

			<b>Content:</b><br />';
			include($tcgpath.'admin/theme/text-editor.php');
			echo '<textarea name="entry" id="entry" class="textEditor" style="width: 93%;" rows="10" /></textarea><br />
			<small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to.</i></small><br /><br />

			<b>New Masteries:</b><br />
			<textarea name="masters" style="width: 93%;" rows="4" /></textarea><br /><br />

			<b>New Level Ups:</b><br />
			<textarea name="levels" style="width: 93%;" rows="4" /></textarea><br /><br />

			<b>New Affiliates:</b><br />
			<small><i>Use HTML links to display the linked affiliated TCG on the update. Otherwise type <code>None</code>.</i></small>
			<textarea name="affiliates" style="width: 93%;" rows="4" /></textarea>
		</td>

		<td width="2%">&nbsp;</td>

		<td width="30%" valign="top">
			<b>Publish</b><br />
			<select name="month" id="month" style="width: 48%;">
			<option value="'.$cur_month.'">'.$current_month.'</option>';
			for($m=1; $m<=12; $m++) {
				if ($m < 10) { $_mon = "0$m"; }
				else { $_mon = $m; }
				echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
			}
			echo '</select>
			<input type="text" name="date" id="date" style="width:10%;" value="'.$current_date.'" />';
			$start = date('Y');
			$end = $start-40;
			$yearArray = range($start,$end);
			echo ' <select name="year" id="year">
			<option value="'.$current_year.'">'.$current_year.'</option>';
			foreach ($yearArray as $year) {
				$selected = ($year == $start) ? 'selected' : '';
				echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
			}
			echo '</select><br />
			<input type="radio" name="status" value="Draft" /> Draft 
			<input type="radio" name="status" value="Published" /> Publish 
			<input type="radio" name="status" value="Scheduled" /> Schedule<br /><br />

			<b>New Decks:</b><br />
			<textarea name="decks" style="width: 90%;" rows="2" /></textarea><br />
			<input type="text" name="amount" value="" style="width: 58%;" /> <small><i>Card Amount</i></small><br /><br />

			<b>New Members:</b><br />
			<small><i>Type <code>None</code> on the fields if there are no new members or referrals.</i></small>
			<input type="text" name="members" value="" style="width: 90%;" /><br /><br />
				
			<b>Referrals:</b><br />
			<input type="text" name="referrals" value="" style="width: 90%;" /><br /><br />
			
			<b>Games:</b><br />
			<input type="text" name="games" value="" style="width: 90%;" /><br /><br />
			
			<b>Wishes:</b><br />
			<small><i>Select the appropriate choice if there are granted wishes or none for this update.</i></small><br />
			<input type="radio" name="wish" value="Yes" /> Yes &nbsp;&nbsp;&nbsp; 
			<input type="radio" name="wish" value="None" /> None
			
			<div align="right" style="margin-top:20px;">
				<input type="submit" name="add" class="btn-success" value="Add Blog" /> 
				<input type="reset" name="reset" class="btn-cancel" value="Reset" />
			</div>
		</td>
	</tr>
	</table>
	</form>
	</center>';
}



/********************************************************
 * Action:			Delete Blog Posts
 * Description:		Show page for deleting a blog post
 */
if( $act == "delete" ) {
	if ( isset($_POST['delete']) ) {
		$id=$_POST['id'];
		$delete = $database->query("DELETE FROM `tcg_blog` WHERE `post_id`='$id'");
		if ( !$delete ) {
			$error[] = "Sorry, there was an error and the blog post was not deleted from the database. ".mysqli_error()."";
		} else {
			$success[] = "The blog post has been successfully deleted from the database.";
		}
	}

	if ( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		echo '<h1>Delete a Blog Post</h1>
		<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) {
				echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />';
			}
		}
		if ( isset($success) ) {
			foreach ( $success as $msg ) {
				echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />';
			}
		}
		echo '</center>

		<form method="post" action="'.$PHP_SELF.'?mod=blog&action=delete&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<p>Are you sure you want to delete this update? <b>This action can not be undone!</b><br />
		Click on the button below to delete the update:<br />
		<input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
		</form>';
	}
}



/********************************************************
 * Action:			Edit Blog Posts
 * Description:		Show page for editing a blog post
 */
if( $act == "edit" ) {
	if ( isset($_POST['update']) ) {
		$timestamp = $_POST['year'] . "-" .$_POST['month'] . "-" . $_POST['date'];
		$id = $sanitize->for_db($_POST['id']);
		$mem = $sanitize->for_db($_POST['members']);
		$mas = $sanitize->for_db($_POST['masters']);
		$lvl = $sanitize->for_db($_POST['levels']);
		$decks = $sanitize->for_db($_POST['decks']);
		$wish = $sanitize->for_db($_POST['wish']);
		$amount = $sanitize->for_db($_POST['amount']);
		$stat = $sanitize->for_db($_POST['status']);
		$icon = $sanitize->for_db($_POST['icon']);
		$game = $sanitize->for_db($_POST['games']);
		$auth = $sanitize->for_db($_POST['author']);
		$refer = $sanitize->for_db($_POST['referrals']);
		$aff = $_POST['affiliates'];
		$entry = $_POST['entry'];
		$title = $_POST['title'];

		$entry = nl2br($entry);
		$entry = str_replace("'","\'",$entry);
		$title = str_replace("'","\'",$title);

		$update = $database->query("UPDATE `tcg_blog` SET `post_date`='$timestamp', `post_title`='$title', `post_icon`='$icon', `post_auth`='$auth', `post_member`='$mem', `post_master`='$mas', `post_level`='$lvl', `post_affiliate`='$aff', `post_game`='$game', `post_referral`='$refer', `post_deck`='$decks', `post_wish`='$wish', `post_amount`='$amount', `post_entry`='$entry', `post_status`='$stat' WHERE `post_id`='$id' LIMIT 1") or print ("Can't update entry.<br />" . mysqli_connect_error());

		if ( !$update ) { $error[] = "Sorry, there was an error and the blog entry was not updated. ".mysqli_error().""; }
		else { $success[] = "The blog entry has been updated successfully!"; }
	}

	if ( !isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid entry ID."); }
	else { $id = (int)$_GET['id']; }

	$row = $database->get_assoc("SELECT * FROM `tcg_blog` WHERE `post_id`='$id'") or print ("Can't select entry.<br />" . $row . "<br />" . mysqli_connect_error());
	$old_timestamp = $row['post_date'];
	$old_title = htmlspecialchars($row['post_title']);
	$old_aff = htmlspecialchars($row['post_affiliate']);
	$old_entry = htmlspecialchars($row['post_entry']);
	$old_mem = $row['post_member'];
	$old_mas = $row['post_master'];
	$old_lvl = $row['post_level'];
	$old_refer = $row['post_referral'];
	$old_decks = $row['post_deck'];
	$old_wish = $row['post_wish'];
	$old_amount = $row['post_amount'];
	$old_stat = $row['post_status'];
	$old_icon = $row['post_icon'];
	$old_auth = $row['post_auth'];
	$old_game = $row['post_game'];
	$old_title = str_replace('"','\'',$old_title);

	$old_month = date("F", strtotime($old_timestamp));
	$old_date = date("d", strtotime($old_timestamp));
	$old_year = date("Y", strtotime($old_timestamp));
	$oldm = date("m", strtotime($old_timestamp));
	$oldy = date("Y", strtotime($old_timestamp));

	echo '<h1>Edit a Blog Post</h1>
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=blog&action=edit&id='.$id.'">
	<input type="hidden" name="id" value="'.$id.'" />
	<input type="hidden" name="author" value="'.$old_auth.'" />
	<table width="100%" cellspacing="0" cellpadding="5">
	<tr>
		<td width="68%" valign="top">
            <table width="100%">
			<tr>
				<td width="49%">
					<b>Title:</b><br />
					<input type="text" name="title" value="'.$old_title.'" style="width: 93%;" />
				</td>
				<td width="2%"></td>
				<td width="49%">
					<b>Icon:</b> <i>(Optional)</i><br />
					<input type="text" name="icon" value="'.$old_icon.'" style="width: 93%;" />
				</td>
			</tr>
			</table><br />

			<b>Content:</b><br />';
			include('theme/text-editor.php');
			echo '<textarea style="width:96%" rows="15" name="entry" id="entry">'.$old_entry.'</textarea><br />
			<small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to.</i></small><br /><br />

			<b>New Masteries:</b><br />
			<textarea style="width: 96%" rows="6" name="masters" id="masters">'.$old_mas.'</textarea><br /><br />

			<b>New Level Ups:</b><br />
			<textarea style="width: 96%" rows="6" name="levels" id="levels">'.$old_lvl.'</textarea><br /><br />

			<b>New Affiliates:</b><br />
			<small><i>Use HTML links to display the linked affiliated TCG on the update. Otherwise type <code>None</code>.</i></small>
			<textarea name="affiliates" id="affiliates" style="width:96%;">'.$old_aff.'</textarea>
		</td>

		<td width="2%">&nbsp;</td>

		<td width="30%" valign="top">
			<b>Publish:</b><br />
			<select name="month" id="month" style="width: 45%;">
				<option value="'.$oldm.'">'.$old_month.'</option>';
				for($m=1; $m<=12; $m++) {
					if ($m < 10) { $_mon = "0$m"; }
					else { $_mon = $m; }
					echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
				}
			echo '</select>
			<input type="text" name="date" id="date" size="1" value="'.$old_date.'" />';
			$start = date('Y');
			$end = $start-40;
			$yearArray = range($start,$end);
			echo ' <select name="year" id="year">
			<option value="'.$oldy.'">'.$old_year.'</option>';
			foreach ($yearArray as $year) {
				$selected = ($year == $start) ? 'selected' : '';
				echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
			}
			echo '</select><br />';
			if( $old_stat == "Draft" ) {
                echo '<input type="radio" name="status" value="Draft" checked /> Draft 
                <input type="radio" name="status" value="Published" /> Published 
                <input type="radio" name="status" value="Scheduled" /> Scheduled';
			} else if( $old_stat == "Published" ) {
                echo '<input type="radio" name="status" value="Draft" /> Draft 
                <input type="radio" name="status" value="Published" checked /> Published 
                <input type="radio" name="status" value="Scheduled" /> Scheduled';
			} else {
                echo '<input type="radio" name="status" value="Draft" /> Draft 
                <input type="radio" name="status" value="Published" /> Published 
                <input type="radio" name="status" value="Scheduled" checked /> Scheduled';
			}
			
			echo '<br /><br />
			
			<b>New Decks:</b><br />
			<textarea name="decks" id="decks" style="width: 90%;" rows="2" />'.$old_decks.'</textarea><br />
			<input type="text" name="amount" id="amount" value="'.$old_amount.'" style="width: 58%;" /> <small><i>Card Amount</i></small><br /><br />

			<b>New Members:</b><br />
			<small><i>Type <code>None</code> on the fields if there are no new members or referrals.</i></small>
			<input type="text" name="members" id="members" value="'.$old_mem.'" style="width:90%;" /><br /><br />
	
			<b>Referrals:</b><br />
			<input type="text" name="referrals" id="referrals" value="'.$old_refer.'" style="width:90%;" /><br /><br />
			
			<b>Games:</b><br />
			<input type="text" name="games" value="'.$old_game.'" style="width: 90%;" /><br /><br />
			
			<b>Wishes:</b><br />
			<small><i>Select the appropriate choice if there are granted wishes or none for this update.</i></small><br />';
			if( $old_wish == "Yes" ) {
				echo '<input type="radio" name="wish" value="Yes" checked /> Yes &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="wish" value="None" /> None';
			}
			else {
				echo '<input type="radio" name="wish" value="Yes" /> Yes &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="wish" value="None" checked /> None';
			}
		echo '<br /><br />
		<div align="right" style="margin-top:20px;">
			<input type="submit" name="update" class="btn-success" value="Edit Blog" /> 
			<input type="reset" name="reset" class="btn-cancel" value="Reset" />
		</div>
		</td>
	</tr>
	</table>
	</form>
	</center>';
}
?>