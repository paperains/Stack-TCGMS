<?php
#####################################
########## Add Form Action ##########
#####################################
if ($stat == "added") {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
    else {
        $check->Password();
        if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,6})$/",strtolower($_POST['email']))) { exit("<h1>Error</h1>\nThat e-mail address is not valid, please use another."); }

        $name = $sanitize->for_db($_POST['name']);
        $email = $sanitize->for_db($_POST['email']);
        $url = $sanitize->for_db($_POST['url']);
        $refer = $sanitize->for_db($_POST['refer']);
        $pass = md5($sanitize->for_db($_POST['password']));
        $password2 = $sanitize->for_db($_POST['password2']);
        $stat = $sanitize->for_db($_POST['status']);
        $col = $sanitize->for_db($_POST['collecting']);
        $mc = $sanitize->for_db($_POST['memcard']);
        $bio = $sanitize->for_db($_POST['about']);
        $month = $_POST['month'];
        $day = $_POST['day'];
        $year = $_POST['year'];
        
        $birthday = $year."-".$month."-".$day;
        $regdate = date("Y-m-d H:i:s", strtotime("now"));
        $date = date("Y-m-d", strtotime("now"));
        
        $insert = $database->query("INSERT INTO `user_list` (`name`,`email`,`url`,`refer`,`birthday`,`password`,`status`,`collecting`,`memcard`,`about`,`level`,`regdate`) VALUES ('$name','$email','$url','$refer','$birthday','$pass','Pending','$col','$mc','$bio','1','$regdate')");
        
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
            
        if ($insert == TRUE && $clogs === TRUE && $ctrades === TRUE) {
            $activity = '<span class="fas fa-user" aria-hidden="true"></span> <a href="/members.php?id='.$name.'">'.$name.'</a> became a member of '.$tcgname.'!';
            $database->query("INSERT INTO `user_items` (`name`,`mastered`,`mcard`,`ecard`) VALUES ('$name','None','None','None')");
            $database->query("INSERT INTO `trades` (`name`,`updated`) VALUES ('$name','$date')");
            echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
            echo '<p>The member was successfully added to the database.</p>';
            echo '<p>Want to <a href="index.php?action=add&page=members">add</a> another?</p>';
        } else {
            echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
            echo '<p>Sorry, there was an error and the member was not added.<br />';
            die("Error:". mysqli_connect_error());
        }
    }
} // END ADDED STATUS

else {
    echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Add a Member</h1>
    <p>Use this form to add a member to the database. <b>If the member has submitted a join form, they are already in the database!</b> Use the <a href="index.php?page=members">edit</a> form to update information for existing members.</p>
    <form method="post" action="index.php?action=add&page=members&stat=added">
    <input type="hidden" name="memcard" value="No" />
    <input type="hidden" name="about" value="Coming Soon" />
    <table width="100%" cellpadding="5" cellspacing="3" border="0">
    <tr>
        <td width="10%" class="headSub">Name:</td><td width="40%" valign="middle"><input type="text" name="name" style="width: 90%;" /></td>
        <td width="10%" class="headSub">Email:</td><td width="40%" valign="middle"><input type="text" name="email" style="width: 90%;" /></td>
    </tr>
    <tr>
        <td class="headSub">Trade Post:</td><td valign="middle"><input type="text" name="url" style="width: 90%;" /></td>
        <td class="headSub">Birthday:</td><td valign="middle"><select name="month" style="width: 45%;">';
        for($m=1; $m<=12; $m++) {
            if ($m < 10) { $_mon = "0$m"; }
            else { $_mon = $m; }
            echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
        }
        echo '</select> <select name="day">';
        for($i=1; $i<=31; $i++) {
            if ($i < 10) { $_days = "0$i"; }
            else { $_days = $i; }
            echo '<option value="'.$_days.'">'.$_days.'</option>';
        }
        echo '</select> ';
        //get the current year
        $date=date('Y');
        $start=$date-10;
        $end=$start-40;
        // set start and end year range i.e the start year
        $yearArray = range($start,$end);
        // here you displaying the dropdown list
        echo '<select name="year">';
        foreach ($yearArray as $year) {
        // this allows you to select a particular year
        $selected = ($year == $start) ? 'selected' : '';
        echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
        }
        echo '</select></td>
    </tr>
    <tr>
        <td class="headSub">Collecting:</td><td valign="middle"><select name="collecting" style="width: 95%;">';
        $sql = $database->query("SELECT * FROM `tcg_cards` WHERE status='Active' ORDER BY deckname ASC");
        while($row = mysqli_fetch_assoc($sql)) {
            $name=stripslashes($row['filename']);
            $deckname=stripslashes($row['deckname']);
            echo "<option value=\"$name\">$deckname</option>\n";
        }
        echo '</select></td>
        <td class="headSub">Referral:</td><td valign="middle"><select name="refer" style="width: 95%;" />';
        $mem = $database->query("SELECT * FROM `user_list` ORDER BY name ASC");
        while($row = mysqli_fetch_assoc($mem)) {
            $name=stripslashes($row['name']);
            echo "<option value=\"$name\">$name</option>\n";
        }
        echo '</select></td>
    </tr>
    <tr>
        <td class="headSub">Password:</td><td valign="middle"><input type="password" name="password" value="" style="width: 90%;" /></td>
        <td class="headSub">(type twice):</td><td v><input type="password" name="password2" value="" style="width: 90%;" /></td>
    </tr>
    <tr><td valign="middle" colspan="4" align="center"><input type="submit" name="submit" class="btn-success" value="Add Member" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td></tr>
    </table>';
}
?>
