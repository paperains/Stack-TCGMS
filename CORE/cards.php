<?php
include('admin/class.lib.php');
include($header);

$series = (isset($_GET['series']) ? $sanitize->for_db($_GET['series']) : null);

#################################
########## SHOW SERIES ##########
#################################
if (empty($view)) {
    if (empty($set)) {
        echo'<h1>Cards</h1>
        <p><img src="'.$tcgcards.'filler.png" align="left" /> <img src="'.$tcgcards.'pending.png" align="left" style="margin-right: 10px;" />Below you will find all of the current card decks arranged in sets where decks are sorted by <em>categories</em>, then by file name. The <b>#</b> indicates the number of cards, while the <b>$</b> indicates the worth of the cards. If you can\'t find a deck in particular, try using the <i>ctrl/command + f</i> to find it.</p>
        <center><div class="box-info"><b>Pro Tip!</b> If you are having troubles with your eTCG\'s auto upload function, you can grab the weekly releases <a href="/cards.php?view=zips">here</a>.</div></center><br />
        <div class="statLink">
            <a href="/cards.php?view=released">Released: '; $counts->numCards('Active','1'); echo' decks</a>
            <a href="/cards.php?view=upcoming">Upcoming: '; $counts->numCards('Upcoming',''); echo ' decks</a>
            <a href="/cards.php?view=claimed">Claimed: '; $counts->numClaimed('Claims'); echo ' decks</a>
            <a href="/cards.php?view=donated">Donated: '; $counts->numClaimed('Donation'); echo ' decks</a>
        </div><br />';
		$sql_set = $database->query("SELECT `series`, COUNT(deckname) FROM `tcg_cards` WHERE `status`='Active' GROUP BY `series`");
		echo '<center>';
		while ($row=mysqli_fetch_assoc($sql_set)) {
			echo '<a href="'.$tcgurl.'cards.php?set='.$row['series'].'"><img src="'.$tcgcards.''.$row['series'].'.png" border="0" /></a> ';
		}
		echo '</center>';
	} else {
	    echo'<h1>Cards : '.$series.'</h1>
        <p><img src="'.$tcgcards.'filler.png" align="left" /> <img src="'.$tcgcards.'pending.png" align="left" style="margin-right: 10px;" />Below you will find all of the current card decks arranged in sets where decks are sorted by <em>categories</em>, then by file name. The <b>#</b> indicates the number of cards, while the <b>$</b> indicates the worth of the cards. If you can\'t find a deck in particular, try using the <i>ctrl/command + f</i> to find it.</p>';
        // SHOW SEARCH FORM
        echo '<center><form method="post" action="">
        <input type="text" name="term" placeholder="Search released decks..." size="30" /> <input type="submit" name="search" value="   Search!   " />
        </form><br />';
        // DO SEARCH HERE
        if ( isset($_REQUEST['term']) ) {
            $term = $sanitize->for_db($_POST['term']);
            $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' AND (`deckname` LIKE '%".$term."%' OR `filename` LIKE '%".$term."%' OR `series` LIKE '%".$term."%' OR `donator` LIKE '%".$term."%' OR `maker` LIKE '%".$term."%') ORDER BY `deckname` ASC");
            if (mysqli_num_rows($sql) == 0) { echo '<div class="box-warning"><b>Error!</b> Your search query didn\'t match any data from the database.</div>'; }
            else {
                echo '<div class="box-success"><b>Success!</b> The data below shows any matches from your search query: <b>'.$term.'</b>.</div><br />
                <table width="80%" cellspacing="3" class="border">
                <tr><td class="headLineSmall" width="30%">Deckname</td><td class="headLineSmall" width="15%">Series</td><td class="headLineSmall" width="15%">Donator / Maker</td></tr>';
                while ($search = mysqli_fetch_assoc($sql)) {
                    echo '<tr><td class="tableBodySmall"><a href="/cards.php?view=released&deck='.$search['filename'].'">'.$search['deckname'].'</a></td><td class="tableBodySmall">'.$search['set'].'</td><td class="tableBodySmall"><a href="/members.php?id="'.$search['donator'].'">'.$search['donator'].'</a> / <a href="/members.php?id='.$search['maker'].'">'.$search['maker'].'</a></td></tr>';
                }
                echo '</table><br />';
            }
        }
        echo '</center>';
		$c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
		for($i=1; $i<=$c; $i++) {
			$sql = $database->query("SELECT * FROM `tcg_cards` WHERE `category`='$i' AND `series`='$series' AND `status`='Active' ORDER BY `category` ASC, `filename` ASC");
			$cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
			$count = mysqli_num_rows($sql);
			if($count==0) {echo "";}
			else {
				echo "<h2>".$cat['name']."</h2>\n";
				echo '<table width="100%" cellpadding="0" cellspacing="3" class="border">';
				echo '<tr><td width="70%" class="headLine">Deck Name</td><td width="20%" class="headLine">Deck Color</td><td width="10%" class="headLine">#/$</td></tr>';
				while($row=mysqli_fetch_assoc($sql)) {
					echo '<tr><td align="center" class="tableBody"><a href="'.$tcgurl.'cards.php?view=released&deck='.$row['filename'].'">'.$row['deckname'].'</a></td><td align="center" class="tableBody"><font color="'.$row['color'].'">'.$row['color'].'</font></td><td align="center" class="tableBody">';
					if($row['filename']=="member") {
						$select2 = $database->query("SELECT * FROM `user_list` WHERE `memcard`='Yes'");
						$memnum = mysqli_num_rows($select2);
						echo "$memnum/0";
					} else { echo "$row[count]/$row[worth]"; }
					echo '</td></tr>';
				}
				echo '</table>';
			}
        }
	}
} // END EMPTY VIEW

###################################
########## SHOW RELEASED ##########
###################################
else if ($view=="released") {
    if (empty($deck)) {
        echo '<h1>Cards : Released</h1>
        <p><img src="'.$tcgcards.'filler.png" align="left" /> <img src="'.$tcgcards.'pending.png" align="left" style="margin-right: 10px;" />Below you will find all of the current card decks arranged in sets where decks are sorted by <em>categories</em>, then by file name. The <b>#</b> indicates the number of cards, while the <b>$</b> indicates the worth of the cards. If you can\'t find a deck in particular, try using the <i>ctrl/command + f</i> to find it.</p>';
        // SHOW SEARCH FORM
        echo '<center><form method="post" action="">
        <input type="text" name="term" placeholder="Search released decks..." size="30" /> <input type="submit" name="search" value="   Search!   " />
        </form><br />';
        // DO SEARCH HERE
        if ( isset($_REQUEST['term']) ) {
            $term = $sanitize->for_db($_POST['term']);
            $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' AND (`deckname` LIKE '%".$term."%' OR `filename` LIKE '%".$term."%' OR `series` LIKE '%".$term."%' OR `donator` LIKE '%".$term."%' OR `maker` LIKE '%".$term."%') ORDER BY `deckname` ASC");
            if (mysqli_num_rows($sql) == 0) { echo '<div class="box-warning"><b>Error!</b> Your search query didn\'t match any data from the database.</div>'; }
            else {
                echo '<div class="box-success"><b>Success!</b> The data below shows any matches from your search query: <b>'.$term.'</b>.</div><br />
                <table width="80%" cellspacing="3" class="border">
                <tr><td class="headLineSmall" width="30%">Deckname</td><td class="headLineSmall" width="15%">Series</td><td class="headLineSmall" width="15%">Donator / Maker</td></tr>';
                while ($search = mysqli_fetch_assoc($sql)) {
                    echo '<tr><td class="tableBodySmall"><a href="/cards.php?view=released&deck='.$search['filename'].'">'.$search['deckname'].'</a></td><td class="tableBodySmall">'.$search['series'].'</td><td class="tableBodySmall"><a href="/members.php?id="'.$search['donator'].'">'.$search['donator'].'</a> / <a href="/members.php?id='.$search['maker'].'">'.$search['maker'].'</a></td></tr>';
                }
                echo '</table><br />';
            }
        }
        echo '</center>';
		$c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
		for($i=1; $i<=$c; $i++) {
			$sql = $database->query("SELECT * FROM `tcg_cards` WHERE `category`='$i' AND `status`='Active' ORDER BY `category` ASC, `filename` ASC");
			$cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
			$count = mysqli_num_rows($sql);
			if($count==0) {echo "";}
			else {
				echo "<h2><span class=\"line-center\">".$cat['name']."</span></h2>\n";
				echo '<table width="100%" cellpadding="0" cellspacing="3" class="border">';
				echo '<tr><td width="70%" class="headLine">Deck Name</td><td width="20%" class="headLine">Deck Color</td><td width="10%" class="headLine">#/$</td></tr>';
				while($row=mysqli_fetch_assoc($sql)) {
					echo '<tr><td align="center" class="tableBody"><a href="'.$tcgurl.'cards.php?view=released&deck='.$row['filename'].'">'.$row['deckname'].'</a></td><td align="center" class="tableBody"><font color="'.$row['color'].'">'.$row['color'].'</font></td><td align="center" class="tableBody">';
					if($row['filename']=="member") {
						$select2 = $database->query("SELECT * FROM `user_list` WHERE `memcard`='Yes'");
						$memnum = mysqli_num_rows($select2);
						echo "$memnum/0";
					} else { echo "$row[count]/$row[worth]"; }
					echo '</td></tr>';
				}
				echo '</table>';
			}
		}
	} else {
		$query = $database->query("SELECT * FROM `tcg_cards` WHERE filename='$deck' AND status='Active'");
		while($row=mysqli_fetch_assoc($query)) {
			echo '<h1><font color="'.$row['color'].'"><span class="fas fa-tint" aria-hidden="true"></span></font> '.$row['deckname'].'</h1>
			<p>'.$row['description'].'</p>
			<center><table width="100%" cellspacing="0" border="0" class="table table-bordered table-striped">
			<tr><td colspan="3"><h2>'.$row['series'].'</h2></td></tr>
            <tr>
				<td width="30%" rowspan="5" align="center" valign="top">
                    <img src="'.$tcgcards.''.$row['filename'].'.'.$tcgext.'" /><br />';
                    include("admin/wish.php");
                echo '</td>
                <td width="70%" rowspan="6" align="center">
                    <div style="width: 510px;">';
                    if($set=="member") {
                        $query2 = $database->query("SELECT * FROM `user_list` WHERE `memcard`='Yes' ORDER BY `name`");
                        while($row2=mysqli_fetch_assoc($query2)) {
                            echo '<img src="'.$tcgcards.'mc-'.$row2['name'].''.$tcgext.'" />';
                        }
                    } else {
                        for($x=1;$x<=$row['count'];$x++) {
                            if($x<10) {
                                echo '<img src="'.$tcgcards.''.$row['filename'];
                                echo "0";
                                echo $x.'.'.$tcgext.'" />';
                            } else {
                                echo '<img src="'.$tcgcards.''.$row['filename'].''.$x.'.'.$tcgext.'" />';
                            }
                        }
                    }
                echo '</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" valign="middle"><b>Deck/File Name:</b> '.$row['deckname'].' (<i>'.$row['filename'].'</i>)</td>
            </tr>
            <tr>
				<td width="40%" valign="middle"><b>Made/Donated by:</b> '.$row['maker'].' / '.$row['donator'].'</td>
                <td width="40%" valign="middle"><b>Color:</b> <font color="'.$row['color'].'">'.$row['color'].'</font></td>
            </tr>
            <tr>
				<td valign="middle"><b>Released:</b> '.$row['released'].'</td>
                <td valign="middle"><b>Masterable:</b> '.$row['masterable'].'</td>
            </td>
            <tr><td colspan="2" valign="middle"><b>Wished by:</b> ';
				$w = $database->query("SELECT * FROM `user_wishlist` WHERE deck='$deck'");
				$c = mysqli_num_rows($w);
				if($c != 0) {   
					$names = array();   
					while($rw = mysqli_fetch_array($w)) {
						$names[] = '<a href="'.$tcgurl.'members.php?id='.$rw['name'].'">'.$rw['name'].'</a>';
					}
					echo implode(', ', $names);
				} else { echo "None"; }
				echo '</td>
            </tr>
            <tr><td colspan="2" valign="middle"><b>Mastered by:</b> '.$row['masters'].'</td></tr>
            </table></center>';
        }
	}
}

###################################
########## SHOW UPCOMING ##########
###################################
else if ($view=="upcoming") {
	if (empty($deck)) {
		echo '<h1>Cards : Upcoming</h1>
		<p>Below you will find all of the upcoming card decks here at '.$tcgname.' which are either complete (made but haven\'t been released) or incomplete (work in progress). Any decks that have been listed here are no longer <em>subject for claiming or donation</em>.</p>';
        // SHOW SEARCH FORM
        echo '<center><form method="post" action="">
        <input type="text" name="term" placeholder="Search upcoming decks..." size="30" /> <input type="submit" name="search" value="   Search!   " />
        </form><br />';
        // DO SEARCH HERE
        if ( isset($_REQUEST['term']) ) {
            $term = $sanitize->for_db($_POST['term']);
            $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Upcoming' AND (`deckname` LIKE '%".$term."%' OR `filename` LIKE '%".$term."%' OR `series` LIKE '%".$term."%' OR `donator` LIKE '%".$term."%' OR `maker` LIKE '%".$term."%') ORDER BY `deckname` ASC");
            if (mysqli_num_rows($sql) == 0) { echo '<div class="box-warning"><b>Error!</b> Your search query didn\'t match any data from the database.</div>'; }
            else {
                echo '<div class="box-success"><b>Success!</b> The data below shows any matches from your search query: <b>'.$term.'</b>.</div><br />
                <table width="80%" cellspacing="3" class="border">
                <tr><td class="headLineSmall" width="30%">Deckname</td><td class="headLineSmall" width="15%">Series</td><td class="headLineSmall" width="15%">Donator / Maker</td></tr>';
                while ($search = mysqli_fetch_assoc($sql)) {
                    echo '<tr><td class="tableBodySmall"><a href="/cards.php?view=upcoming&deck='.$search['filename'].'">'.$search['deckname'].'</a></td><td class="tableBodySmall">'.$search['set'].'</td><td class="tableBodySmall"><a href="/members.php?id="'.$search['donator'].'">'.$search['donator'].'</a> / <a href="/members.php?id='.$search['maker'].'">'.$search['maker'].'</a></td></tr>';
                }
                echo '</table><br />';
            }
        }
        echo '</center><table width="100%" cellspacing="0" border="0">
			<tr><td width="45%" valign="top">
				<h2>Recently Made</h2><small>Do not take from these as they aren\'t released yet!</small><center>';
				$r = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Upcoming' ORDER BY `id` DESC LIMIT 9");
				while($rec = mysqli_fetch_assoc($r)) {
					$digits = rand(01,$rec['count']);
					if ($digits < 10) { $_digits = "0$digits"; }
					else { $_digits = $digits; }
					$card = "$rec[filename]$_digits";
					echo '<a href="'.$tcgurl.'cards.php?view=upcoming&deck='.$rec['filename'].'"><img src="'.$tcgcards.''.$card.'.png"></a>';
				}
				echo '</center>
				</td><td width="1%">&nbsp;</td>
				<td width="54%" valign="top"><h2>Upcoming Week\'s Releases</h2>
					<table width="100%" cellspacing="3" class="border">
					<tr><td width="80%" class="headLineSmall">Deck</td><td width="20%" class="headLineSmall">Votes</td></tr>';
					$vs = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Upcoming' ORDER BY `votes` DESC LIMIT 4");
					$vc = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `status`='Upcoming' ORDER BY `votes` DESC LIMIT 4");
					if ($vc['votes'] == 0) { echo '<tr><td colspan="2" class="tableBodySmall" align="center"><i>There are no voted decks at the moment.</i></td></tr>'; }
					else {
						while($v = mysqli_fetch_assoc($vs)) {
							echo '<tr><td class="tableBodySmall"><a href="'.$tcgurl.'cards.php?view=upcoming&deck='.$v['filename'].'">'.$v['deckname'].'</a> ('.$v['filename'].')</td><td class="tableBodySmall" align="center">'.$v['votes'].'</td></tr>';
						}
					}
					echo '</table>
					<h2>Top 5 Donators</h2><small>The data below shows only the decks that are already made.</small>
					<table width="100%" cellspacing="3" class="border">
						<tr><td width="80%" class="headLineSmall">Member</td><td width="20%" class="headLineSmall">Decks</td></tr>';
						$ds = $database->query("SELECT donator, COUNT(*) AS `count` FROM `tcg_cards` GROUP BY `donator` ORDER BY `count` DESC LIMIT 5");
						while($d = mysqli_fetch_assoc($ds)) {
							echo '<tr><td class="tableBodySmall"><a href="'.$tcgurl.'members.php?id='.$d['donator'].'">'.$d['donator'].'</a></td><td class="tableBodySmall" align="center">'.$d['count'].'</td></tr>';
						}
					echo '</table>
				</td>
			</tr>
		</table>';
		$c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
		for($i=1; $i<=$c; $i++) {
			$select = $database->query("SELECT * FROM `tcg_cards` WHERE `category`='$i' AND `status`='Upcoming' ORDER BY `series` ASC, `filename` ASC");
			$cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
			$count = mysqli_num_rows($select);
			echo "<h2>".$cat['name']."</h2>";
			if($count==0) { echo '<center>There are currently no card decks in this category.</center>'; }
			else {
				echo '<table width="100%" cellpadding="0" cellspacing="3" class="border">';
				echo '<tr><td width="30%" class="headLine">Series</td><td width="60%" class="headLine">Deckname</td><td width="10%" class="headLine">#/$</td></tr>';
				while($row=mysqli_fetch_assoc($select)) {
					echo '<tr class="rows"><td align="center" class="tableBodySmall">'.$row['series'].'</td><td align="center" class="tableBodySmall"><a href="'.$tcgurl.'cards.php?view=upcoming&deck='.$row['filename'].'">'.$row['deckname'].'</a> ('.$row['filename'].')</td><td align="center" class="tableBodySmall">';
					if($row['filename']=="member") {
						$memnum = $database->num_rows("SELECT * FROM `user_list` WHERE `membercard`='Yes'");
						echo "$memnum/0";
					} else { echo "$row[count]/$row[worth]"; }
					echo '</td></tr>';
				}
				echo '</table>';
			}
		}
	} else {
		$query = $database->query("SELECT * FROM `tcg_cards` WHERE filename='$deck' AND status='Upcoming'");
		while($row=mysqli_fetch_assoc($query)) {
			echo '<h1><font color="'.$row['color'].'"><span class="fas fa-tint" aria-hidden="true"></span></font> '.$row['deckname'].'</h1>
			<p>'.$row['description'].'</p>
			<center><div class="box-warning"><b>This is an upcoming deck!</b><br />Please do not take the cards below until it is released.</div><br />
			<table width="100%" cellspacing="0" border="0" class="table table-bordered table-striped">
			<tr><td colspan="3"><h2>'.$row['series'].'</h2></td></tr>
            <tr>
				<td width="30%" colspan="2" align="center" valign="top" height="150">
                    <img src="'.$tcgcards.''.$row['filename'].'.'.$tcgext.'" /><br />';
                    include("admin/wish.php");
                echo '</td>
                <td width="70%" rowspan="6" align="center">
                    <div style="width: 510px;">';
                        if($set=="member") {
                            $query2 = $database->query("SELECT * FROM `user_list` WHERE `memcard`='Yes' ORDER BY `name`");
                            while($row2=mysqli_fetch_assoc($query2)) {
                                echo '<img src="'.$tcgcards.'mc-'.$row2['name'].''.$tcgext.'" />';
                            }
                        } else {
                            for($x=1;$x<=$row['count'];$x++) {
                                if($x<10) {
                                    echo '<img src="'.$tcgcards.''.$row['filename'];
                                    echo "0";
                                    echo $x.'.'.$tcgext.'" />';
                                } else {
                                    echo '<img src="'.$tcgcards.''.$row['filename'].''.$x.'.'.$tcgext.'" />';
                                }
                            }
                        }
                    echo '</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" valign="middle"><b>Deck/File Name:</b> '.$row['deckname'].' (<i>'.$row['filename'].'</i>)</td>
            </tr>
            <tr>
				<td width="40%" valign="middle"><b>Made/Donated:</b> '.$row['maker'].' / '.$row['donator'].'</td>
                <td width="40%" valign="middle"><b>Color:</b> <font color="'.$row['color'].'">'.$row['color'].'</font></td>
            </tr>
            <tr>
				<td valign="middle"><b>Released:</b> '.$row['released'].'</td>
                <td valign="middle"><b>Masterable:</b> '.$row['masterable'].'</td>
            </tr>
            <tr><td colspan="2" valign="middle"><b>Wished by:</b> ';
				$w = $database->query("SELECT * FROM `user_wishlist` WHERE deck='$deck'");
				$c = mysqli_num_rows($w);
				if($c != 0) {   
					$names = array();   
					while($rw = mysqli_fetch_array($w)) {
						$names[] = '<a href="'.$tcgurl.'members.php?id='.$rw['name'].'">'.$rw['name'].'</a>';
					}
					echo implode(', ', $names);
				} else { echo "None"; }
				echo '</td>
            </tr>
            <tr><td colspan="2" valign="middle"><b>Mastered by:</b> '.$row['masters'].'</td></tr>
            </table>
            </center>';
        }
	}
} // END VIEW UPCOMING

##################################
########## SHOW CLAIMED ##########
##################################
else if ($view=="claimed") {
	echo '<h1>Cards : Claimed Decks</h1>
	<p>Below is the complete list of our claimed decks. These decks are no longer subject for claiming and/or donation and are already on its way to being made. Make sure to check this list first before sending in a donation.</p>';
    // SHOW SEARCH FORM
    echo '<center><form method="post" action="">
    <input type="text" name="term" placeholder="Search claimed decks..." size="30" /> <input type="submit" name="search" value="   Search!   " />
    </form><br />';
    // DO SEARCH HERE
    if ( isset($_REQUEST['term']) ) {
        $term = $sanitize->for_db($_POST['term']);
        $sql = $database->query("SELECT * FROM `tcg_donations` WHERE `type`='Claims' AND (`deckname` LIKE '%".$term."%' OR `feature` LIKE '%".$term."%' OR `series` LIKE '%".$term."%' OR `name` LIKE '%".$term."%') ORDER BY `deckname` ASC");
        if (mysqli_num_rows($sql) == 0) { echo '<div class="box-warning"><b>Error!</b> Your search query didn\'t match any data from the database.</div><br />'; }
        else {
            echo '<div class="box-success"><b>Success!</b> The data below shows any matches from your search query: <b>'.$term.'</b>.</div><br />
            <table width="80%" cellspacing="3" class="border">
            <tr><td class="headLineSmall" width="30%">Deckname</td><td class="headLineSmall" width="15%">Series</td><td class="headLineSmall" width="15%">Donator</td></tr>';
            while ($search = mysqli_fetch_assoc($sql)) {
                echo '<tr><td class="tableBodySmall">'.$search['feature'].'</td><td class="tableBodySmall">'.$search['series'].'</td><td class="tableBodySmall">'.$search['name'].'</td></tr>';
            }
            echo '</table><br />';
        }
    }
	echo '</center><table width="100%" cellspacing="3" class="border">
		<tr>
			<td class="headLine" width="15%">Category</td>
			<td class="headLine" width="25%">Features</td>
			<td class="headLine" width="20%">Series</td>
			<td class="headLine" width="20%">Donator</td>
		</tr>';
		$sql = $database->query("SELECT * FROM `tcg_donations` WHERE `type`='Claims' ORDER BY `category`, `deckname` ASC");
		while ($row=mysqli_fetch_assoc($sql)) {
			echo '<tr>
			<td class="tableBody" align="center">'.$row['category'].'</td>
			<td class="tableBody" align="center">'.$row['feature'].'</td>
			<td class="tableBody" align="center">'.$row['series'].'</td>
			<td class="tableBody" align="center">'.$row['name'].'</td>
			</tr>';
		}
	echo '</table>';
} // END VIEW CLAIMED

##################################
########## SHOW DONATED ##########
##################################
else if ($view=="donated") {
	echo '<h1>Cards : Donated Decks</h1>
	<p>Below is the complete list of our donated decks. These decks are no longer subject for claiming and/or donation and are already on its way to being made. Make sure to check this list first before sending in a donation.</p>';
    // SHOW SEARCH FORM
    echo '<center><form method="post" action="">
    <input type="text" name="term" placeholder="Search donated decks..." size="30" /> <input type="submit" name="search" value="   Search!   " />
    </form><br />';
    // DO SEARCH HERE
    if ( isset($_REQUEST['term']) ) {
        $term = $sanitize->for_db($_POST['term']);
        $sql = $database->query("SELECT * FROM `tcg_donations` WHERE `type`='Donation' AND (`deckname` LIKE '%".$term."%' OR `feature` LIKE '%".$term."%' OR `series` LIKE '%".$term."%' OR `name` LIKE '%".$term."%') ORDER BY `deckname` ASC");
        if (mysqli_num_rows($sql) == 0) { echo '<div class="box-warning"><b>Error!</b> Your search query didn\'t match any data from the database.</div><br />'; }
        else {
            echo '<div class="box-success"><b>Success!</b> The data below shows any matches from your search query: <b>'.$term.'</b>.</div><br />
            <table width="80%" cellspacing="3" class="border">
            <tr><td class="headLineSmall" width="30%">Deckname</td><td class="headLineSmall" width="15%">Series</td><td class="headLineSmall" width="15%">Donator</td></tr>';
            while ($search = mysqli_fetch_assoc($sql)) {
                echo '<tr><td class="tableBodySmall"><a href="'.$search['url'].'">'.$search['feature'].'</a></td><td class="tableBodySmall">'.$search['series'].'</td><td class="tableBodySmall">'.$search['name'].'</td></tr>';
            }
            echo '</table><br />';
        }
    }
	echo '</center><table width="100%" cellspacing="3" class="border">
		<tr>
			<td class="headLine" width="15%">Category</td>
			<td class="headLine" width="25%">Features</td>
			<td class="headLine" width="20%">Series</td>
			<td class="headLine" width="15%">Donator</td>
            <td class="headLine" width="15%">Maker</td>
		</tr>';
		$sql = $database->query("SELECT * FROM `tcg_donations` WHERE `type`='Donation' ORDER BY `category`, `deckname` ASC");
		while ($row=mysqli_fetch_assoc($sql)) {
			echo '<tr>
			<td class="tableBody" align="center">'.$row['category'].'</td>
			<td class="tableBody" align="center"><a href="'.$row['url'].'" target="_blank">'.$row['feature'].'</a></td>
			<td class="tableBody" align="center">'.$row['series'].'</td>
			<td class="tableBody" align="center">'.$row['name'].'</td>
            <td class="tableBody" align="center">'.$row['maker'].'</td>
			</tr>';
		}
	echo '</table>';
} // END VIEW DONATED

###############################
########## SHOW ZIPS ##########
###############################
else if ($view == "zips") {
    echo '<h1>Cards : Weekly Zips</h1>
    <p>Below is the list of active card zips based on the weekly release. If you are having troubles uploading your cards using your eTCG\'s auto upload function, you can grab the zips below.</p>
    <div class="statLink">
        <a href="" target="_blank">0000-00-00</a>
    </div>';
} // END VIEW ZIPS
include($footer);
?>