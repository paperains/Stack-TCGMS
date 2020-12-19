<?php
ob_start();
session_set_cookie_params(7200,"/");
session_start();

date_default_timezone_set('Asia/Manila');

include('class.lib.php');

$database = new Database;
$sanitize = new Sanitize;

$login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
$rewards = $database->num_rows("SELECT * FROM `user_rewards` WHERE `name`='".$row['name']."'");
$message = $database->num_rows("SELECT * FROM `user_mbox` WHERE `recipient`='".$row['name']."' AND `read_to`='1'");
$player = $row['name'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title> <?php echo $tcgname; ?> </title> 
<meta name="viewport" content="width=device-width">
<meta name="author" content="<?php echo $tcgowner; ?> (c) 2020" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="Language" content="English">
<meta name="resource-type" content="document">
<meta name="distribution" content="Global">
<meta name="copyright" content="<?php echo $tcgurl; ?>">
<meta name="robots" content="Index,Follow">
<meta name="rating" content="General">
<meta name="revisit-after" content="1 day">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="icon" type="image/png" href="/theme/icon.ico" />
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i|Work+Sans:400,700" rel="stylesheet">
<style type="text/css">
body {
    font: 400 14px 'Open Sans', sans-serif;
    margin: 0;
}
input, textarea, select, submit, reset {
	font: 400 14px 'Open Sans', sans-serif;
	border: 1px solid #cccccc;
	background-color: #ffffff;
	border-radius: 5px;
	padding: 5px 10px;
	color: #808080;
	margin: 2px;
}
</style>
</head>

<body>

<?php
$database = new Database;
$sanitize = new Sanitize;
$check = new Check;

if ( $go == "send" ) {
    // $check->Value();
    $name = $sanitize->for_db($_POST['name']);
    $url = $sanitize->for_db($_POST['url']);
    $chat = $_POST['chat'];
    
    $date = date("Y-m-d H:i:s", strtotime("now"));
    $chat = nl2br($chat);
        
    if (!get_magic_quotes_gpc()) {
        $chat = addslashes($chat);
    }
    
    $insert = $database->query("INSERT INTO `tcg_chatbox` (`name`,`url`,`msg`,`timestamp`) VALUES ('$name','$url','$chat','$date')") or print("Can't insert into table tcg_chatbox.<br />" . $result . "<br />Error:" . mysqli_connect_error());
    if (!$insert) {
        echo '<center>There was an error and your chat was not recorded.</center>';
    } else {
        header("Location: admin/chat.form.php");
    }
}

else {
    // SHOW FORM
    if (empty($login)) {
        echo '<center>Please login to your account to use the chat box!</center>';
    } else {
        echo '<form method="post" action="chat.form.php?go=send">
        <input type="hidden" name="name" value="'.$row['name'].'" />
        <input type="hidden" name="url" value="'.$row['url'].'" />
        <input type="text" name="chat" placeholder="Enter message here..." style="width:82%;" /> <input type="submit" name="submit" id="submit" value=" Chat !" />
        </form>';
    }
}
?>

</body>
</html>