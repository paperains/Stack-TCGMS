<?php
include("admin/class.lib.php");
include($header);

if ( empty($login) ) { header("Location: account.php?do=login"); }

$user = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$login'");
$free = $database->get_assoc("SELECT * FROM `user_freebies` WHERE `free_id`='$id'");
$logChk = $database->get_assoc("SELECT * FROM `user_logs` WHERE `log_name`='".$user['usr_name']."' AND `title`='Freebies #".$free['free_id']."' AND `subtitle`='(".$free['free_date'].")'");

if ( empty($go) ) {
	if ( ($logChk['log_title'] == "Freebies #".$free['free_id']) && ($logChk['log_subtitle'] == "(".$free['free_date'].")") ) {
		echo '<h1>Freebies #'.$free['free_id'].' ('.$free['free_date'].') : Halt!</h1>
		<p>You have already claimed this freebie! If you missed your claims, here they are:</p>
		<center><b>'.$logChk['log_title'].' '.$logChk['log_subtitle'].':</b> '.$logChk['log_rewards'].'</center>';
	}

	else {
		echo '<h1>Freebies #'.$free['free_id'].': '.$free['free_date'].'</h1>';
		echo '<blockquote class="wish">
		<strong><span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span></strong>';
		if ( $free['free_type'] == 1 ) {
			echo 'Take choice cards spelling <b>'.$free['free_word'].'</b>!';
		}
		else if ( $free['free_type'] == 2 ) {
			echo 'Take a total of <b>'.$free['free_amount'].'</b> choice pack from any deck!';
		}
		else if ( $free['free_type'] == 3 ) {
			echo 'Take a total of <b>'.$free['free_amount'].'</b> random pack from any deck!';
		}
		else if ( $free['free_type'] == 4 ) {
			echo 'Take a total of 3 choice cards from any <b>'.$free['free_cat'].'</b> decks!';
		}
		echo '<strong><span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span></strong><br />
			<div class="notice">
				<b>You can only submit once!</b> Make sure to check your choices first before submitting.
			</div>
		</blockquote>

		<center>
		<form method="post" action="/freebies.php?id='.$id.'&go=claimed">
		<input type="hidden" name="name" value="'.$user['usr_name'].'">
		<input type="hidden" name="type" value="'.$free['free_type'].'">';
		if ( $free['free_type'] == 1 ) {
			$w = $free['free_word'];
			$trim = str_replace(" ", "", $w);
			$length = strlen($trim);
			echo '<input type="hidden" name="word" value="'.$trim.'">
			<table width="100%" cellspacing="3" class="border">';
			for ($i=0; $i<$length; $i++) {
				$word = $trim[$i];
				echo '<tr>
					<td width="10%" class="headLine">'.$word.'</td>
					<td width="90%" class="tableBody">
						<select name="card'.$i.'" style="width:85%;">';
				if( is_numeric($word) ) {
					// Query your database here for all released cards you want when the "word" is a number
					$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_released` <= '".$free['free_date']."' AND `card_status`='Active' ORDER BY `card_filename` ASC");
					while( $row = mysqli_fetch_assoc($query) ) {
						$filename = stripslashes($row['card_filename']);
						echo '<option value="'.$filename.'">'.$row['card_deckname'].' ('.$filename.')</option>';
					}
					echo '</select><select name="num'.$i.'">';
					for($j=0; $j<=20; $j++) {
						$j = str_pad($j,2,"0",STR_PAD_LEFT);
						if( (substr($j, 0, 1) == $word) || (substr($j, 1, 2) == $word) ) {
							echo '<option value="'.$j.'">'.$j.'</option>';
						}
					}
					echo '</select></td></tr>';
				}

				else {
					$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_released` <= '".$free['free_date']."' AND `card_status`='Active' AND (`card_filename` LIKE '$word%' OR `card_filename` LIKE '%$word%' OR `card_filename` LIKE '%$word') ORDER BY `card_filename` ASC");

					// Start dropdown for each letter
					while( $row = mysqli_fetch_assoc($query) ) {
						$filename = stripslashes($row['card_filename']);
						echo '<option value="'.$filename.'">'.$row['card_deckname'].' ('.$filename.')</option>';
					}
					echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1"></td>
					</tr>';
				}
			}
			echo '<tr>
				<td class="tableBody" colspan="2" align="center">
					<input type="submit" name="submit" class="btn-success" value="Claim Freebies" /> 
					<input type="reset" name="reset" class="btn-cancel" value="Reset" />
				</td>
			</tr>
			</table>
		</form>
		</center>';
		}

		else if ( $free['free_type'] == 2 ) {
			echo '<input type="hidden" name="amount" value="'.$free['free_amount'].'">
			<table width="90%" cellspacing="3" class="border">';
			$c = $free['free_amount'];
			for ($i=1; $i<=$c; $i++) {
				echo '<tr>
				<td width="10%" class="headLine">Choice #'.$i.'</td>
				<td width="90%" class="tableBody">
					<select name="card'.$i.'" style="width:85%;">';
				$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_released` <= '".$free['free_date']."' AND `card_status`='Active' ORDER BY `card_filename` ASC");
				while( $row = mysqli_fetch_assoc($query) ) {
					$filename = stripslashes($row['card_filename']);
					echo '<option value="'.$filename.'">'.$row['card_deckname'].' ('.$filename.')</option>';
				}
				echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1"></td>';
				echo '</tr>
				<tr>
					<td class="tableBody" colspan="2" align="center">
						<input type="submit" name="submit" class="btn-success" value="Claim Freebies" /> 
						<input type="reset" name="reset" class="btn-cancel" value="Reset" />
					</td>
				</tr>
				</table>
			</form>
			</center>';
			}
		}

		else if ( $free['free_type'] == 3 ) {
			echo '<input type="hidden" name="amount" value="'.$free['free_amount'].'">';
			$c = $free['amount'];
			for($i=1; $i<=$c; $i++) {
				echo '<input type="hidden" name="card'.$i.'" value="'; $general->randtype('Active'); echo '" />';
			}
			echo '<table width="90%" cellspacing="3" class="border">
			<tr>
				<td class="tableBody" colspan="2" align="center">
					<input type="submit" name="submit" class="btn-success" value="Claim Freebies" />
				</td>
			</tr>
			</table>
		</form>
		</center>';
		}

		else if ( $free['free_type'] == 4 ) {
			echo '<input type="hidden" name="amount" value="3">
			<table width="90%" cellspacing="3" class="border">';
			for($i=1; $i<=3; $i++) {
				echo '<tr>
					<td width="10%" class="headLine">Choice #'.$i.'</td>
					<td width="90%" class="tableBody">
						<select name="card'.$i.'" style="width:85%;">';
				$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_released` <= '".$free['free_date']."' AND `card_status`='Active' AND `card_cat`='".$free['free_cat']."' ORDER BY `card_filename` ASC");
				while( $row = mysqli_fetch_assoc($query) ) {
					$filename = stripslashes($row['card_filename']);
					echo '<option value="'.$filename.'">'.$row['card_deckname'].' ('.$filename.')</option>';
				}
				echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1"></td>';
				echo '</tr>
				<tr>
					<td class="tableBody" colspan="2" align="center">
						<input type="submit" name="submit" class="btn-success" value="Claim Freebies" /> 
						<input type="reset" name="reset" class="btn-cancel" value="Reset" />
					</td>
				</tr>
			</table>
		</form>
		</center>';
			}
		}
	}
} // end empty go

else if ( $go == "claimed" ) {
	if(!isset($_SERVER['HTTP_REFERER'])){
		echo $ForbiddenAccess;
	}

	else {
		if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
			exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
		}

		else {
			$check->Value();
			$today = date("Y-m-d", strtotime("now"));
			$name = $sanitize->for_db($_POST['name']);
			$type = $sanitize->for_db($_POST['type']);
			$word = $sanitize->for_db($_POST['word']);
			$amount = $sanitize->for_db($_POST['amount']);

			$get = $database->get_assoc("SELECT * FROM `user_freebies` WHERE `free_id`='$id'");

			echo '<h1>Freebies #'.$get['free_id'].' ('.$get['free_date'].')</h1>
			<p>Your freebies pulls has been logged on your permanent logs, make sure to log it on your trade post as well.</p>
			<center>';

			// Do rewards depending on wish type
			if ( $type == 1 ) {
				$amount = strlen($word);
				for($i=0; $i<$amount; $i++) {
					$card = "card$i";
					$card2 = "num$i";
					echo '<img src="'.$tcgcards.''.$_POST[$card].''.$_POST[$card2].'.'.$tcgext.'" />';
					$pulled .= $_POST[$card].$_POST[$card2].", ";
				}
			}

			else if ( $type == 2 || $type == 3 || $type == 4 ) {
				for($i=0; $i<$amount; $i++) {
					$card = "card$i";
					$card2 = "num$i";
					echo '<img src="'.$tcgcards.''.$_POST[$card].''.$_POST[$card2].'.'.$tcgext.'" />';
					$pulled .= $_POST[$card].$_POST[$card2].", ";
				}
			}

			$rewards = substr_replace($pulled,"",-2);
			echo '<br /><strong>Freebies #'.$get['free_id'].' ('.$get['free_date'].'):</strong> '.$rewards;
			$title = "Freebies #".$get['free_id'];
			$database->query("UPDATE `user_items` SET `itm_cards`=cards+'$amount' WHERE `itm_name`='$name'");
			$database->query("INSERT INTO `user_logs` (`log_name`,`log_type`,`log_title`,`log_subtitle`,`log_rewards`,`log_date`) VALUES ('$name','Pulls','$title','(".$get['free_date'].")','$rewards','$today')");
			echo '</center>';
		}
	}
} // end go claimed

include($footer);
?>