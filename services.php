<?php
include("admin/class.lib.php");
include($header);

if ( empty($login) ) {
	header("Location: account.php?do=login");
}

if ( empty($form) ) {
	header("Location: account.php");
}

$result = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active' AND `card_worth`='1'") or die("Unable to select from database.");

include('theme/headers/acct-header.php');
###############################
########## DO CLAIMS ##########
###############################
if ( $form == "deck-claims" ) {
	$month = date("m", strtotime("now"));
	$max = $database->num_rows("SELECT count(*) FROM `tcg_donations` WHERE `deck_donator`='$player' AND `deck_date`='$month' GROUP BY `deck_date`");

	// Check if user has less than 5 deck claims
	if ($max < $settings->getValue('xtra_deck_cards')) {
		if ( isset($_POST['submit']) ) {
			$check->Donation();
			$name = $sanitize->for_db($_POST['name']);
			$cat = $sanitize->for_db($_POST['category']);
			$deck = $sanitize->for_db($_POST['deckname']);
			$feat = $sanitize->for_db($_POST['feature']);
			$pass = $sanitize->for_db($_POST['pass']);
			$set = $sanitize->for_db($_POST['set']);
			$date = date("Y-m-d", strtotime("now"));

			$insert = $database->query("INSERT INTO `tcg_donations` (`deck_donator`,`deck_cat`,`deck_filename`,`deck_feature`,`deck_set`,`deck_type`,`deck_pass`,`deck_date`) VALUES ('$name','$cat','$deck','$feat','$set','Claims','$pass','$date')");

			if ( !$insert ) {
				$error[] = "There was an error while processing your claims.<br />
				Kindly send us your claim details instead at <u>".$tcgemail."</u>. ".mysqli_error()."";
			} else {
				$success[] = "Your deck claim has been added to the database.";
			}
		} // end form process

		echo '<h1>Deck Claims</h1>
		<p>Use the form below to submit your claims. Please make sure that the deck you\'re about to claim hasn\'t been claimed by anyone else. All claims are password-protected by the claimant, so don\'t forget to provide any dummy password that you can use when you\'re going to <a href="/services.php?form=deck-donations">send your donations</a>.</p>
        <ul><li>You can only claim <b>5 decks</b> for each month.</li></ul>

		<center>';
		if ( isset($error) ) {
			foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
		}

		if ( isset($success) ) {
			foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
		}
		echo '</center>

		<form method="post" action="/services.php?form=deck-claims">
			<table width="100%" cellspacing="3" class="table table-sliced table-striped">
            <tbody>
			<tr>
				<td width="15%"><b>Name:</b></td>
				<td><input type="text" name="name" value="'.$player.'" readonly style="width:90%;" readonly /></td>
				<td width="15%"><b>Password:</b></td>
				<td><input type="text" name="pass" placeholder="for donation purposes" style="width:90%;"></td>
			</tr>
			<tr>
				<td width="15%"><b>Category:</b></td>
				<td width="35%">
					<select name="category" style="width:97%;">
						<option value="">-----</option>';
					$c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
					for($i=1; $i<=$c; $i++) {
						$cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
						echo '<option value="'.$cat['cat_id'].'">'.$cat['cat_name'].'</option>';
					}
					echo '</select>
				</td>
				<td width="15%"><b>File Name:</b></td>
				<td width="35%"><input type="text" name="deckname" style="width:90%;" placeholder="e.g. samplecard"></td>
			</tr>
			<tr>
				<td><b>Feature:</b></td>
				<td><input type="text" name="feature" placeholder="e.g. Sample Card" style="width:90%;"></td>
				<td><b>Set/Series:</b></td>
				<td>
                    <select name="set" style="width:97%;">
						<option value="">-----</option>';
					$c = $database->num_rows("SELECT * FROM `tcg_cards_set`");
					for($i=1; $i<=$c; $i++) {
						$set = $database->get_assoc("SELECT * FROM `tcg_cards_set` WHERE `set_id`='$i'");
						echo '<option value="'.$set['set_name'].'">'.$set['set_name'].'</option>';
					}
					echo '</select>
                </td>
			</tr>
            </tbody>
            </table>
			<input type="submit" name="submit" id="submit" class="btn-success" value="Send Claims" /> 
			<input type="reset" name="reset" class="btn-danger" value="Reset" />
		</form>';
	}

	else {
		echo '<h1>Claims Limit!</h1>
		<p>You\'ve already reached the maximum number of claims for this month! Kindly wait until next month to make your claims, thank you!</p>';
	} // end max claim check
} // end do claims

##################################
########## DO MASTERIES ##########
##################################
else if ( $form == "masteries" ) {
	if ( $act == "sent" ) {
		if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
			exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
		}

		else {
			$id = $sanitize->for_db($_POST['id']);
			$name = $sanitize->for_db($_POST['name']);
			$mastered = $sanitize->for_db($_POST['mastered']);
			$new = $sanitize->for_db($_POST['new']);

			// Update user's masteries on their profile
			$rowMas1 = $database->query("SELECT * FROM `user_items` WHERE `itm_id`='$id'");
			while ( $rowmas = mysqli_fetch_assoc($rowMas1) ) {
				if ( $rowmas['itm_masteries'] != "None" ) { $mast1="$rowmas[itm_masteries], "; }
				else { $mast1=""; }
			}
			$update = $database->query("UPDATE `user_items` SET `itm_masteries`='$mast1$mastered' WHERE `itm_id`='$id'");

			// Update card's masters on the page
			$rowMas2 = $database->query("SELECT * FROM `tcg_cards` WHERE `card_filename`='$mastered'");
			while ( $rowmas2 = mysqli_fetch_assoc($rowMas2) ) {
				if ( $rowmas2['card_masters'] != "None") { $mast2="$rowmas2[card_masters], "; }
				else { $mast2=""; }
			}
			$update2 = $database->query("UPDATE `tcg_cards` SET `card_masters`='$mast2$name' WHERE `card_filename`='$mastered'");

			$mast = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `card_filename`='$mastered'");

			// Do logs for activities recording
			$date = date("Y-m-d", strtotime("now"));
			$activity = '<span class="fas fa-flag-checkered" aria-hidden="true"></span> <a href="/members.php?id='.$name.'">'.$name.'</a> mastered the <a href="/cards.php?view=released&deck='.$mastered.'">'.$mast['card_deckname'].'</a> deck!';

			// Process masteries if all queries are correct
			if ( $update === TRUE && $update2 === TRUE ) {
				$database->query("UPDATE `user_list` SET `usr_deck`='$new' WHERE `usr_id`='$id'");
				$database->query("INSERT INTO `tcg_activities` (`act_name`,`act_rec`,`act_type`,`act_slug`,`act_date`) VALUES ('$name','$activity','master','$mastered','$date')");

				echo '<h1>Congrats!</h1>
				<p>Congratulations on mastering the '.$mastered.' deck, '.$name.'! Here are your rewards. If you have mastered more than one deck, please do not use the back button to fill out another form (you will receive the same random cards if you do). A copy of these rewards have been recorded on your on-site permanent activity logs.</p>

				<center>';
				$min=1; $max = mysqli_num_rows($result); $rewards = null; $choices = null; $cW = null; $rW = null;
				for($i=1; $i<=$settings->getValue('prize_master_choice'); $i++) {
					$card = "choice$i";
					$card2 = "choicenum$i";
					echo '<img src="'.$tcgcards.''.$_POST[$card].''.$_POST[$card2].'.png" /> ';
					$choices .= $_POST[$card].$_POST[$card2].", ";

                    $cX = $database->get_assoc("SELECT `card_worth` FROM `tcg_cards` WHERE `card_filename`='".$_POST[$card]."'");
                    $cW .= $cX['card_worth'].', ';
				}

				for($i=0; $i<$settings->getValue('prize_master_reg'); $i++) {
					mysqli_data_seek($result,rand($min,$max)-1);
					$row = mysqli_fetch_assoc($result);
					$digits = rand(01,$row['card_count']);
					if ($digits < 10) { $_digits = "0$digits"; }
					else { $_digits = $digits; }
					$card = "$row[card_filename]$_digits";
                    $card2 = $row['card_filename'];
					echo '<img src="'.$tcgcards.''.$card.'.png" border="0" /> ';

                    $rX = $database->get_assoc("SELECT `card_worth` FROM `tcg_cards` WHERE `card_filename`='$card2'");
                    $rW .= $rX['card_worth'].', ';
					$rewards .= $card.", ";
				}

                // Calculate card worth for choice and random
                $cW = substr_replace($cW,"",-2);
                $rW = substr_replace($rW,"",-2);
                $cArr = explode(", ", $cW);
                $rArr = explode(", ", $rW);

                $cSum = 0; $rSum = 0;
                foreach( $cArr as $val ) { $cSum += $val; }
                foreach( $rArr as $val ) { $rSum += $val; }
                $tCards = $cSum + $rSum;

                // Explode all bombs
		        $curValue = explode(' | ', $settings->getValue( 'prize_master_cur' ));
		        $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
		        $curOld = explode(' | ', $general->getItem( 'itm_currency' ));

                $curLog = ''; $curImg = ''; $curCln = '';
                for($i=0; $i<count($curValue); $i++) {
                    $cn = substr_replace($curName[$i],"",-4);
                    // Pluralize the currencies if more than 1
                    if( $curValue[$i] > 1 ) {
                        $var = substr($cn, -1);
                        if( $var == "y" ) {
                            $vtn = substr_replace($cn,"ies",-1);
                        } else if( $var == "o" ) {
                            $vtn = substr_replace($cn,"oes",-1);
                        }
                        else { $vtn = $cn.'s'; }
                    } else { $vtn = $cn; }

                    if( $curValue[$i] != 0 ) {
                        $curLog .= str_repeat(substr_replace(', '.$curName[$i],"",-4), $curValue[$i]);
                        $curImg .= '<img src="/images/'.$curName[$i].'"> [x'.$curValue[$i].']';
                        $curCln .= ', +'.$curValue[$i].' '.$vtn;
                        $curOld[$i] += $curValue[$i];
                    } else {}
                }
                $total = implode(" | ", $curOld);

				echo $curImg;
				echo '<p><strong>Deck Mastery ('.$mastered.'):</strong> '.$choices.''.$rewards.''.$curCln.'</p>
				</center>';

				// Insert acquired data
				$today = date("Y-m-d", strtotime("now"));
				$newSet = $choices."".$rewards."".$curLog;
				$database->query("INSERT INTO `user_logs` (`log_name`,`log_type`,`log_title`,`log_subtitle`,`log_rewards`,`log_date`) VALUES ('$player','Service','Deck Mastery','($mastered)','$newSet','$today')");
				$database->query("UPDATE `user_items` SET `itm_currency`='$total', `itm_cards`=itm_cards+'$tCards' WHERE `itm_name`='$player'");
			}

			else {
				echo '<h1>Error</h1>
				<p>It looks like there was an error in processing your mastery form. Send the information to '.$tcgemail.' and we will send you your rewards ASAP. Thank you and sorry for the inconvenience.</p>';
			}
		}
	} // end form process

	// Show mastery form
	else {
        // Explode bombs
        $curValue = explode(' | ', $settings->getValue( 'prize_master_cur' ));
        $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
        foreach( $curValue as $key => $value ) {
            $tn = substr_replace($curName[$key],"",-4);
            if( $curValue[$key] > 1 ) {
                $var = substr($tn, -1);
                if( $var == "y" ) {
                    $tn = substr_replace($tn,"ies",-1);
                } else if( $var == "o" ) {
                    $tn = substr_replace($tn,"oes",-1);
                }
                else { $tn = $tn.'s'; }
            } else { $tn = $tn; }
            if( $curValue[$key] == 0 ) {}
            else {
                $arrayCur[] = '<li class="spacer">- <b>'.$curValue[$key].'</b> '.$tn.'</li>';
            }
        }
        // Fix all bombs after explosions
        $arrayCur = implode(" ", $arrayCur);
        
		echo '<h1>Master Form</h1>
        <table width="100%">
        <tr>
            <td width="38%" valign="top">
		<p>Congratulations! You\'re almost there to master a deck! But before submitting your masteries, please make sure that you have collected all <b>20 cards</b> of the deck and that none remains pending from your trade post.</p>
        <p><b>You will receive the following rewards:</b>
        <li class="spacer">- <b>'.$settings->getValue('prize_master_choice').'</b> choice cards</li>
        <li class="spacer">- <b>'.$settings->getValue('prize_master_reg').'</b> random cards</li>';
        echo $arrayCur.'</p>
            </td>

            <td width="2%"></td>

            <td width="70%" valign="top">
        <p><b>Please fill out one form for each mastered deck!</b></p>

		<form method="post" action="/services.php?form=masteries&action=sent">
			<input type="hidden" name="id" value="'.$row['usr_id'].'" />
			<input type="hidden" name="name" value="'.$row['usr_name'].'" />';
			for($i=1; $i<=$settings->getValue('prize_master_reg'); $i++) {
                echo '<input type="hidden" name="random'.$i.'" value="'; $general->randtype('Active','1'); echo '" />';
			}
			echo '<table cellspacing="3" width="100%" class="table table-sliced table-striped">
            <tbody>
			<tr>
				<td width="30%"><b>Deck to Master:</b></td>
				<td>
					<select name="mastered" style="width: 97%;">
						<option value="">-----</option>';
					$mast = $database->query("SELECT * FROM `tcg_cards` WHERE `card_mast`='Yes' AND `card_status`='Active' ORDER BY `card_filename` ASC");
					while ( $mas = mysqli_fetch_assoc($mast) ) {
						echo '<option value="'.$mas['card_filename'].'">'.$mas['card_deckname'].' ('.$mas['card_filename'].")</option>\n";
					} // end while
					echo '</select>
				</td>
			</tr>
			<tr>
				<td><b>New Collecting:</b></td>
				<td>
					<select name="new" style="width: 97%;">
						<option value="">-----</option>';
					$coll = $database->query("SELECT * FROM `tcg_cards` WHERE `card_mast`='Yes' AND `card_status`='Active' ORDER BY `card_filename` ASC");
					while( $col = mysqli_fetch_assoc($coll) ) {
						echo '<option value="'.$col['card_filename'].'">'.$col['card_deckname'].' ('.$col['card_filename'].")</option>\n";
					} // end while
					echo '</select>
				</td>
			</tr>
			<tr>
				<td valign="top"><b>Choice Cards:</b></td>
				<td>';
				for($i=1; $i<=$settings->getValue('prize_master_choice'); $i++) {
					echo '<select name="choice'.$i.'" style="width: 80%;">
						<option value="">---</option>';
					$choice = $database->query("SELECT * FROM `tcg_cards` WHERE `card_mast`='Yes' AND `card_status`='Active' ORDER BY `card_filename` ASC");
					while( $cho = mysqli_fetch_assoc($choice) ) {
						$filename = stripslashes($cho['card_filename']);
						echo '<option value="'.$filename.'">'.$cho['card_deckname'].' ('.$filename.")</option>\n";
					}
					echo '</select> 
					<input type="text" name="choicenum'.$i.'" placeholder="00" style="width:7%;" maxlength="2" /><br />';
				}
				echo '</td>
			</tr>
            </tbody>
			</table>
			<input type="submit" name="submit" class="btn-success" value="Send Mastery" /> 
			<input type="reset" name="reset" class="btn-danger" value="Reset" />
		</form>
            </td>
        </tr>
        </table>';
	}
} // end do masteries

##########################################
########## DO SPECIAL MASTERIES ##########
##########################################
else if ($form == "special-masteries") {
    if ($act == "sent") {
        if( !isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST" ) { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
        else {
            $id = $sanitize->for_db($_POST['id']);
            $name = $sanitize->for_db($_POST['name']);
            $mastered = $sanitize->for_db($_POST['mastered']);
            $set = $sanitize->for_db($_POST['set']);

            if( $mastered == "mcard" ) { $mcard = "Member Card"; }
            else if( $mastered == "ecard" ) { $mcard = "Event Card"; }
            
            // Simplify settings value
            $spcreg = $settings->getValue('prize_special_reg');
            $spccur = $settings->getValue('prize_special_cur');

            // CHECK SETS FOR DUPLICATE VALUES
            $dup_check = $database->query("SELECT itm_name, itm_$mastered FROM `user_items` WHERE itm_name='$name'");
            if( mysqli_num_rows($dup_check) > 0 ) {
                while( $row = mysqli_fetch_assoc($dup_check) ) {
                    $newset_array = explode(', ', $set);
                    $newset_count = count($newset_array);
                    $dontadd = 0;

                    for( $i = 0; $i < $newset_count; $i++ ) {
                        $dontadd += substr_count($row[$mastered], $newset_array[$i]);
                    }
    				
                    if ($dontadd == 0) {
                        $new = $row[$mastered].', '.$set;
                        $database->query("UPDATE `user_items` SET `itm_$mastered`='$new' WHERE `itm_name`='$name'");

                        // Declare empty strings
                        $rewards = '';
                        $curLog = '';
                        $curImg = '';
                        $curCln = '';
                        $rw = '';

                        echo '<h1>Special Mastery ('.$mcard.')</h1>';
                        $min=1; $max=mysqli_num_rows($result);
                        for( $i = 0; $i < $spcreg; $i++ ) {
                            mysqli_data_seek($result,rand($min,$max)-1);
                            $row=mysqli_fetch_assoc($result);
                            $digits = rand(01,$row['card_count']);
                            if ($digits < 10) { $_digits = "0$digits"; }
                            else { $_digits = $digits; }
                            $card = "$row[card_filename]$_digits";
		                    $card2 = $row['card_filename'];
							echo '<img src="'.$tcgcards.''.$card.'.png" border="0" /> ';

		                    $rX = $database->get_assoc("SELECT `card_worth` FROM `tcg_cards` WHERE `card_filename`='$card2'");
		                    $rW .= $rX['card_worth'].', ';
							$rewards .= $card.", ";
						}
						$rewards = substr_replace($rewards,"",-2);

		                // Calculate card worth for random
		                $rW = substr_replace($rW,"",-2);
		                $rArr = explode(", ", $rW);

		                $rSum = 0;
		                foreach( $rArr as $val ) { $rSum += $val; }

                        // Explode all bombs
				        $curValue = explode(' | ', $settings->getValue( 'prize_special_cur' ));
				        $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
				        $curOld = explode(' | ', $general->getItem( 'itm_currency' ));

		                for($i=0; $i<count($curValue); $i++) {
		                    // Pluralize the currencies if more than 1
		                    $cn = substr_replace($curName[$i],"",-4);
		                    if( $curValue[$i] > 1 ) {
		                        $var = substr($cn, -1);
		                        if( $var == "y" ) {
		                            $vtn = substr_replace($cn,"ies",-1);
		                        } else if( $var == "o" ) {
		                            $vtn = substr_replace($cn,"oes",-1);
		                        }
		                        else { $vtn = $cn.'s'; }
		                    } else { $vtn = $cn; }

		                    if( $curValue[$i] != 0 ) {
		                        $curLog .= str_repeat(substr_replace(', '.$curName[$i],"",-4), $curValue[$i]);
		                        $curImg .= '<img src="/images/'.$curName[$i].'"> [x'.$curValue[$i].']';
		                        $curCln .= ', +'.$curValue[$i].' '.$vtn;
		                        $curOld[$i] += $curValue[$i];
		                    } else {}
		                }
		                $total = implode(" | ", $curOld);

						echo $curImg;
						echo '<p><strong>Special Mastery ('.$mcard.'):</strong> '.$rewards.''.$curCln.'</p>
						</center>';

						// Insert acquired data
						$today = date("Y-m-d", strtotime("now"));
						$newSet = $rewards."".$curLog;
						$database->query("INSERT INTO `user_logs` (`log_name`,`log_type`,`log_title`,`log_subtitle`,`log_rewards`,`log_date`) VALUES ('$player','Service','Special Mastery','($mcard)','$newSet','$today')");
						$database->query("UPDATE `user_items` SET `itm_currency`='$total', `itm_cards`=itm_cards+'$rSum' WHERE `itm_name`='$player'");
                    }

                    else {
                    	echo 'Seems like you\'ve already used one of the cards on your submitted set. Please go back and recheck your cards.';
                    }
                }
            }
        }
    } else {
        $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_name`='$player'");
        echo '<h1>Special Mastery Form</h1>
        <p>If you have mastered <i>one set</i> of member or event cards, fill out the form below to receive your rewards.<br />
        <b>Please fill out one form for each mastered sets!</b><br />
        <b><u>1 set = 10 cards gained</u></b></p>
        <form method="post" action="/services.php?form=special-masteries&action=sent">
        <input type="hidden" name="id" value="'.$row['usr_id'].'" />
        <input type="hidden" name="name" value="'.$row['usr_name'].'" />
        <center><table cellspacing="3" width="80%" class="border">
        <tr>
            <td class="headLine" width="30%">Mastery Type:</td>
            <td class="tableBody"><input type="radio" name="mastered" value="mcard"> Member Card &nbsp; <input type="radio" name="mastered" value="ecard"> Event Card
        </tr>
        <tr>
            <td class="headLine" width="30%">Set Completed:</td>
            <td class="tableBody"><textarea name="set" rows="3" style="width:94%;"></textarea></td>
        </tr>
        <tr><td colspan="2" class="tableBody" align="center"><input type="submit" name="submit" class="btn-success" value="Send Special Mastery!" /></td></tr>
        </table><center>
        </form>';
    }
} // end do special masteries

##################################
########## DO LEVEL UPS ##########
##################################
else if ( $form == "level-up" ) {
	if ( $act == "sent" ) {
		if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
			exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
		}

		else {
			$check->Value();
			$id = $sanitize->for_db($_POST['id']);
			$name = $sanitize->for_db($_POST['name']);
			$email = $sanitize->for_db($_POST['email']);
			$level = $sanitize->for_db($_POST['newlevel']);

			// Check level for activity recording
			$date = date("Y-m-d", strtotime("now"));
			$update = $database->query("UPDATE `user_list` SET `usr_level`='$level' WHERE `usr_id`='$id'");
			$lvlnow = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_id`='$id'");
			$lvlNew = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `lvl_id`='$level'"); // Fetch new level
			$diff = $lvlnow['usr_level'] - 1;
			$lvlOld = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `lvl_id`='$diff'");
			$lvlSlug = $lvlOld['lvl_name'] .' > '. $lvlNew['lvl_name'];
			$activity = '<span class="fas fa-level-up-alt" aria-hidden="true"></span> <a href="/members.php?id='.$name.'">'.$name.'</a> ranked up from '.$lvlOld['lvl_name'].' to '.$lvlNew['lvl_name'].'!';

			// Insert data if queries are correct
			if ( $update === TRUE ) {
				$database->query("INSERT INTO `tcg_activities` (`act_name`,`act_rec`,`act_type`,`act_slug`,`act_date`) VALUES ('$name','$activity','level','$lvlSlug','$date')");

				echo '<h1>Congrats!</h1>
				<p>Congrats on leveling up, '.$name.'! Here are your rewards. If you have leveled up more than once, please do not use the back button to fill out another form (you will receive the same random cards if you do). A copy of these rewards have been recorded on your on-site permanent activity logs.</p>

				<center>';
				$min=1; $max = mysqli_num_rows($result); $rewards = null; $choices = null; $cW = null; $rW = null;
				for($i=1; $i<=$settings->getValue('prize_level_choice'); $i++) {
					$card = "choice$i";
					$card2 = "choicenum$i";
					echo '<img src="'.$tcgcards.''.$_POST[$card].''.$_POST[$card2].'.png" />';
					$choices .= $_POST[$card].$_POST[$card2].", ";

                    $cX = $database->get_assoc("SELECT `card_worth` FROM `tcg_cards` WHERE `card_filename`='".$_POST[$card]."'");
                    $cW .= $cX['card_worth'].', ';
				}

				for($i=0; $i<$settings->getValue('prize_level_reg'); $i++) {
					mysqli_data_seek($result,rand($min,$max)-1);
					$row = mysqli_fetch_assoc($result);
					$digits = rand(01,$row['card_count']);
					if ($digits < 10) { $_digits = "0$digits"; }
					else { $_digits = $digits; }
					$card = "$row[card_filename]$_digits";
                    $card2 = $row['card_filename'];
					echo '<img src="'.$tcgcards.''.$card.'.png" border="0" /> ';
					$rewards .= $card.", ";

                    $rX = $database->get_assoc("SELECT `card_worth` FROM `tcg_cards` WHERE `card_filename`='$card2'");
                    $rW .= $rX['card_worth'].', ';
				}
				
                // Count card worth of choice and random
				$cW = substr_replace($cW,"",-2);
                $rW = substr_replace($rW,"",-2);
                $cArr = explode(", ", $cW);
                $rArr = explode(", ", $rW);

                $cSum = 0; $rSum = 0;
                foreach( $cArr as $val ) { $cSum += $val; }
                foreach( $rArr as $val ) { $rSum += $val; }
                $tCards = $cSum + $rSum;

				// Explode all bombs
		        $curValue = explode(' | ', $settings->getValue( 'prize_level_cur' ));
		        $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
		        $curOld = explode(' | ', $general->getItem( 'itm_currency' ));

		        $curLog = ''; $curImg = ''; $curCln = '';
                for($i=0; $i<count($curValue); $i++) {
                    $cn = substr_replace($curName[$i],"",-4);
                    // Pluralize the currencies if more than 1
                    if( $curValue[$i] > 1 ) {
                        $var = substr($cn, -1);
                        if( $var == "y" ) {
                            $vtn = substr_replace($cn,"ies",-1);
                        } else if( $var == "o" ) {
                            $vtn = substr_replace($cn,"oes",-1);
                        }
                        else { $vtn = $cn.'s'; }
                    } else { $vtn = $cn; }

                    if( $curValue[$i] != 0 ) {
                        $curLog .= str_repeat(substr_replace(', '.$curName[$i],"",-4), $curValue[$i]);
                        $curImg .= '<img src="/images/'.$curName[$i].'"> [x'.$curValue[$i].']';
                        $curCln .= ', +'.$curValue[$i].' '.$vtn;
                        $curOld[$i] += $curValue[$i];
                    } else {}
                }
                $total = implode(" | ", $curOld);

				echo $curImg;
				echo '<p><strong>Level Up ('.$level.'. '.$lvlNew['lvl_name'].'):</strong> '.$choices.''.$rewards.''.$curCln.'</p>
				</center>';

				// Insert acquired data
				$today = date("Y-m-d", strtotime("now"));
				$newSet = $choices."".$rewards."".$curLog;
				$database->query("INSERT INTO `user_logs` (`log_name`,`log_type`,`log_title`,`log_subtitle`,`log_rewards`,`log_date`) VALUES ('$player','Service','Level Up','(".$level.". ".$lvlNew['lvl_name'].")','$newSet','$today')");
				$database->query("UPDATE `user_items` SET `itm_currency`='$total', `itm_cards`=itm_cards+'$tCards' WHERE `itm_name`='$player'");
			}

			else {
				echo '<h1>Error</h1>
				<p>It looks like there was an error in processing your level up form. Send the information to '.$tcgemail.' and we will send you your rewards ASAP. Thank you and sorry for the inconvenience.</p>';
			}
		}
	} // end form process

	// Show level up form
	else {
        // Explode bombs
        $curValue = explode(' | ', $settings->getValue( 'prize_level_cur' ));
        $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
        foreach( $curValue as $key => $value ) {
            $tn = substr_replace($curName[$key],"",-4);
            if( $curValue[$key] > 1 ) {
                $var = substr($tn, -1);
                if( $var == "y" ) {
                    $tn = substr_replace($tn,"ies",-1);
                } else if( $var == "o" ) {
                    $tn = substr_replace($tn,"oes",-1);
                }
                else { $tn = $tn.'s'; }
            } else { $tn = $tn; }
            if( $curValue[$key] == 0 ) {}
            else {
                $arrayCur[] = '<li class="spacer">- <b>'.$curValue[$key].'</b> '.$tn.'</li>';
            }
        }
        // Fix all bombs after explosions
        $arrayCur = implode(" ", $arrayCur);

		$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$login'");

        $sum = $row['usr_level'] + 1;
        $lvlC = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `lvl_id`='$sum'");
        $lvl_cards = $lvlC['lvl_cards'];

		echo '<h1>Level Up Form</h1>
        <table width="100%">
        <tr>
            <td width="38%" valign="top">
		<p>Hey there, '.$player.'! As of now, you are currently at Level '.$row['usr_level'].'; which means, you need <b>'.$lvl_cards.' cards</b> to move on to the next level. The form already determines your next level, so you only need to select the choice of cards that you need at the moment.</p>
        <p><b>You will receive the following rewards:</b>
        <li class="spacer">- <b>'.$settings->getValue('prize_level_choice').'</b> choice cards</li>
        <li class="spacer">- <b>'.$settings->getValue('prize_level_reg').'</b> random cards</li>';
        echo $arrayCur.'</p>
            </td>

            <td width="2%"></td>

            <td width="70%" valign="top">
        <p><b>Please fill out one form for each level up!</b></p>
		<form method="post" action="/services.php?form=level-up&action=sent">
			<input type="hidden" name="id" value="'.$row['usr_id'].'" />
			<input type="hidden" name="name" value="'.$row['usr_name'].'" />
			<input type="hidden" name="email" value="'.$row['usr_email'].'" />';
			for($i=1; $i<=$settings->getValue('prize_level_reg'); $i++) {
				echo '<input type="hidden" name="random'.$i.'" value="'; $general->randtype('Active','1'); echo '" />';
			}

			echo '<table cellspacing="3" width="100%" class="table table-sliced table-striped">
            <tbody>
			<tr>
				<td width="30%"><b>New Level:</b></td>
				<td>';
				if ($row['usr_level'] == "10") {
                    echo '<input type="text" name="newlevel" style="width:90%;" value="10" readonly /">';
                } else {
                    $lvlCurrent = $row['usr_level'] + 1;
                    $l = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `lvl_id`='$lvlCurrent'");
                    echo '<select name="newlevel" style="width: 97%;">
                    <option value="'.$l['lvl_id'].'">'.$l['lvl_name'].' (Level '.$l['lvl_id'].')</option>';
                    echo '</select>';
                }
				echo '</td>
			</tr>
			<tr>
				<td valign="top"><b>Choice Cards:</b></td>
				<td>';
				for($i=1; $i<=$settings->getValue('prize_level_choice'); $i++) {
					echo '<select name="choice'.$i.'" style="width: 80%;">
						<option value="">---</option>';
					$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_mast`='Yes' AND `card_status`='Active' ORDER BY `card_filename` ASC");
					while ( $row2 = mysqli_fetch_assoc($query) ) {
						$filename = stripslashes($row2['card_filename']);
						echo '<option value="'.$filename.'">'.$row2['card_deckname'].' ('.$filename.')</option>';
					}
					echo '</select> 
					<input type="text" name="choicenum'.$i.'" placeholder="00" style="width:8%;" maxlength="2" /><br />';
				}
				echo '</td>
			</tr>
            </tbody>
            </table>
			<input type="submit" name="submit" class="btn-success" value="Level Up" /> 
			<input type="reset" name="reset" class="btn-danger" value="Reset" />
		</form>
            </td>
        </tr>
        </table>';
	}
} // end do level ups

########################################
########## DO TRADING REWARDS ##########
########################################
else if ( $form == "trading-rewards" ) {
	// Add trade logs form
	if ( $act == "add-trades" ) {
		if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
			exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
		}

		else {
			$name = $sanitize->for_db($_POST['name']);
			$out = htmlspecialchars(strip_tags($_POST['out']));
			$inc = htmlspecialchars(strip_tags($_POST['inc']));
			$to = htmlspecialchars(strip_tags($_POST['to']));
			$date = $_POST['year']."-".$_POST['month']."-".$_POST['day'];

			$total = explode(",", $out);
			$total = count($total);

			$result = $database->query("INSERT INTO `user_trades` (`trd_name`,`trd_trader`,`trd_out`,`trd_inc`,`trd_date`) VALUES ('$name','$to','$out','$inc','$date')") or print("Can't insert into table trades_$name.<br />" . $result . "<br />Error:" . mysqli_connect_error($result));

			// Insert acquired data
			if ( $result === TRUE ) {
				$database->query("UPDATE `user_trades_rec` SET `trd_points`=trd_points+'$total', `trd_date`='$date' WHERE `trd_name`='$name'");
				echo '<h1>Trade Logs Added</h1>
				<p>Your external trading logs has been added to the database!</p>';
			} else {
				echo '<h1>Trade Logs Error</h1>
				<p>It seems that there was a problem processing your trade logs form. Kindly send your information to <a href="mailto:'.$tcgemail.'">'.$tcgemail.'</a> or through our Discord server. Thank you and we apologize for the inconvenience.</p>';
			}
		}
	} // end add trade logs form process

	// Redeem trade rewards
	else if ( $act == "redeem" ) {
		if ( !isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST" ) {
			exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
		}

		else {
			$sets = $sanitize->for_db($_POST['sets']);
			$name = $sanitize->for_db($_POST['name']);
			$diff = 25*$sets;

			$update = $database->query("UPDATE `user_trades_rec` SET `trd_points`=trd_points-'$diff', `trd_turnins`=trd_turnins+'$sets', `trd_redeems`=trd_redeems+'$diff' WHERE `trd_name`='$name'") or print("Can't insert into table user_trades.<br />" . $update . "<br />Error:" . mysqli_connect_error($update));

			// Process form if queries are correct
			if ( $update === TRUE ) {
				echo '<h1>Redeem Rewards</h1>
				<p>Get your redeemed rewards for '.$sets.' stamp cards below!</p><center>';
				$min = 1; $max = mysqli_num_rows($result); $rewards = null; $total = $settings->getValue( 'prize_trade_reg' ) * $sets; $rW = null;

                // Explode all bombs
		        $curValue = explode(' | ', $settings->getValue( 'prize_trade_cur' ));
		        $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
		        $curOld = explode(' | ', $general->getItem( 'itm_currency' ));
		        $curLog = ''; $curImg = ''; $curCln = '';
                for($i=0; $i<count($curValue); $i++) {
                    $cn = substr_replace($curName[$i],"",-4);
                    // Pluralize the currencies if more than 1
                    if( $curValue[$i] > 1 ) {
                        $var = substr($cn, -1);
                        if( $var == "y" ) {
                            $vtn = substr_replace($cn,"ies",-1);
                        } else if( $var == "o" ) {
                            $vtn = substr_replace($cn,"oes",-1);
                        }
                        else { $vtn = $cn.'s'; }
                    } else { $vtn = $cn; }

                    if( $curValue[$i] != 0 ) {
                        $curLog .= str_repeat(substr_replace(', '.$curName[$i],"",-4), $curValue[$i] * $sets);
                        $curImg .= '<img src="/images/'.$curName[$i].'"> [x'.$curValue[$i] * $sets.']';
                        $curCln .= ', +'.$curValue[$i] * $sets.' '.$vtn;
                        $curOld[$i] += $curValue[$i] * $sets;
                    } else {}
                }
                $total2 = implode(" | ", $curOld);

				for($i=0; $i<$total; $i++) {
					mysqli_data_seek($result,rand($min,$max)-1);
					$row = mysqli_fetch_assoc($result);
					$digits = rand(01,$row['card_count']);
					if ($digits < 10) { $_digits = "0$digits"; }
					else { $_digits = $digits; }
					$card = "$row[card_filename]$_digits";
                    $card2 = $row['card_filename'];
					echo '<img src="'.$tcgcards.''.$card.'.png" border="0" /> ';
					$rewards .= $card.", ";

                    $rX = $database->get_assoc("SELECT `card_worth` FROM `tcg_cards` WHERE `card_filename`='$card2'");
                    $rW .= $rX['card_worth'].', ';
				}
				$rewards = substr_replace($rewards,"",-2);

                // Count card worth of choice and random
				$rW = substr_replace($rW,"",-2);
                $rArr = explode(", ", $rW);
                $rSum = 0;
                foreach( $rArr as $val ) { $rSum += $val; }

				echo $curImg;
				echo '<p><strong>Trade Points (x'.$sets.'):</strong> '.$rewards.', '.$curCln.'</p>
				</center>';

				// Insert acquired data
				$today = date("Y-m-d", strtotime("now"));
				$newSet = $rewards.''.$curLog;
				$database->query("INSERT INTO `user_logs` (`log_name`,`log_type`,`log_title`,`log_subtitle`,`log_rewards`,`log_date`) VALUES ('$name','Service','Trade Points','(x$sets)','$newSet','$today')");
				$database->query("UPDATE `user_items` SET `itm_currency`='$total2', `itm_cards`=itm_cards+'$rSum' WHERE `itm_name`='$name'");
			}

			else {
				echo '<h1>Trading Rewards: Error</h1>
				<p>It seems that there was a problem processing your trade logs form. Kindly send your information to <a href="mailto:'.$tcgemail.'">'.$tcgemail.'</a> or through our Discord server. Thank you and we apologize for the inconvenience.</p>';
			}
		}
	} // end redeem trade rewards form process

	// Show default trading rewards page
	else {
		$chk = $database->get_assoc("SELECT * FROM `user_trades_rec` WHERE `trd_name`='$player'");
		if ( $chk['trd_points'] < 25 ) {
			$current_month = date("F");
			$current_date = date("d");
			$current_year = date("Y");
			$cur_month = date("m");

			echo '<h1>Add Trade Logs</h1>
            <table width="100%">
            <tr>
                <td width="38%" valign="top">
			<p>You don\'t have enough cards traded on your on-site trade logs, hence, a fewer amount of trade points! Kindly add your external trade logs first before claiming a new set of rewards. Do not worry about counting the cards that you have traded away, the system will automatically count it for you!</p>
			<p>- Make sure to <u>add ONLY the logs that you haven\'t turned in yet.</u></p>
                </td>
                
                <td width="2%"></td>

                <td width="70%" valign="top">
			<center>
				<div class="box-info">You now have a total worth of <b>'.$chk['trd_points'].'</b> trading points on your record.</div>
			</center>
			<br />

			<form method="post" action="/services.php?form=trading-rewards&action=add-trades">
				<input type="hidden" name="name" value="'.$player.'" />
				<table width="100%" cellspacing="3" class="table table-sliced table-striped">
                <tbody>
				<tr>
					<td width="15%"><b>Date:</b></td>
					<td width="45%">
						<select name="month" style="width:40%;">
							<option value="'.$cur_month.'">'.$current_month.'</option>';
						for($m=1; $m<=12; $m++) {
							if ($m < 10) { $_mon = "0$m"; }
							else { $_mon = $m; }
							echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
						}
						echo '</select> 
						<input type="text" name="day" id="day" size="2" value="'.$current_date.'" /> ';
						$start = date('Y');
						$end = $start-10;
						$yearArray = range($start,$end);
						echo '<select name="year">
							<option value="">-----</option>';
						foreach ($yearArray as $year) {
							$selected = ($year == $start) ? 'selected' : '';
							echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
						}
						echo '</select>
					</td>
                </tr>
                <tr>
					<td><b>Traded With:</b></td>
					<td>
						<select name="to" style="width: 98%;" />';
                        $mem = $database->query("SELECT `usr_name` FROM `user_list` ORDER BY `usr_name` ASC");
						while ( $mr = mysqli_fetch_assoc($mem) ) {
							$name = stripslashes($mr['usr_name']);
							echo '<option value="'.$name.'">'.$name."</option>\n";
						}
						echo '</select>
					</td>
				</tr>
				<tr>
					<td><b>Outgoing:</b></td>
					<td><input type="text" name="out" placeholder="e.g. blackcats04, rubies10, mc-'.$player.'" style="width:90%;" /></td>
				</tr>
				<tr>
					<td><b>Incoming:</b></td>
					<td><input type="text" name="inc" placeholder="e.g. tigers11, winter17, mc-Player" style="width:90%;" /></td>
				</tr>
                </tbody>
                </table>
				<input type="submit" name="submit" class="btn-success" value="Send Logs" /> 
				<input type="reset" name="reset" class="btn-danger" value="Reset" />
			</form>
                </td>
            </tr>
            </table>';
		}

        else if( $sub == "add-logs" ) {
            $current_month = date("F");
			$current_date = date("d");
			$current_year = date("Y");
			$cur_month = date("m");

			echo '<h1>Add Trade Logs</h1>
            <table width="100%">
            <tr>
                <td width="38%" valign="top">
			<p>You don\'t have enough cards traded on your on-site trade logs, hence, a fewer amount of trade points! Kindly add your external trade logs first before claiming a new set of rewards. Do not worry about counting the cards that you have traded away, the system will automatically count it for you!</p>
			<p>- Make sure to <u>add ONLY the logs that you haven\'t turned in yet.</u></p>
                </td>
                
                <td width="2%"></td>

                <td width="70%" valign="top">
			<center>
				<div class="box-info">You now have a total worth of <b>'.$chk['trd_points'].'</b> trading points on your record.</div>
			</center>
			<br />

			<form method="post" action="/services.php?form=trading-rewards&action=add-trades">
				<input type="hidden" name="name" value="'.$player.'" />
				<table width="100%" cellspacing="3" class="table table-sliced table-striped">
                <tbody>
				<tr>
					<td width="15%"><b>Date:</b></td>
					<td width="45%">
						<select name="month" style="width:40%;">
							<option value="'.$cur_month.'">'.$current_month.'</option>';
						for($m=1; $m<=12; $m++) {
							if ($m < 10) { $_mon = "0$m"; }
							else { $_mon = $m; }
							echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
						}
						echo '</select> 
						<input type="text" name="day" id="day" size="2" value="'.$current_date.'" /> ';
						$start = date('Y');
						$end = $start-10;
						$yearArray = range($start,$end);
						echo '<select name="year">
							<option value="">-----</option>';
						foreach ($yearArray as $year) {
							$selected = ($year == $start) ? 'selected' : '';
							echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
						}
						echo '</select>
					</td>
                </tr>
                <tr>
					<td><b>Traded With:</b></td>
					<td>
						<select name="to" style="width: 98%;" />';
                        $mem = $database->query("SELECT `usr_name` FROM `user_list` ORDER BY `usr_name` ASC");
						while ( $mr = mysqli_fetch_assoc($mem) ) {
							$name = stripslashes($mr['usr_name']);
							echo '<option value="'.$name.'">'.$name."</option>\n";
						}
						echo '</select>
					</td>
				</tr>
				<tr>
					<td><b>Outgoing:</b></td>
					<td><input type="text" name="out" placeholder="e.g. blackcats04, rubies10, mc-'.$player.'" style="width:90%;" /></td>
				</tr>
				<tr>
					<td><b>Incoming:</b></td>
					<td><input type="text" name="inc" placeholder="e.g. tigers11, winter17, mc-Player" style="width:90%;" /></td>
				</tr>
                </tbody>
                </table>
				<input type="submit" name="submit" class="btn-success" value="Send Logs" /> 
				<input type="reset" name="reset" class="btn-danger" value="Reset" />
			</form>
                </td>
            </tr>
            </table>';
        }

		else {
            // Explode bombs
            $curValue = explode(' | ', $settings->getValue( 'prize_trade_cur' ));
            $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
            foreach( $curValue as $key => $value ) {
                $tn = substr_replace($curName[$key],"",-4);
                if( $curValue[$key] > 1 ) {
                    $var = substr($tn, -1);
                    if( $var == "y" ) {
                        $tn = substr_replace($tn,"ies",-1);
                    } else if( $var == "o" ) {
                        $tn = substr_replace($tn,"oes",-1);
                    }
                    else { $tn = $tn.'s'; }
                } else { $tn = $tn; }
                if( $curValue[$key] == 0 ) {}
                else {
                    $arrayCur[] = '<li class="spacer">- <b>'.$curValue[$key].'</b> '.$tn.'</li>';
                }
            }
            // Fix all bombs after explosions
            $arrayCur = implode(" ", $arrayCur);

            // Get total stamp cards
            function getPoints($divisor, $dividend) {
                $quotient = (int)($divisor / $dividend);
                $remainder = $divisor % $dividend;
                return array( $quotient, $remainder );
            }
            list($quotient, $remainder) = getPoints($chk['trd_points'], 25);

			echo '<h1>Trading Rewards</h1>
            <table width="100%">
            <tr>
                <td width="38%" valign="top">
			<p>You currently have a total of <b>'.$chk['trd_points'].'</b> trade points on your record!</p>
            <p>Please keep in mind that the form automatically counts the total stamp cards that you can redeem based on your trade points. Hence, you can\'t change how many stamp cards you\'ll be redeeming.</p>
                </td>

                <td width="2%"></td>

                <td width="70%" valign="top">
            <p><b>You will receive the following rewards:</b>
            <li class="spacer">- <b>'.$settings->getValue('prize_trade_reg').'</b> random cards for each stamp cards</li>';
            echo $arrayCur.'<br />
			<form method="post" action="/services.php?form=trading-rewards&action=redeem">
				<input type="hidden" name="name" value="'.$player.'" />
				<table width="100%" cellspacing="3" class="table table-sliced table-striped">
                <tbody>
				<tr>
					<td width="15%"><b>Stamp Cards:</b></td>
					<td width="35%"><input type="text" name="sets" style="width:90%" value="'.$quotient.'" readonly /></td>
                </tr>
                </tbody>
                </table>
				<input type="submit" name="submit" class="btn-success" value="Redeem my trading rewards!" />
                <button type="button" onclick="window.location.href=\'services.php?form=trading-rewards&sub=add-logs\';" class="btn-primary">Or add more logs</button>
			</form>
                </td>
            </tr>
            </table>';
		}
	}
} // end do trading rewards

######################################
########## DO CONTACT ADMIN ##########
######################################
else if ( $form == "contact" ) {
	if ( isset($_POST['submit']) ) {
		$from = $sanitize->for_db($_POST['sender']);
		$to = $sanitize->for_db($_POST['recipient']);
		$date = date("Y-m-d H:i:s", strtotime("now"));
        $message = $_POST['message'];
		$message = nl2br($message);
        $message = str_replace("'", "\'", $message);

		$insert = $database->query("INSERT INTO `user_mbox` (`msg_subject`,`msg_text`,`msg_sender`,`msg_recipient`,`msg_box_from`,`msg_box_to`,`msg_see_from`,`msg_see_to`,`msg_del_from`,`msg_del_to`,`msg_origin`,`msg_date`) VALUES ('General Contact','$message','$from','$to','Out','In','0','1','0','0','','$date')");

		// Process form if queries are correct
		if ( !$insert ) {
			$error[] = "Sorry, there was an error while processing your form.<br />
			Send the information to ".$tcgemail." and we will send you a reply ASAP. ".mysqli_error()."";
		}

		else {
			$database->query("UPDATE `user_mbox` SET `msg_origin`=LAST_INSERT_ID() WHERE `msg_id`=LAST_INSERT_ID()");
			$success[] = "Thank you for sending in a contact form! I will try to get back to you within the next few days.<br />
			If you don't hear from me within a week, please poke me via Discord (Aki#6429).";
		}
	} // end form process

	// Show contact form
	echo '<h1>General Contact</h1>
	<p>If you have any inquiries regarding '.$tcgname.' that you wish to ask or share to the administrator, please use the form below to keep in touch with them. Kindly give them at least 48 hours to get back to you! If for any reason that you haven\'t heard from them after the given time, you can poke them directly on our Discord server as chances are your email was missed or it didn\'t reach them at all.</p>

	<center>';
	if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
	if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
	echo '</center>

	<form method="post" action="/services.php?form=contact">
		<input type="hidden" name="sender" value="'.$player.'" />
		<input type="hidden" name="recipient" value="'.$tcgowner.'" />
		<textarea name="message" rows="5" style="width:96%;">Type your message here.</textarea><br />
		<input type="submit" name="submit" class="btn-success" value="Send Inquiry" /> 
		<input type="reset" name="reset" class="btn-danger" value="Reset" />
	</form>';
} // end do contact admin

##################################################
############## DO DOUBLES EXCHANGE ###############
# Use this only if you don't have anything else. #
##################################################
else if ( $form == "doubles" ) {
	if ( $act == "sent" ) {
		if ( !isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST" ) {
			exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
		}

		else {
			$from = $sanitize->for_db($_POST['sender']);
			$to = $sanitize->for_db($_POST['recipient']);
			$cards = htmlspecialchars(strip_tags($_POST['cards']));
			$date = date("Y-m-d H:i:s", strtotime("now"));

			$total = explode(",", $cards);
			$total = count($total);

			$message = "Hello, ".$tcgowner."! I have exchanged the following doubles for ".$total." random cards:\n".$cards."\nMany thanks!";

			$insert = $database->query("INSERT INTO `user_mbox` (`msg_subject`,`msg_text`,`msg_sender`,`msg_recipient`,`msg_box_from`,`msg_box_to`,`msg_see_from`,`msg_see_to`,`msg_del_from`,`msg_del_to`,`msg_origin`,`msg_date`) VALUES ('Doubles Exchange','$message','$from','$to','Out','In','0','1','0','0','','$date')");

			// Process form if queries are correct
			if ( !$insert ) {
				echo '<h1>Doubles Exchange : Error</h1>
				<p>It looks like there was an error in processing your doubles form. Send the information to '.$tcgemail.' and we will send you your doubles ASAP. Thank you and sorry for the inconvenience.</p>';
			}

			else {
				echo '<h1>Doubles Exchange : Pick Up</h1>
				<p>Thanks for trading in your double cards. Below are your cards! Don\'t forget to take down your doubled cards and log them.</p>

				<center>';
				$min = 1; $max = mysqli_num_rows($result); $rewards = null;
				for($i=0; $i<$total; $i++) {
					mysqli_data_seek($result,rand($min,$max)-1);
					$row = mysqli_fetch_assoc($result);
					$digits = rand(01,$row['card_count']);
					if ($digits < 10) { $_digits = "0$digits"; }
					else { $_digits = $digits; }
					$card = "$row[card_filename]$_digits";
					echo '<img src="'.$tcgcards.''.$card.'.png" border="0" /> ';
					$rewards .= $card.", ";
				}
				$rewards = substr_replace($rewards,"",-2);

				echo '<p><strong>Doubles Exchange (x'.$total.' cards):</strong> '.$rewards.'</p>
				</center>';

				// Insert acquired data
				$today = date("Y-m-d", strtotime("now"));
				$database->query("INSERT INTO `user_logs` (`log_name`,`log_type`,`log_title`,`log_subtitle`,`log_rewards`,`log_date`) VALUES ('$from','Service','Doubles Exchange','(x$total cards)','$rewards','$today')");
			}
		}
	} // end form process

     else {
        echo '<h1>Doubles Exchange</h1>
        <p>Welcome to the Doubles Exchange services, a place where you can <b>swap in any cards you have more than one copy of</b>! If you have multiple copies of the same card with no one to trade them out to, you can use this service to refresh your trade post and get rid of the cards that no one seems to want.</p>
        <p>Do keep in mind that <u>only duplicates from your <b>trade pile</b> count</u>! This means, you must have at least 2 copies of a single card that does not include cards you have already mastered and/or cards you are currently keeping.</p>
        <p><b>Example:</b> If I have 2 copies of autumn01 from my trade pile, I can exchange one copy of it. However, if I have 2 copies of autumn01, 1 from my keeping and 1 from my trade pile, it won\'t be eligible for an exchange since I can trade it out easily.</p>
        <ul><li>You can exchange as much doubles you want, as long as you have two copies of the same card from your trade pile.</li>
        <li>Make sure to separate the cards with commas, then followed by a space to help the script count the cards you are exchanging with.</li></ul>
        <form method="post" action="/services.php?form=doubles&action=sent">
        <input type="hidden" name="sender" value="'.$row['usr_name'].'" />
        <input type="hidden" name="recipient" value="'.$settings->getValue('tcg_owner').'" />
        <textarea name="cards" rows="3" style="width:95%;" /></textarea><br /> 
        <input type="submit" name="submit" class="btn-success" value="Exchange" />
        <input type="reset" name="reset" class="btn-danger" value="Reset" />
        </form>';
    }
} // end do doubles exchange

include('theme/headers/acct-footer.php');
include ($footer);
?>