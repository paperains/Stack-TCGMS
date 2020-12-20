<?php
ob_start();
session_set_cookie_params(7200,"/");
session_start();

/* CHANGE TO YOUR OWN TIMEZONE */
date_default_timezone_set('Asia/Manila');

$database = new Database;
$sanitize = new Sanitize;

$login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
$rewards = $database->num_rows("SELECT * FROM `user_rewards` WHERE `name`='".$row['name']."'");
$message = $database->num_rows("SELECT * FROM `user_mbox` WHERE `recipient`='".$row['name']."' AND `read_to`='1'");
$player = $row['name'];
?>


<!-- BEGIN HTML CODE HERE -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>TCG NAME</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link href="/theme/style.css" rel="stylesheet">
    <link href="/theme/mobile.css" rel="stylesheet">
    <link href="/theme/general.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/theme/favicon.ico" />
    <link href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,300i,400,400i,500,500i,600,600i,900,900i|Playfair+Display:400,400i,700,700i" rel="stylesheet">
    <script src="/theme/tabcontent.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>
    function insertext(text){
        var TheTextBox = document.getElementById("comment");
        TheTextBox.value = TheTextBox.value + text;
    }

    $(function () {
        $(document).scroll(function () {
            var $nav = $(".navbar-fixed-top");
            $nav.toggleClass('scrolled', $(this).scrollTop() > 180);
        });
    });
    </script>
</head>

<body onload="setBj(); stat();" onKeyPress="catchKeyCode();">
<div align="center">
    <div id="wrapper">

    <!-- BEGIN HEADERS -->
        <div id="logo">
            <a href="<?php echo $tcgurl; ?>" alt="Your Site Title">YOUR SITE TITLE</a>
        </div><!-- /#logo -->

        <div id="menu" class="navbar-fixed-top">
            <div class="pull-left">
                <a href="javascript:void(0);" class="icon" onclick="myFunction()"><i class="fas fa-bars"></i></a>
                <a href="/index.php">home</a>
                <a href="/about.php">about</a>
                <a href="/members.php?page=join">join us</a>
                <a href="/cards.php">cards</a>
                <a href="/members.php">members</a>
                <a href="/games.php">games</a>
                <a href="/site.php">site</a>
            </div>

            <!-- OPTIONAL -->
            <div class="pull-right">
                <a href="<?php echo $tcgdiscord; ?>" target="_blank"><span class="fab fa-discord" aria-hidden="true"></span></a>
                <a href="<?php echo $tcgtwitter; ?>" target="_blank"><span class="fab fa-twitter" aria-hidden="true"></span></a>
                <a href="mailto:<?php echo $tcgemail; ?>"><span class="fas fa-envelope" aria-hidden="true"></span></a>
            </div>
        </div><!-- /#menu -->
    <!-- END HEADERS -->


    <!-- BEGIN CONTENT -->
        <div id="container">
