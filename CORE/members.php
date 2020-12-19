<?php
include("admin/class.lib.php");
include($header);

if ($page == "join") {
    if ($stat == "sent") {
        if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { echo '<div class="box"><p>You did not press the submit button; this page should not be accessed directly.</p></div>'; }
        else {
            $check->Member();
            if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,6})$/",strtolower($_POST['email']))) { exit("<h1>Error</h1>\nThat e-mail address is not valid, please use another.<br /><br />"); }
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
            
            $headers = "From: $name <$email> \n";
            $headers .= "Reply-To: $name <$email>";
            
            echo '<h1>Welcome!</h1>
            <p>Welcome to '.$tcgname.'! Below is your starter pack. You will not be able to play games until you have been approved by the owner but you can take cards from updates posted on or after '.date("F j, Y").'.</p>
            <center>';
            for($i=1; $i<=$settings->getValue('cards_start_choice'); $i++) {
                $card = "choice$i";
                echo '<img src="'.$tcgcards.''.$collecting;
                echo $_POST[$card];
                echo '.'.$tcgext.'" />';
            }
            for($i=1; $i<=$settings->getValue('cards_start_reg'); $i++) {
                $card = "random$i";
                echo '<img src="'.$tcgcards;
                echo $_POST[$card];
                echo '.'.$tcgext.'" />';
            }
            echo "<br /><br />
            <b>Starter Pack:</b>&nbsp;";
            $choice = null; $rand = null;
            for($i=1; $i<=$settings->getValue('cards_start_choice'); $i++) {
                $card = "choice$i";
                echo $collecting;
                echo $_POST[$card];
                echo ", ";
                // LOGS MOD
                $cards = "$collecting$_POST[$card]";
                $choice .= $cards.", ";
            }
            for($i=1; $i<=$settings->getValue('cards_start_reg'); $i++) {
                $card = "random$i";
                echo $_POST[$card];
                echo ", ";
                // LOGS MOD
                $cards2 = "$_POST[$card]";
                $rand .= $cards2.", ";
            }
            $choice = substr_replace($choice,"",-2);
            $rand = substr_replace($rand,"",-2);
            echo "</center>";

            $insert = $database->query("INSERT INTO `user_list` (`name`,`email`,`url`,`refer`,`birthday`,`password`,`collecting`,`about`,`twitter`,`discord`,`regdate`) VALUES ('$name','$email','$url','$refer','$bday','$pass','$collecting','$bio','N / A','N / A','$date')");

            // Create Logs Table
            $query1 = "CREATE TABLE IF NOT EXISTS `logs_$name` (
            `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
            `type` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
            `title` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
            `subtitle` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
            `rewards` text COLLATE utf8_unicode_ci NOT NULL,
            `timestamp` date NOT NULL DEFAULT '0000-00-00'
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
            $clogs = $database->query($query1);

            // Create Trades Table
            $query2 = "CREATE TABLE IF NOT EXISTS `trades_$name` (
            `trader` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
            `outgoing` text COLLATE utf8_unicode_ci NOT NULL,
            `incoming` text COLLATE utf8_unicode_ci NOT NULL,
            `timestamp` date NOT NULL DEFAULT '0000-00-00'
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
            $ctrades = $database->query($query2);

            if (mail($recipient,$subject,$message,$headers)) {
                if($insert === TRUE && $clogs === TRUE && $ctrades === TRUE) {
                    $database->query("INSERT INTO `logs_$name` (`name`,`type`,`title`,`rewards`,`timestamp`) VALUES ('$name','Service','Starter Pack','$choice, $rand','$date2')");
                    $database->query("INSERT INTO `trades` (`name`,`points`,`updated`) VALUES ('$name','0','$date2')");
                    $database->query("INSERT INTO `user_items` (`name`,`cards`,`timestamp`) VALUES ('$name','10','$date2')");
                    
                    /** Referral rewards **/
                    if ($refer == "None") { }
                    else { $database->query("INSERT INTO `user_rewards` (`name`,`type`,`subtitle`,`mcard`,`cards`,`timestamp`) VALUES ('$refer','Services','(Referral)','1','$date2')"); }

                    $recipient2 = "$email";
                    $subject2 = $tcgname.": Starter Pack";
                    $message2 = "Thanks for joining ".$tcgname.", $name! We are very excited that you are going to be joining us. Your account is currently pending approval, but you can begin playing games regardless. Below is a copy of your starter pack, in case you did not pick it up from the site. \n\n";
                    for($i=1; $i<=$settings->getValue('cards_start_choice'); $i++) {
                        $card = "choice$i";
                        $message2 .= "$tcgcards$collecting";
                        $message2 .= $_POST[$card];
                        $message2 .= ".$tcgext\n";
                    }
                    for($i=1; $i<=$settings->getValue('cards_start_reg'); $i++) {
                        $card = "random$i";
                        $message2 .= "$tcgcards";
                        $message2 .= $_POST[$card];
                        $message2 .= ".$tcgext\n";
                    }
                    $message2 .= "\nThanks again for joining and happy trading!\n\n";
                    $message2 .= "-- $tcgowner\n";
                    $message2 .= "$tcgname: $tcgurl\n";
                    $headers2 = "From: $tcgname <$tcgemail> \n";
                    $headers2 .= "Reply-To: $tcgname <$tcgemail>";
                    mail($recipient2,$subject2,$message2,$headers2);
                } else {
                    echo '<h1>Error</h1>
                    <p>It looks like there was an error in processing your join form. Send the information to '.$tcgemail.' and we will send you your starter pack ASAP. Thank you and sorry for the inconvenience.</p>';
                }
            }
        }
    } else {
        echo '<h1>Become a queen!</h1>
        <p>We are glad that you\'re finally joining us here at '.$tcgname.'! Before filling up the form, kindly please have a moment to read the set of rules below, many thanks~!</p>
        <h2>Members must...</h2>
        <ol>
            <li>have a working website (trade post) and email address must be valid.</li>
            <li>use a realistic name or nickname. If your name is already taken on the members list, please change your name or add a number instead (alphanumeric only).</li>
            <li>upload your starter pack within two weeks. If you need more time, just please let me know.</li>
            <li>update your trade posts at least <i>every two months</i>. If you do not, your status will be changed to <b>inactive</b> and you must reactivate your membership to continue trading.</li>
            <li>keep a detailed log on your trade post so we know where you got your cards and other '.$tcgname.' stuff from.</li>
            <li>send a hiatus notice if you need to, because if your trade post is left un-updated, I\'ll assume that you stopped playing or no longer interested.</li>
            <li><u>NOT DIRECT-LINK</u> any graphics from '.$tcgname.'. Please upload them to your own server or a free image site, such as <a href="http://www.photobucket.com/" target="_blank">Photobucket</a> or <a href="http://www.imgur.com/" target="_blank">Imgur</a>.</li>
            <li><u>NOT CHEAT</u> anywhere and in anyway possible. Which means...
                <ul><li>..you are <i>not allowed</i> to refresh any prize page or randomizer unless you are told to do so.</li>
                <li>..you are not allowed to give out answers to fellow players as well.</li>
                <li>..you will play the games only <u>ONCE</u> per round unless told otherwise.</li>
                <li>..you have to wait for the next game update in order to play again.</li></ul></li>
            <li>provide a password to be able to access forms and the interactive section. This password is encoded in the database and cannot be retrieved or viewed by anyone.</li>
            <li>be nice and polite to other members as much as possible. If members don\'t want to trade, respect their decision. Let\'s make this place peaceful and enjoyable.</li>
        </ol>
        
        <h2><span class="line-center">Freebies can/must...</span></h2>
        <ol>
            <li>be taken from the latest update regardless of joining after it was posted.</li>
            <li>be taken anytime as they are not restricted to any deadlines.</li>
            <li>be all claimed at the same time as we do not allow claiming them in parts.</li>
        </ol>
        <p>Lastly, never forget to comment on any updates where the freebies were announced with what you\'ve taken. Otherwise, you will be asked to remove any of the cards you took without commenting.</p>
        
        <h2><span class="line-center">Join Form</span></h2>
        <form method="post" action="/members.php?page=join&stat=sent">
        <input type="hidden" name="about" value="Coming Soon" />';
        for($i=1; $i<=$settings->getValue('cards_start_choice'); $i++) {
            $sql = $database->get_assoc("SELECT * FROM `tcg_cards`");
            $digit = rand(01,$sql['count']);
            if ($digit < 10) { $_digits = "0$digit"; }
            else { $_digits = $digit; }
            echo "<input type=\"hidden\" name=\"choice$i\" value=\"$_digits\" />\n";
        }
        for($i=1; $i<=$settings->getValue('cards_start_reg'); $i++) {
            echo '<input type="hidden" name="random'.$i.'" value="'; $general->randtype('Active'); echo "\" />\n";
        }
        echo '<table width="100%" cellpadding="0" cellspacing="3" class="border">
        <tr>
            <td width="15%" class="headLine">Name:</td><td width="35%" class="tableBody"><input type="text" name="name" style="width: 90%;" placeholder=" Jane Doe " /></td>
            <td class="headLine">Email:</td><td class="tableBody"><input type="text" name="email" style="width: 90%;" placeholder="username@domain.tld" /></td>
        </tr>
        <tr>
            <td class="headLine">Trade Post:</td><td class="tableBody"><input type="text" name="url" style="width: 90%;" placeholder="http://" /></td>
            <td class="headLine">Birthday:</td><td class="tableBody"><select name="month" style="width:40%;">
            <option>-----</option>';
            for($m=1; $m<=12; $m++) {
                if ($m < 10) { $_mon = "0$m"; }
                else { $_mon = $m; }
                echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
            }
            echo '</select> &nbsp; <select name="day">
            <option>-----</option>';
            for($i=1; $i<=31; $i++) {
                if ($i < 10) { $_days = "0$i"; }
                else { $_days = $i; }
                echo '<option value="'.$_days.'">'.$_days.'</option>';
            }
            echo '</select> &nbsp; ';
            //get the current year
            $date=date('Y');
            $start=$date-10;
            $end=$start-40;
            // set start and end year range i.e the start year
            $yearArray = range($start,$end);
            // here you displaying the dropdown list
            echo '<select name="year">
            <option value="">-----</option>';
            foreach ($yearArray as $year) {
            // this allows you to select a particular year
            $selected = ($year == $start) ? 'selected' : '';
            echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
            }
        echo '</select></td>
        </tr>
        <tr>
            <td class="headLine">Collecting:</td><td class="tableBody"><select name="collecting" style="width: 97%;">
            <option value="">-----</option>';
            $query = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' ORDER BY `series` ASC, `deckname` ASC");
            while($row=mysqli_fetch_assoc($query)) {
                $series=stripslashes($row['series']);
                $name=stripslashes($row['filename']);
                $deckname=stripslashes($row['deckname']);
                echo '<option value="'.$name.'">'.$series.' - '.$deckname."</option>\n";
            }
            echo '</select></td>
            <td class="headLine">Referral:</td><td class="tableBody"><select name="refer" style="width: 97%;" />
            <option value="None">None (e.g. TCG wiki)</option>';
            $mem = $database->query("SELECT * FROM `user_list` ORDER BY name ASC");
            while($row1=mysqli_fetch_assoc($mem)) {
                $name=stripslashes($row1['name']);
                echo "<option value=\"$name\">$name</option>\n";
            }
            echo '</select></td>
        </tr>
        <tr>
            <td class="headLine">Member Card:</td><td class="tableBody" colspan="3"><input type="text" name="mcard" style="width: 95%;" placeholder="http://" /></td>
        </tr>
        <tr><td class="headLine">Password:</td><td class="tableBody" colspan="3"><input type="password" name="password" placeholder="********" style="width:46%;" /> <input type="password" name="password2" placeholder="Retype your password" style="width:46%;" /></td></tr> 
        <tr><td colspan="4" class="tableBody" align="center"><input type="submit" name="submit" class="btn-success" value="Join Hanayaka" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
        </table></form>';
    }
} // END JOIN PAGE

else {
    if (empty($id)) {
        if (empty($stat)) {
            echo '<h1>Members</h1>
            <p>This is the full list of <b>active</b>, <b>pending</b> and members currently in <b>hiatus</b> of <i>'.$tcgname.'</i>. Please take note that all <b>pending</b> members are <u>allowed to participate in the TCG until approved</u>, but only <b>active</b> or approved members have a full access of the TCG.</p>
            <p>All members are sorted by <em>level</em> (but levels that have a member will be visible only), and then by <em>name in alphabetical order</em>. If you want to view the member\'s profile, decks they have mastered, achievements that they may have and the likes, just click on their member card.</p>
            <div class="statLink">
                <a href="/members.php?stat=pending">Pending ('; $counts->numAll('user_list','Pending'); echo ')</a>
                <a href="/members.php?stat=hiatus">Hiatus ('; $counts->numAll('user_list','Hiatus'); echo ')</a>
                <a href="/members.php?stat=inactive">Inactive ('; $counts->numAll('user_list','Inactive'); echo ')</a>
                <a href="/members.php?stat=retired">Retired ('; $counts->numAll('user_list','Retired'); echo ')</a>
            </div>';
            $lvlcount = $database->num_rows("SELECT * FROM `tcg_levels`");
            for($i=1; $i<=$lvlcount; $i++) {
                $select = $database->query("SELECT * FROM `user_list` WHERE `level`='$i' AND `status`='Active' ORDER BY `name`");
                $count = mysqli_num_rows($select);
                if($count==0) { echo "&nbsp;"; }
                else {
                    echo "<h2>Level ".$i."</h2>\n";
                    echo '<center>';
                    while($row=mysqli_fetch_assoc($select)) {
                        echo '<div class="memList">';
                            if ($row['memcard']=="Yes") { echo '<a href="/members.php?id='.$row['name'].'"><img src="'.$tcgcards.'mc-'.$row['name'].'.'.$tcgext.'" /></a>'; }
                            else { echo '<a href="/members.php?id='.$row['name'].'"><img src="'.$tcgcards.'mc-filler.'.$tcgext.'" /></a>'; }
                            echo '<div class="memName" align="center"><a href="/members.php?id='.$row['name'].'">'.$row['name'].'</a></div>';
                            echo '<div class="socIcon">';
                                $prejoin = $row['prejoiner'];
                                if ($prejoin=="Beta") { echo '<li><font color="#ffa500"><span class="fas fa-crown" aria-hidden="true" title="Prejoin Beta Tester"></span></font></li>'; }
                                else if ($prejoin=="Yes") { echo '<li><font color="#c81b3c"><span class="fas fa-crown" aria-hidden="true" title="Prejoiner"></span></font></li>'; }
                                else { echo '<li><font color="#636363"><span class="fas fa-crown" aria-hidden="true" title="Non-Prejoiner"></span></font></li>'; }
                                echo '<li><a href="'.$row['url'].'" target="_blank" title="Visit Trade Post"><span class="fas fa-home" aria-hidden="true"></span></a></li>
                                <li><span class="fas fa-gift" aria-hidden="true" title="Born on '.date("F d", strtotime($row['birthday'])).'"></span></li>
                                <li><a href="/cards.php?view=released&deck='.$row['collecting'].'"><span class="fas fa-puzzle-piece" aria-hidden="true" title="Collecting '.$row['collecting'].'"></span></a></li>
                            </div>
                        </div>';
                    }
                    echo "</center>";
                }
            }
        } // END EMPTY STATUS
        
        else if ($stat=="pending") {
            echo '<h1>Members : Pending</h1>
            <p>Below is the complete list of all pending members here at '.$tcgname.'. Although there is nothing much in their profile, you can click their names to view it.</p>
            <div class="statLink">
                <a href="/members.php">Active ('; $counts->numAll('user_list','Active'); echo ')</a>
                <a href="/members.php?stat=hiatus">Hiatus ('; $counts->numAll('user_list','Hiatus'); echo ')</a>
                <a href="/members.php?stat=inactive">Inactive ('; $counts->numAll('user_list','Inactive'); echo ')</a>
                <a href="/members.php?stat=retired">Retired ('; $counts->numAll('user_list','Retired'); echo ')</a>
            </div><br />';
            $general->member('Pending');
        }
        
        else if ($stat=="hiatus") {
            echo '<h1>Members : Hiatus</h1>
            <p>Below is the complete list of all members under hiatus here at '.$tcgname.'. Please take note that members who are in hiatus may or may not accept trades, so we suggest to send them a message or check their trade post first to make sure.</p>
            <div class="statLink">
                <a href="/members.php">Active ('; $counts->numAll('user_list','Active'); echo ')</a>
                <a href="/members.php?stat=pending">Pending ('; $counts->numAll('user_list','Pending'); echo ')</a>
                <a href="/members.php?stat=inactive">Inactive ('; $counts->numAll('user_list','Inactive'); echo ')</a>
                <a href="/members.php?stat=retired">Retired ('; $counts->numAll('user_list','Retired'); echo ')</a>
            </div><br />';
            $general->member('Hiatus');
        }
        
        else if ($stat=="inactive") {
            echo '<h1>Members : Inactive</h1>
            <p>Below is the complete list of all inactive members at '.$tcgname.'. These are members who are no longer active in the TCG or the TCG community in general.</p>
            <div class="statLink">
                <a href="/members.php">Active ('; $counts->numAll('user_list','Active'); echo ')</a>
                <a href="/members.php?stat=pending">Pending ('; $counts->numAll('user_list','Pending'); echo ')</a>
                <a href="/members.php?stat=hiatus">Hiatus ('; $counts->numAll('user_list','Hiatus'); echo ')</a>
                <a href="/members.php?stat=retired">Retired ('; $counts->numAll('user_list','Retired'); echo ')</a>
            </div><br />';
            $general->member('Inactive');
        }
        
        else if ($stat=="retired") {
            echo '<h1>Members : Retired</h1>
            <p>Below is the complete list of all members who quitted '.$tcgname.'. They are no longer a part of the TCG, so trading is no longer possible for them as they may have adopted their cards out.</p>
            <div class="statLink">
                <a href="/members.php">Active ('; $counts->numAll('user_list','Active'); echo ')</a>
                <a href="/members.php?stat=pending">Pending ('; $counts->numAll('user_list','Pending'); echo ')</a>
                <a href="/members.php?stat=hiatus">Hiatus ('; $counts->numAll('user_list','Hiatus'); echo ')</a>
                <a href="/members.php?stat=inactive">Inactive ('; $counts->numAll('user_list','Inactive'); echo ')</a>
            </div><br />';
            $general->member('Retired');
        }
    } // END EMPTY ID (FOR PROFILE)
    
    else {
        $query = $database->query("SELECT * FROM `user_list` WHERE name='$id'");
        $sql_msg = $database->query("SELECT * FROM `user_list` WHERE `email`='$login'");
        $sql_item = $database->query("SELECT * FROM `user_items` WHERE name='$id'");
        $log1 = $database->query("SELECT * FROM `logs_$id` WHERE `name`='$id' ORDER BY `timestamp` DESC");
        $log2 = $database->query("SELECT * FROM `trades_$id` ORDER BY `timestamp` DESC");
        $item=mysqli_fetch_assoc($sql_item);
        $msg=mysqli_fetch_assoc($sql_msg);
        
        if (empty($do)) {
            while ($row=mysqli_fetch_assoc($query)) {
                $lvl = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `level`='".$row['level']."'");
                echo '<h1>Profile : '.$row['name'].'</h1>
                <table width="100%" cellspacing="0" border="0" class="customTable">
                <tbody>
                    <tr><td colspan="3" align="center">';
                        if($row['memcard']=="Yes") { echo '<img src="'.$tcgcards.'mc-'.$row['name'].'.'.$tcgext.'" /> '; }
                        else { echo '<img src="'.$tcgcards.'mc-filler.'.$tcgext.'" /> '; }
                        if ($row['level'] < 10) {
                            $num = '0'.$row['level'];
                            echo '<img src="/images/badges/aki01-0'.$row['level'].'.png" />';
                        } else {
                            echo '<img src="/images/badges/aki01-'.$row['level'].'.png" />';
                        }
                    echo '</td></tr>
                    <tr>
                        <td width="30%"><b>Member Card:</b> mc-'.$row['name'].'</td>
                        <td width="30%"><b>Status:</b> '.$row['status'].'</td>
                        <td width="40%"><b>Rank:</b> '.$lvl['name'].' (<i>Level '.$row['level'].'</i>)</td>
                    </tr>
                    <tr>
                        <td><b>Birthday:</b> '.date("F d", strtotime($row['birthday'])).'</td>
                        <td><b>Card Count:</b> '.$item['cards'].'</td>
                        <td><b>Collecting:</b> <a href="/cards.php?view=released&deck='.$row['collecting'].'">'.$row['collecting'].'</a></td>
                    </tr>
                    <tr>
                        <td><b>Joined:</b> '.date("F d, Y", strtotime($row['regdate'])).'</td>
                        <td colspan="2"><b>Last seen:</b> '.date("F d, Y", strtotime($row['session'])).'</i> at <i>'.date("h:i A", strtotime($row['session'])).'</td>
                    </tr>
                    <tr><td colspan="3"><b>Wishlist:</b> ';
                    $sql_wish = $database->query("SELECT * FROM `user_wishlist` WHERE name='" . $row['name'] . "' ORDER BY deck ASC");
                    $count = mysqli_num_rows($sql_wish);
                    if($count != 0) {
                        $wishes = array();
                        while($row_wish = mysqli_fetch_array($sql_wish)) {
                            $wishes[] = "<a href=\"/cards.php?view=released&deck=$row_wish[deck]\">$row_wish[deck]</a></li>";
                        }
                        echo implode(', ', $wishes);
                    } else { echo '<i>This trader haven\'t added any decks on their wishlist yet.</i>'; }
                    echo '</td></tr>
                    </tbody>
                </table><br />
                <div class="statLink">
                    <a onclick="window.open(\''.$row['url'].'\',\'_blank\');"><span class="fas fa-home" aria-hidden="true"></span> Visit trade post</a> 
                    <a onclick="location.href=\'messages.php?id='.$msg['name'].'&page=create&to='.$id.'\'"><span class="fas fa-comment" aria-hidden="true"></span> Send a message</a> ';
                    if (empty($login)) { }
                    else {
                        echo '<a onclick="window.open(\'https://www.twitter.com/'.$row['twitter'].'\',\'_blank\');"><span class="fab fa-twitter" aria-hidden="true"></span> Follow on Twitter</a>
                        <a title="'.$row['discord'].'"><span class="fab fa-discord" aria-hidden="true"></span> '.$row['discord'].'</a>';
                    }
                echo '</div>
                <h2>About Me</h2>
                <p>'.$row['about'].'</p>
                
                <h2>Wanna Trade?</h2>
                <ul><li>Please allow at least <i>7 days</i> for a response to your trade request.</li>
                <li>If the form doesn\'t work, feel free to email me at <b>'.$row['email'].'</b></li>
                <li><b>Please spell out card names COMPLETELY.</b> (ie. do NOT type cardname01/02; DO type cardname01, cardname02)</li>
                <li>If you aren\'t sure what to give me, just put <b>card00</b> and I\'ll visit your profile!</li></ul>
                <form method="post" action="/members.php?id='.$id.'&do=email">
                <input type="hidden" name="id" value="'.$row['id'].'" />
                <table width="100%" cellspacing="3" class="border">
                <tr>
                    <td class="headLine" width="15%">Name:</td><td class="tableBody" width="35%"><input type="text" name="name" value="" style="width: 90%;" /></td>
                    <td class="headLine">Email:</td><td class="tableBody"><input type="text" name="email" value="" style="width: 90%;" />
                </tr>
                <tr>
                    <td class="headLine">Trade Post:</td><td class="tableBody"><input type="text" name="url" value="http://" style="width: 90%;" /></td>
                    <td class="headLine">Member Cards:</td><td class="tableBody"><input type="radio" name="member" value="yes" /> Yes &nbsp;&nbsp; <input type="radio" name="member" value="no"> No</td>
                </tr>
                <tr><td class="headLine">You Give:</td><td class="tableBody" colspan="3"><input type="text" name="giving" value="" style="width: 96%;" /></td></tr>
                <tr><td class="headLine">You Want:</td><td class="tableBody" colspan="3"><input type="text" name="for" value="" style="width: 96%;" /></td></tr>
                <tr><td class="tableBody" colspan="4" align="center"><input type="submit" name="submit" class="btn-success" value="Trade" /> <input type="reset" name="reset" class="btn-cancel" value="Reset" /></td></tr>
                </table>
                </form>
                
                <h1>Galleries</h1>
                <p>Below is the complete list of my '.$tcgname.' galleries that I have gained while being a part of the TCG and/or being active by playing. If you think I\'m missing some cards or badges, kindly let Aki know!</p>
                <ul class="tabs" data-persist="true">
                    <li><a href="#masteries">Masteries</a></li>
                    <li><a href="#specials">Special Cards</a></li>
                    <li><a href="#logs">Logs</a></li>
                </ul>
                <div class="tabcontents">
                    <div id="specials" align="center"><h2>Member Cards</h2>';
                if ($item['mcard'] == "None") { echo '<i>This member doesn\'t traded any member cards yet.</i>'; }
                else { echo '<img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $item['mcard']).'.png">'; }
                    echo '<h2>Event Cards</h2>';
                if ($item['ecard'] == "None") { echo '<i>This member haven\'t pulled any event cards yet.</i>'; }
                else { echo '<img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $item['ecard']).'.png">'; }
                    echo '</div>
                    <div id="masteries" align="center">';
                if ($item['mastered'] == "None") { echo '<i>This member haven\'t mastered any decks yet.</i>'; }
                else { echo '<img src="'.$tcgcards.''.str_replace(", ", ".png\" title=\"\"> <img src=\"$tcgcards", $item['mastered']).'.png">'; }
                    echo '</div>
                    <div id="logs" align="center">
                    <h2>Activity Logs</h2>
                    <div style="text-align: justify; padding-right: 20px; margin-top: 20px; line-height: 20px; font-size: 14px; overflow: auto; height: 300px;">';
                    $timestamp = '';
                    while($row=mysqli_fetch_assoc($log1)){
                        if ($row['timestamp'] != $timestamp){
                            echo '<br /><b>'.date('F d, Y', strtotime($row['timestamp'])).' -----</b><br/>';
                            $timestamp = $row['timestamp'];
                        }
                        echo '- <i>'.$row['title'];
                        if (empty($row['subtitle'])) {}
                        else { echo ' '.$row['subtitle']; }
                        echo ':</i> '.$row['rewards'].'<br />';
                    }
                    echo '</div><br />
                    <h2>Trade Logs</h2>
                    <div style="text-align: justify; padding-right: 20px; margin-top: 20px; line-height: 20px; font-size: 14px; overflow: auto; height: 300px;">';
                    $timestamp = '';
                    while($row=mysqli_fetch_assoc($log2)){
                        if ($row['timestamp'] != $timestamp){
                            echo '<br /><b>'.date('F d, Y', strtotime($row['timestamp'])).' -----</b><br/>';
                            $timestamp = $row['timestamp'];
                        }
                        echo '- <i>Traded '.$row['trader'];
                        echo ':</i> my '.$row['outgoing'].' for '.$row['incoming'].'<br />';
                    }
                    echo '</div></div>
                </div>';
            } // END WHILE
        }
        
        else {
            $check->Value();
            if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,6})$/",strtolower($_POST['email']))) { exit("<h1>Error</h1>\nThat e-mail address is not valid, please use another.<br /><br />"); }
            $id = $sanitize->for_db($_POST['id']);
            $query = $database->query("SELECT * FROM `user_list` WHERE id='$id'");
            $row = mysqli_fetch_assoc($query);
            
            $recipient = "$row[email]";
            $subject = "$tcgname: Trade Request";
            
            $message = "The following member has sent you a trade request: \n";
            $message .= "Name: {$_POST['name']} \n";
            $message .= "Email: {$_POST['email']} \n";
            $message .= "URL: {$_POST['url']} \n";
            $message .= "Offering: {$_POST['giving']} \n";
            $message .= "For: {$_POST['for']} \n";
            $message .= "Member Cards?: {$_POST['member']} \n";
            
            $headers = "From: {$_POST['name']} <no-reply@hakumei.org> \n";
            $headers .= "Reply-To: <{$_POST['email']}>";
            
            if (mail($recipient,$subject,$message,$headers)) {
                echo '<h1>Success!</h1>
                <p>Your trade request has been successfully sent! The member should (hopefully) respond within a week.</p>';
            } else {
                echo '<h1>Error</h1>
                <p>It looks like there was an error in processing your trade form. Why don\'t you check out their website to see if they have a trade form there?</p>';
            }
        } // END TRADE PROCESS
    } // END PROFILE VIEW
}
include($footer);
?>