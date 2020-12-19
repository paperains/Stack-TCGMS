<?php
include("admin/class.lib.php");
include($header);

$to = isset($_GET['to']) ? $_GET['to'] : null;

if (empty($login)) {
    header("Location: account.php?do=login");
}

if (empty($id)) {
    echo '<h1>Oops?</h1>
    <p>It seems like you\'re trying to access a page directly! Please go back and click the correct link.</p>';
} else {

###### CREATE A MESSAGE PAGE ######
if ($page == "create") {
    if ($stat == "sent") {
        if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
            exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
        } else {
            $id = $sanitize->for_db($_POST['id']);
            $from = $sanitize->for_db($_POST['sender']);
            $to = $sanitize->for_db($_POST['recipient']);
            $subject = $sanitize->for_db($_POST['subject']);
            $message = $_POST['message'];
            $date = date("Y-m-d H:i:s", strtotime("now"));
            
            $message = nl2br($message);

            if (!get_magic_quotes_gpc()) {
                $message = addslashes($message);
            }

            $insert = $database->query("INSERT INTO `user_mbox` (`id`,`subject`,`message`,`sender`,`recipient`,`mbox_from`,`mbox_to`,`read_from`,`read_to`,`del_from`,`del_to`,`origin`,`timestamp`) VALUES ('','$subject','$message','$from','$to','Out','In','0','1','0','0','','$date')");

            if($insert == TRUE) {
                $database->query("UPDATE `user_mbox` SET origin=LAST_INSERT_ID() WHERE id=LAST_INSERT_ID()");
                header("Location: messages.php?id=$from");
            } else {
                echo '<h1>Error</h1>';
                echo '<p>An error occurred and your message was not sent to the recipient.</p>';
            }
        }
    } else {
        echo '<h1>Create Message</h1>';
        echo '<center><form method="post" action="/messages.php?id='.$id.'&page=create&stat=sent">
        <input type="hidden" name="timestamp" id="timestamp" value="'.$date.'" />
        <input type="hidden" name="sender" id="sender" value="'.$id.'" />
         
        <table width="80%" cellpadding="0" cellspacing="3" border="0">
        <tr>
            <td width="30%" class="headLine">Subject:</td>
            <td width="70%" class="tableBody"><input type="text" name="subject" id="subject" style="width:95%;" /></td>
        </tr>
        <tr>
            <td class="headLine">Recipient:</td>
            <td class="tableBody">';
            $rec = $database->get_assoc("SELECT `name` FROM `user_list` WHERE `name`='$to'");
            if ($to = $rec['name']) { echo '<input type="text" style="width: 98%;" name="recipient" id="recipient" value="'.$rec['name'].'" readonly />'; }
            else { echo '<select name="recipient" id="recipient" style="width:98%;" />
                <option>----- Select Recipient -----</option>';
                $rec = $database->query("SELECT `name` FROM `user_list` ORDER BY `name` ASC");
                while($re = mysqli_fetch_assoc($rec)) {
                    echo '<option value="'.$re['name'].'">'.$re['name'].'</option>';
                }
            echo '</select>';
            }
            echo '</td>
        </tr>
        <tr>
            <td class="headLine">Message:</td>
            <td class="tableBody"><textarea name="message" id="message" style="width: 95%;" rows="6" /></textarea></td>
        </tr>
        <tr>
            <td colspan="2" class="tableBody" align="center"><input type="submit" name="submit" id="submit" class="btn-success" value="Send" /> <input type="reset" name="reset" id="reset" class="btn-warning" value="Reset" /></td>
        </tr>
        </table>
        </form></center>';
    }
}

###### OUTBOX PAGE ######
if ($page == "outbox") {
    $sql = $database->query("SELECT * FROM `user_mbox` WHERE sender='".$id."' AND mbox_from='Out' AND del_from='0' ORDER BY id DESC");
    $count = mysqli_num_rows($sql);
    if (empty($view)) {
        echo '<h1>My Outbox</h1>';
        echo '<p>Here are the list of the personal messages you\'ve sent to your fellow traders.</p>';
        
            echo '<table width="100%" cellpadding="0" cellspacing="3" border="0">
            <tr><td width="17%" valign="top">
                <li><a href="messages.php?id='.$id.'&page=create">Compose</a></li>';
                /** COUNT UNREAD MESSAGES **/
                $unread = $database->num_rows("SELECT * FROM `user_mbox` WHERE read_to=1 AND recipient='".$id."'");
                if ($unred == 0) { echo '<li><a href="messages.php?id='.$id.'&page=inbox">Inbox</a> (0)</li>'; }
                else { echo '<li><a href="messages.php?id='.$id.'&page=inbox">Inbox</a> ('.$unread.')</li>'; }
                /** COUNT OUTBOX **/
                $out = $database->num_rows("SELECT * FROM `user_mbox` WHERE mbox_from='Out' AND sender='".$id."'");
                if ($out == 0) { echo '<li><a href="messages.php?id='.$id.'&page=outbox">Outbox</a> (0)<br /></li>'; }
                else { echo '<li><a href="messages.php?id='.$id.'&page=outbox">Outbox</a> ('.$out.')<br /></li>'; }
                echo '<li><a href="messages.php?id='.$id.'&page=outbox&delete=true">Delete</a></li>
            </td><td width="80%" valign="top">
                <table width="100%" class="border" cellspacing="5"><tr>';
            if ($count == 0) { echo '<td width="100%" valign="top" class="tableBody"><p>You don\'t have any sent messages.</p></td>'; }
            else {
                echo '<td width="10%" class="headLine"><span class="fas fa-check" aria-hidden="true"></span></td><td width="90%" class="headLine">Message Information</td></tr>';
                while($mes = mysqli_fetch_assoc($sql)) {
                    echo '<tr>
                    <td class="tableBody" align="center"><input type="checkbox" name="del_from" id="del_from" value="1" /></td>
                    <td class="tableBody"><a href="messages.php?id='.$id.'&page=outbox&view='.$mes['id'].'">'.$mes['subject'].'</a><br />
                    Sent to: '.$mes['sentto'].' on '.date("F d, Y h:i A", strtotime($mes['timestamp'])).'</td>
                    </tr>';
                }
                echo '<tr><td colspan="2" class="tableBody"><input type="submit" name="delete" id="delete" class="btn-cancel" value="Delete" /></td></tr>';
            }
            echo '</table></td></tr>
            </table>';
        
    } else {
        $mrow = $database->get_assoc("SELECT * FROM `user_mbox` WHERE `id`='$view' AND `sender`='$id' AND `mbox_from`='Out'");
        $subject = stripslashes($mrow['subject']);
        $sentto = stripslashes($mrow['recipient']);
        $message = stripslashes($mrow['message']);
        
        $breaks = array("<br />","<br>","<br/>"); 
        $message = str_ireplace($breaks, "\n", $message);
        
        echo '<h1>My Outbox</h1>';
        echo '<p>Here are the list of your sent messages to your fellow traders.</p>';
        echo '<center><table width="100%" cellpadding="0" cellspacing="5" border="0" class="border">
        <tr>
            <td width="20%" valign="top" class="headLine">Subject:</td>
            <td width="80%" valign="middle" class="tableBody"><input type="text" name="subject" id="subject" value="'.$subject.'" /></td>
        </tr>
        <tr>
            <td valign="top"  class="headLine">Sent To:</td>
            <td valign="middle" class="tableBody"><input type="text" name="recipient" id="recipient" value="'.$sentto.'" /></td>
        </tr>
        <tr>
            <td valign="top" class="headLine">Message:</td>
            <td valign="middle" class="tableBody"><textarea name="message" id="message" rows="10" style="width:95%;" />'.$message.'</textarea></td>
        </tr>
        </table></center>';          
    }
}

###### INBOX PAGE ######
if ($page == "inbox") {
    $sql = $database->query("SELECT * FROM `user_mbox` WHERE `recipient`='$id' AND `mbox_to`='In' AND `del_to`='0' ORDER BY `id` DESC");
    $count = mysqli_num_rows($sql);
    if(empty($view)) {
        echo '<h1>My Messages</h1>';
        echo '<p>Here are the list of the personal messages that you\'ve received from your fellow traders.</p>';
        echo '<table width="100%" cellpadding="0" cellspacing="5" border="0">
            <tr><td width="20%" valign="top">
                <li><a href="messages.php?id='.$id.'&page=create">Compose</a></li>';
                /** COUNT UNREAD MESSAGES **/
                $unread = $database->num_rows("SELECT * FROM `user_mbox` WHERE `read_to`=1 AND `recipient`='$id'");
                if($unread == 0) { echo '<li><a href="messages.php?id='.$id.'&page=inbox">Inbox (0)</a></li>'; }
                else { echo '<li><a href="messages.php?id='.$id.'&page=inbox">Inbox ('.$unread.')</a></li>'; }
                /** COUNT OUTBOX **/
                $out = $database->num_rows("SELECT * FROM `user_mbox` WHERE `mbox_from`='Out' AND `sender`='$id'");
                if($out == 0) { echo '<li><a href="messages.php?id='.$id.'&page=outbox">Outbox (0)</a></li>'; }
                else { echo '<li><a href="messages.php?id='.$id.'&page=outbox">Outbox ('.$out.')</a></li>'; }
            echo '</td><td width="80%" valign="top">
                <table width="100%" class="border" cellspacing="4">
                <tr>';
            if ($count == 0) { echo '<td width="100%" valign="top" class="tableBody"><p>You don\'t have any messages.</p></td>'; }
            else {
                echo '<td width="10%" class="headLine"><span class="fas fa-check" aria-hidden="true"></span></td><td width="90%" class="headLine">Message Information</td></tr>';
                while($mes = mysqli_fetch_assoc($sql)) {
                    if($mes['read_to']=="1") {
                        echo '<tr>
                        <td class="tableBody" align="center"><input type="checkbox" name="del_to" id="del_to" value="1" /></td>
                        <td class="tableBody"><a href="messages.php?id='.$id.'&page=inbox&view='.$mes['id'].'"><b>'.$mes['subject'].'</b></a><br />
                        From: <b>'.$mes['sentby'].'</b> on <b>'.date("F d, Y h:i A", strtotime($mes['timestamp'])).'</b></td>
                        </tr>';
                    } else {
                        echo '<tr>
                        <td class="tableBody" align="center"><input type="checkbox" name="del_to" id="del_to" value="1" /></td>
                        <td class="tableBody"><a href="messages.php?id='.$id.'&page=inbox&view='.$mes['id'].'">'.$mes['subject'].'</a><br />
                        From: '.$mes['sender'].' on '.date("F d, Y h:i A", strtotime($mes['timestamp'])).'</td>
                        </tr>';
                    }
                }
                echo '<tr><td colspan="2" class="tableBody"><input type="submit" name="delete" id="delete" class="btn-cancel" value="Delete" /></td></tr>';
            }
        echo '</table></td></tr></table>';
    } else {
        if ($stat == "replied") {
            if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") {
                exit("<p>You did not press the submit button; this page should not be accessed directly.</p>");
            } else {
                $from = $sanitize->for_db($_POST['sender']);
                $to = $sanitize->for_db($_POST['recipient']);
                $subject = $sanitize->for_db($_POST['subject']);
                $date = $sanitize->for_db($_POST['timestamp']);
                $origin = $sanitize->for_db($_POST['origin']);
                $message = $_POST['message'];
                
                $message = nl2br($message);
                    
                if (!get_magic_quotes_gpc()) { $message = addslashes($message); }
                
                $insert = $database->query("INSERT INTO `user_mbox` (`subject`,`message`,`sender`,`recipient`,`mbox_from`,`mbox_to`,`read_from`,`read_to`,`del_from`,`del_to`,`origin`,`timestamp`) VALUES ('$subject','$message','$from','$to','Out','In','0','1','0','0','$origin','$date')");
                
                if ($insert == TRUE) {
                    header("Location: messages.php?id=$id&page=inbox");
                } else {
                    echo '<h1>Error</h1>';
                    echo '<p>An error occurred and your message was not sent to the recipient.</p>';
                }
            }
        } else {
            $mrow = $database->get_assoc("SELECT * FROM `user_mbox` WHERE `id`='$view' AND `recipient`='$id' AND `mbox_to`='In'");
            $mid = $mrow['id'];
            $subject = stripslashes($mrow['subject']);
            $sentby = stripslashes($mrow['sender']);
            $message = stripslashes($mrow['message']);
            $origin = stripslashes($mrow['origin']);
            $timestamp = date("F d, Y h:i:s", strtotime($mrow['timestamp']));
            $date = date("Y-m-d H:i:s", strtotime("now"));
            
            $breaks = array("<br />","<br>","<br/>");  
            $message = str_ireplace($breaks, " ", $message);
            
            $update = $database->query("UPDATE `user_mbox` SET `read_to`='0' WHERE `id`='$mid' AND `recipient`='$id' AND `mbox_to`='In'");
            
            echo '<h1>My Messages</h1>';
            echo '<p>Here are the list of the personal messages that you\'ve received from your fellow traders.</p>';
            echo '<center><form method="post" action="messages.php?id='.$id.'&page=inbox&view='.$view.'&stat=replied">
            <input type="hidden" name="origin" id="origin" value="'.$origin.'" />
            <input type="hidden" name="timestamp" id="timestamp" value="'.$date.'" />
            <input type="hidden" name="sender" id="sender" value="'.$id.'" />
            <table width="100%" cellpadding="0" cellspacing="5" border="0" class="border">
            <tr>
                <td width="20%" valign="top" class="headLine">Subject:</td>
                <td width="80%" valign="middle" class="tableBody"><input type="text" name="subject" id="subject" value="RE: '.$subject.'" style="width: 95%;" /></td>
            </tr>
            <tr>
                <td valign="top" class="headLine">Reply To:</td>
                <td valign="middle" class="tableBody"><input type="text" name="recipient" id="recipient" value="'.$sentby.'" readonly style="width: 95%;" /></td>
            </tr>
            <tr>
                <td valign="top" class="headLine">Message:</td>
                <td valign="middle" class="tableBody"><textarea name="message" id="message" rows="10" style="width:95%;">

--------------------
'.$timestamp.'
'.$message.'</textarea></td>
            </tr>
            <tr><td colspan="2" class="tableBody" align="center"><input type="submit" name="submit" id="submit" class="btn-success" value="Send" /> <input type="reset" name="reset" id="reset" class="btn-cancel" value="Reset" />
            </table></form></center>';
        }
    }
}

} // END ID CHECK
include($footer);
?>