<?php
/********************************************************
 * Moderation:		Games Settings
 * Description:		Show main page of game settings list
 */
if( empty($act) ) {
	if( isset($_POST['mass-weekly']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$update = $database->query("UPDATE `tcg_games` SET `game_set`='Weekly' WHERE `game_id`='$id'");
		}
		if ( !$update ) { $error[] = "Sorry, there was an error and the games were not updated. ".mysqli_error().""; }
		else { $success[] = "The games were successfully updated from the database."; }
	}

	if( isset($_POST['mass-set1']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$update = $database->query("UPDATE `tcg_games` SET `game_set`='Set A' WHERE `game_id`='$id'");
		}
		if ( !$update ) { $error[] = "Sorry, there was an error and the games were not updated. ".mysqli_error().""; }
		else { $success[] = "The games were successfully updated from the database."; }
	}

	if( isset($_POST['mass-set2']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$update = $database->query("UPDATE `tcg_games` SET `game_set`='Set B' WHERE `game_id`='$id'");
		}
		if ( !$update ) { $error[] = "Sorry, there was an error and the games were not updated. ".mysqli_error().""; }
		else { $success[] = "The games were successfully updated from the database."; }
	}

	if( isset($_POST['mass-monthy']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$update = $database->query("UPDATE `tcg_games` SET `game_set`='Monthly' WHERE `game_id`='$id'");
		}
		if ( !$update ) { $error[] = "Sorry, there was an error and the games were not updated. ".mysqli_error().""; }
		else { $success[] = "The games were successfully updated from the database."; }
	}

	if( isset($_POST['mass-special']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$update = $database->query("UPDATE `tcg_games` SET `game_set`='Special' WHERE `game_id`='$id'");
		}
		if ( !$update ) { $error[] = "Sorry, there was an error and the games were not updated. ".mysqli_error().""; }
		else { $success[] = "The games were successfully updated from the database."; }
	}

	if( isset($_POST['mass-delete']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$delete = $database->query("DELETE FROM `tcg_games` WHERE `game_id`='$id'");
		}
		if ( !$delete ) { $error[] = "Sorry, there was an error and the games were not deleted. ".mysqli_error().""; }
		else { $success[] = "The games were successfully deleted from the database."; }
	}

	if( isset($_POST['mass-activate']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$update = $database->query("UPDATE `tcg_games` SET `game_status`='Active' WHERE `game_id`='$id'");
		}
		if ( !$update ) { $error[] = "Sorry, there was an error and the games were not deleted. ".mysqli_error().""; }
		else { $success[] = "The games were successfully deleted from the database."; }
	}

	if( isset($_POST['mass-deactivate']) ) {
		$getID = $_POST['id'];
		foreach( $getID as $id ) {
			$update = $database->query("UPDATE `tcg_games` SET `game_status`='Inactive' WHERE `game_id`='$id'");
		}
		if ( !$update ) { $error[] = "Sorry, there was an error and the games were not deleted. ".mysqli_error().""; }
		else { $success[] = "The games were successfully deleted from the database."; }
	}

	echo '<h1>Games</h1>
	<p>Change your TCG game\'s settings through this page.<br />
	If you want to <u>add a game based on the pre-added ones</u> to the database, <a href="'.$PHP_SELF.'?mod=games&action=add">use this form</a>.<br />
	If you are going to <u>add a password gate game</u>, <a href="'.$PHP_SELF.'?mod=games&action=add-password">use this form</a> instead.</p>

	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	echo '</center>

	<form method="post" action="'.$PHP_SELF.'?mod=games">
	<table width="100%" cellspacing="0" cellpadding="0" class="table table-bordered table-striped">
	<thead>
	<tr>
		<td width="5"></td>
		<td width="10%">Set</td>
		<td width="18%">Game</td>
		<td width="28%">Subtitle</td>
		<td width="8%">Choice</td>
		<td width="8%">Random</td>
		<td width="8%">Currencies</td>
		<td width="15%">Action</td>
	</tr>
	</thead>
	<tbody>';
	$sql = $database->query("SELECT * FROM `tcg_games` ORDER BY `game_set`, `game_slug`");
	while( $row = mysqli_fetch_assoc( $sql ) ) {
		echo '<tr>
		<td align="center"><input type="checkbox" name="id[]" value="'.$row['game_id'].'" /></td>
		<td align="center">'.$row['game_set'].'</td>
		<td align="center">';
			if( $row['game_status'] == "Inactive" ) { echo '<span style="color:red;" title="Inactive">'.$row['game_title'].'</span>'; }
			else { echo $row['game_title']; }
		echo '</td>
		<td align="center">'.$row['game_subtitle'].'</td>
		<td align="center">'.$row['game_choice_array'].'</td>
		<td align="center">'.$row['game_random_array'].'</td>
		<td align="center">'.$row['game_currency_array'].'</td>
		<td align="center">';
		if( $row['game_status'] == "Inactive" ) { echo '<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=games&action=activate&id='.$row['game_id'].'\';" title="Activate Game" class="btn-default"><span class="fas fa-check" aria-hidden="true"></span></button> '; }
		else { echo '<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=games&action=deactivate&id='.$row['game_id'].'\';" title="Deactivate Game" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button> '; }
        if( empty($row['game_pass_array']) ) { echo '<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=games&action=edit&id='.$row['game_id'].'\';" title="Edit Game" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> '; }
        else { echo '<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=games&action=edit-password&id='.$row['game_id'].'\';" title="Edit Game" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> '; }
			echo '<button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=games&action=delete&id='.$row['game_id'].'\';" title="Delete Game" class="btn-cancel"><span class="fas fa-trash-alt" aria-hidden="true"></span></button>
		</td>
		</tr>';
	}
	echo '<tr>
		<td align="center"><span class="arrow-right">↳</span></td>
		<td colspan="7">With selected: 
			<input type="submit" name="mass-weekly" value="Weekly" class="btn-default" />
			<input type="submit" name="mass-set1" value="Set A" class="btn-default" />
			<input type="submit" name="mass-set2" value="Set B" class="btn-default" />
			<input type="submit" name="mass-monthly" value="Monthly" class="btn-default" />
			<input type="submit" name="mass-special" value="Special" class="btn-default" />
			<input type="submit" name="mass-activate" value="Activate" class="btn-success" />
			<input type="submit" name="mass-deactivate" value="Deactivate" class="btn-warning" />
			<input type="submit" name="mass-delete" value="Delete" class="btn-cancel" />
		</td>
	<tr>
	</tbody>
	</table>
	</form>';
}



/********************************************************
 * Action:			Add Pre Games
 * Description:		Show page for adding a pre game
 */
if( $act == "add" ) {
	if( isset($_POST['add']) ) {
		$game = $sanitize->for_db($_POST['game']);
		$set = $sanitize->for_db($_POST['set']);
		$choice = $sanitize->for_db($_POST['choice']);
		$random = $sanitize->for_db($_POST['random']);
		$currency = $sanitize->for_db($_POST['currency']);
		$multiple = $sanitize->for_db($_POST['multiple']);
		$excerpt = $_POST['excerpt'];
		$blurbs = $_POST['entry'];
		$title = $_POST['title'];
		$sub = $_POST['sub'];
		$blurbs = nl2br($blurbs);

		$excerpt = str_replace("'","\'",$excerpt);
		$blurbs = str_replace("'","\'",$blurbs);
		$title = str_replace("'","\'",$title);
		$sub = str_replace("'","\'",$sub);
		
		$insert = $database->query("INSERT INTO `tcg_games` (`game_slug`,`game_title`,`game_set`,`game_subtitle`,`game_excerpt`,`game_desc`,`game_multiple`,`game_choice_array`,`game_random_array`,`game_currency_array`) VALUES ('$game','$title','$set','$sub','$excerpt','$blurbs','$multiple','$choice','$random','$currency')");
		
		if( !$insert ) { $error[] = "Sorry, there was an error and the game was not added. ".mysqli_error().""; }
		else { $success[] = "The game was successfully added to the database!"; }
	}

	echo '<h1>Add a Pre-added Game</h1>
	<p>Use this form to add a pre-added game to the database.<br />
	If you are going to add a password gate game, <a href="'.$PHP_SELF.'?mod=games&action=add-password">use this form</a> instead.</p>

	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
	}
	if ( isset($success) ) {
		foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
	}
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=games&action=add">
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="68%" valign="top">
			<table width="100%" cellpadding="5">
			<tr>
				<td width="20%"><b>Game Title:</b></td>
				<td width="80%"><input type="text" name="title" id="title" placeholder="Black Jack" style="width:90%;" /></td>
			</tr>
			<tr>
				<td><b>Game Slug:</b></td>
				<td><input type="text" name="game" id="game" placeholder="black-jack" style="width:90%;" /></td>
			</tr>
			<tr>
				<td><b>Game Subtitle:</b></td>
				<td><input type="text" name="sub" id="sub" placeholder="Won, Draw, Lost" style="width:90%;" /></td>
			</tr>
			<tr>
				<td><b>Game Excerpt:</b></td>
				<td><input type="text" name="excerpt" id="excerpt" placeholder="e.g. Black Jack with a twist" style="width:90%;" /></td>
			</tr>
			</table><br />
			
			<b>Game Blurbs/Mechanics:</b><br />';
			include('theme/text-editor.php');
			echo '<textarea name="entry" id="entry" class="textEditor" rows="10" style="width:92%"></textarea><br />
			<small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to and <u>PHP is not allowed</u>.</i></small>
		</td>

		<td width="2%">&nbsp;</td>

		<td width="30%" valign="top">
			<b>Game Set:</b><br />
			<select name="set" id="set" style="width:96%;" />
				<option value="Weekly">Weekly</option>
				<option value="Set A">Bi-weekly A</option>
				<option value="Set B">Bi-weekly B</option>
				<option value="Monthly">Monthly</option>
				<option value="Special">Special</option>
			</select><br /><br />

			<b>Choice Cards:</b><br />
			<small><i>Separate values with a comma for multiple rewards.</i></small><br />
			<input type="text" name="choice" id="choice" placeholder="e.g. 4, 3, 2" style="width:90%;" /><br /><br />

			<b>Random Cards:</b><br />
			<small><i>Separate values with a comma for multiple rewards.</i></small><br />
			<input type="text" name="random" id="random" placeholder="e.g. 4, 3, 2" style="width:90%;" /><br /><br />

			<b>Currencies:</b><br />
			<small><i>Amount of currencies, separate values with a vertical slash followed by a comma for multiple currencies per multiple rewards.</i></small><br />
			<input type="text" name="currency" id="currency" placeholder="e.g. 5 | 3 | 1, 6 | 4 | 2, 7 | 5 | 3" style="width:90%;" /><br /><br />

			<b>With multiple rewards?</b><br />
			<input type="radio" name="multiple" id="multiple" value="1" /> Yes &nbsp;&nbsp;&nbsp; 
			<input type="radio" name="multiple" id="multiple" value="0" /> No<br /><br />

			<input type="submit" name="add" class="btn-success" value="Add Game" />
			<input type="reset" name="reset" class="btn-cancel" value="Reset" />
		</td>
	</tr>
	</table>
	</form>
	</center>';
}



/********************************************************
 * Action:			Delete Games
 * Description:		Show page for deleting a game
 */
if( $act == "delete" ) {
	if ( isset($_POST['delete']) ) {
		$id = $_POST['id'];
		$delete = $database->query("DELETE FROM `tcg_games` WHERE `game_id`='$id'");
		
		if ( !$delete ) { $error[] = "Sorry, there was an error and the game wasn't deleted. ".mysqli_error().""; }
		else { $success[] = "The game was successfully deleted from the database."; }
	}

	if ( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		echo '<h1>Delete a Game</h1>
		<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}
		if ( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}
		echo '</center>

		<form method="post" action="'.$PHP_SELF.'?mod=games&action=delete&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<p>Are you sure you want to delete this game? <b>This action can not be undone!</b><br />
		Click on the button below to delete the game:<br />
		<input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
		</form>';
	}
}



/********************************************************
 * Action:			Activate Games
 * Description:		Show page for activating a game
 */
if( $act == "activate" ) {
	if ( isset($_POST['activate']) ) {
		$id = $_POST['id'];
		$activate = $database->query("UPDATE `tcg_games` SET `game_status`='Active' WHERE `game_id`='$id'");
		
		if ( !$activate ) { $error[] = "Sorry, there was an error and the game wasn't activated. ".mysqli_error().""; }
		else { $success[] = "The game was successfully activated from the database."; }
	}

	if ( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		echo '<h1>Activate a Game</h1>
		<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}
		if ( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}
		echo '</center>

		<form method="post" action="'.$PHP_SELF.'?mod=games&action=activate&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<p>Are you sure you want to activate this game? Click on the button below to activate the game:<br />
		<input type="submit" name="activate" class="btn-success" value="Activate"></p>
		</form>';
	}
}



/********************************************************
 * Action:			Deactivate Games
 * Description:		Show page for deactivating a game
 */
if( $act == "deactivate" ) {
	if ( isset($_POST['deactivate']) ) {
		$id = $_POST['id'];
		$activate = $database->query("UPDATE `tcg_games` SET `game_status`='Inactive' WHERE `game_id`='$id'");
		
		if ( !$activate ) { $error[] = "Sorry, there was an error and the game wasn't deactivated. ".mysqli_error().""; }
		else { $success[] = "The game was successfully deactivated from the database."; }
	}

	if ( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		echo '<h1>Deactivate a Game</h1>
		<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}
		if ( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}
		echo '</center>

		<form method="post" action="'.$PHP_SELF.'?mod=games&action=deactivate&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<p>Are you sure you want to deactivate this game? Click on the button below to deactivate the game:<br />
		<input type="submit" name="deactivate" class="btn-cancel" value="Deactivate"></p>
		</form>';
	}
}



/********************************************************
 * Action:			Edit Pre Games
 * Description:		Show page for editing a pre game
 */
if( $act == "edit" ) {
	if( isset($_POST['update']) ) {
		$id = $sanitize->for_db($_POST['id']);
		$game = $sanitize->for_db($_POST['game']);
		$set = $sanitize->for_db($_POST['set']);
		$multiple = $sanitize->for_db($_POST['multiple']);
		$choice = $sanitize->for_db($_POST['choice']);
		$random = $sanitize->for_db($_POST['random']);
		$currency = $sanitize->for_db($_POST['currency']);
        $status = $sanitize->for_db($_POST['status']);
        $date = $_POST['year']."-".$_POST['month']."-".$_POST['day'];
		$excerpt = $_POST['excerpt'];
		$blurbs = $_POST['entry'];
		$title = $_POST['title'];
		$sub = $_POST['sub'];
		$blurbs = nl2br($blurbs);

		$excerpt = str_replace("'","\'",$excerpt);
		$blurbs = str_replace("'","\'",$blurbs);
		$title = str_replace("'","\'",$title);
		$sub = str_replace("'","\'",$sub);
		
		$update = $database->query("UPDATE `tcg_games` SET `game_slug`='$game', `game_title`='$title', `game_set`='$set', `game_subtitle`='$sub', `game_excerpt`='$excerpt', `game_desc`='$blurbs', `game_status`='$status', `game_multiple`='$multiple', `game_choice_array`='$choice', `game_random_array`='$random', `game_currency_array`='$currency', `game_updated`='$date' WHERE `game_id`='$id'");
		
		if( !$update ) { $error[] = "Sorry, there was an error and the game was not updated. ".mysqli_error().""; }
		else { $success[] = "The game was successfully updated from the database."; }
	}

	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		$row = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `game_id`='$id'");
		echo '<h1>Edit a Pre-added Game</h1>
		<p>Use this form to edit an existing game in the database. Use the <a href="'.$PHP_SELF.'?mod=games&action=add">add</a> form to add a new game.</p>

		<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}
		if ( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}

		echo '<form method="post" action="'.$PHP_SELF.'?mod=games&action=edit&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td width="68%" valign="middle">
				<table width="100%" cellpadding="5">
				<tr>
					<td width="20%"><b>Game Title:</b></td>
					<td width="80%"><input type="text" name="title" id="title" value="'.$row['game_title'].'" style="width:90%;" /></td>
				</tr>
				<tr>
					<td><b>Game Slug:</b></td>
					<td><input type="text" name="game" id="game" value="'.$row['game_slug'].'" style="width:90%;" /></td>
				</tr>
				<tr>
					<td><b>Game Subtitle:</b></td>
					<td><input type="text" name="sub" id="sub" value="'.$row['game_subtitle'].'" style="width:90%;" /></td>
				</tr>
				<tr>
					<td><b>Game Excerpt:</b></td>
					<td><input type="text" name="excerpt" id="excerpt" value="'.$row['game_excerpt'].'" style="width:90%;" /></td>
				</tr>
				</table><br />

				<b>Game Blurbs/Mechanics:</b><br />';
				include('theme/text-editor.php');
				echo '<textarea name="entry" id="entry" class="textEditor" rows="12" style="width:92%">'.$row['game_desc'].'</textarea><br />
				<small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to and <u>PHP is not allowed</u>.</i></small>
			</td>

			<td width="2%">&nbsp;</td>

			<td width="30%" valign="top">
                <b>Date Updated:</b><br />
                <select name="month" id="month" style="width: 45%;">
                    <option value="'.date("m", strtotime($row['game_updated'])).'">'.date("F", strtotime($row['game_updated'])).'</option>';
                    for($m=1; $m<=12; $m++) {
                        if ($m < 10) { $_mon = "0$m"; }
                        else { $_mon = $m; }
                        echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                    }
                echo '</select>
                <input type="text" name="day" id="day" size="1" value="'.date("d", strtotime($row['game_updated'])).'" />';
                $start = date('Y');
                $end = $start-40;
                $yearArray = range($start,$end);
                echo ' <select name="year" id="year">
                <option value="'.date("Y", strtotime($row['game_updated'])).'">'.date("Y", strtotime($row['game_updated'])).'</option>';
                foreach ($yearArray as $year) {
                    $selected = ($year == $start) ? 'selected' : '';
                    echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                }
                echo '</select><br />';
                if( $row['game_status'] == "Active" ) {
                    echo '<input type="radio" name="status" value="Active" checked /> Active 
                    <input type="radio" name="status" value="Inactive" /> Inactive';
                } else {
                    echo '<input type="radio" name="status" value="Active" /> Active 
                    <input type="radio" name="status" value="Inactive" /> Inactive';
                }
                echo '<br /><br />

				<b>Game Set:</b><br />
				<select name="set" id="set" style="width:96%;" />
					<option value="'.$row['game_set'].'">Current: '.$row['game_set'].'</option>
					<option value="Weekly">Weekly</option>
					<option value="Set A">Bi-weekly A</option>
					<option value="Set B">Bi-weekly B</option>
					<option value="Monthly">Monthly</option>
					<option value="Special">Special</option>
				</select><br /><br />

				<b>Choice / Random Cards:</b><br />
				<input type="text" name="choice" id="choice" value="'.$row['game_choice_array'].'" style="width:40%;" /> 
                <input type="text" name="random" id="random" value="'.$row['game_random_array'].'" style="width:40%;" /><br /><br />

				<b>Currencies:</b><br />
				<small><i>Amount of currencies to reward.</i></small><br />
				<input type="text" name="currency" id="currency" value="'.$row['game_currency_array'].'" style="width:90%;" /><br /><br />

				<b>With multiple rewards?</b><br />';
				if( $row['game_multiple'] == 1 ) {
					echo '<input type="radio" name="multiple" id="multiple" value="1" checked /> Yes &nbsp;&nbsp;&nbsp; 
					<input type="radio" name="multiple" id="multiple" value="0" /> No';
				} else {
					echo '<input type="radio" name="multiple" id="multiple" value="1" /> Yes &nbsp;&nbsp;&nbsp; 
					<input type="radio" name="multiple" id="multiple" value="0" checked /> No';
				}
				echo '<br /><br />
				<input type="submit" name="update" class="btn-success" value="Edit Game" />
				<input type="reset" name="reset" class="btn-cancel" value="Reset" />
			</td>
		</tr>
		</table>
		</form>
		</center>';
	}
}



/********************************************************
 * Action:			Add Password Game
 * Description:		Show form for adding a password gate
 */
if( $act == "add-password" ) {
	if( isset($_POST['add']) ) {
		$slug = $sanitize->for_db($_POST['game']);
		$pass = $sanitize->for_db($_POST['password']);
		$clue = $sanitize->for_db($_POST['clue']);
		$type = $sanitize->for_db($_POST['type']);
		$set = $sanitize->for_db($_POST['set']);
		$choice = $sanitize->for_db($_POST['choice']);
		$random = $sanitize->for_db($_POST['random']);
		$currency = $sanitize->for_db($_POST['currency']);
		$multiple = $sanitize->for_db($_POST['multiple']);
		$question = $_POST['question'];
		$excerpt = $_POST['excerpt'];
		$blurbs = $_POST['entry'];
		$title = $_POST['title'];
		$sub = $_POST['sub'];
		$blurbs = nl2br($blurbs);

		$question = str_replace("'","\'",$question);
		$excerpt = str_replace("'","\'",$excerpt);
		$blurbs = str_replace("'","\'",$blurbs);
		$title = str_replace("'","\'",$title);
		$sub = str_replace("'","\'",$sub);
		
		$insert = $database->query("INSERT INTO `tcg_games` (`game_slug`,`game_title`,`game_set`,`game_subtitle`,`game_excerpt`,`game_desc`,`game_multiple`,`game_ques_array`,`game_pass_array`,`game_clue_array`,`game_choice_array`,`game_random_array`,`game_currency_array`,`game_type`) VALUES ('$slug','$title','$set','$sub','$excerpt','$blurbs','$multiple','$question','$pass','$clue','$choice','$random','$currency','$type')");
		
		if( !$insert ) { $error[] = "Sorry, there was an error and the game was not added. ".mysqli_error().""; }
		else { $success[] = "The game was successfully added to the database!"; }
	}

	echo '<h1>Add a Password Game</h1>
	<p>Use this form to add a password gate game to the database. Otherwise <a href="'.$PHP_SELF.'?mod=games">proceed to this page</a> to update an existing game.<br />
	If your password gate game have multiple set of rewards, separate them with a comma and space (e.g. 10, 8, 6, 4, 2).</p>

	<center>';
	if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
	if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	
	echo '<form method="post" action="'.$PHP_SELF.'?mod=games&action=add-password">
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td width="68%" valign="top">
			<table width="100%" cellpadding="5">
			<tr>
				<td width="20%"><b>Game Title:</b></td>
				<td width="80%"><input type="text" name="title" id="title" placeholder="Black Jack" style="width:90%;" /></td>
			</tr>
			<tr>
				<td><b>Game Slug:</b></td>
				<td><input type="text" name="game" id="game" placeholder="black-jack" style="width:90%;" /></td>
			</tr>
			<tr>
				<td><b>Game Subtitle:</b></td>
				<td><input type="text" name="sub" id="sub" style="width:90%;" /></td>
			</tr>
			<tr>
				<td><b>Game Excerpt:</b></td>
				<td><input type="text" name="excerpt" id="excerpt" placeholder="e.g. Black Jack with a twist" style="width:90%;" /></td>
			</tr>
			</table><br />
			
			<b>Game Blurbs/Mechanics:</b><br />';
			include('theme/text-editor.php');
			echo '<textarea name="entry" id="entry" class="textEditor" rows="5" style="width:92%"></textarea><br />
			<small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to and <u>PHP is not allowed</u>.</i></small><br /><br />

			<b>Game Questions:</b><br />
			<textarea name="question" id="question" rows="5" style="width:92%"></textarea><br />
			<small><i>This content area doesn\'t support HTML tags and <u>PHP is not allowed</u>!</i></small><br /><br />

			<table width="100%" cellpadding="5">
			<tr>
				<td width="20%"><b>Game Passwords:</b></td>
				<td width="80%"><input type="text" name="password" id="password" placeholder="chocolate, cakes, biscuits, candies, cookies" style="width:90%;" /></td>
			</tr>
			<tr>
				<td><b>Image/Clue:</b></td>
				<td><input type="text" name="clue" id="clue" placeholder="clue01.jpg, clue02.jpg, clur03.jpg, clue04.jpg, clue05.jpg" style="width:90%;" /><br /><small><i>If this is an image type, just type the filename and the extension (e.g. clue01.jpg).</i></small></td>
			</tr>
			</table>
		</td>

		<td width="2%">&nbsp;</td>

		<td width="30%" valign="top">
			<b>Game Set:</b><br />
			<select name="set" id="set" style="width:96%;" />
				<option value="Weekly">Weekly</option>
				<option value="Set A">Bi-weekly A</option>
				<option value="Set B">Bi-weekly B</option>
				<option value="Monthly">Monthly</option>
				<option value="Special">Special</option>
			</select><br /><br />

			<b>Game Type:</b><br />
			<select name="type" id="type" style="width:96%;" />
				<option value="image">Image</option>
				<option value="text">Text</option>
			</select><br /><br />

			<b>Choice Cards:</b><br />
			<input type="text" name="choice" id="choice" placeholder="e.g. 2, 1, 0" style="width:90%;" /><br /><br />

			<b>Random Cards:</b><br />
			<input type="text" name="random" id="random" placeholder="e.g. 5, 4, 3" style="width:90%;" /><br /><br />

			<b>Currencies:</b><br />
			<small><i>Amount of currencies, separate values with a vertical slash followed by a comma for multiple currencies per multiple rewards.</i></small><br />
			<input type="text" name="currency" id="currency" placeholder="e.g. 5 | 3 | 1, 6 | 4 | 2, 7 | 5 | 3" style="width:90%;" /><br /><br />

			<b>With multiple rewards?</b><br />
			<input type="radio" name="multiple" id="multiple" value="1" /> Yes &nbsp;&nbsp;&nbsp; 
			<input type="radio" name="multiple" id="multiple" value="0" /> No<br /><br />

			<input type="submit" name="add" class="btn-success" value="Add Game" />
			<input type="reset" name="reset" class="btn-cancel" value="Reset" />
		</td>
	</tr>
	</table>
	</form>
	</center>';
}



/********************************************************
 * Action:			Edit Password Games
 * Description:		Show page for editing a password game
 */
if( $act == "edit-password" ) {
	if( isset($_POST['update']) ) {
		$id = $sanitize->for_db($_POST['id']);
		$slug = $sanitize->for_db($_POST['game']);
		$pass = $sanitize->for_db($_POST['password']);
		$clue = $sanitize->for_db($_POST['clue']);
		$type = $sanitize->for_db($_POST['type']);
		$set = $sanitize->for_db($_POST['set']);
		$choice = $sanitize->for_db($_POST['choice']);
		$random = $sanitize->for_db($_POST['random']);
		$currency = $sanitize->for_db($_POST['currency']);
		$multiple = $sanitize->for_db($_POST['multiple']);
        $date = $_POST['year']."-".$_POST['month']."-".$_POST['day'];
		$question = $_POST['question'];
		$excerpt = $_POST['excerpt'];
		$blurbs = $_POST['entry'];
		$title = $_POST['title'];
		$sub = $_POST['sub'];
		$blurbs = nl2br($blurbs);

		$question = str_replace("'","\'",$question);
		$excerpt = str_replace("'","\'",$excerpt);
		$blurbs = str_replace("'","\'",$blurbs);
		$title = str_replace("'","\'",$title);
		$sub = str_replace("'","\'",$sub);
		
		$update = $database->query("UPDATE `tcg_games` SET `game_slug`='$slug', `game_title`='$title', `game_set`='$set', `game_subtitle`='$sub', `game_excerpt`='$excerpt', `game_desc`='$blurbs', `game_multiple`='$multiple', `game_ques_array`='$question', `game_pass_array`='$pass',`game_clue_array`='$clue', `game_choice_array`='$choice', `game_random_array`='$random', `game_currency_array`='$currency', `game_updated`='$date' WHERE `game_id`='$id'");
		
		if( !$update ) { $error[] = "Sorry, there was an error and the game was not updated. ".mysqli_error().""; }
		else { $success[] = "The game was successfully updated from the database."; }
	}

	if( empty($id) ) {
		echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
	} else {
		$row = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `game_id`='$id'");
		echo '<h1>Edit a Password Game</h1>
		<p>Use this form to edit a password gate game in the database. Use the <a href="'.$PHP_SELF.'?mod=games&action=add-password">add</a> form to add a new password gate game.</p>

		<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}
		if ( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}

		echo '<form method="post" action="'.$PHP_SELF.'?mod=games&action=edit-password&id='.$id.'">
		<input type="hidden" name="id" value="'.$id.'" />
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td width="68%" valign="middle">
				<table width="100%" cellpadding="5">
				<tr>
					<td width="20%"><b>Game Title:</b></td>
					<td width="80%"><input type="text" name="title" id="title" value="'.$row['game_title'].'" style="width:90%;" /></td>
				</tr>
				<tr>
					<td><b>Game Slug:</b></td>
					<td><input type="text" name="game" id="game" value="'.$row['game_slug'].'" style="width:90%;" /></td>
				</tr>
				<tr>
					<td><b>Game Subtitle:</b></td>
					<td><input type="text" name="sub" id="sub" value="'.$row['game_subtitle'].'" style="width:90%;" /></td>
				</tr>
				<tr>
					<td><b>Game Excerpt:</b></td>
					<td><input type="text" name="excerpt" id="excerpt" value="'.$row['game_excerpt'].'" style="width:90%;" /></td>
				</tr>
				</table><br />

				<b>Game Blurbs/Mechanics:</b><br />';
				include('theme/text-editor.php');
				echo '<textarea name="entry" id="entry" class="textEditor" rows="12" style="width:92%">'.$row['game_desc'].'</textarea><br />
				<small><i>This content area supports the given HTML tags above, but you can add more such as <code>tables</code> if you need to and <u>PHP is not allowed</u>.</i></small><br /><br />

				<b>Game Questions:</b><br />
				<textarea name="question" id="question" rows="5" style="width:92%">'.$row['game_ques_array'].'</textarea><br />
				<small><i>This content area doesn\'t support HTML tags and <u>PHP is not allowed</u>!</i></small><br /><br />

				<table width="100%" cellpadding="5">
				<tr>
					<td width="20%"><b>Game Passwords:</b></td>
					<td width="80%"><input type="text" name="password" id="password" value="'.$row['game_pass_array'].'" style="width:90%;" /></td>
				</tr>
				<tr>
					<td><b>Image/Clue:</b></td>
					<td><input type="text" name="clue" id="clue" value="'.$row['game_clue_array'].'" style="width:90%;" /><br /><small><i>If this is an image type, just type the filename and the extension (e.g. clue01.jpg).</i></small></td>
				</tr>
				</table>
			</td>

			<td width="2%">&nbsp;</td>

			<td width="30%" valign="top">
                <b>Date Updated:</b><br />
                <select name="month" id="month" style="width: 45%;">
                    <option value="'.date("m", strtotime($row['game_updated'])).'">'.date("F", strtotime($row['game_updated'])).'</option>';
                    for($m=1; $m<=12; $m++) {
                        if ($m < 10) { $_mon = "0$m"; }
                        else { $_mon = $m; }
                        echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                    }
                echo '</select>
                <input type="text" name="day" id="day" size="1" value="'.date("d", strtotime($row['game_updated'])).'" />';
                $start = date('Y');
                $end = $start-40;
                $yearArray = range($start,$end);
                echo ' <select name="year" id="year">
                <option value="'.date("Y", strtotime($row['game_updated'])).'">'.date("Y", strtotime($row['game_updated'])).'</option>';
                foreach ($yearArray as $year) {
                    $selected = ($year == $start) ? 'selected' : '';
                    echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                }
                echo '</select><br />';
                if( $row['game_status'] == "Active" ) {
                    echo '<input type="radio" name="status" value="Active" checked /> Active 
                    <input type="radio" name="status" value="Inactive" /> Inactive';
                } else {
                    echo '<input type="radio" name="status" value="Active" /> Active 
                    <input type="radio" name="status" value="Inactive" /> Inactive';
                }
                echo '<br /><br />
                
				<b>Game Set:</b><br />
				<select name="set" id="set" style="width:96%;" />
					<option value="'.$row['game_set'].'">Current: '.$row['game_set'].'</option>
					<option value="Weekly">Weekly</option>
					<option value="Set A">Bi-weekly A</option>
					<option value="Set B">Bi-weekly B</option>
					<option value="Monthly">Monthly</option>
					<option value="Special">Special</option>
				</select><br /><br />

				<b>Choice Cards:</b><br />
				<input type="text" name="choice" id="choice" value="'.$row['game_choice_array'].'" style="width:90%;" /><br /><br />

				<b>Random Cards:</b><br />
				<input type="text" name="random" id="random" value="'.$row['game_random_array'].'" style="width:90%;" /><br /><br />

				<b>Currencies:</b><br />
				<small><i>Amount of currencies, separate values with a vertical slash followed by a comma for multiple currencies per multiple rewards.</i></small><br />
				<input type="text" name="currency" id="currency" value="'.$row['game_currency_array'].'" style="width:90%;" /><br /><br />

				<b>With multiple rewards?</b><br />';
				if( $row['game_multiple'] == 1 ) {
					echo '<input type="radio" name="multiple" id="multiple" value="1" checked /> Yes &nbsp;&nbsp;&nbsp; 
					<input type="radio" name="multiple" id="multiple" value="0" /> No';
				} else {
					echo '<input type="radio" name="multiple" id="multiple" value="1" /> Yes &nbsp;&nbsp;&nbsp; 
					<input type="radio" name="multiple" id="multiple" value="0" checked /> No';
				}
				echo '<br /><br />
				<input type="submit" name="update" class="btn-success" value="Edit Game" />
				<input type="reset" name="reset" class="btn-cancel" value="Reset" />
			</td>
		</tr>
		</table>
		</form>
		</center>';
	}
}
?>