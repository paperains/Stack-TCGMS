<?php
ob_start();
session_set_cookie_params(7200,"/");
session_start();

$database = new Database;
$sanitize = new Sanitize;

$login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
$row = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
$rewards = $database->num_rows("SELECT * FROM `user_rewards` WHERE `name`='".$row['name']."'");
$player = $row['name'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title> C O R E &nbsp;&nbsp; | &nbsp;&nbsp; an online TCG management system </title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=iso-8859-1" />
<meta name="description" content="A content management system that you can use for your online TCG." />
<meta name="author" content="Aki (c) 2020" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="Language" content="English" />
<meta name="resource-type" content="document" />
<meta name="distribution" content="Global" />
<meta name="copyright" content="https://core.reijou.net/" />
<meta name="robots" content="Index,Follow" />
<meta name="rating" content="General" />
<meta name="revisit-after" content="1 day" />
<link href="/theme/icon.png" rel="icon" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="theme/style.css" />
<link href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Nunito:400,700,400i,700i" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	// hides the menu as soon as the DOM is ready
	$('#l1').hide();
	$('#l2').hide(); 
	$('#l3').hide(); 
	$('#l4').hide(); 
	// toggles the menu on clicking the noted link  

	$('#s1').click(function() {
		$(".slideable:not(#l1)").slideUp();
	$('#l1').slideToggle(100);
	return false;
	});

	$('#s2').click(function() {
		$(".slideable:not(#l2)").slideUp();
	$('#l2').slideToggle (100);
	return false;
	});

	$('#s3').click(function() {
		$(".slideable:not(#l3)").slideUp();
	$('#l3').slideToggle (100);
	return false;
	});

	$('#s4').click(function() {
		$(".slideable:not(#l4)").slideUp();
	$('#l4').slideToggle (100);
	return false;
    });
});
</script>
</style>
</head>

<body>
	<div id="topBar">
		<div class="pull-left">
			<div class="logo"><b>Core</b>Admin</div>
		</div>

		<div class="pull-right">
            <a href="<?php echo $tcgurl; ?>"><span class="fas fa-home" aria-hidden="true"></span> <?php echo $tcgname; ?></a>
			<a href="/account.php"><span class="fas fa-user" aria-hidden="true"></span> My Account</a>
			<a href="/account.php?do=logout"><span class="fas fa-sign-out-alt" aria-hidden="true"></span> Logout</a>
		</div>
	</div><!-- /#topBar -->

    <?php include('sidebar.php'); ?>

    <div id="container">