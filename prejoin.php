<?php
include('admin/class.lib.php');
include($header);

####################################
########## SHOW DONATIONS ##########
####################################
if ( empty($form) ) {
	echo '<h1>Prejoin Donations</h1>
	<p>Below is the complete list of the prejoin donated decks. If you want to claim or donate a deck, you can do so <a href="/prejoin.php?form=deck-claims">here (for claims)</a> and <a href="/prejoin.php?form=deck-donations">here (for donations)</a>.</p>

	<p>Please keep in mind that you have to claim the deck first before sending in your donations!</p>

	<table width="100%" cellspacing="3" class="border">
	<tr>
		<td class="headLine" width="15%">Category</td>
		<td class="headLine" width="25%">Features</td>
		<td class="headLine" width="20%">Set/Series</td>
		<td class="headLine" width="20%">Donator</td>
	</tr>';
	$sql = $database->query("SELECT * FROM `tcg_donations` WHERE `type`='Donation' ORDER BY `category`, `deckname` ASC");
	while ( $row = mysqli_fetch_assoc($sql) ) {
		echo '<tr>
		<td class="tableBody" align="center">'.$row['category'].'</td>
		<td class="tableBody" align="center">';
		if ( $row['url'] == "" ) { echo $row['feature']; }
		else { echo '<a href="'.$row['url'].'" target="_blank">'.$row['feature'].'</a>'; }
		echo '</td>
		<td class="tableBody" align="center">'.$row['set'].'</td>
		<td class="tableBody" align="center">'.$row['name'].'</td>
		</tr>';
	}
	echo '</table>';
} // end default donations page

###############################
########## DO CLAIMS ##########
###############################
else if ( $form == "deck-claims" ) {
	if ( isset($_POST['submit']) ) {
		$check->Donation();
		$name = $sanitize->for_db($_POST['name']);
		$cat = $sanitize->for_db($_POST['category']);
		$deck = $sanitize->for_db($_POST['deckname']);
		$feat = $sanitize->for_db($_POST['feature']);
		$pass = $sanitize->for_db($_POST['pass']);
		$set = $sanitize->for_db($_POST['set']);
		$date = date("Y-m-d", strtotime("now"));

		$insert = $database->query("INSERT INTO `tcg_donations` (`name`,`category`,`deckname`,`feature`,`set`,`type`,`pass`,`date`) VALUES ('$name','$cat','$deck','$feat','$set','Claim','$pass','$date')");

		if ( !$insert ) {
			$error[] = '<p>There was an error while processing your donations. Kindly send us your donation details instead at <u>'.$tcgemail.'</u>. Thank you and sorry for the inconvenience!</p>';
		}

		else {
			$success[] = '<p>Your deck claim has been added to the database!<br />
			You can send the donation link using the <a href="/prejoin.php?form=deck-donations">donations</a> form once ready. Claim <a href="/prejoin.php?form=deck-claims">more?</a></p>';
		}
	} // end process form

	echo '<h1>Deck Claims</h1>
	<p>Use the form below to submit your claims. Please make sure that the deck you\'re about to claim hasn\'t been claimed by anyone else. All claims are password-protected by the claimant, so don\'t forget to provide any dummy password that you can use when you\'re going to send your donations.</p>

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

	<form method="post" action="/prejoin.php?form=deck-claims">
	<table width="100%" cellspacing="3" class="border">
	<tr>
		<td class="headLine" width="15%">Name:</td>
		<td class="tableBody"><input type="text" name="name" placeholder="Jane Doe" style="width:90%;"></td>
		<td class="headLine" width="15%">Password:</td>
		<td class="tableBody"><input type="text" name="pass" placeholder="for donation purposes" style="width:90%;"></td>
	</tr>
	<tr>
		<td class="headLine" width="15%">Category:</td>
		<td class="tableBody" width="35%">
			<select name="category" style="width:97%;">
				<option value="">-----</option>';
			$catcount = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
			for($i=1; $i<=$catcount; $i++) {
				$cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
				echo '<option value="'.$i.'">'.$cat['name'].'</option>';
			}
			echo '</select>
		</td>
		<td class="headLine" width="15%">File Name:</td>
		<td class="tableBody" width="35%"><input type="text" name="deckname" style="width:90%;" placeholder="e.g. whitetigers"></td>
	</tr>
	<tr>
		<td class="headLine">Feature:</td><td class="tableBody">
		<input type="text" name="feature" placeholder="usually what\'s the deck all about" style="width:90%;"></td>
		<td class="headLine">Set (or series):</td>
		<td class="tableBody"><input type="text" name="set" placeholder="REMOVE if this is not applicable" style="width:90%;"></td>
	</tr>
	<tr>
		<td class="tableBody" align="center" colspan="4">
			<input type="submit" name="submit" class="btn-success" value="Send Claims" /> 
			<input type="reset" name="reset" class="btn-cancel" value="Reset" />
		</td>
	</tr>
	</table>
	</form>';
} // end do claims page

##################################
########## DO DONATIONS ##########
##################################
else if ( $form == "deck-donations" ) {
	if ( isset($_POST['submit']) ) {
		$check->Value();
		$deck = $sanitize->for_db($_POST['deckname']);
		$pass = $sanitize->for_db($_POST['pass']);
		$url = $sanitize->for_db($_POST['url']);
		$date = date("Y-m-d", strtotime("now"));

		$pass_query = $database->query("SELECT `pass` FROM `tcg_donations` WHERE `deckname`='$deck'");
		$row = mysqli_fetch_assoc($pass_query);

		// Check if password matches
		if ( $row['pass'] != $pass ) {
			exit('<h1>Deck Donations : Error</h1><p>It seems like the password you\'ve provided is incorrect!</p>');
		}

		// Else, update donations
		$update = $database->query("UPDATE `tcg_donations` SET `url`='$url', `type`='Donation' WHERE `deckname`='$deck' AND `pass`='$pass'");

		// Process form if queries are correct
		if ( !$update ) {
			$error[] = '<p>There was an error while processing your donations. Kindly send us your donation details instead at <u>'.$tcgemail.'</u>. Thank you and sorry for the inconvenience!</p>';
		}

		else {
			$success[] = '<p>Your deck donation has been received and a deck maker will check it!<br />
			Once it is approved, you will receive your rewards on your mailbox. Donate <a href="/prejoin.php?form=deck-donations">more?</a></p>'
		}
	} // end form process

	echo '<h1>Deck Donations</h1>
	<p>Use the form below to submit your donations. Please keep in mind the exclusive guidelines before donating any deck.</p>

	<ul>
		<li>Donated images must be in high quality and unedited, preferrably 600x600 pixels up to 1600x1600 pixels.</li>
		<li>Horizontal images are much preferred than vertical ones to avoid the subjects getting cropped just to fit the card template.</li>
		<li>Only images that is related to [TCG SUBJECT] that will fit to our sets are allowed.</li>
		<li>Donations need at least XX images, but more is encouraged.</li>
		<li>For <b>every deck</b> you donate, you will get X random cards and X CURRENCY.</li>
	</ul>

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

	<form method="post" action="/prejoin.php?form=deck-donations">
		<table width="100%" cellspacing="3" class="border">
		<tr>
			<td class="headLine" width="15%">File Name:</td>
			<td class="tableBody"><input type="text" name="deckname" placeholder="e.g. whitetigers" style="width:90%;"></td>
			<td class="headLine" width="15%">Password:</td>
			<td class="tableBody"><input type="text" name="pass" placeholder="********" style="width:90%;"></td>
		</tr>
		<tr>
			<td class="headLine">Link:</td>
			<td class="tableBody" colspan="3"><input type="text" name="url" placeholder="Link to download your donation" style="width:96%;"></td>
		</tr>
		<tr>
			<td class="tableBody" align="center" colspan="4">
				<input type="submit" name="submit" class="btn-success" value="Send Donation" /> 
				<input type="reset" name="reset" class="btn-cancel" value="Reset" />
			</td>
		</tr>
		</table>
	</form>';
} // end do donations page

##############################
########## DO VOTES ##########
##############################
else if ( $form == "votes" ) {
	if ( isset($_POST['submit']) ) {
		$check->Value();
		$name = $sanitize->for_db($_POST['name']);
		$date = date("Y-m-d H:i:s", strtotime("now"));

		// Check if vote is more than 24 hours
		$t = $database->get_assoc("SELECT * FROM `deck_votes` WHERE `name`='$name'");
		$now = date("Y-m-d H:i:s", strtotime('now'));
		$yesterday = $t['timestamp'];
		if ( $now > $yesterday ) {
			for($i=1;$i<=10;$i++) {
				$card = "deck$i";
				$deck = $sanitize->for_db($_POST[$card]);
				$decks .= $deck.', ';
				$update = $database->query("UPDATE `tcg_cards` SET `votes`=votes+'1' WHERE `filename`='$deck'");
			} // end for
			$decks = substr_replace($decks,"",-2);

			// Process form if queries are correct
			if ( !$update ) {
				$error[] = '<p>There was an error while processing your votes. Kindly send us your voting details instead at <u>'.$tcgemail.'</u>. Thank you and sorry for the inconvenience!</p>';
			} else {
				$database->query("INSERT INTO `deck_votes` (`name`,`decks`,`timestamp`) VALUES ('$name','$decks','$date')");
				$success[] = '<p>Your votes has been added to the database. You can vote again after 24 hours for the decks you\'ve just voted.</p>';
			}
		}

		else {
			$error[] = '<p>It seems like it hasn\'t been 24 hours since the last time you\'ve sent in a vote. Kindly wait for a couple of more hours before voting again, thank you!</p>';
		}
	} // end form process

	// Show deck voting page
	echo '<h1>Deck Voting</h1>
	<p>Use the form below to vote 10 decks that you wish to be released as prejoin decks.<br />
	<u>Please vote only 1 deck per dropdown once a day!</u> You can vote every day until the voting phase expires and you can vote for the same decks per day.</p>

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

	echo '<!-- SET YOUR OWN COUNTER -->
		<div data-type="countdown" data-id="2104673" class="tickcounter" style="width: 40%; position: relative; padding-bottom: 12%">
			<a href="//www.tickcounter.com/countdown/2104673/voting-period" title="Voting Period">Voting Period</a>
			<a href="//www.tickcounter.com/" title="Countdown">Countdown</a>
		</div>

		<script>(function(d, s, id) { var js, pjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//www.tickcounter.com/static/js/loader.js"; pjs.parentNode.insertBefore(js, pjs); }(document, "script", "tickcounter-sdk"));</script>
	</center><br />

	<form method="post" action="/prejoin.php?form=votes">
		<table width="100%" cellspacing="3" class="border">
		<tr>
			<td class="headLine" width="15%">Name:</td>
			<td class="tableBody"><input type="text" name="name" placeholder="Jane Doe" style="width:90%;"></td>
		</tr>';
		for($i=1;$i<=10;$i++) {
			echo '<tr>
			<td class="headLine">Vote '.$i.'</td>
			<td class="tableBody">
				<select name="deck'.$i.'" style="width:95;%">';
				$decks = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Upcoming' ORDER BY `filename` ASC");
				while ( $row = mysqli_fetch_assoc($decks) ) {
					echo '<option value="'.$row['filename'].'">'.$row['deckname'].' ('.$row['filename'].')</option>';
				}
				echo '</select>
			</td>
			</tr>';
		}
		echo '<tr>
			<td class="tableBody" align="center" colspan="4">
				<input type="submit" name="submit" class="btn-success" value="Send Votes" /> 
				<input type="reset" name="reset" class="btn-cancel" value="Reset" />
			</td>
		</tr>
		</table>
	</form>

	<h2>Vote Logs</h2>
	<div style="padding-right: 20px; margin-top: 20px; line-height: 20px; font-size: 14px; overflow: auto; height: 300px;">';
	$vchk = $database->query("SELECT * FROM `deck_votes` ORDER BY `timestamp` DESC");
	while ( $votes = mysqli_fetch_assoc($vchk) ) {
		echo '<u>'.date("Y-m-d H:i:s", strtotime($votes['timestamp'])).':</u> <i>'.$votes['name'].'</i> voted for '.$votes['decks'].'<br />';
	}
	echo '</div>';
} // end do votes page

include ($footer);
?>