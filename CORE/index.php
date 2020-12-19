<?php include('admin/class.lib.php');
include($header);

$blog_id = (isset($_GET['id']) ? $_GET['id'] : null);
$action = (isset($_GET['action']) ? $_GET['action'] : null);
$blog_postnumber = 1;

if(!isset($_GET['page'])) { $page = 1; }
else { $page = (int)$_GET['page']; }
$from = (($page * $blog_postnumber) - $blog_postnumber);
$sql = $database->query("SELECT * FROM `tcg_blog` WHERE `status`='Published' ORDER BY `id` DESC LIMIT $from, $blog_postnumber");

if (empty($blog_id)) {
    while($row = mysqli_fetch_array($sql)) {
        $mon = date("M", strtotime($row['timestamp']));
        $day = date("d", strtotime($row['timestamp']));
        $today = $row['timestamp'];
        $id = $row['id'];
        $title = stripslashes($row['title']);
        $mem = stripslashes($row['members']);
        $mas = stripslashes($row['masters']);
        $lvl = stripslashes($row['levels']);
        $refer = stripslashes($row['referrals']);
        $aff = stripslashes($row['affiliates']);
        $amount = stripslashes($row['amount']);
        $auth = stripslashes($row['author']);
        $icon = stripslashes($row['icon']);
        $entry = stripslashes($row['entry']);

    echo '<div class="date">
        <span class="month">'.$mon.'</span>
        <span class="day">'.$day.'</span>
    </div><!-- /.date -->
    <h1 class="entry-title"><a href="/index.php?id='.$id.'">'.$title.'</a></h1>
    <div class="post-info">
        <span class="fas fa-user" aria-hidden="true"></span> <a href="mailto:'.$tcgemail.'">'.$auth.'</a>
        <span class="fas fa-comment" aria-hidden="true"></span> <a href="/index.php?id='.$id.'">Leave a comment?</a>
    </div>
    
    <table cellspacing="3" width="100%">
    <tr>
        <td width="15%" valign="top" align="center" class="post-game">
            <img src="/images/icons/'.$icon.'" class="post-icon" />
            <h3>Weekly</h3>
            <a href="">Link</a>
            <a href="">Link</a>
            <a href="">Link</a>';
            
            $range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `timestamp`='$today' ORDER BY `id` DESC");
            if($range['sets'] == "Set A") {
                echo '<h3>Set A</h3>
                <a href="">Link</a>
                <a href="">Link</a>
                <a href="">Link</a>';
            } else if ($range['sets'] == "Set B") {
                echo '<h3>Set B</h3>
                <a href="">Link</a>
                <a href="">Link</a>
                <a href="">Link</a>';
            } else {
                echo '<h3>Na-da!</h3>
                <center>There are no bi-weekly games for this week!</center>';
            }

            echo '<h3>Monthly</h3>
            <a href="">Link</a>
            <a href="">Link</a>
            <a href="">Link</a>
        </td>
        <td width="2%">&nbsp;</td>
        <td width="83%" valign="top" class="post-body">
            <table width="100%" cellpadding="0" cellspacing="3" border="0" class="border">';
    if($row['members']=="None") { echo '<tr><td width="20%" class="headLine">Members:</td><td class="tableBody"><center><i>There are currently no new members.</i></center></td></tr>'; }
    else { echo '<tr><td width="20%" class="headLine">Members:</td><td class="tableBody">'.$mem.' &mdash; <i>Welcome to the harem!</i></td></tr>'; }
    if($row['masters']=="None") { }
    else { echo '<tr><td width="20%" class="headLine">Masteries:</td><td class="tableBody">'.$mas.' &mdash; <i>Keep up the good work!</i></td></tr>'; }
    if($row['levels']=="None") { }
    else { echo '<tr><td width="20%" class="headLine">Level Ups:</td><td class="tableBody">'.$lvl.' &mdash; <i>Good job, congrats!</td></tr>'; }
    if($row['referrals']=="None") { }
    else { echo '<tr><td width="20%" class="headLine">Referrals:</td><td class="tableBody">'.$refer.' &mdash; <i>Thank you for promoting Hanayaka!</i></td></tr>'; }
    if($row['affiliates']=="None") { }
    else { echo '<tr><td width="20%" class="headLine">Affiliates:</td><td class="tableBody">'.$aff.' &mdash; <i>Check out our neighbors!</i></td></tr>'; }
            echo '</table>';
    
    if($row['wish']=="None") { }
    else {
        $wish = $database->query("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='$today'");
        echo '<h2><span class="line-center">Wishing Stars</span></h2>
        <p>Kindly take a total max of <u>2 cards per deck</u> if there are no restrictions indicated.</p>
        <ol>';
        while ($rowish = mysqli_fetch_array($wish)) {
            $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
            for ($i=0; $i<=$c; $i++) { $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='".$rowish['color']."'"); }
            if ($rowish['type'] == 1) {
                echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for choice cards spelling <b>'.$rowish['word'].'</b>!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; <a href="/wishes.php?id='.$rowish['id'].'"><span class="fas fa-external-link-alt" aria-hidden="true"></span></a></li>';
            }
            else if ($rowish['type'] == 2) {
                echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for <b>'.$rowish['amount'].'</b> choice pack from any deck!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; <a href="/wishes.php?id='.$rowish['id'].'"><span class="fas fa-external-link-alt" aria-hidden="true"></span></a></li>';
            }
            else if ($rowish['type'] == 3) {
                echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for <b>'.$rowish['amount'].'</b> random pack from any deck!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; <a href="/wishes.php?id='.$rowish['id'].'"><span class="fas fa-external-link-alt" aria-hidden="true"></span></a></li>';
            }
            else if ($rowish['type'] == 4) {
                echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for 3 choice cards from any <b>'.$cat['name'].'</b> decks!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; <a href="/wishes.php?id='.$rowish['id'].'"><span class="fas fa-external-link-alt" aria-hidden="true"></span></a></li>';
            }
            else if ($rowish['type'] == 5) {
                echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for <b>double deck release</b>!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; And everything multiplies~</li>';
            }
            else {
                echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for <b>double game rewards</b> from the '.$rowish['set'].' set!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; No need to refresh the rewards page!</li>';
            }
        }
        echo '</ol>';
    }
    
    if($row['decks']=="None") { }
    else {
        echo '<h2><span class="line-center">New Releases</span></h2>
        <center>';
            $decks = $row['decks'];
            $array = explode(', ',$decks);
            $array_count = count($array);
            for($i=0; $i<=($array_count -1); $i++) {
                $digits = rand(01,20);
                if ($digits < 10) { $_digits = "0$digits"; }
                else { $_digits = $digits; }
                echo "<a href=\"cards.php?view=released&deck=$array[$i]\"><img src=\"$tcgcards";
                echo "$array[$i]$_digits";
                echo ".png\" border=\"0\" /></a>";
            }
        echo '<br /><a href="/releases.php?date='.$row['timestamp'].'">Click here</a>  for your deck release pulls.</center>';
    }
    
    if($entry=="") { }
    else { echo $entry; }
    } // END WHILE
    
    echo '</td>
    </tr>
    </table>';
    
    // SHOW BLOG PAGINATION
    $total_results = mysqli_fetch_array($database->query("SELECT COUNT(*) as num FROM `tcg_blog`"));
    if (isset($_GET['page']) && $_GET['page']!="") { $page_no = $_GET['page']; }
    else { $page_no = 1; }

    $total_records_per_page = 1;

    $offset = ($page_no-1) * $total_records_per_page;
    $previous_page = $page_no - 1;
    $next_page = $page_no + 1;
    $adjacents = "2";

    $result_count = $database->query("SELECT COUNT(*) AS total_records FROM `tcg_blog`");
    $total_records = mysqli_fetch_array($result_count);
    $total_records = $total_records['total_records'];
    $total_no_of_pages = ceil($total_records / $total_records_per_page);
    $second_last = $total_no_of_pages - 1; // total pages minus 1

    echo '<div align="center">';
    echo '<small><strong>Page '.$page_no.' of '.$total_no_of_pages.'</strong></small><br />';
    echo '<ul class="pagination">';
    echo '<li '; if($page_no <= 1) { echo 'class="disabled"'; } echo '>
        <a '; if($page_no > 1) { echo 'href="?page='.$previous_page.'"'; } echo '>Previous</a>
        </li>';

        if ($total_no_of_pages <= 5){       
            for ($counter = 1; $counter <= $total_no_of_pages; $counter++){
                if ($counter == $page_no) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                else { echo '<li><a href="?page='.$counter.'">'.$counter.'</a></li>'; }
            }
        }
        elseif($total_no_of_pages > 5){
            if($page_no <= 4) {         
                for ($counter = 1; $counter < 6; $counter++) {
                    if ($counter == $page_no) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                    else { echo '<li><a href="?page='.$counter.'">'.$counter.'</a></li>'; }
                }
                echo '<li><a>...</a></li>';
                echo '<li><a href="?page='.$second_last.'">'.$second_last.'</a></li>';
                echo '<li><a href="?page='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';
            }
            elseif($page_no > 4 && $page_no < $total_no_of_pages - 4) {
                echo '<li><a href="?page=1">1</a></li>';
                echo '<li><a href="?page=2">2</a></li>';
                echo '<li><a>...</a></li>';
                for ($counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++) {
                    if ($counter == $page_no) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                    else { echo '<li><a href="?page='.$counter.'">'.$counter.'</a></li>'; }                  
            }
            echo '<li><a>...</a></li>';
            echo '<li><a href="?page='.$second_last.'">'.$second_last.'</a></li>';
            echo '<li><a href="?page='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';      
            }
            else {
                echo '<li><a href="?page=1">1</a></li>';
                echo '<li><a href="?page=2">2</a></li>';
                echo '<li><a>...</a></li>';
                for ($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++) {
                    if ($counter == $page_no) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                    else { echo '<li><a href="?page='.$counter.'">'.$counter.'</a></li>'; }
                }
            }
        }
        echo '<li '; if($page_no >= $total_no_of_pages) { echo 'class="disabled"'; } echo '>
        <a '; if($page_no < $total_no_of_pages) { echo 'href="?page='.$next_page; } echo '">Next</a>
        </li>';
        if($page_no < $total_no_of_pages) { echo '<li><a href="?page='.$total_no_of_pages.'">Last &rsaquo;&rsaquo;</a></li>'; }
    echo '<li><a href="/archive.php">Archive</a></li></ul>';
    echo '</div>';
} // END EMPTY BLOG

else {
    if ($action=="add") {
        if (empty($_POST['comment'])) { die("You didn't post anything on the comment field! Please make sure to fill up the form before hitting submit."); }
        $id = htmlspecialchars(strip_tags($_POST['id']));
        $name = htmlspecialchars(strip_tags($_POST['name']));
        $url = htmlspecialchars(strip_tags($_POST['url']));
        $comment = $_POST['comment'];
        $comment = nl2br($comment);
        if (!get_magic_quotes_gpc()) { $comment = addslashes($comment); }
        
        $comm_info = $database->get_assoc("SELECT * FROM `tcg_blog` WHERE `id`='$id'");
        $comm_time = date("ymd", strtotime("now"));
        $strip = str_replace("'","\'",$comm_info['comm']);
        $oldcomm = $strip;
        $log = '<li class="comment"><div class="commTitle"><span class="fas fa-calendar-alt" aria-hidden="true"></span> '.$comm_time.' <a href="'.$url.'" target="_blank">'.$name.'</a></div><div class="tableBody">'.$comment.'</div></li>';
        $newcomm = $log."\n".$oldcomm;
        $insert = $database->query("UPDATE `tcg_blog` SET `comm`='$newcomm' WHERE `id`='$id'");
        if ($insert == TRUE) { header("Location: index.php?id=" . $id); }
        else { echo '<h1>Error!</h1><p>There was an error while processing your form and your comment was not added to the database.</p>'; }
    } // END ADD PROCESS
    else {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid ID specified."); }
        $id = (int)$_GET['id'];
        $sql = $database->query("SELECT * FROM `tcg_blog` WHERE `id`='$id' AND `status`='Published' LIMIT 1") or print ("Can't select entry from table tcg_blog.<br />" . $sql1 . "<br />" . mysqli_connect_error());
        while($row = mysqli_fetch_array($sql)) {
            $mon = date("M", strtotime($row['timestamp']));
            $day = date("d", strtotime($row['timestamp']));
            $id = $row['id'];
            $today = $row['timestamp'];
            $title = stripslashes($row['title']);
            $mem = stripslashes($row['members']);
            $mas = stripslashes($row['masters']);
            $lvl = stripslashes($row['levels']);
            $refer = stripslashes($row['referrals']);
            $aff = stripslashes($row['affiliates']);
            $amount = stripslashes($row['amount']);
            $auth = stripslashes($row['author']);
            $icon = stripslashes($row['icon']);
            $entry = stripslashes($row['entry']);

            echo '<div class="date">
                <span class="month">'.$mon.'</span>
                <span class="day">'.$day.'</span>
            </div><!-- /.date -->
            <h1 class="entry-title"><a href="/index.php?id='.$id.'">'.$title.'</a></h1>
            <div class="post-info">
                <span class="fas fa-user" aria-hidden="true"></span> <a href="mailto:'.$tcgemail.'">'.$auth.'</a>
                <span class="fas fa-comment" aria-hidden="true"></span> <a href="/index.php?id='.$id.'">Leave a comment?</a>
            </div>
            
            <table cellspacing="3" width="100%">
            <tr>
                <td width="15%" valign="top" align="center" class="post-game">
                    <img src="/images/icons/'.$icon.'" class="post-icon" />
                    <h3>Weekly</h3>
                    <a href="">Link</a>
                    <a href="">Link</a>
                    <a href="">Link</a>';
            
                    $range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `timestamp`='$today' ORDER BY `id` DESC");
                    if($range['sets'] == "Set A") {
                        echo '<h3>Set A</h3>
                        <a href="">Link</a>
                        <a href="">Link</a>
                        <a href="">Link</a>';
                    } else if ($range['sets'] == "Set B") {
                        echo '<h3>Set B</h3>
                        <a href="">Link</a>
                        <a href="">Link</a>
                        <a href="">Link</a>';
                    } else {
                        echo '<h3>Na-da!</h3>
                        <center>There are no bi-weekly games for this week!</center>';
                    }

                    echo '<h3>Monthly</h3>
                    <a href="">Link</a>
                    <a href="">Link</a>
                    <a href="">Link</a>
                </td>
                <td width="2%">&nbsp;</td>
                <td width="83%" valign="top" class="post-body">
                    <table width="100%" cellpadding="0" cellspacing="3" border="0" class="border">';
            if($row['members']=="None") { echo '<tr><td width="20%" class="headLine">Members:</td><td class="tableBody"><center><i>There are currently no new members.</i></center></td></tr>'; }
            else { echo '<tr><td width="20%" class="headLine">Members:</td><td class="tableBody">'.$mem.' &mdash; <i>Welcome to the harem!</i></td></tr>'; }
            if($row['masters']=="None") { }
            else { echo '<tr><td width="20%" class="headLine">Masteries:</td><td class="tableBody">'.$mas.' &mdash; <i>Keep up the good work!</i></td></tr>'; }
            if($row['levels']=="None") { }
            else { echo '<tr><td width="20%" class="headLine">Level Ups:</td><td class="tableBody">'.$lvl.' &mdash; <i>Good job, congrats!</i></td></tr>'; }
            if($row['referrals']=="None") { }
            else { echo '<tr><td width="20%" class="headLine">Referrals:</td><td class="tableBody">'.$refer.' &mdash; <i>Thank you for promoting Hanayaka!</i></td></tr>'; }
            if($row['affiliates']=="None") { }
            else { echo '<tr><td width="20%" class="headLine">Affiliates:</td><td class="tableBody">'.$aff.' &mdash; <i>Check out our neighbors!</i></td></tr>'; }
                    echo '</table>';
            
            if($row['wish']=="None") { }
            else {
                $wish = $database->query("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='$today'");
                echo '<h2><span class="line-center">Wishing Stars</span></h2>
                <p>Kindly take a total max of <u>2 cards per deck</u> if there are no restrictions indicated.</p>
                <ol>';
                while ($rowish = mysqli_fetch_array($wish)) {
                    $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                    for ($i=0; $i<=$c; $i++) { $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='".$rowish['color']."'"); }
                    if ($rowish['type'] == 1) {
                        echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for choice cards spelling <b>'.$rowish['word'].'</b>!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; <a href="/wishes.php?id='.$rowish['id'].'"><span class="fas fa-external-link-alt" aria-hidden="true"></span></a></li>';
                    }
                    else if ($rowish['type'] == 2) {
                        echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for <b>'.$rowish['amount'].'</b> choice pack from any deck!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; <a href="/wishes.php?id='.$rowish['id'].'"><span class="fas fa-external-link-alt" aria-hidden="true"></span></a></li>';
                    }
                    else if ($rowish['type'] == 3) {
                        echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for <b>'.$rowish['amount'].'</b> random pack from any deck!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; <a href="/wishes.php?id='.$rowish['id'].'"><span class="fas fa-external-link-alt" aria-hidden="true"></span></a></li>';
                    }
                    else if ($rowish['type'] == 4) {
                        echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for 3 choice cards from any <b>'.$cat['name'].'</b> decks!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; <a href="/wishes.php?id='.$rowish['id'].'"><span class="fas fa-external-link-alt" aria-hidden="true"></span></a></li>';
                    }
                    else if ($rowish['type'] == 5) {
                        echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for <b>double deck release</b>!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; And everything multiplies~</li>';
                    }
                    else {
                        echo '<li><b>'.$rowish['name'].'</b> &mdash; <span class="fas fa-quote-left" aria-hidden="true" style="margin-right: 20px;"></span>I wish for <b>double game rewards</b> from the '.$rowish['set'].' set!<span class="fas fa-quote-right" aria-hidden="true" style="margin-left: 20px;"></span> &nbsp;&nbsp; No need to refresh the rewards page!</li>';
                    }
                }
                echo '</ol>';
            }
            
            if($row['decks']=="None") { }
            else {
                echo '<h2><span class="line-center">New Releases</span></h2>
                <center>';
                    $decks = $row['decks'];
                    $array = explode(', ',$decks);
                    $array_count = count($array);
                    for($i=0; $i<=($array_count -1); $i++) {
                        $digits = rand(01,20);
                        if ($digits < 10) { $_digits = "0$digits"; }
                        else { $_digits = $digits; }
                        echo "<a href=\"cards.php?view=released&deck=$array[$i]\"><img src=\"$tcgcards";
                        echo "$array[$i]$_digits";
                        echo ".png\" border=\"0\" /></a>\n";
                    }
                echo '<br /><a href="/releases.php?date='.$row['timestamp'].'">Click here</a>  for your deck release pulls.</center>';
            }
            
            if($entry=="") { }
            else { echo $entry; }
        } // END WHILE
        
        echo '</td>
            </tr>
            </table>';
        
        // LOGIN CHECK
        if (empty($login)) {
            echo '<h2>Login</h2>';
            echo '<p>Kindly please login to your account in able to post a comment on our updates. <b>This is only for current members.</b></p>';
        } else {
            $sqlmem = $database->query("SELECT * FROM `user_list` WHERE `email`='$login'") or print ("Can't select member from table user_list.<br />" . $sqlmem . "<br />" . mysqli_connect_error());
            $rowmem = mysqli_fetch_assoc($sqlmem);
            echo '<h2>Add a comment</h2>
            <form method="post" action="/index.php?id='.$id.'&action=add">
            <table width="100%">
                <input type="hidden" name="id" id="id" value="'.$id.'" />
                <input type="hidden" name="name" id="name" value="'.$rowmem['name'].'" />
                <input type="hidden" name="url" id="url" value="'.$rowmem['url'].'" />
                <tr>
                    <td valign="top" class="headLine"><label for="comment">Comment:</label></td>
                    <td class="tableBody"><textarea style="width: 95%" rows="5" name="comment" id="comment"></textarea></td>
                </tr>
                <tr>
                    <td class="tableBody" colspan="2" align="center"><input type="submit" name="submit_comment" id="submit_comment" class="button" value="   Add Comment   " /></td>
                </tr>
            </table>
            </form>
            <h2>Comments</h2>';
        } // END COMMENT FORM
        
        // SHOW COMMENTS
        $comm_sql = $database->query("SELECT * FROM `tcg_blog` WHERE `id`='$id'");
        $comm = mysqli_fetch_assoc($comm_sql);
        echo $comm['comm'];
    } // END SHOW FULL POST
}

include($footer);
?>
