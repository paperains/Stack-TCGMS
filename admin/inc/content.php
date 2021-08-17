<?php
/********************************************************
 * Moderation:		Page Content
 * Description:		Show main page of page content list
 */
if( empty($act) ) {
	// Mass delete page content
    if( isset($_POST['mass-delete']) ) {
        $getID = $_POST['id'];
        foreach( $getID as $id ) {
            $delete = $database->query("DELETE FROM `tcg_pages` WHERE `page_id`='$id'");
        }
		if( !$delete ) { $error[] = "Sorry, there was an error and the pages were not deleted from the database. ".mysqli_error().""; }
		else { $success[] = "The pages were deleted successfully from the database."; }
    }

    // Mass draft page content
	if( isset($_POST['mass-draft']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$draft = $database->query("UPDATE `tcg_pages` SET `page_status`='Draft' WHERE `page_id`='$id'");
		}
		if ( !$draft ) { $error[] = "Sorry, there was an error and the selected page contents were not drafted. ".mysqli_error().""; }
		else { $success[] = "The selected page contents has been successfully drafted from the database."; }
	}

	// Mass archive page content
	if( isset($_POST['mass-archive']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$archive = $database->query("UPDATE `tcg_pages` SET `page_status`='Archived' WHERE `page_id`='$id'");
		}
		if ( !$archive ) { $error[] = "Sorry, there was an error and the selected page contents were not archived. ".mysqli_error().""; }
		else { $success[] = "The selected page contents has been successfully archived from the database."; }
	}
    
	echo '<h1>Page Content</h1>
	<p>&raquo; Do you want to <a href="'.$PHP_SELF.'?mod=content&action=add">add a page</a>?</p>
	
	<center>';
	if( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div>'; }
	}
	echo '</center>

	<form method="post" action="'.$PHP_SELF.'?mod=content">
	<table width="100%" cellspacing="0" class="table table-bordered table-striped">
	<thead>
	<tr>
		<td width="5%"></td>
		<td width="5%">ID</td>
		<td width="43%">Title</td>
		<td width="20%">Added on</td>
		<td width="10%">Status</td>
		<td width="17%">Action</td>
	</tr>
	</thead>
	<tbody>';
	$sql = $database->query("SELECT * FROM `tcg_pages` ORDER BY `page_id` DESC");
	while( $row = mysqli_fetch_array($sql) ) {
		$date  = date("F d, Y", strtotime($row['page_date']));
		$id = $row['page_id'];
		$title = strip_tags(stripslashes($row['page_title']));
		if (mb_strlen($title) >= 30) {
			$title = substr($title, 0, 30);
			$title = $title . "...";
		}
		echo '<tr>
        <td align="center"><input type="checkbox" name="id[]" value="'.$row['page_id'].'" /></td>
		<td align="center">'.$row['page_id'].'</td>
		<td>'.$title.'</td>
		<td align="center">'.$date.'</td>
		<td align="center">'.$row['page_status'].'</td>
		<td align="center">
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=content&action=edit&id='.$id.'\';" class="btn-success">Edit</button> 
			<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=content&action=delete&id='.$id.'\';" class="btn-cancel">Delete</button>
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
 * Action:			Add Page Content
 * Description:		Show page for adding page content
 */
if( $act == "add" ) {
	if( isset($_POST['add']) ) {
		$title = $_POST['title'];
		$slug = $sanitize->for_db($_POST['slug']);
		$parent = $sanitize->for_db($_POST['parent']);
		$status = $sanitize->for_db($_POST['status']);
		$timestamp = $_POST['year']."-".$_POST['month']."-".$_POST['date'];
		$content = $_POST['entry'];
		$content = nl2br($content);

		$content = str_replace("'","\'",$content);
		$title = str_replace("'","\'",$title);
        
		$result = $database->query("INSERT INTO `tcg_pages` (`page_title`,`page_slug`,`page_parent`,`page_content`,`page_status`,`page_date`) VALUES ('$title','$slug','$parent','$content','$status','$timestamp')") or print("Can't insert into table tcg_pages.<br />" . $result . "<br />Error:" . mysqli_connect_error());

		if( !$result ) { $error[] = "Sorry, there was an error and your page content was not added. ".mysqli_error().""; }
		else { $success[] = "Your page content has been successfully entered into the database."; }
	}

	$current_month = date("F");
	$current_date = date("d");
	$current_year = date("Y");
	$cur_month = date("m");

	echo '<p>Use the form below to create a new page content for your TCG.<br />
	Use the <a href="'.$PHP_SELF.'?mod=content">edit</a> form to update the information for existing pages.</p>
	
	<center>';
	if( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div>'; }
	}
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=content&action=add">
	<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
		<td width="68%" valign="top">
			<b>Title:</b><br />
			<input type="text" name="title" placeholder="Information" style="width:96%;" /><br /><br />

			<b>Slug:</b> <small><i>Usually the lowercase version of the page\'s title with hyphens (e.g. card-decks)</i></small><br />
			<input type="text" name="slug" placeholder="information" style="width:96%;" /><br /><br />

			<b>Content</b><br />';
			include('theme/text-editor.php');
			echo '<textarea name="entry" id="entry" class="textEditor" style="width:96%;" rows="10" /></textarea><br />
			<small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to.</i></small>
		</td>

		<td width="2%">&nbsp;</td>

		<td width="30%" valign="top">
			<b>Publish:</b><br />
			<select name="month" id="month" style="width:45%;">
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
			<input type="radio" name="status" value="Published" /> Publish<br /><br />
			
			<b>Parent Page:</b><br />
			<select name="parent" style="width: 95%;">';
			$sql = $database->query("SELECT * FROM `tcg_pages` WHERE `page_parent`='0' ORDER BY `page_id`");
            $count = mysqli_num_rows($sql);
			if( $count == 0 ) { echo '<option value="0">None</option>'; }
			else {
				echo '<option value="0">None</option>';
				while( $row = mysqli_fetch_assoc($sql) ) {
					echo '<option value='.$row['page_id'].'">'.$row['page_title'].'</option>';
				}
			}
			echo '</select>
			
			<div align="right" style="margin-top:20px;">
				<input type="submit" name="add" class="btn-success" value="Add Page" /> 
				<input type="reset" name="reset" class="btn-cancel" value="Reset" />
			</div>
		</td>
	</tr>
	</table>
	</form>
	<center>';
}



/********************************************************
 * Action:			Delete Content
 * Description:		Show page for deleting page content
 */
if( $act == "delete" ) {
	if ( isset($_POST['delete']) ) {
		$id = $_POST['id'];
		$delete = $database->query("DELETE FROM `tcg_pages` WHERE `page_id`='$id'");
		if( !$delete ) { $error[] = "Sorry, there was an error and the page was not deleted from the database. ".mysqli_error().""; }
		else { $success[] = "The page has been successfully deleted from the database."; }
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
		<form method="post" action="'.$PHP_SELF.'?mod=content&action=delete&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<p>Are you sure you want to delete this page content? <b>This action can not be undone!</b><br />
		Click on the button below to delete the page:<br />
		<input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
		</form>';
	}
}



/********************************************************
 * Action:			Edit Content
 * Description:		Show page for editing page content
 */
if( $act == "edit" ) {
	if( isset($_POST['update']) ) {
		$title = $_POST['title'];
		$id = $sanitize->for_db($_POST['id']);
		$slug = $sanitize->for_db($_POST['slug']);
		$parent = $sanitize->for_db($_POST['parent']);
		$status = $sanitize->for_db($_POST['status']);
		$timestamp = $_POST['year']."-".$_POST['month']."-".$_POST['date'];
		$content = $_POST['entry'];
		$content = nl2br($content);

		$content = str_replace("'","\'",$content);
		$title = str_replace("'","\'",$title);

		$update = $database->query("UPDATE `tcg_pages` SET `page_date`='$timestamp', `page_title`='$title', `page_slug`='$slug', `page_parent`='$parent', `page_content`='$content', `page_status`='$status' WHERE `page_id`='$id' LIMIT 1") or print ("Can't update page content.<br />" . mysqli_connect_error());

		if( !$update ) { $error[] = "Sorry, there was an error and the page content was not updated. ".mysqli_error().""; }
		else { $success[] = "The page content has been updated successfully!"; }
	}

	if( !isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id']) ) { die("Invalid page ID."); }
	else { $id = (int)$_GET['id']; }
	$row = $database->get_assoc("SELECT * FROM `tcg_pages` WHERE `page_id`='$id'") or print ("Can't select page.<br />" . $sql . "<br />" . mysqli_connect_error());
	$old_timestamp = $row['page_date'];
	$old_title = stripslashes($row['page_title']);
	$old_slug = stripslashes($row['page_slug']);
	$old_parent = stripslashes($row['page_parent']);
	$old_status = stripslashes($row['page_status']);
	$old_content = stripslashes($row['page_content']);
	$old_title = str_replace('"','\'',$old_title);

	$old_month = date("F", strtotime($old_timestamp));
	$old_date = date("d", strtotime($old_timestamp));
	$old_year = date("Y", strtotime($old_timestamp));
	$oldm = date("m", strtotime($old_timestamp));
	$oldy = date("Y", strtotime($old_timestamp));

	echo '<center>';
	if( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=content&action=edit&id='.$id.'">
	<input type="hidden" name="id" value="'.$id.'" />
	<table width="100%" cellspacing="5" cellpadding="0">
	<tr>
		<td width="68%" valign="top">
			<b>Title:</b><br />
			<input type="text" name="title" id="title" value="'.$old_title.'" style="width:96%;" /><br /><br />

			<b>Slug:</b> <small><i>Usually the lowercase version of the page\'s title with hyphens (e.g. card-decks)</i></small><br />
			<input type="text" name="slug" id="slug" value="'.$old_slug.'" style="width:96%;" /><br /><br />

			<b>Content:</b><br />';
			include('theme/text-editor.php');
			echo '<textarea style="width:96%" rows="10" name="entry" id="entry" class="textEditor" id="content">'.$old_content.'</textarea><br />
			<i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to.</i>
		</td>

		<td width="2%">&nbsp;</td>

		<td width="30%" valign="top">
			<b>Publish:</b><br />
			<select name="month" id="month" style="width:45%;">
				<option value="'.$oldm.'">'.$old_month.'</option>';
				for($m=1; $m<=12; $m++) {
					if ($m < 10) { $_mon = "0$m"; }
					else { $_mon = $m; }
					echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
				}
			echo '</select> 
			<input type="text" name="date" id="date" style="width:10%;" value="'.$old_date.'" /> ';
			$start = date('Y');
			$end = $start-40;
			$yearArray = range($start,$end);
			echo '<select name="year" id="year">
				<option value="'.$oldy.'">'.$old_year.'</option>';
				foreach ($yearArray as $year) {
					$selected = ($year == $start) ? 'selected' : '';
					echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
				}
			echo '</select><br /><br />';
			if( $old_status == "Published" ) {
                echo '<input type="radio" name="status" value="Draft" /> Draft 
                <input type="radio" name="status" value="Published" checked /> Publish';
			} else {
                echo '<input type="radio" name="status" value="Draft" checked /> Draft 
                <input type="radio" name="status" value="Published" /> Publish';
			}

			echo '<br /><br />
			
			<b>Parent Page:</b><br />
            <select name="parent">';
				$old = $database->get_assoc("SELECT * FROM `tcg_pages` WHERE `page_id`='$old_parent' ORDER BY `page_id`");
				if( $old_parent == 0 ) { echo '<option value="0">None</option>'; }
				else {
                    echo '<option value='.$old_parent.'">'.$old['page_title'].'</option>';
                }

                $new = $database->query("SELECT * FROM `tcg_pages` WHERE `page_parent`='0' ORDER BY `page_id`");
				$count = mysqli_num_rows($new);
				if( $count == 0 ) { echo '<option value="0">None</option>'; }
				else {
					echo '<option value="0">None</option>';
					while( $row = mysqli_fetch_assoc($new) ) {
						echo '<option value='.$row['page_id'].'">'.$row['page_title'].'</option>';
					}
				}
				echo '</select>
				
			<div align="right" style="margin-top:20px;">
				<input type="submit" name="update" class="btn-success" value="Update Page" /> 
				<input type="reset" name="reset" class="btn-cancel" value="Reset" />
			</div>
		</td>
	</tr>
	</table>
	</form>
	</center>';
}
?>