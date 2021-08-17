<?php
include('admin/class.lib.php');
include($header);
include('theme/headers/deck-header.php');

/********************************************************
 * Action:			Set / Series
 * Description:		Show list of set or series
 */
if ( empty($view) ) {
	if ( empty($set) ) {
		echo'<h1>Cards</h1>
		<table width="100%" cellspacing="3">
        <tr>
            <td width="35%" valign="top"><img src="'.$tcgcards.'filler.'.$tcgext.'" align="left" /> <img src="'.$tcgcards.'pending.'.$tcgext.'" align="left" /></td>
            <td width="65%" valign="top">Below you will find all of the current card decks arranged in sets where decks are sorted by <em>categories</em>, then by file name. The <b>#</b> indicates the number of cards, while the <b>$</b> indicates the worth of the cards. If you can\'t find a deck in particular, try using the <i>ctrl/command + f</i> to find it.</td>
        </tr>
        </table>

		<center>
			<div class="box-info">
				<b>Pro Tip!</b> If you are having troubles with your eTCG\'s auto upload function, you can grab the weekly releases <a href="/cards.php?view=zips">here</a>.
			</div>
		</center><br />

		<center>';
		$sql_set = $database->query("SELECT `card_set`, COUNT(card_deckname) FROM `tcg_cards` WHERE `card_status`='Active' GROUP BY `card_set`");
		while ( $row=mysqli_fetch_assoc($sql_set) ) {
			echo '<a href="'.$tcgurl.'cards.php?set='.$row['card_set'].'"><img src="'.$tcgcards.''.$row['card_set'].'.png" border="0" /></a> ';
		}
		echo '</center>';
	}

	else {
		echo'<h1>Cards : '.$set.'</h1>';

		// SHOW SEARCH FORM
		$general->cardSearch('cards','card','Active');

		$sql = $database->query("SELECT * FROM `tcg_cards` WHERE `card_set`='$set' AND `card_status`='Active' ORDER BY `card_filename` ASC");
		while( $row = mysqli_fetch_assoc($sql) ) {
            $digits = rand(01,$row['card_count']);
            if ($digits < 10) { $_digits = "0$digits"; }
            else { $_digits = $digits; }
            $card = "$row[card_filename]$_digits";
            echo '<div class="deck_prev"><a href="'.$tcgurl.'cards.php?view=released&deck='.$row['card_filename'].'"><img src="'.$tcgcards.''.$card.'.'.$tcgext.'"></a><br /><a href="'.$tcgurl.'cards.php?view=released&deck='.$row['card_filename'].'">'.$row['card_deckname'].'</a></div>';
		}

        $tcgName = substr_replace($settings->getValue('tcg_name'),"",-4);
        if( $set == $tcgName ) {
            $events = $database->query("SELECT * FROM `tcg_cards_event` ORDER BY `event_group` ASC, `event_date` DESC");
            $group = null;
            while( $row = mysqli_fetch_assoc($events) ) {
                if ($row['event_group'] != $group) {
                    $group = $row['event_group'];
                    echo '<h2>'.$row['event_group'].'</h2>';
                }
                echo '<div class="deck_prev"><img src="'.$tcgcards.''.$row['event_filename'].'.'.$tcgext.'" title="'.$row['event_title'].' ('.$row['event_filename'].')" /><br /><b>'.$row['event_title'].'</b></div>';
            }
        }
	}
} // end empty view




/********************************************************
 * Action:			Released Decks
 * Description:		Show list of released decks
 */
else if ( $view == "released" ) {
	if ( empty($deck) ) {
		echo '<h1>Cards : Released</h1>';

		// SHOW SEARCH FORM
		$general->cardSearch('cards','card','Active');

		$c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
		for($i=1; $i<=$c; $i++) {
			$sql = $database->query("SELECT * FROM `tcg_cards` WHERE `card_cat`='$i' AND `card_status`='Active' ORDER BY `card_cat` ASC, `card_filename` ASC");
			$cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
			$counts = mysqli_num_rows($sql);
			if( $counts == 0 ) { echo ""; }
			else {
				echo '<h3>'.$cat['cat_name'].'</h3>
				<table width="100%" class="table table-sliced table-striped"><thead>
				<tr>
					<td width="70%" align="center"><b>Deck Name</b></td>
					<td width="20%" align="center"><b>Deck Color</b></td>
					<td width="10%" align="center"><b>#/$</b></td>
				</tr></thead>
                <tbody>';
				while( $row = mysqli_fetch_assoc($sql) ) {
					echo '<tr>
						<td align="center"><a href="'.$tcgurl.'cards.php?view=released&deck='.$row['card_filename'].'">'.$row['card_deckname'].'</a></td>
						<td align="center"><font color="'.$row['card_color'].'">'.$row['card_color'].'</font></td>
						<td align="center">';
					if ( $row['card_filename'] == "member" ) {
						$query = $database->num_rows("SELECT * FROM `user_list` WHERE `usr_mcard`='Yes'");
						echo $memnum.'/0';
					}
					else {
						echo $row['card_count'].'/'.$row['card_worth'];
					}
						echo '</td>
					</tr>';
				}
				echo '</tbody></table>';
			}
		}
	}

	else {
		$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_filename`='$deck' AND `card_status`='Active'");
		while( $row = mysqli_fetch_assoc($query) ) {
			echo '<h1><font color="'.$row['card_color'].'"><span class="fas fa-tint" aria-hidden="true"></span></font> '.$row['card_deckname'].'</h1>
			<p>'.$row['card_desc'].'</p>
			<center>
			<table width="100%" cellspacing="0" border="0" class="table table-bordered table-striped">
			<tr>
				<td colspan="3"><h3>'.$row['card_set'].'</h3></td>
			</tr>
			<tr>
				<td width="30%" rowspan="5" align="center" valign="top">
					<img src="'.$tcgcards.''.$row['card_filename'].'.'.$tcgext.'" /><br />';
					include("admin/wish.php");
				echo '</td>
				<td colspan="2" valign="middle">
					<b>Deck/File Name:</b> '.$row['card_deckname'].' (<i>'.$row['card_filename'].'</i>)
				</td>
			</tr>
			<tr>
				<td width="40%" valign="middle"><b>Made/Donated by:</b> '.$row['card_maker'].' / '.$row['card_donator'].'</td>
				<td width="40%" valign="middle">
					<b>Color:</b> <font color="'.$row['card_color'].'">'.$row['card_color'].'</font>
				</td>
			</tr>
			<tr>
				<td valign="middle"><b>Released:</b> '.$row['card_released'].'</td>
				<td valign="middle"><b>Masterable:</b> '.$row['card_mast'].'</td>
			</tr>
			<tr>
				<td colspan="2" valign="middle"><b>Wished by:</b> ';
				$w = $database->query("SELECT * FROM `user_wishlist` WHERE `wlist_deck`='$deck'");
				$c = mysqli_num_rows($w);
				if( $c != 0 ) {
					$names = array();
					while ( $rw = mysqli_fetch_array($w) ) {
						$names[] = '<a href="'.$tcgurl.'members.php?id='.$rw['wlist_name'].'">'.$rw['wlist_name'].'</a>';
					}
					echo implode(', ', $names);
				}
				else { echo "None"; }
				echo '</td>
			</tr>
			<tr>
				<td colspan="2" valign="middle"><b>Mastered by:</b> '.$row['card_masters'].'</td>
			</tr>
			<tr>	
				<td width="70%" colspan="3" align="center">';
					// Get total deck width
					$width = $settings->getValue('cards_size_width') * $row['card_break'];
					echo '<div style="width: '.$width.'px;">';
					if ( $set == "member" ) {
						$sql = $database->query("SELECT * FROM `user_list` WHERE `usr_mcard`='Yes' ORDER BY `usr_name`");
						while( $row2 = mysqli_fetch_assoc($sql) ) {
							echo '<img src="'.$tcgcards.'mc-'.$row2['usr_name'].''.$tcgext.'" />';
						}
					}

					else {
						for($x=1;$x<=$row['card_count'];$x++) {
							if ( $x<10 ) {
								echo '<img src="'.$tcgcards.''.$row['card_filename'].'0'.$x.'.'.$tcgext.'" />';
							}
							else {
								echo '<img src="'.$tcgcards.''.$row['card_filename'].''.$x.'.'.$tcgext.'" />';
							}
						}
					}
					echo '</div>
				</td>
			</tr>
			</table><br />';
            if( $row['card_released'] >= '2020-07-31' && $row['card_released'] <= '2020-08-31' ) {
                $rmd = $database->get_assoc("SELECT `rmd_masters` FROM `rmd_event_cards` WHERE `rmd_filename`='".$row['card_filename']."'");
                echo '<table width="100%" class="table table-bordered table-sliced">
                <thead><tr><td width="25%" align="center"><b>1st Anniversary Badge</b></td><td width="75%" align="center"><b>Event Masters</b></td></tr></thead>
                <tbody><tr><td align="center" valign="middle"><img src="/images/cards/rmd-'.$row['card_filename'].'.png" title="rmd-'.$row['card_filename'].'" /></td><td align="center">'.$rmd['rmd_masters'].'</td></tr></tbody>
                </table>';
            } else {}
			echo '</center>';
		}
	}
} // end show released




/********************************************************
 * Action:			Upcoming Decks
 * Description:		Show list of upcoming decks
 */
else if ( $view == "upcoming" ) {
	if ( empty($deck) ) {
		echo '<h1>Cards : Upcoming</h1>
		<p>Below you will find all of the upcoming card decks here at '.$tcgname.' which are either complete (made but haven\'t been released) or incomplete (work in progress). Any decks that have been listed here are no longer <em>subject for claiming or donation</em>.</p>';

		// SHOW SEARCH FORM
		$general->cardSearch('cards','card','Upcoming');

		echo '<table width="100%" cellspacing="0" border="0">
		<tr>
			<td width="50%" valign="top">
				<h3>Recently Made</h3>
				<small>Do not take from these as they aren\'t released yet!</small>

				<center>';
				$r = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Upcoming' ORDER BY `card_id` DESC LIMIT 12");
				while ( $rec = mysqli_fetch_assoc($r) ) {
					$digits = rand(01,$rec['card_count']);
					if ( $digits < 10 ) { $_digits = '0'.$digits; }
					else { $_digits = $digits; }
					$card = $rec['card_filename'].''.$_digits;
					echo '<a href="'.$tcgurl.'cards.php?view=upcoming&deck='.$rec['card_filename'].'"><img src="'.$tcgcards.''.$card.'.'.$tcgext.'"></a>';
				}
				echo '</center>
			</td>

			<td width="1%">&nbsp;</td>

			<td width="49%" valign="top">
				<h3>Upcoming Week\'s Releases</h3>
				<table width="100%" cellspacing="3" class="border">
				<tr>
					<td width="80%" class="headLineSmall">Deck</td>
					<td width="20%" class="headLineSmall">Votes</td>
				</tr>';
				$vs = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Upcoming' ORDER BY `card_votes` DESC LIMIT 4");
				$vc = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `card_status`='Upcoming' ORDER BY `card_votes` DESC LIMIT 4");
				if ( $vc['card_votes'] == 0 ) {
					echo '<tr>
					<td colspan="2" class="tableBodySmall" align="center"><i>There are no voted decks at the moment.</i></td>
					</tr>';
				}
				else {
					while( $v = mysqli_fetch_assoc($vs) ) {
						echo '<tr>
						<td class="tableBodySmall"><a href="'.$tcgurl.'cards.php?view=upcoming&deck='.$v['card_filename'].'">'.$v['card_deckname'].'</a></td>
						<td class="tableBodySmall" align="center">'.$v['card_votes'].'</td>
						</tr>';
					}
				}
				echo '</table><br />

				<h3>Top 5 Donators</h3>
				<small>The data below shows only the decks that are already made.</small>
				<table width="100%" cellspacing="3" class="border">
				<tr>
					<td width="80%" class="headLineSmall">Member</td>
					<td width="20%" class="headLineSmall">Decks</td>
				</tr>';
				$ds = $database->query("SELECT card_donator, COUNT(*) AS `card_count` FROM `tcg_cards` GROUP BY `card_donator` ORDER BY `card_count` DESC LIMIT 5");
				while ( $d = mysqli_fetch_assoc($ds) ) {
					echo '<tr>
					<td class="tableBodySmall"><a href="'.$tcgurl.'members.php?id='.$d['card_donator'].'">'.$d['card_donator'].'</a></td>
					<td class="tableBodySmall" align="center">'.$d['card_count'].'</td>
					</tr>';
				}
				echo '</table>
			</td>
		</tr>
		</table>';

		$c = $database->num_rows("SELECT * FROM `tcg_cards_set`");
		for($i=1; $i<=$c; $i++) {
            $cat = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_id`='$i'");
			$select = $database->query("SELECT * FROM `tcg_cards` WHERE `card_set`='".$cat['set_name']."' AND `card_status`='Upcoming' ORDER BY `card_set` ASC, `card_filename` ASC");
			$counts = mysqli_num_rows($select);
			if ( $counts == 0 ) {}
			else {
				echo '<br /><h3>'.$cat['set_name'].'</h3>
                <table width="100%" class="table table-bordered table-striped"><thead>
				<tr>
					<td width="30%" align="center"><b>Color</b></td>
					<td width="60%" align="center"><b>Deckname</b></td>
					<td width="10%" align="center"><b>#/$</b></td>
				</tr></thead>
                <tbody>';
				while ( $row=mysqli_fetch_assoc($select) ) {
					echo '<tr>
					<td align="center"><font color="'.$row['card_color'].'">'.$row['card_color'].'</font></td>
					<td align="center"><a href="'.$tcgurl.'cards.php?view=upcoming&deck='.$row['card_filename'].'">'.$row['card_deckname'].'</a> ('.$row['card_filename'].')</td>
					<td align="center">';
					if ( $row['card_filename'] == "member" ) {
						$memnum = $database->num_rows("SELECT * FROM `user_list` WHERE `usr_mcard`='Yes'");
						echo $memnum.'/0';
					}
					else {
						echo $row['card_count'].'/'.$row['card_worth'];
					}
					echo '</td></tr>';
				}
				echo '</tbody></table>';
			}
		}
	}

	else {
		$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_filename`='$deck' AND `card_status`='Upcoming'");
		while( $row = mysqli_fetch_assoc($query) ) {
			echo '<h1><font color="'.$row['card_color'].'"><span class="fas fa-tint" aria-hidden="true"></span></font> '.$row['card_deckname'].'</h1>
			<p>'.$row['card_desc'].'</p>

			<center>
				<div class="box-warning">
					<b>This is an upcoming deck!</b><br />Please do not take the cards below until it is released.
				</div><br />

				<table width="100%" cellspacing="0" border="0" class="table table-bordered table-striped">
				<tr>
					<td colspan="3"><h3>'.$row['card_set'].'</h3></td>
				</tr>
				<tr>
					<td width="30%" rowspan="5" align="center" valign="top" height="150">
						<img src="'.$tcgcards.''.$row['card_filename'].'.'.$tcgext.'" /><br />';
						include("admin/wish.php");
					echo '</td>
					<td colspan="2" valign="middle">
						<b>Deck/File Name:</b> '.$row['card_deckname'].' (<i>'.$row['card_filename'].'</i>)
					</td>
				</tr>
				<tr>
					<td width="40%" valign="middle"><b>Made/Donated:</b> '.$row['card_maker'].' / '.$row['card_donator'].'</td>
					<td width="40%" valign="middle">
						<b>Color:</b> <font color="'.$row['card_color'].'">'.$row['card_color'].'</font>
					</td>
				</tr>
				<tr>
					<td valign="middle"><b>Released:</b> '.$row['card_released'].'</td>
					<td valign="middle"><b>Masterable:</b> '.$row['card_mast'].'</td>
				</tr>
				<tr>
					<td colspan="2" valign="middle"><b>Wished by:</b> ';
					$w = $database->query("SELECT * FROM `user_wishlist` WHERE `wlist_deck`='$deck'");
					$c = mysqli_num_rows($w);
					if ( $c != 0 ) {
						$names = array();
						while( $rw = mysqli_fetch_array($w) ) {
							$names[] = '<a href="'.$tcgurl.'members.php?id='.$rw['wlist_name'].'">'.$rw['wlist_name'].'</a>';
						}
						echo implode(', ', $names);
					}
					else { echo "None"; }
					echo '</td>
				</tr>
				<tr><td colspan="2" valign="middle"><b>Mastered by:</b> '.$row['card_masters'].'</td></tr>
				<tr>
					<td width="70%" colspan="3" align="center"><p>';
						// Get total deck width
						$width = $settings->getValue('cards_size_width') * $row['card_break'];
						echo '<div style="width: '.$width.'px;">';
						if( $set == "member" ) {
							$sql = $database->query("SELECT * FROM `user_list` WHERE `usr_mcard`='Yes' ORDER BY `usr_name`");
							while( $row2=mysqli_fetch_assoc($query2) ) {
								echo '<img src="'.$tcgcards.'mc-'.$row2['usr_name'].''.$tcgext.'" />';
							}
						}
						else {
							for($x=1;$x<=$row['card_count'];$x++) {
								if ( $x<10 ) {
									echo '<img src="'.$tcgcards.''.$row['card_filename'].'0'.$x.'.'.$tcgext.'" />';
								} else {
									echo '<img src="'.$tcgcards.''.$row['card_filename'].''.$x.'.'.$tcgext.'" />';
								}
							}
						}
						echo '</div>
					</p></td>
				</tr>
				</table>
			</center>';
		}
	}
} // end show upcoming




/********************************************************
 * Action:			Claimed Decks
 * Description:		Show list of claimed decks
 */
else if ( $view == "claimed" ) {
	echo '<h1>Cards : Claimed Decks</h1>
	<p>Below is the complete list of our claimed decks. These decks are no longer subject for claiming and/or donation and are already on its way to being made. Make sure to check this list first before sending in a donation.</p>';

	// SHOW SEARCH FORM
	$general->cardSearch('donations','deck','Claims');

	echo '<table width="100%" class="table table-sliced table-striped"><thead>
	<tr>
		<td align="center" width="15%"><b>Category</b></td>
		<td align="center" width="25%"><b>Features</b></td>
		<td align="center" width="20%"><b>Set</b></td>
		<td align="center" width="20%"><b>Donator</b></td>
	</tr></thead>
    <tbody>';
	$sql = $database->query("SELECT * FROM `tcg_donations` WHERE `deck_type`='Claims' ORDER BY `deck_filename` ASC");
	while ( $row=mysqli_fetch_assoc($sql) ) {
        $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
        for( $i=1; $i<=$c; $i++ ) {
            $cat = $database->get_assoc("SELECT `cat_name` FROM `tcg_cards_cat` WHERE `cat_id`='".$row['deck_cat']."'");
        }
		echo '<tr>
		<td align="center">'.$cat['cat_name'].'</td>
		<td align="center">'.$row['deck_feature'].'</td>
		<td align="center">'.$row['deck_set'].'</td>
		<td align="center">'.$row['deck_donator'].'</td>
		</tr>';
	}
	echo '</tbody></table>';
} // end show claimed




/********************************************************
 * Action:			Donated Decks
 * Description:		Show list of donated decks
 */
else if ( $view == "donated" ) {
	echo '<h1>Cards : Donated Decks</h1>
	<p>Below is the complete list of our donated decks. These decks are no longer subject for claiming and/or donation and are already on its way to being made. Make sure to check this list first before sending in a donation.</p>';

	// SHOW SEARCH FORM
	$general->cardSearch('donations','deck','Donations');

	echo '<table width="100%" class="table table-bordered table-striped"><thead>
	<tr>
		<td align="center" width="15%"><b>Category</b></td>
		<td align="center" width="35%"><b>Features</b></td>
		<td align="center" width="20%"><b>Set</b></td>
		<td align="center" width="10%"><b>Donator</b></td>
		<td align="center" width="10%"><b>Maker</b></td>
	</tr></thead>
    <tbody>';
	$sql2 = $database->query("SELECT * FROM `tcg_donations` WHERE `deck_type`='Donations' ORDER BY `deck_filename` ASC");
	while( $row = mysqli_fetch_assoc($sql2) ) {
        $c2 = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
        for( $i=1; $i<=$c2; $i++ ) {
            $cat2 = $database->get_assoc("SELECT `cat_name` FROM `tcg_cards_cat` WHERE `cat_id`='".$row['deck_cat']."'");
        }
		echo '<tr>
		<td align="center">'.$cat2['cat_name'].'</td>
		<td align="center"><a href="'.$row['deck_url'].'" target="_blank">'.$row['deck_feature'].'</a></td>
		<td align="center">'.$row['deck_set'].'</td>
		<td align="center">'.$row['deck_donator'].'</td>
		<td align="center">'.$row['deck_maker'].'</td>
		</tr>';
	}
	echo '</tbody></table>';
} // end show donated




/********************************************************
 * Action:			Zipped Files
 * Description:		Show list of weekly released zips for non-working autoupload
 */
else if ( $view == "zips" ) {
	echo '<h1>Cards : Weekly Zips</h1>
	<p>Below is the list of active card zips based on the weekly release. If you are having troubles uploading your cards using your eTCG\'s auto upload function, you can grab the zips below.</p>

	<div class="statLink">
        <a href="" target="_blank">0000-00-00</a>
    </div>';
} // end show zips

include('theme/headers/deck-footer.php');
include($footer);
?>