<?php
include("admin/class.lib.php");
include($header);

if (empty($login)) { header("Location: account.php?do=login"); }

$user = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
$free = $database->get_assoc("SELECT * FROM `user_freebies` WHERE `id`='$id'");
$logChk = $database->get_assoc("SELECT * FROM `logs_".$user['name']."` WHERE `name`='".$user['name']."' AND `title`='Freebies #".$free['id']."' AND `subtitle`='(".$free['timestamp'].")'");

if (empty($go)) {
	if ($logChk['title'] == "Freebies #".$free['id'] && $logChk['subtitle'] == "(".$free['timestamp'].")") {
		echo '<h1>Freebies #'.$free['id'].' ('.$free['timestamp'].') : Halt!</h1>
		<p>You have already claimed this freebie! If you missed your claims, here they are:</p>
		<center><b>'.$logChk['title'].' '.$logChk['subtitle'].':</b> '.$logChk['rewards'].'</center>';
	} else {
        echo '<h1>Freebies #'.$free['id'].': '.$free['timestamp'].'</h1>';
		echo '<blockquote class="wish">
		<strong><span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span></strong>';
		if ($free['type'] == 1) { echo 'Take choice cards spelling <b>'.$free['word'].'</b>!'; }
		else if ($free['type'] == 2) { echo 'Take a total of <b>'.$free['amount'].'</b> choice pack from any deck!'; }
		else if ($free['type'] == 3) { echo 'Take a total of <b>'.$free['amount'].'</b> random pack from any deck!'; }
		else if ($free['type'] == 4) { echo 'Take a total of 3 choice cards from any <b>'.$free['color'].'</b> decks!'; }
		echo '<strong><span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span></strong><br />
			<div class="notice"><b>You can only submit once!</b> Make sure to check your choices first before submitting.</div>
		</blockquote>

        <center><form method="post" action="/freebies.php?id='.$id.'&go=claimed">
		<input type="hidden" name="name" value="'.$user['name'].'">
		<input type="hidden" name="type" value="'.$free['type'].'">';
		if ($free['type'] == 1) {
            $w = $free['word'];
            $trim = str_replace(" ", "", $w);
            $length = strlen($trim);
            echo '<input type="hidden" name="word" value="'.$trim.'">
            <table width="100%" cellspacing="3" class="border">';
            for ($i=0; $i<$length; $i++) {
            $word = $trim[$i];
            echo '<tr>
                <td width="10%" class="headLine">'.$word.'</td>
                <td width="90%" class="tableBody"><select name="card'.$i.'" style="width:85%;">';
                if(is_numeric($word)) {
                    //QUERY YOUR DATABASE HERE FOR ALL RELEASED CARDS YOU WANT WHEN THE "WORD" IS A NUMBER
                    $query = $database->query("SELECT * FROM `tcg_cards` WHERE released <= '".$free['timestamp']."' AND `status`='Active' ORDER BY filename ASC");
                    while($row = mysqli_fetch_assoc($query)) {
                        $filename = stripslashes($row['filename']);
                        echo "<option value=\"$filename\">$row[deckname] ($filename)</option>\n";
                    }
                    echo '</select><select name="num'.$i.'">';
                    for($j=0; $j<=20; $j++) {
                        $j = str_pad($j,2,"0",STR_PAD_LEFT);
                        if((substr($j, 0, 1) == $word) || (substr($j, 1, 2) == $word)) {
                            echo '<option value="'.$j.'">'.$j.'</option>';
                        }
                    }
                    echo '</select></td></tr>';
                }
                else {
                    $query = $database->query("SELECT * FROM `tcg_cards` WHERE released <= '".$free['timestamp']."' AND `status`='Active' AND (filename LIKE '$word%' OR filename LIKE '%$word%' OR filename LIKE '%$word') ORDER BY filename ASC");
                    // DROPDOWN FOR EACH CHARACTERS
                    while($row = mysqli_fetch_assoc($query)) {
                        $filename = stripslashes($row['filename']);
                        echo "<option value=\"$filename\">$row[deckname] ($filename)</option>\n";
                    }
                    echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1"></td>';
                    echo '</tr>';
                }
            }
            echo '<tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Claim Freebies" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
            </table></form></center>';
		} else if ($free['type'] == 2) {
			echo '<input type="hidden" name="amount" value="'.$free['amount'].'">
			<table width="90%" cellspacing="3" class="border">';
			$c = $free['amount'];
			for ($i=1; $i<=$c; $i++) {
				echo '<tr><td width="10%" class="headLine">Choice #'.$i.'</td>
				<td width="90%" class="tableBody"><select name="card'.$i.'" style="width:85%;">';
				$query = $database->query("SELECT * FROM `tcg_cards` WHERE released <= '".$free['timestamp']."' AND `status`='Active' ORDER BY filename ASC");
				while($row=mysqli_fetch_assoc($query)) {
					$filename=stripslashes($row['filename']);
					echo "<option value=\"$filename\">$row[deckname] ($filename)</option>\n";
				}
				echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1"></td>';
				echo '</tr>
				<tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Claim Freebies" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
				</table></form></center>';
			}
		} else if ($free['type'] == 3) {
			echo '<input type="hidden" name="amount" value="'.$free['amount'].'">';
			$c = $free['amount'];
			for($i=1; $i<=$c; $i++) {
				echo '<input type="hidden" name="card'.$i.'" value="'; $general->randtype('Active'); echo "\" />\n";
            }
            echo '<table width="90%" cellspacing="3" class="border">
			<tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Claim Freebies" /></td></tr>
			</table></form></center>';
		} else if ($free['type'] == 4) {
			echo '<input type="hidden" name="amount" value="3">
			<table width="90%" cellspacing="3" class="border">';
			for($i=1; $i<=3; $i++) {
				echo '<tr><td width="10%" class="headLine">Choice #'.$i.'</td>
				<td width="90%" class="tableBody"><select name="card'.$i.'" style="width:85%;">';
				$query = $database->query("SELECT * FROM `tcg_cards` WHERE released <= '".$free['timestamp']."' AND `status`='Active' AND `category`='".$free['color']."' ORDER BY filename ASC");
				while($row=mysqli_fetch_assoc($query)) {
					$filename=stripslashes($row['filename']);
					echo "<option value=\"$filename\">$row[deckname] ($filename)</option>\n";
				}
				echo '</select><input type="text" name="num'.$i.'" placeholder="00" size="1"></td>';
				echo '</tr>
				<tr><td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Claim Freebies" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
				</table></form></center>';
			}
		}
    }
}

else if ($go == "claimed") {
	if(!isset($_SERVER['HTTP_REFERER'])){
		echo '<h1>Magical Approach!?</h1>
		<p>Tough luck! It seems like you\'re trying to outwit Liffy by secretly entering her misty forest through an underground method. That will surely anger the gods of the gods, sunshine! Please go back if you haven\'t played the game yet, or come back next week for a new round if you have already.</p>';
	} else {
		if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
			exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
		} else {
			$check->Value();
			$today = date("Y-m-d", strtotime("now"));
			$name = $sanitize->for_db($_POST['name']);
			$type = $sanitize->for_db($_POST['type']);
			$word = $sanitize->for_db($_POST['word']);
			$amount = $sanitize->for_db($_POST['amount']);

			$get = $database->get_assoc("SELECT * FROM `user_freebies` WHERE `id`='$id'");

			echo '<h1>Freebies #'.$get['id'].' ('.$get['timestamp'].')</h1>
			<p>Your freebies pulls has been logged on your permanent logs, make sure to log it on your trade post as well.</p>
			<center>';

			// DO REWARDS DEPENDING ON WISH TYPE
			if ($type == 1) {
				$amount = strlen($word);
				for($i=0; $i<$amount; $i++) {
					$cardImg = "card$i";
					$cardImg2 = "num$i";
					echo "<img src=\"/images/cards/";
					echo $_POST[$cardImg];
					echo $_POST[$cardImg2];
					echo ".png\" />\n";

					$card = "card$i";
					$card2 = "num$i";
					$pulled .= $_POST[$card].$_POST[$card2].", ";
	            }
			} else if ($type == 2 || $type == 3 || $type == 4) {
				for($i=0; $i<$amount; $i++) {
					$cardImg = "card$i";
					$cardImg2 = "num$i";
					echo "<img src=\"/images/cards/";
					echo $_POST[$cardImg];
					echo $_POST[$cardImg2];
					echo ".png\" />\n";

					$card = "card$i";
					$card2 = "num$i";
					$pulled .= $_POST[$card].$_POST[$card2].", ";
	            }
			}
			$rewards = substr_replace($pulled,"",-2);
            echo '<br /><strong>Freebies #'.$get['id'].' ('.$get['timestamp'].'):</strong> '.$rewards;
            $title = "Freebies #".$get['id'];
            $database->query("UPDATE `user_items` SET `cards`=cards+'$amount' WHERE `name`='$name'");
            $database->query("INSERT INTO `logs_$name` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$name','Pulls','$title','(".$get['timestamp'].")','$rewards','$today')");
            echo '</center>';
		}
	}
}

include($footer);
?>