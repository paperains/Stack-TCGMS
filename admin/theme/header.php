<?php
ob_start();
session_set_cookie_params(83600,"/");
session_start();

$database = new Database;
$sanitize = new Sanitize;
$general = new General;
$plugins = new Plugins;
$counts = new Count;

$login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_email`='$login'");
$player = $row['usr_name'];
if( $row['usr_role'] == 7 ) {
    header('Location: '.$tcgurl.'account.php');
}

date_default_timezone_set($settings->getValue('tcg_timezone'));
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title> STACK &nbsp;&nbsp; | &nbsp;&nbsp; an online TCG management system </title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=iso-8859-1" />
<meta name="description" content="A content management system that you can use for your online TCG." />
<meta name="author" content="Aki (c) 2020" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="Language" content="English" />
<meta name="resource-type" content="document" />
<meta name="distribution" content="Global" />
<meta name="copyright" content="https://stack.reijou.net/" />
<meta name="robots" content="Index,Follow" />
<meta name="rating" content="General" />
<meta name="revisit-after" content="1 day" />
<link href="/theme/icon.png" rel="icon" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="<?php echo $tcgurl; ?>admin/theme/1.0.6.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $tcgurl; ?>admin/theme/general.css" />
<link href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Nunito+Sans:400,700,400i,700i" rel="stylesheet">
<script src="<?php echo $tcgurl; ?>theme/tabcontent.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="<?php echo $tcgurl; ?>admin/theme/jquery.js"></script>
</head>

<body>
    <?php include('../admin/theme/sidebar.php'); ?>
    
    <div id="container">
        <div class="topBar">
            <div class="pull-left">
                <!-- <input type="text" placeholder="Search..." size="35" /> -->
            </div>

            <div class="pull-right">
                <a href="<?php echo $tcgurl; ?>"><span class="fas fa-home" aria-hidden="true"></span> <?php echo $tcgname; ?></a>
                <span class="spacer"></span>
                <a href="<?php echo $tcgurl; ?>account.php"><span class="fas fa-user" aria-hidden="true"></span> My Account</a>
                <span class="spacer"></span>
                <a href="<?php echo $tcgurl; ?>account.php?do=logout"><span class="fas fa-sign-out-alt" aria-hidden="true"></span> Logout</a>
            </div>
        </div>

        <div class="content">
            <div class="breadCrumbs">
                <a href="<?php echo $tcgurl; ?>admin/">Dashboard</a>
                <?php if( empty($mod) ) {}
                else {
                    if( empty($act) && !empty($sub) ) {
                        echo ' <span class="spacer">/</span> <a href="'.$tcgurl.'admin/index.php?mod='.$mod.'">'.ucfirst($mod).'</a> 
                        <span class="spacer">/</span> '.ucfirst(str_replace("-"," ",$sub));
                    } else if( empty($sub) && !empty($act) ) {
                        echo ' <span class="spacer">/</span> <a href="'.$tcgurl.'admin/index.php?mod='.$mod.'">'.ucfirst($mod).'</a> 
                        <span class="spacer">/</span> '.ucfirst(str_replace("-"," ",$act));
                    } else if( !empty($sub) && !empty($act) ) {
                        echo ' <span class="spacer">/</span> <a href="'.$tcgurl.'admin/index.php?mod='.$mod.'">'.ucfirst($mod).'</a> 
                        <span class="spacer">/</span> <a href="'.$tcgurl.'admin/index.php?mod='.$mod.'&sub='.$sub.'">'.ucfirst(str_replace("-"," ",$sub)).'</a> 
                        <span class="spacer">/</span> '.ucfirst(str_replace("-"," ",$act));
                    } else {
                        echo ' <span class="spacer">/</span> '.ucfirst($mod);
                    }
                } ?>
                
                <div style="float: right;">
                    Today is <?php echo date("l, jS F Y"); ?> at <?php echo date("g:i A"); ?>
                </div>
            </div>