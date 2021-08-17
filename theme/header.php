<?php
ob_start();
session_set_cookie_params(7200,"/");
session_start();

/* CHANGE TO YOUR OWN TIMEZONE */
date_default_timezone_set('Asia/Manila');

$database = new Database;
$sanitize = new Sanitize;
$settings = new Settings;
$count = new Count;

$login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$login'");
if( empty($login) ) {}
else { $player = $row['usr_name']; }
?>


<!-- BEGIN HTML CODE HERE -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo $settings->getValue( 'tcg_name' ); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link href="<?php echo $tcgurl; ?>theme/style.css" rel="stylesheet">
    <link href="<?php echo $tcgurl; ?>theme/mobile.css" rel="stylesheet">
    <link href="<?php echo $tcgurl; ?>theme/general.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo $tcgurl; ?>theme/favicon.ico" />
    <link href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,500,500i,600,600i,900,900i|Playfair+Display:400,400i,700,700i" rel="stylesheet">
    <script src="<?php echo $tcgurl; ?>theme/tabcontent.js" type="text/javascript"></script>
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
            <a href="<?php echo $tcgurl; ?>" alt="<?php echo $tcgname; ?>"><?php echo $tcgname; ?></a>
        </div><!-- /#logo -->

        <div id="menu" class="navbar-fixed-top">
            <div class="pull-left">
                <a href="javascript:void(0);" class="icon" onclick="myFunction()"><i class="fas fa-bars"></i></a>
                <a href="<?php echo $tcgurl; ?>">home</a>
                <a href="<?php echo $tcgurl; ?>about.php">about</a>
                <?php if( $settings->getValue( 'tcg_registration' ) == "0" || !empty($login) ) {}
                else { echo '<a href="'.$tcgurl.'members.php?page=join">join us</a>'; } ?>
                <a href="<?php echo $tcgurl; ?>cards.php">cards</a>
                <a href="<?php echo $tcgurl; ?>members.php">members</a>
                <a href="<?php echo $tcgurl; ?>games.php">games</a>
                <a href="<?php echo $tcgurl; ?>site.php">site</a>
                <?php if( $settings->getValue( 'tcg_forum' ) == "0" ) {}
                else { echo '<a href="'.$tcgurl.'forums">forums</a>'; }
                if( $row['usr_role'] == "1" || $row['usr_role'] == "2" || $row['usr_role'] == "3" || $row['usr_role'] == "4" || $row['usr_role'] == "5" ) {
                    echo '<a href="'.$tcgurl.'admin/" target="_blank">admin</a>';
                }
                else {} ?>
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
