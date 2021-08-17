<?php
include("admin/class.lib.php");
include($header);

$wish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_status`='Granted' AND `wish_id`='$id'");
$logChk = $database->get_assoc("SELECT * FROM `user_logs` WHERE `log_name`='".$player."' AND `log_title`='Wishes #".$wish['wish_id']."' AND `log_subtitle`='(".$wish['wish_date'].")'");

// Check if user is logged in or not
if ( empty($login) ) { header("Location: account.php?do=login"); }

// Let's begin pulling wishes
if ( empty($go) ) {
	if ( ($logChk['log_title'] == "Wishes #".$wish['wish_id']) && ($logChk['log_subtitle'] == "(".$wish['wish_date'].")") ) {
		echo '<h1>Wishes #'.$wish['wish_id'].' ('.$wish['wish_date'].') : Halt!</h1>
		<p>You have already claimed this wish! If you missed your claims, here they are:</p>
		<center><b>'.$logChk['log_title'].' '.$logChk['log_subtitle'].':</b> '.$logChk['log_rewards'].'</center>';
	} else {
		echo '<h1>Wishes #'.$wish['wish_id'].': '.$wish['wish_date'].'</h1>
        <table width="100%">
        <tr><td width="54%" valign="top">
		<ul>
			<li><b><i>Is the card restriction mentioned on the update?</i></b><br />
			- If there are no card limit specified on the update, kindly take a <u>max of 2 cards per deck</u>.</li>
		</ul>';

		// List down card categories
		$c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
		for ($i=0; $i<=$c; $i++) {
			$cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='".$wish['wish_cat']."'");
		}

		echo '<blockquote class="wish">
		<span class="author">Wished by <b><a href="/members.php?id='.$wish['wish_name'].'">'.$wish['wish_name'].'</a></b>:</span><br />
		<strong><span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span></strong>';
		if ( $wish['wish_type'] == 1 ) { echo 'I wish for choice cards spelling <b>'.$wish['wish_word'].'</b>!'; }
		else if ( $wish['wish_type'] == 2 ) { echo 'I wish for <b>'.$wish['wish_amount'].'</b> choice pack from any deck!'; }
		else if ( $wish['wish_type'] == 3 ) { echo 'I wish for <b>'.$wish['wish_amount'].'</b> random pack from any deck!'; }
		else if ( $wish['wish_type'] == 4 ) { echo 'I wish for 3 choice cards from any <b>'.$cat['cat_name'].'</b> decks!'; }
		else if ( $wish['wish_type'] == 5 ) { echo 'I wish for <b>double deck release</b>!'; }
		else { echo 'I wish for <b>double game rewards</b> from the '.$wish['wish_set'].' set!'; }
		echo '<strong><span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span></strong><br />
			<div class="notice">
				<b>You can only submit once!</b> Make sure to check your choices first before submitting.
			</div>
		</blockquote>
        </td><td width="2%"></td>
        <td width="44%" valign="top">

		<form method="post" action="/wishes.php?id='.$id.'&go=claimed">
		<input type="hidden" name="name" value="'.$player.'">
		<input type="hidden" name="type" value="'.$wish['wish_type'].'">';

		// Show wish form for SPELLING
		if ( $wish['wish_type'] == 1 ) {
			$w = $wish['wish_word'];
			$trim = str_replace(" ", "", $w);
			$length = strlen($trim);

			echo '<input type="hidden" name="word" value="'.$trim.'">
			<table width="100%" class="table table-sliced table-striped"><tbody>';
			for ($i=0; $i<$length; $i++) {
				$word = $trim[$i];
				echo '<tr>
				<td width="18%" align="right"><b>'.$word.'</b></td>
				<td width="82%"><select name="card'.$i.'" style="width:85%;">';
				if ( is_numeric($word) ) {
					// Query your database for all released cards you want when the "word" is a number
					$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_released` <= '".$wish['wish_date']."' AND `card_status`='Active' AND `card_worth`='1' ORDER BY `card_filename` ASC");
					while ( $row = mysqli_fetch_assoc($query) ) {
						$filename = stripslashes($row['card_filename']);
						echo '<option value="'.$filename.'">'.$row['card_deckname'].' ('.$filename.")</option>\n";
					}
					echo '</select><select name="num'.$i.'">';
					for($j=0; $j<=20; $j++) {
						$j = str_pad($j,2,"0",STR_PAD_LEFT);
						if ( (substr($j, 0, 1) == $word) || (substr($j, 1, 2) == $word) ) {
							/*if ($j < 10) { $j = '0'.$j; }*/
							echo '<option value="'.$j.'">'.$j.'</option>';
						}
					}
					echo '</select></td></tr>';
				}

				else {
					$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_released` <= '".$wish['wish_date']."' AND `card_status`='Active' AND `card_worth`='1' AND (`card_filename` LIKE '$word%' OR `card_filename` LIKE '%$word%' OR `card_filename` LIKE '%$word') ORDER BY `card_filename` ASC");

					// Display dropdown for each letters
					while ( $row = mysqli_fetch_assoc($query) ) {
						$filename = stripslashes($row['card_filename']);
						echo '<option value="'.$filename.'">'.$row['card_deckname'].' ('.$filename.")</option>\n";
					}
					echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1"></td>';
					echo '</tr>';
				}
			}
			echo '</tbody></table>
            <input type="submit" name="submit" class="btn-success" value="Claim Wish" /> 
			<input type="reset" name="reset" class="btn-danger" value="Reset" />
		</form>';
		}

		// Show wish form for CHOICE PACK
		else if ( $wish['wish_type'] == 2 ) {
			echo '<input type="hidden" name="amount" value="'.$wish['wish_amount'].'">
			<table width="100%" class="table table-sliced table-striped"><tbody>';
			$c = $wish['wish_amount'];
			for ($i=1; $i<=$c; $i++) {
				echo '<tr>
				<td width="18%" align="right"><b>Choice #'.$i.'</b></td>
				<td width="82%"><select name="card'.$i.'" style="width:85%;">';
				$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_released` <= '".$wish['wish_date']."' AND `card_status`='Active' AND `card_worth`='1' ORDER BY `card_filename` ASC");
				while($row=mysqli_fetch_assoc($query)) {
					$filename=stripslashes($row['card_filename']);
					echo '<option value="'.$filename.'">'.$row['card_deckname'].' ('.$filename.")</option>\n";
				}
				echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1">
				</td></tr>';
			}
			echo '</tbody></table>
            <input type="submit" name="submit" class="btn-success" value="Claim Wish" /> 
			<input type="reset" name="reset" class="btn-danger" value="Reset" />
		</form>';
		}

		// Show wish form for RANDOM PACK
		else if ( $wish['wish_type'] == 3 ) {
			echo '<input type="hidden" name="amount" value="'.$wish['wish_amount'].'">';
			$c = $wish['wish_amount'];
			for($i=1; $i<=$c; $i++) {
				echo '<input type="hidden" name="card'.$i.'" value="'; $general->randtype('Active','1'); echo "\" />\n";
			}
			echo '<center><input type="submit" name="submit" class="btn-success" value="Claim Wish" /></center>
		</form>';
		}

		// Show wish form for CHOICE CATEGORY
		else if ( $wish['wish_type'] == 4 ) {
			echo '<input type="hidden" name="amount" value="3">
			<table width="100%" class="table table-sliced table-striped"><tbody>';
			for($i=1; $i<=3; $i++) {
				echo '<tr>
				<td width="18%" align="right"><b>Choice #'.$i.'</b></td>
				<td width="82%"><select name="card'.$i.'" style="width:83%;">';
				$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_released` <= '".$wish['wish_date']."' AND `card_status`='Active' AND `card_worth`='1' AND `card_cat`='".$wish['wish_cat']."' ORDER BY `card_filename` ASC");
				while($row=mysqli_fetch_assoc($query)) {
					$filename=stripslashes($row['card_filename']);
					echo "<option value=\"$filename\">$row[card_deckname] ($filename)</option>\n";
				}
				echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1"></td>
				</tr>';
			}
			echo '</tbody></table>
            <input type="submit" name="submit" class="btn-success" value="Claim Wish" /> 
			<input type="reset" name="reset" class="btn-danger" value="Reset" />
		</form>';
		}
        echo '</td></tr>
        </table>';
	}
} // End empty $go

// Process pulled wish
else if ( $go == "claimed" ) {
	if ( !isset($_SERVER['HTTP_REFERER']) ){
		echo $ForbiddenAccess;
	}

	else {
		if ( !isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST" ) {
			exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
		}

		else {
			$today = date("Y-m-d", strtotime("now"));
			$name = $sanitize->for_db($_POST['name']);
			$type = $sanitize->for_db($_POST['type']);
			$word = $sanitize->for_db($_POST['word']);
			$amount = $sanitize->for_db($_POST['amount']);

			$get = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_id`='$id'");

			echo '<h1>Wishes #'.$get['wish_id'].' ('.$get['wish_date'].')</h1>
			<p>Your wish pulls has been logged on your permanent logs, make sure to log it on your trade post as well.</p>

			<center>';

			// Generate cards according to wish type
			if ( $type == 1 ) {
				$amount = strlen($word);
				for($i=0; $i<$amount; $i++) {
					$cardImg = "card$i";
					$cardImg2 = "num$i";
					echo '<img src="/images/cards/'.$_POST[$cardImg].''.$_POST[$cardImg2].'.png" />';
					$pulled .= $_POST[$cardImg].$_POST[$cardImg2].", ";
				}
			} else if ( ($type == 2) || ($type == 3) || ($type == 4) ) {
				for($i=1; $i<=$amount; $i++) {
					$cardImg = "card$i";
					$cardImg2 = "num$i";
					echo '<img src="/images/cards/'.$_POST[$cardImg].''.$_POST[$cardImg2].'.png" />';
					$pulled .= $_POST[$cardImg].$_POST[$cardImg2].", ";
	            }
			}
			$rewards = substr_replace($pulled,"",-2);
			echo '<br /><strong>Wishes #'.$get['wish_id'].' ('.$get['wish_date'].'):</strong> '.$rewards;

			// Insert acquired data
			$title = "Wishes #".$get['wish_id'];
			$database->query("UPDATE `user_items` SET `itm_cards`=itm_cards+'$amount' WHERE `itm_name`='$name'");
			$database->query("INSERT INTO `user_logs` (`log_name`,`log_type`,`log_title`,`log_subtitle`,`log_rewards`,`log_date`) VALUES ('$name','Pulls','$title','(".$get['wish_date'].")','$rewards','$today')");
			echo '</center>';
		}
	}
} // end wish form

include($footer);
?>