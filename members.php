<?php
include("admin/class.lib.php");
include($header);

if ( $page == "join" ) {
    echo '<div class="box">';
	// Check if TCG registration is on or off
	if ( $settings->getValue( 'tcg_registration' ) == "0" ) {
		echo '<h1>Registration : Closed!</h1>
		<p>We regret to inform you that '.$tcgname.' is currently closed for registration. If you want to join us, kindly check on us constantly and wait for us to open the TCG for you! Thank you so much for your interest!</p>';
	}

	// If registration is on, show form
	else {
		if ( $stat == "sent" ) {
			if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
				echo '<p>You did not press the submit button; this page should not be accessed directly.</p>';
			}

			else {
				$check->Member();
				if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,6})$/",strtolower($_POST['email']))) {
					exit("<h1>Error</h1>\nThat e-mail address is not valid, please use another.<br /><br />");
				}

				$name = $sanitize->for_db($_POST['name']);
				$email = $sanitize->for_db($_POST['email']);
				$url = $sanitize->for_db($_POST['url']);
				$refer = $sanitize->for_db($_POST['refer']);
				$pass = md5($sanitize->for_db($_POST['password']));
				$password2 = $sanitize->for_db($_POST['password2']);
				$collecting = $sanitize->for_db($_POST['collecting']);
				$mcard = $sanitize->for_db($_POST['mcard']);
				$bio = $sanitize->for_db($_POST['about']);
				$bday = $_POST['year']."-".$_POST['month']."-".$_POST['day'];

				$date = date('Y-m-d', strtotime("now"));
				$date2 = date('Y-m-d', strtotime("now"));

				$recipient = "$tcgemail";
				$subject = "New Member";

				$message = "The following member has joined $tcgname: \n";
				$message .= "Name: $name \n";
				$message .= "Email: $email \n";
				$message .= "Trade Post: $url \n";
				$message .= "Collecting: $collecting \n";
				$message .= "Referral: $refer \n";
				$message .= "Birthday: $bday \n";
				$message .= "Member Card: $mcard \n";
				$message .= "To add them to the approved member list, go to your admin panel.\n";

				$headers = "From: $name <no-reply@hakumei.org> \n";
				$headers .= "Reply-To: $name <$email>";

				echo '<h1>Welcome!</h1>
				<p>Welcome to '.$tcgname.'! Below is your starter pack. You will not be able to play games until you have been approved by the owner but you can take cards from updates posted on or after '.date("F j, Y").'.</p>

				<center>';
				$choice = null; $rand = null; $cW = null; $rW = null;
				for($i=1; $i<=$settings->getValue('prize_start_choice'); $i++) {
					$card = "choice$i";
					echo '<img src="'.$tcgcards.''.$collecting;
					echo $_POST[$card];
					echo '.'.$tcgext.'" />';
					$choice .= $collecting.$_POST[$card].", ";
				}

				for($i=1; $i<=$settings->getValue('prize_start_reg'); $i++) {
					$card = "random$i";
					echo '<img src="'.$tcgcards;
					echo $_POST[$card];
					echo '.'.$tcgext.'" />';
					$rand .= $_POST[$card].", ";
				}
				echo '<br /><br />

				<b>Starter Pack:</b> ';
				$choice = substr_replace($choice,"",-2);
				$rand = substr_replace($rand,"",-2);
				echo '</center>';

				$total = $settings->getValue('prize_start_choice') + $settings->getValue('prize_start_reg');

				$insert = $database->query("INSERT INTO `user_list` (`usr_name`,`usr_email`,`usr_url`,`usr_refer`,`usr_bday`,`usr_pass`,`usr_deck`,`usr_bio`,`usr_twitter`,`usr_discord`,`usr_reg`) VALUES ('$name','$email','$url','$refer','$bday','$pass','$collecting','$bio','N / A','N / A','$date')");

				if ( mail($recipient,$subject,$message,$headers) ) {
					if ( $insert === TRUE ) {
                        $currSP = explode(", ", $settings->getValue('tcg_currency'));
                        $money = '';
                        for($j=0; $j<count($currSP); $j++) {
                            $money .= '0 | ';
                        }
                        $money = substr_replace($money,"",-2);
						$database->query("INSERT INTO `user_logs` (`log_name`,`log_type`,`log_title`,`log_rewards`,`log_date`) VALUES ('$name','Service','Starter Pack','$choice, $rand','$date2')");
						$database->query("INSERT INTO `user_trades_rec` (`trd_name`,`trd_date`) VALUES ('$name','$date2')");
						$database->query("INSERT INTO `user_items` (`itm_name`,`itm_cards`,`itm_currency`) VALUES ('$name','$total','$money')");

						// Referral rewards
						if ( $refer == "None" ) { }
						else {
							$database->query("INSERT INTO `user_rewards` (`rwd_name`,`rwd_type`,`rwd_subtitle`,`rwd_mcard`,`rwd_cards`,`rwd_date`) VALUES ('$refer','Services','(Referral)','No','1','$date2')");
						}

						// Email message
						$recipient2 = "$email";
						$subject2 = $tcgname.": Starter Pack";
						$message2 = "Thanks for joining ".$tcgname.", $name! We are very excited that you are going to be joining us. Your account is currently pending approval, but you can begin playing games regardless. Below is a copy of your starter pack, in case you did not pick it up from the site.\n\n";

						for($i=1; $i<=$settings->getValue('prize_start_choice'); $i++) {
							$card = 'choice'.$i;
							$message2 .= $tcgcards.''.$collecting.''.$_POST[$card].'.'.$tcgext;
							$message2 .= "\n";
						}

						for($i=1; $i<=$settings->getValue('prize_start_reg'); $i++) {
							$card = 'random'.$i;
							$message2 .= $tcgcards.''.$_POST[$card].'.'.$tcgext;
							$message2 .= "\n";
						}

						$message2 .= "\nThanks again for joining and happy trading!\n\n";
						$message2 .= "-- $tcgowner\n";
						$message2 .= "$tcgname: $tcgurl\n";
						$headers2 = "From: $tcgname <$tcgemail> \n";
						$headers2 .= "Reply-To: $tcgname <$tcgemail>";
						mail($recipient2,$subject2,$message2,$headers2);
					}

					else {
						echo '<h1>Error</h1>
						<p>It looks like there was an error in processing your join form. Send the information to '.$tcgemail.' and we will send you your starter pack ASAP. Thank you and sorry for the inconvenience.</p>';
					}
				}
			}
		}

		else {
			// Change to your own registration rules
			echo '<h1>Join Us!</h1>
			<p>We are glad that you\'re finally joining us here at '.$tcgname.'! Before filling up the form, kindly please have a moment to read the set of rules below, many thanks~!</p>

			<table width="100%">
            <tr><td width="55%" valign="top"><h3>Members must...</h3>
			<ol>
				<li>have a working website (trade post) and email address must be valid.</li>
				<li>use a realistic name or nickname. If your name is already taken on the members list, please change your name or add a number instead (alphanumeric only).</li>
				<li>upload your starter pack within two weeks. If you need more time, just please let me know.</li>
				<li>update your trade posts at least <i>every two months</i>. If you do not, your status will be changed to <b>inactive</b> and you must reactivate your membership to continue trading.</li>
				<li>keep a detailed log on your trade post so we know where you got your cards and other '.$tcgname.' stuff from.</li>
				<li>send a hiatus notice if you need to, because if your trade post is left un-updated, I\'ll assume that you stopped playing or no longer interested.</li>
				<li><u>NOT DIRECT-LINK</u> any graphics from '.$tcgname.'. Please upload them to your own server or a free image site, such as <a href="http://www.photobucket.com/" target="_blank">Photobucket</a> or <a href="http://www.imgur.com/" target="_blank">Imgur</a>.</li>
				<li><u>NOT CHEAT</u> anywhere and in anyway possible. Which means...
					<ul>
						<li>..you are <i>not allowed</i> to refresh any prize page or randomizer unless you are told to do so.</li>
						<li>..you are not allowed to give out answers to fellow players as well.</li>
						<li>..you will play the games only <u>ONCE</u> per round unless told otherwise.</li>
						<li>..you have to wait for the next game update in order to play again.</li>
					</ul>
				</li>
				<li>provide a password to be able to access forms and the interactive section. This password is encoded in the database and cannot be retrieved or viewed by anyone.</li>
				<li>be nice and polite to other members as much as possible. If members don\'t want to trade, respect their decision. Let\'s make this place peaceful and enjoyable.</li>
			</ol>

			<h3>Freebies can/must...</h3>
			<ol>
				<li>be taken from the latest update regardless of joining after it was posted.</li>
				<li>be taken anytime as they are not restricted to any deadlines.</li>
				<li>be all claimed at the same time as we do not allow claiming them in parts.</li>
			</ol>

			<p>Lastly, never forget to comment on any updates where the freebies were announced with what you\'ve taken. Otherwise, you will be asked to remove any of the cards you took without commenting.</p></td>
            <td width="2%"></td>
            <td width="43%" valign="top">
			<h3>Join Form</h3>
			<form method="post" action="'.$tcgurl.'members.php?page=join&stat=sent">
			<input type="hidden" name="about" value="Coming Soon" />';
			for($i=1; $i<=$settings->getValue('prize_start_choice'); $i++) {
				$sql = $database->get_assoc("SELECT * FROM `tcg_cards`");
				$digit = rand(01,$sql['card_count']);
				if ($digit < 10) { $_digits = "0$digit"; }
				else { $_digits = $digit; }
				echo "<input type=\"hidden\" name=\"choice$i\" value=\"$_digits\" />\n";
			}

			for($i=1; $i<=$settings->getValue('prize_start_reg'); $i++) {
				echo '<input type="hidden" name="random'.$i.'" value="'; $general->randtype('Active','1'); echo "\" />\n";
			}
			echo '<table width="100%" class="table table-sliced table-striped">
            <tbody>
			<tr>
				<td width="22%"><b>Name:</b></td>
				<td width="78%"><input type="text" name="name" style="width: 90%;" placeholder=" Jane Doe " /></td>
            </tr>
            <tr>
				<td><b>Email:</b></td>
				<td><input type="text" name="email" style="width: 90%;" placeholder="username@domain.tld" /></td>
			</tr>
			<tr>
				<td><b>Trade Post:</b></td>
				<td><input type="text" name="url" style="width: 90%;" placeholder="http://" /></td>
            </tr>
            <tr>
				<td><b>Birthday:</b></td>
				<td>
					<select name="month" style="width:45%;">';
						for($m=1; $m<=12; $m++) {
							if ( $m < 10 ) { $_mon = "0$m"; }
							else { $_mon = $m; }
							echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
						}
					echo '</select> <select name="day">';
						for($i=1; $i<=31; $i++) {
							if ( $i < 10 ) { $_days = "0$i"; }
							else { $_days = $i; }
							echo '<option value="'.$_days.'">'.$_days.'</option>';
						}
					echo '</select> ';
					$date = date('Y');
					$start = $date - 10;
					$end = $start - 40;
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
				<td><b>Collecting:</b></td>
				<td>
					<select name="collecting" style="width: 97%;">
						<option value="">-----</option>';
						$query = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active' AND `card_worth`='1' ORDER BY `card_set` ASC, `card_deckname` ASC");
						while ( $row = mysqli_fetch_assoc($query) ) {
							$set = stripslashes($row['card_set']);
							$name = stripslashes($row['card_filename']);
							$deckname = stripslashes($row['card_deckname']);
							echo '<option value="'.$name.'">'.$set.' - '.$deckname."</option>\n";
						} // end while
					echo '</select>
				</td>
            </tr>
            <tr>
				<td><b>Referral:</b></td>
				<td>
					<select name="refer" style="width: 97%;" />
						<option value="None">None (e.g. TCG wiki, Google search)</option>';
						$mem = $database->query("SELECT * FROM `user_list` ORDER BY `usr_name` ASC");
						while ( $row = mysqli_fetch_assoc($mem) ) {
							$name = stripslashes($row['usr_name']);
							echo '<option value="'.$name.'">'.$name."</option>\n";
						} // end while
					echo '</select>
				</td>
			</tr>
			<tr>
				<td><b>Member Card:</b></td>
				<td><input type="text" name="mcard" style="width:90%;" placeholder="http://" /></td>
			</tr>
			<tr>
				<td><b>Password:</b></td>
				<td>
					<input type="password" name="password" placeholder="********" style="width:90%;" /> 
					<input type="password" name="password2" placeholder="Retype your password" style="width:90%;" />
				</td>
			</tr>
            </tbody></table>
            <input type="submit" name="submit" class="btn-success" value="Join '.$tcgname.'" /> 
			<input type="reset" name="reset" class="btn-danger" value="Reset" />
			</form>
            </td></tr></table>';
		}
	}
    echo '</div>';
} // end join page

else {
	if ( empty($id) ) {
        include('theme/headers/mem-header.php');
		if ( empty($stat) ) {
			echo '<h1>Members</h1>
			<p>This is the full list of <b>active</b>, <b>pending</b> and members currently in <b>hiatus</b> of <i>'.$tcgname.'</i>. Please take note that all <b>pending</b> members are <u>allowed to participate in the TCG until approved</u>, but only <b>active</b> or approved members have a full access of the TCG.</p>

			<p>All members are sorted by <em>level</em> (but levels that have a member will be visible only), and then by <em>name in alphabetical order</em>. If you want to view the member\'s profile, decks they have mastered, achievements that they may have and the likes, just click on their member card.</p>';

			$lvlcount = $database->num_rows("SELECT * FROM `tcg_levels`");
			for($i=1; $i<=$lvlcount; $i++) {
				$select = $database->query("SELECT * FROM `user_list` WHERE `usr_level`='$i' AND `usr_status`='Active' ORDER BY `usr_name`");
				$counts = mysqli_num_rows($select);
				if ( $counts == 0 ) { }
				else {
					echo "<h2>Level ".$i."</h2>\n";
					echo '<center>';
					while ( $row = mysqli_fetch_assoc($select) ) {
						echo '<div class="memList">
                        <table width="340">
                        <tr><td colspan="2" class="memName"><a href="'.$tcgurl.'members.php?id='.$row['usr_name'].'">'.$row['usr_name'].'</a></td></tr>
                        <tr><td width="135" align="center">';
						if ( $row['usr_mcard'] == "Yes" ) {
							echo '<a href="'.$tcgurl.'members.php?id='.$row['usr_name'].'"><img src="'.$tcgcards.'mc-'.$row['usr_name'].'.'.$tcgext.'" /></a>';
						}
						else {
							echo '<a href="'.$tcgurl.'members.php?id='.$row['usr_name'].'"><img src="'.$tcgcards.'mc-filler.'.$tcgext.'" /></a>';
						}
						echo '</td><td width="215">
                        <div class="socIcon">';
							$prejoin = $row['usr_pre'];
							if( $prejoin == "Beta" ) {
                                echo '<li><font color="#e81a33"><span class="fas fa-cannabis" aria-hidden="true" title="Beta Tester"></span></font></li>';
                            } else if ( $prejoin == "Yes" ) {
								echo '<li><font color="#ffa500"><span class="fas fa-cannabis" aria-hidden="true" title="Prejoiner"></span></font></li>';
							}
							else {
								echo '<li><font color="#636363"><span class="fas fa-cannabis" aria-hidden="true" title="Non-Prejoiner"></span></font></li>';
							}
							echo '<li><a href="'.$row['usr_url'].'" target="_blank" title="Visit Trade Post"><span class="fas fa-home" aria-hidden="true"></span></a></li>';
							if ( $row['usr_rand_trade'] == "0" ) {
								echo '<li><font color="#d9a3a9"><span class="fas fa-bell-slash" aria-hidden="true" title="I don\'t accept random trades!"></span></font></li>';
							}
							else {
								echo '<li><font color="#a4c8de"><span class="fas fa-bell" aria-hidden="true" title="Send me any random trades, please!?"></span></font></li>';
							}

							if ( $row['usr_auto_trade'] == "0" ) {
								echo '<li><font color="#d9a3a9"><span class="fas fa-toggle-off" aria-hidden="true" title="Please don\'t put your trades through!"></span></font></li>';
							}
							else {
								echo '<li><font color="#a4c8de"><span class="fas fa-toggle-on" aria-hidden="true" title="Feel free to put all your trades through!"></span></font></li>';
							}
						echo '</div>
                        Born on '.date("F d", strtotime($row['usr_bday'])).'<br />
						Collecting <a href="'.$tcgurl.'cards.php?view=released&deck='.$row['usr_deck'].'">'.$row['usr_deck'].'</a><br />';
                        if( $row['usr_twitter'] == "N / A" ) { echo 'I don\'t have a Twitter!<br />'; }
                        else { echo 'Twitter <a href="https://twitter.com/'.$row['usr_twitter'].'" target="_blank">@'.$row['usr_twitter'].'</a><br />'; }
                        if( $row['usr_discord'] == "N / A" ) { echo 'I don\'t have a Discord!'; }
                        else { echo 'Discord <a href="">'.$row['usr_discord'].'</a>'; }
                            echo '</td></tr>
                        </table>
						</div>';
					}
					echo '</center>';
				}
			}
		} // end empty stat

		else if ( $stat == "pending" ) {
			echo '<h1>Members : Pending</h1>
			<p>Below is the complete list of all pending members here at '.$tcgname.'. Although there is nothing much in their profile, you can click their names to view it.</p>';
			$general->member('Pending');
		}

		else if ( $stat == "hiatus" ) {
			echo '<h1>Members : Hiatus</h1>
			<p>Below is the complete list of all members under hiatus here at '.$tcgname.'. Please take note that members who are in hiatus may or may not accept trades, so we suggest to send them a message or check their trade post first to make sure.</p>';
			$general->member('Hiatus');
		}

		else if ( $stat == "inactive" ) {
			echo '<h1>Members : Inactive</h1>
			<p>Below is the complete list of all inactive members at '.$tcgname.'. These are members who are no longer active in the TCG or the TCG community in general.</p>';
			$general->member('Inactive');
		}

		else if ( $stat == "retired" ) {
			echo '<h1>Members : Retired</h1>
			<p>Below is the complete list of all members who quitted '.$tcgname.'. They are no longer a part of the TCG, so trading is no longer possible for them as they may have adopted their cards out.</p>';
			$general->member('Retired');
		}
        include('theme/headers/mem-footer.php');
	} // end empty ID

	// View full member profile
	else {
        $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_name`='$id'");
		$item = $database->get_assoc("SELECT * FROM `user_items` WHERE `itm_name`='$id'");
		$msg = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$login'");
		$log1 = $database->query("SELECT * FROM `user_logs` WHERE `log_name`='$id' ORDER BY `log_date` DESC");
		$log2 = $database->query("SELECT * FROM `user_trades` WHERE `trd_name`='$id' ORDER BY `trd_date` DESC");
		$lvl = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `lvl_id`='".$row['usr_level']."'");

		echo '<h1>Profile : '.$row['usr_name'].'</h1>
        <table width="100%">
        <tr><td width="20%" valign="top">
            <div class="socIcon2">
                <li><a href="'.$row['usr_url'].'" target="_blank" title="Visit Trade Post"><span class="fas fa-home" aria-hidden="true"></span></a></li>
                <li><span class="fas fa-gift" aria-hidden="true" title="Born on '.date("F d", strtotime($row['usr_bday'])).'"></span></li>';
                $prejoin = $row['usr_pre'];
				if( $prejoin == "Beta" ) {
                    echo '<li><font color="#e81a33"><span class="fas fa-cannabis" aria-hidden="true" title="Beta Tester"></span></font></li>';
                } else if ( $prejoin == "Yes" ) {
					echo '<li><font color="#ffa500"><span class="fas fa-cannabis" aria-hidden="true" title="Prejoiner"></span></font></li>';
				}
				else {
					echo '<li><font color="#636363"><span class="fas fa-cannabis" aria-hidden="true" title="Non-Prejoiner"></span></font></li>';
				}

                if( $row['usr_twitter'] == "N / A" ) {}
                else { echo '<li><a href="https://twitter.com/'.$row['usr_twitter'].'" target="_blank"><span class="fab fa-twitter" aria-hidden="true" title="@'.$row['usr_twitter'].'"></span></a></li>'; }
                if( $row['usr_discord'] == "N / A" ) {}
                else { echo '<li><a href=""><span class="fab fa-discord" aria-hidden="true" title="'.$row['usr_discord'].'"></span></a></li>'; }
            echo '</div><br />
            <center>
            
            <h3>'.$row['usr_name'].'</h3>';
            if ( $row['usr_mcard'] == "Yes" ) { echo '<img src="'.$tcgcards.'mc-'.$row['usr_name'].'.'.$tcgext.'" /><br />'; }
            else { echo '<img src="'.$tcgcards.'mc-filler.'.$tcgext.'" /><br />'; }
            echo '(mc-'.$row['usr_name'].')<br /><br />
            <img src="'.$tcgimg.'badges/'.$item['itm_badge'].'-'.$row['usr_level'].'.png" /><br />(Level '.$row['usr_level'].')
            </center>
        </td><td width="2%"></td>
        <td width="78%" valign="top">
            <ul class="tabs" data-persist="true">
                <li><a href="#overview">About Me</a></li>
                <li><a href="#wishlist">Wishlists</a></li>
                <li><a href="#masteries">Masteries</a></li>
                <li><a href="#gallery">Gallery</a></li>
                <li><a href="#logs">Logs</a></li>
                <li><a href="#trademe">Wanna trade?</a></li>
            </ul>

            <div class="tabcontents">
				<div id="overview">
                    <table width="100%" cellspacing="0" border="0" class="table table-sliced table-striped">
                    <tbody><tr>
                        <td width="50%"><b>Status:</b> '.$row['usr_status'].'</td>
                        <td width="50%"><b>Rank:</b> '.$lvl['lvl_name'].' (<i>Level '.$row['usr_level'].'</i>)</td>
                    </tr>
                    <tr>
                        <td><b>Collecting:</b> <a href="'.$tcgurl.'cards.php?view=released&deck='.$row['usr_deck'].'">'.$row['usr_deck'].'</a></td>
                        <td><b>Card Count:</b> '.$item['itm_cards'].'</td>
                    </tr>
                    <tr>
                        <td><b>Joined:</b> '.date("F d, Y", strtotime($row['usr_reg'])).'</td>
                        <td colspan="2"><b>Last seen:</b> '.date("F d, Y", strtotime($row['usr_sess'])).'</i> at <i>'.date("h:i A", strtotime($row['usr_sess'])).'</td>
                    </tr></tbody>
                    </table>
                    
                    <p>'.$row['usr_bio'].'</p>
                </div><!-- #overview -->

                <div id="wishlist">';
				$sql_wish = $database->query("SELECT * FROM `user_wishlist` WHERE `wlist_name`='".$row['usr_name']."' ORDER BY `wlist_deck` ASC");
				$counts = mysqli_num_rows($sql_wish);
				if ( $counts != 0 ) {
					$wishes = array();
					while ( $row_wish = mysqli_fetch_array($sql_wish) ) {
						$wishes[] = '<a href="'.$tcgurl.'cards.php?view=released&deck='.$row_wish['wlist_deck'].'"><img src="'.$tcgcards.''.$row_wish['wlist_deck'].'.'.$tcgext.'" title="'.$row_wish['wlist_deck'].'" /></a>';
					}
					echo implode(' ', $wishes);
				} else {}
				echo '</div><!-- #wishlist -->

                <div id="masteries" align="center">';
				if ( $item['itm_masteries'] == "None" ) { echo '<i>This member haven\'t mastered any decks yet.</i>'; }
                else { echo '<img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $item['itm_masteries']).'.png">'; }
				echo '</div><!-- #gallery -->

                <div id="gallery" align="center">';
                if ( $item['itm_mcard'] == "None" ) {}
                else {
                    echo '<h3>Member Cards</h3>
                    <img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $item['itm_mcard']).'.png">';
                }
                echo '<br /><br />';
                if ( $item['itm_ecard'] == "None" ) {}
                else {
                    echo '<h3>Event Cards</h3>
                    <img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $item['itm_ecard']).'.png">';
                }
                echo '<br /><br />';
                if ( $item['itm_milestone'] == "" ) {}
                else {
                    echo '<h3>Milestone Cards</h3>
                    <img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $item['itm_milestone']).'.png">';
                }
                echo '</div><!-- #gallery -->

                <div id="logs" align="center">
                    <h3>Activity Logs</h3>
                    <div style="text-align: justify; padding-right: 20px; margin-top: 20px; line-height: 20px; font-size: 14px; overflow: auto; height: 300px;">';
                    //Put currency names in an array
                    $currencies = explode(', ',$settings->getValue('tcg_currency'));
                    foreach( $currencies as $c ) {
                        $currencyNames[] = substr($c, 0, -4);
                    }
                    
                    $timestamp = '';
                    while ( $row = mysqli_fetch_assoc($log1) ) {
                        $rewards = explode(', ',$row['log_rewards']);
                    
                        // Declare empty strings
                        $txtString = ''; 
                        $curString = ''; 

                        // Display cards for each reward if NOT a currency
                        foreach( $rewards as $r ) {
                            if( !in_array($r, $currencyNames) ) {
                                $txtString .= $r.', ';
                            }
                        }

                        // Get count of how many of each reward is present
                        $values = array_count_values($rewards);

                        // Display currencies that are in rewards and quantity only if exists in rewards
                        foreach( $currencyNames as $cn ) {
                            if( array_key_exists($cn, $values) ) {
                                // Pluralize the currencies if more than 1
                                if( $values[$cn] > 1 ) {
                                    $var = substr($cn, -1);
                                    if( $var == "y" ) {
                                        $vtn = substr_replace($cn,"ies",-1);
                                    } else if( $var == "o" ) {
                                        $vtn = substr_replace($cn,"oes",-1);
                                    }
                                    else { $vtn = $cn.'s'; }
                                } else { $vtn = $cn; }
                                $curString .= ', +'.$values[$cn].' '.$vtn;
                            }
                        }

                        // Display text of rewarded cards
                        $txtString = substr_replace($txtString,"",-2);

                        if ( $row['log_date'] != $timestamp ) {
                            echo '<br /><b>'.date('F d, Y', strtotime($row['log_date'])).' -----</b><br/>';
                            $timestamp = $row['log_date'];
                        }
                        echo '<li class="spacer">- <b>'.$row['log_title'];
                        if ( empty($row['log_subtitle']) ) {}
                        else { echo ' '.$row['log_subtitle']; }
                        echo ':</b> '.$txtString.''.$curString.'</li>';
                    } // end activity logs
                    echo '</div><br /><br />

                    <h3>Trade Logs</h3>
                    <div style="text-align: justify; padding-right: 20px; margin-top: 20px; line-height: 20px; font-size: 14px; overflow: auto; height: 300px;">';
                    $timestamp = '';
                    while ( $row = mysqli_fetch_assoc($log2) ) {
                        if ( $row['trd_date'] != $timestamp ) {
                            echo '<br /><b>'.date('F d, Y', strtotime($row['trd_date'])).' -----</b><br/>';
                            $timestamp = $row['trd_date'];
                        }
                        echo '<li class="spacer">- <b>Traded '.$row['trd_trader'];
                        echo ':</b> my '.$row['trd_out'].' for '.$row['trd_inc'].'</li>';
                    } // end trade logs
                    echo '</div>
                </div><!-- #logs -->

                <div id="trademe">';
                    if( isset($_POST['submit']) ) {
                        $email = $sanitize->for_db($_POST['email']);
                        $name = $sanitize->for_db($_POST['name']);
                        $url = $sanitize->for_db($_POST['url']);
                        $mc = $sanitize->for_db($_POST['member']);
                        $give = $sanitize->for_db($_POST['giving']);
                        $for = $sanitize->for_db($_POST['for']);
                        $id = $sanitize->for_db($_POST['id']);
                        $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_id`='$id'");
			
                        $recipient = "$row[usr_email]";
                        $subject = "$tcgname: Trade Request";

                        $message = "The following member has sent you a trade request: \n";
                        $message .= "Name: $name \n";
                        $message .= "Email: $email \n";
                        $message .= "URL: $url \n";
                        $message .= "Offering: $give \n";
                        $message .= "For: $for \n";
                        $message .= "Member Cards?: $member \n";

                        $headers = "From: $name <$recipient> \n";
                        $headers .= "Reply-To: <no-reply@hakumei.org>";

                        if( mail($recipient,$subject,$message,$headers) ) {
                            echo '<h2>Success!</h2>
                            <p>Your trade request has been successfully sent! The member should (hopefully) respond within a week.</p>';
                        } else {
                            echo '<h2>Error</h2>
                            <p>It looks like there was an error in processing your trade form. Why don\'t you check out their website to see if they have a trade form there?</p>';
                        }
                    } // end form process

                    $mem = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_name`='$id'");
                    echo '<ul>
                        <li>Please allow at least <i>7 days</i> for a response to your trade request.</li>
                        <li>If the form doesn\'t work, feel free to email me at <b>'.$mem['usr_email'].'</b></li>
                        <li><b>Please spell out card names COMPLETELY.</b> (ie. do NOT type cardname01/02; DO type cardname01, cardname02)</li>
                        <li>If you aren\'t sure what to give me, just put <b>card00</b> and I\'ll visit your profile!</li>
                    </ul>

                    <form method="post" action="'.$tcgurl.'members.php?id='.$id.'">
                        <input type="hidden" name="id" value="'.$mem['usr_id'].'" />
                        <table width="100%" class="table table-sliced table-striped">
                        <tbody><tr>
                            <td width="20%" align="right"><b>Name:</b></td>
                            <td width="78%"><input type="text" name="name" value="" style="width: 92%;" /></td>
                        </tr>
                        <tr>
                            <td align="right"><b>Email:</b></td>
                            <td><input type="text" name="email" value="" style="width: 92%;" />
                        </tr>
                        <tr>
                            <td align="right"><b>Trade Post:</b></td>
                            <td><input type="text" name="url" value="http://" style="width: 92%;" /></td>
                        </tr>
                        <tr>
                            <td align="right"><b>Member Cards:</b></td>
                            <td>
                                <input type="radio" name="member" value="yes" /> Yes &nbsp;&nbsp; 
                                <input type="radio" name="member" value="no"> No
                            </td>
                        </tr>
                        <tr>
                            <td align="right"><b>You Give:</b></td>
                            <td><input type="text" name="giving" value="" style="width: 92%;" /></td>
                        </tr>
                        <tr>
                            <td align="right"><b>You Want:</b></td>
                            <td><input type="text" name="for" value="" style="width: 92%;" /></td>
                        </tr>
                        </tbody></table>
                        <input type="submit" name="submit" class="btn-success" value="Trade" /> 
                        <input type="reset" name="reset" class="btn-danger" value="Reset" />
                    </form>
                </div><!-- #trademe -->
			</div><!-- /.tabcontents -->
        </td></tr>
        </table>';
	} // end profile view
} // end member page
include($footer);
?>