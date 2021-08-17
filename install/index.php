<?php
ob_start();
/*
 * Setup Stack CMS
 * Make sure you have changed the SQL values
 * from class.lib.php before running this.
 */

// Establish database connection
require_once('/home/path/to/TCG/root/admin/class.lib.php');
?>
<html>
<head><title>STACK : Installation</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=iso-8859-1" />
<meta name="description" content="A content management system that you can use for your online TCG." />
<meta name="author" content="Aki (c) 2020" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="Language" content="English" />
<meta name="resource-type" content="document" />
<meta name="distribution" content="Global" />
<meta name="copyright" content="https://design-with.in/resources/stack/" />
<meta name="robots" content="Index,Follow" />
<meta name="rating" content="General" />
<meta name="revisit-after" content="1 day" />
<link href="/theme/icon.png" rel="icon" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="style.css" />
<link href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Nunito+Sans:400,700,400i,700i" rel="stylesheet">
</head>

<body>
<div align="center">
	<img src="../admin/theme/images/logo.png" height="80" />
	<div class="box">

<?php
if( !$database->connect() ) {
	echo 'Error: could not connect to MySQL.';
	exit;
}



/********************************************************
 * Action:			Install STACK
 * Description:		Show main page setting up an admin account
 */
if( empty($act) ) {
	if( isset($_POST['setup']) ) {
		// Create table for member roles
		$user_roles = "CREATE TABLE IF NOT EXISTS `user_role` (
		`role_id` int(2) NOT NULL AUTO_INCREMENT,
		`role_title` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (`role_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$roles = $database->query($user_roles);

		// Insert values for member roles
		$role_insert = "INSERT INTO `user_role` (`role_id`, `role_title`)
		VALUES
		(1,'Admin'),
		(2,'Co-Admin'),
		(3,'Editor'),
		(4,'Moderator'),
		(5,'Deck Maker'),
		(6,'Game Master'),
		(7,'Member')
		ON DUPLICATE KEY UPDATE
		role_title = VALUES(role_title);";
		$get_role = $database->query($role_insert);

		// Create table for user items
		$user_items = "CREATE TABLE IF NOT EXISTS `user_items` (
		`itm_id` int(11) NOT NULL AUTO_INCREMENT,
		`itm_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`itm_badge` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
		`itm_mcard` text COLLATE utf8_unicode_ci NOT NULL,
		`itm_ecard` text COLLATE utf8_unicode_ci NOT NULL,
		`itm_masteries` text COLLATE utf8_unicode_ci NOT NULL,
		`itm_milestone` text COLLATE utf8_unicode_ci NOT NULL,
		`itm_cards` int(11) NOT NULL DEFAULT '0',
		`itm_currency` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (`itm_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$item = $database->query($user_items);

		// Create table for trade records
		$user_trades = "CREATE TABLE IF NOT EXISTS `user_trades_rec` (
		`trd_id` int(11) NOT NULL AUTO_INCREMENT,
		`trd_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`trd_points` int(5) NOT NULL DEFAULT '0',
		`trd_turnins` int(5) NOT NULL DEFAULT '0',
		`trd_redeems` int(11) NOT NULL DEFAULT '0',
		`trd_date` date NOT NULL,
		PRIMARY KEY (`trd_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$trade = $database->query($user_trades);

		// Create table for members
		$user_list = "CREATE TABLE IF NOT EXISTS `user_list` (
		`usr_id` int(11) NOT NULL AUTO_INCREMENT,
		`usr_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
		`usr_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		`usr_url` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
		`usr_pass` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
		`usr_bday` date NOT NULL,
		`usr_level` int(2) NOT NULL DEFAULT '1',
		`usr_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Pending',
		`usr_role` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '7',
		`usr_deck` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		`usr_pre` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
		`usr_mcard` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
		`usr_refer` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'None',
		`usr_bio` text COLLATE utf8_unicode_ci NOT NULL,
		`usr_twitter` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N/A',
		`usr_discord` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N/A',
		`usr_rand_trade` int(1) NOT NULL DEFAULT '1',
		`usr_auto_trade` int(1) NOT NULL DEFAULT '1',
		`usr_sess` datetime NOT NULL,
		`usr_log` datetime NOT NULL,
		`usr_reg` date NOT NULL,
		PRIMARY KEY (`usr_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$user = $database->query($user_list);
		
		// Create table for timezones
		$timezone = "CREATE TABLE IF NOT EXISTS `tcg_timezones` (
		`tzone_id` int(11) NOT NULL AUTO_INCREMENT,
        `tzone_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
        `tzone_region` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`tzone_id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $tzone = $database->query($timezone);
        
        // Insert values for timezone
        $tzone_insert = "INSERT INTO `tcg_timezones` (`tzone_id`, `tzone_name`, `tzone_region`) VALUES
        (1, '(GMT-11:00) Midway Island', 'Pacific/Midway'),
        (2, '(GMT-11:00) Samoa', 'Pacific/Samoa'),
        (3, '(GMT-10:00) Hawaii', 'Pacific/Honolulu'),
        (4, '(GMT-09:00) Alaska', 'America/Anchorage'),
        (5, '(GMT-08:00) Pacific Time (US &amp; Canada)', 'America/Los Angeles'),
        (6, '(GMT-08:00) Tijuana', 'America/Tijuana'),
        (7, '(GMT-07:00) Chihuahua', 'America/Chihuahua'),
        (8, '(GMT-07:00) Mazatlan', 'America/Mazatlan'),
        (9, '(GMT-07:00) Mountain Time (US &amp; Canada)', 'America/Denver'),
        (10, '(GMT-06:00) Central America', 'America/Managua'),
        (11, '(GMT-06:00) Central Time (US &amp; Canada)', 'America/Chicago'),
        (12, '(GMT-06:00) Mexico City/Guadalajara', 'America/Mexico City'),
        (13, '(GMT-06:00) Monterrey', 'America/Monterrey'),
        (14, '(GMT-05:00) Bogota/Quito', 'America/Bogota'),
        (15, '(GMT-05:00) Eastern Time (US &amp; Canada)', 'America/New York'),
        (16, '(GMT-05:00) Lima', 'America/Lima'),
        (17, '(GMT-04:00) Atlantic Time (Canada)', 'Canada/Atlantic'),
        (18, '(GMT-04:30) Caracas', 'America/Caracas'),
        (19, '(GMT-04:00) La Paz', 'America/La Paz'),
        (20, '(GMT-04:00) Santiago', 'America/Santiago'),
        (21, '(GMT-03:30) Newfoundland', 'America/St. Johns'),
        (22, '(GMT-03:00) Brasilia', 'America/Sao Paulo'),
        (23, '(GMT-03:00) Buenos Aires/Georgetown', 'America/Argentina/Buenos Aires'),
        (24, '(GMT-03:00) Greenland', 'America/Godthab'),
        (25, '(GMT-02:00) Mid-Atlantic', 'America/Noronha'),
        (26, '(GMT-01:00) Azores', 'Atlantic/Azores'),
        (27, '(GMT-01:00) Cape Verde Is.', 'Atlantic/Cape Verde'),
        (28, '(GMT+00:00) Casablanca', 'Africa/Casablanca'),
        (29, '(GMT+00:00) London/Edinburgh', 'Europe/London'),
        (30, '(GMT+00:00) Dublin', 'Europe/Dublin'),
        (31, '(GMT+00:00) Lisbon', 'Europe/Lisbon'),
        (32, '(GMT+00:00) Monrovia', 'Africa/Monrovia'),
        (33, '(GMT+00:00) UTC', 'UTC'),
        (34, '(GMT+01:00) Amsterdam', 'Europe/Amsterdam'),
        (35, '(GMT+01:00) Belgrade', 'Europe/Belgrade'),
        (36, '(GMT+01:00) Berlin/Bern', 'Europe/Berlin'),
        (37, '(GMT+01:00) Bratislava', 'Europe/Bratislava'),
        (38, '(GMT+01:00) Brussels', 'Europe/Brussels'),
        (39, '(GMT+01:00) Budapest', 'Europe/Budapest'),
        (40, '(GMT+01:00) Copenhagen', 'Europe/Copenhagen'),
        (41, '(GMT+01:00) Ljubljana', 'Europe/Ljubljana'),
        (42, '(GMT+01:00) Madrid', 'Europe/Madrid'),
        (43, '(GMT+01:00) Paris', 'Europe/Paris'),
        (44, '(GMT+01:00) Prague', 'Europe/Prague'),
        (45, '(GMT+01:00) Rome', 'Europe/Rome'),
        (46, '(GMT+01:00) Sarajevo', 'Europe/Sarajevo'),
        (47, '(GMT+01:00) Skopje', 'Europe/Skopje'),
        (48, '(GMT+01:00) Stockholm', 'Europe/Stockholm'),
        (49, '(GMT+01:00) Vienna', 'Europe/Vienna'),
        (50, '(GMT+01:00) Warsaw', 'Europe/Warsaw'),
        (51, '(GMT+01:00) West Central Africa', 'Africa/Lagos'),
        (52, '(GMT+01:00) Zagreb', 'Europe/Zagreb'),
        (53, '(GMT+02:00) Athens', 'Europe/Athens'),
        (54, '(GMT+02:00) Bucharest', 'Europe/Bucharest'),
        (55, '(GMT+02:00) Cairo', 'Africa/Cairo'),
        (56, '(GMT+02:00) Harare', 'Africa/Harare'),
        (57, '(GMT+02:00) Helsinki/Kyiv', 'Europe/Helsinki'),
        (58, '(GMT+02:00) Istanbul', 'Europe/Istanbul'),
        (59, '(GMT+02:00) Jerusalem', 'Asia/Jerusalem'),
        (60, '(GMT+02:00) Pretoria', 'Africa/Johannesburg'),
        (61, '(GMT+02:00) Riga', 'Europe/Riga'),
        (62, '(GMT+02:00) Sofia', 'Europe/Sofia'),
        (63, '(GMT+02:00) Tallinn', 'Europe/Tallinn'),
        (64, '(GMT+02:00) Vilnius', 'Europe/Vilnius'),
        (65, '(GMT+03:00) Baghdad', 'Asia/Baghdad'),
        (66, '(GMT+03:00) Kuwait', 'Asia/Kuwait'),
        (67, '(GMT+03:00) Minsk', 'Europe/Minsk'),
        (68, '(GMT+03:00) Nairobi', 'Africa/Nairobi'),
        (69, '(GMT+03:00) Riyadh', 'Asia/Riyadh'),
        (70, '(GMT+03:00) Volgograd', 'Europe/Volgograd'),
        (71, '(GMT+03:30) Tehran', 'Asia/Tehran'),
        (72, '(GMT+04:00) Abu Dhabi', 'Asia/Muscat'),
        (73, '(GMT+04:00) Baku', 'Asia/Baku'),
        (74, '(GMT+04:00) Moscow/St. Petersburg', 'Europe/Moscow'),
        (75, '(GMT+04:00) Muscat', 'Asia/Muscat'),
        (76, '(GMT+04:00) Tbilisi', 'Asia/Tbilisi'),
        (77, '(GMT+04:00) Yerevan', 'Asia/Yerevan'),
        (78, '(GMT+04:30) Kabul', 'Asia/Kabul'),
        (79, '(GMT+05:00) Karachi/Islamabad', 'Asia/Karachi'),
        (80, '(GMT+05:00) Tashkent', 'Asia/Tashkent'),
        (81, '(GMT+05:30) New Delhi/Mumbai', 'Asia/Calcutta'),
        (82, '(GMT+05:30) Kolkata', 'Asia/Kolkata'),
        (83, '(GMT+05:45) Kathmandu', 'Asia/Katmandu'),
        (84, '(GMT+06:00) Almaty', 'Asia/Almaty'),
        (85, '(GMT+06:00) Dhaka/Astana', 'Asia/Dhaka'),
        (86, '(GMT+06:00) Ekaterinburg', 'Asia/Yekaterinburg'),
        (87, '(GMT+06:30) Rangoon', 'Asia/Rangoon'),
        (88, '(GMT+07:00) Bangkok', 'Asia/Bangkok'),
        (89, '(GMT+07:00) Jakarta', 'Asia/Jakarta'),
        (90, '(GMT+07:00) Novosibirsk', 'Asia/Novosibirsk'),
        (91, '(GMT+08:00) Beijing/Hong Kong', 'Asia/Hong Kong'),
        (92, '(GMT+08:00) Chongqing', 'Asia/Chongqing'),
        (93, '(GMT+08:00) Krasnoyarsk', 'Asia/Krasnoyarsk'),
        (94, '(GMT+08:00) Kuala Lumpur', 'Asia/Kuala Lumpur'),
        (95, '(GMT+08:00) Manila', 'Asia/Manila'),
        (96, '(GMT+08:00) Perth', 'Australia/Perth'),
        (97, '(GMT+08:00) Singapore', 'Asia/Singapore'),
        (98, '(GMT+08:00) Taipei', 'Asia/Taipei'),
        (99, '(GMT+08:00) Ulaan Bataar', 'Asia/Ulan Bator'),
        (100, '(GMT+08:00) Urumqi', 'Asia/Urumqi'),
        (101, '(GMT+09:00) Irkutsk', 'Asia/Irkutsk'),
        (102, '(GMT+09:00) Tokyo', 'Asia/Tokyo'),
        (103, '(GMT+09:00) Seoul', 'Asia/Seoul'),
        (104, '(GMT+09:30) Adelaide', 'Australia/Adelaide'),
        (105, '(GMT+09:30) Darwin', 'Australia/Darwin'),
        (106, '(GMT+10:00) Brisbane', 'Australia/Brisbane'),
        (107, '(GMT+10:00) Canberra', 'Australia/Canberra'),
        (108, '(GMT+10:00) Guam', 'Pacific/Guam'),
        (109, '(GMT+10:00) Hobart', 'Australia/Hobart'),
        (110, '(GMT+10:00) Melbourne', 'Australia/Melbourne'),
        (111, '(GMT+10:00) Port Moresby', 'Pacific/Port Moresby'),
        (112, '(GMT+10:00) Sydney', 'Australia/Sydney'),
        (113, '(GMT+10:00) Yakutsk', 'Asia/Yakutsk'),
        (114, '(GMT+11:00) Vladivostok', 'Asia/Vladivostok'),
        (115, '(GMT+12:00) Auckland', 'Pacific/Auckland'),
        (116, '(GMT+12:00) Fiji/Marshall Is.', 'Pacific/Fiji'),
        (117, '(GMT+12:00) International Date Line West', 'Pacific/Kwajalein'),
        (118, '(GMT+12:00) Kamchatka', 'Asia/Kamchatka'),
        (119, '(GMT+12:00) Magadan', 'Asia/Magadan'),
        (120, '(GMT+12:00) Wellington', 'Pacific/Auckland'),
        (121, '(GMT+13:00) Nuku\'alofa', 'Pacific/Tongatapu');";
        $get_tzone = $database->query($tzone_insert);

		if( !$roles || !$get_role || !$item || !$trade || !$user || !$tzone || !$get_tzone ) {
			$error[] = "Sorry, there was an error and the admin account was not set up.";
		} else {
			$check->Member();
			$name = $sanitize->for_db($_POST['name']);
			$email = $sanitize->for_db($_POST['email']);
			$url = $sanitize->for_db($_POST['url']);
			$pass = md5($sanitize->for_db($_POST['password']));
			$pass2 = $sanitize->for_db($_POST['password2']);
			$deck = $sanitize->for_db($_POST['collecting']);
			$role = $sanitize->for_db($_POST['role']);
			$date = date('Y-m-d', strtotime("now"));

			$insert = $database->query("INSERT INTO `user_list` (`usr_name`,`usr_email`,`usr_url`,`usr_pass`,`usr_status`,`usr_role`,`usr_deck`,`usr_reg`) VALUES ('$name','$email','$url','$pass','Active','$role','$deck','$date')");

			$in_items = $database->query("INSERT INTO `user_items` (`itm_name`,`itm_mcard`,`itm_ecard`,`itm_masteries`,`itm_milestone`) VALUES ('$name','None','None','None','None')");

			$in_trade = $database->query("INSERT INTO `user_trades_rec` (`trd_name`,`trd_date`) VALUES ('$name','$date')");

			if( $insert === TRUE && $in_items === TRUE && $in_trade === TRUE ) {
				header("Location: index.php?action=tcg-setup");
                exit;
			}
		}
	}

	echo '<h1>Welcome</h1>
	<p>Hey there, cap\'n! Welcome to the installation process of STACK; an online trading card game management system! Just fill in the information below and you\'ll be on your way to setting up your new TCG.</p>

	<p>Please provide the following information for your admin account; do keep in mind that this will be your TCG information as a player. Don\'t worry, you can always change these settings later.</p>
		
	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) {
			echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />';
		}
	}
	echo '</center>

	<form method="post" action="index.php">
	<input type="hidden" name="role" value="1" />
	<input type="hidden" name="collecting" value="none" />
	<table width="100%" cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td width="20%" valign="top"><b>Your Name</b></td>
		<td width="2%">&nbsp;</td>
		<td width="78%" valign="top"><input type="text" name="name" placeholder="Jane Doe" size="30" /></td>
	</tr>
	<tr>
		<td valign="top"><b>Password</b></td>
		<td>&nbsp;</td>
		<td valign="top">
			<input type="password" name="password" size="30" /><br />
			<input type="password" name="password2" size="30" /><br />
			<small><b>Important:</b> You will need this password to log in. Make sure to type twice for verification.</small>
		</td>
	</tr>
	<tr>
		<td valign="top"><b>Your Email</b></td>
		<td>&nbsp;</td>
		<td valign="top">
			<input type="text" name="email" placeholder="username@domain.tld" size="30" /><br />
			<small>This will be the email you\'ll use to trade with other players.</small>
		</td>
	</tr>
	<tr>
		<td valign="top"><b>Your Trade Post</b></td>
		<td>&nbsp;</td>
		<td valign="top">
			<input type="text" name="url" placeholder="https://" size="30" /><br />
			<small>This will be your website address to show your cards.</small>
		</td>
	</tr>
	<tr>
		<td colspan="3"><input type="submit" name="setup" class="btn-success" value="Setup Account" /></td>
	</tr>
	</table>
	</form>';
}



/********************************************************
 * Action:			Setup TCG
 * Description:		Show page setting up the TCG
 */
if( $act == "tcg-setup" ) {
	if( isset($_POST['submit']) ) {
		// Create table for TCG settings
		$tcg_config = "CREATE TABLE IF NOT EXISTS `tcg_settings` (
			`conf_settings` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
			`conf_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`conf_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$config = $database->query($tcg_config);

		if( !$config ) {
			$error[] = "Sorry, there was an error and the TCG was not set up.";
		} else {
			$owner = $sanitize->for_db($_POST['tcg-owner']);
			$tcgname = $sanitize->for_db($_POST['tcg-name']);
			$tcgmail = $sanitize->for_db($_POST['tcg-email']);
			$tcgurl = $sanitize->for_db($_POST['tcg-url']);
			$abpath = $sanitize->for_db($_POST['tcg-abpath']);
			$header = $sanitize->for_db($_POST['tcg-header']);
			$footer = $sanitize->for_db($_POST['tcg-footer']);
			$timezone = $sanitize->for_db($_POST['tcg-timezone']);
			$update = $sanitize->for_db($_POST['tcg-update']);

			$insert = $database->query("INSERT INTO `tcg_settings` (`conf_settings`,`conf_value`,`conf_desc`) VALUES 
				('tcg_owner','$owner','Your name as the TCG owner'),
				('tcg_name','$tcgname','Name or title of your TCG'),
				('tcg_email','$tcgmail','Email address of your TCG'),
				('tcg_url','$tcgurl','The website URL of your TCG'),
				('tcg_timezone','$timezone','Your TCG\'s local timezone'),
				('tcg_discord','','The invitation code of the Discord server'),
				('tcg_twitter','','The username of your TCG\'s Twitter account'),
				('tcg_registration','0','TCG registration status [Close (0) / Open (1)]'),
				('tcg_currency','','Global names as image of your currencies'),
				('update_scope','$update','The day for your weekly update'),
				('update_title','','Title of your update\'s placeholder'),
				('update_text','','Short blurb for your update\'s placeholder'),
				('file_path_absolute','$abpath','Absolute path of your TCG directory'),
				('file_path_header','$header','Full path to your layout\'s header file'),
				('file_path_footer','$footer','Full path to your layout\'s footer file'),
				('file_path_cards','','URL path to your TCG cards'),
				('file_path_img','','URL path to your general images'),
				('cards_file_type','','File type of the card images [gif | jpg | png]'),
				('cards_size_width','','Width of the card template in pixels'),
				('cards_size_height','','Height of the card template in pixels'),
				('button_size_width','','Width of the link button including affiliates in pixels'),
				('button_size_height','','Height of the link button including affiliates in pixels'),
				('prize_start_choice','','Choice cards for new members starter pack'),
				('prize_start_reg','','Random cards for new members starter pack'),
				('prize_start_cur','','Amount of currencies for starter pack'),
                ('prize_start_bonus','','Random cards for starter pack bonus'),
				('prize_master_choice','','Number of choice cards for mastering a deck'),
				('prize_master_reg','','Number of random cards for mastering a deck'),
				('prize_master_cur','','Amount of currencies for mastering a deck'),
				('prize_level_choice','','Number of choice cards for leveling up'),
				('prize_level_reg','','Number of regular cards for leveling up'),
				('prize_level_cur','','Amount of currencies for leveling up'),
				('prize_trade_reg','','Number of random cards for trading'),
				('prize_trade_cur','','Amount of currencies for trading'),
				('prize_special_reg','','Number of random cards for special masteries'),
				('prize_special_cur','','Amount of currencies for special masteries'),
				('prize_daily_reg','','Number of random cards for daily login'),
				('prize_daily_cur','','Amount of currencies for daily login'),
                ('prize_deck_reg','','Number of random cards to reward per donated deck'),
				('prize_deck_cur','','Amount of currencies to reward per donated deck'),
				('prize_deckmaker_reg','','Number of random cards to reward for deck making'),
				('prize_deckmaker_cur','','Amount of currencies to reward for deck making'),
				('shop_minimum','','Minimum amount of currency to spend on shop'),
				('post_per_page','1','Number of blog posts to display per page'),
				('item_per_page','25','Number of items to display per page'),
				('xtra_mpot','20','Number of random cards to be displayed for Melting Pot'),
				('xtra_cclaim','20','Number of random cards to be displayed for Card Claim'),
				('xtra_decks','0','Amount of cards to be released per update'),
                ('xtra_deck_cards','5','Deck limit to donate per month'),
				('xtra_wishes','0','Amount of wishes to be granted per update'),
				('xtra_chatbox','0','Enable/disable chatbox feature'),
				('xtra_motm','0','Enable/disable MOTM/MOTW feature'),
				('xtra_motm_vote','0','Open/close MOTM/MOTW voting'),
				('xtra_motm_scope','','Choose between Week or Month')");

            if( !$insert ) {
                $error[] = "Sorry, there was an error and the settings values were not added.";
            } else {
                header("Location: index.php?action=import");
                exit;
            }
		}
	}

	echo '<h1>TCG Setup</h1>
	<p>Now that you have successfully setup your admin account, it is time to setup your TCG. Just fill in the form below and provide the information needed for your TCG. Like your own account, you can also change these later on.</p>

	<center>';
	if ( isset($error) ) {
		foreach ( $error as $msg ) {
			echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />';
		}
	}
	echo '</center>

	<form method="post" action="index.php?action=tcg-setup">
	<table width="100%" cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td width="20%" valign="top"><b>TCG Owner</b></td>
		<td width="2%">&nbsp;</td>
		<td width="78%" valign="top"><input type="text" name="tcg-owner" placeholder="Jane Doe" size="30" /></td>
	</tr>
	<tr>
		<td valign="top"><b>TCG Name</b></td>
		<td>&nbsp;</td>
		<td valign="top"><input type="text" name="tcg-name" placeholder="Moonlight Legend TCG" size="30" /></td>
	</tr>
	<tr>
		<td valign="top"><b>TCG Email</b></td>
		<td>&nbsp;</td>
		<td valign="top">
			<input type="text" name="tcg-email" placeholder="username@domain.tld" size="30" /><br />
			<small>The official email address of your TCG if it has one, otherwise you can use your own email address.</small>
		</td>
	</tr>
	<tr>
		<td valign="top"><b>TCG Website</b></td>
		<td>&nbsp;</td>
		<td valign="top">
			<input type="text" name="tcg-url" placeholder="https://" size="30" /><br />
			<small>The website URL of your TCG.</small>
		</td>
	</tr>
	<tr>
		<td valign="top"><b>TCG Weekly Update</b></td>
		<td>&nbsp;</td>
		<td valign="top">
			<select name="tcg-update">
			<option value="">--- Select Day of Week ---</option>
			<option value="Sunday">Sunday</option>
			<option value="Monday">Monday</option>
			<option value="Tuesday">Tuesday</option>
			<option value="Wednesday">Wednesday</option>
			<option value="Thursday">Thursday</option>
			<option value="Friday">Friday</option>
			<option value="Saturday">Saturday</option>
			</select><br />
			<small>Select the day of your weekly update.</small>
		</td>
	</tr>
	<tr>
		<td valign="top"><b>TCG Timezone</b></td>
		<td>&nbsp;</td>
		<td valign="top">
			<select name="tcg-timezone" />
			<option value="">--- Select Timezone ---</option>';
			$tzone = $database->num_rows("SELECT * FROM `tcg_timezones`");
			for( $i=1; $i<=$tzone; $i++ ) {
                $tz = $database->get_assoc("SELECT * FROM `tcg_timezones` WHERE `tzone_id`='$i' ORDER BY `tzone_region`");
                echo '<option value="'.$tz['tzone_region'].'">'.$tz['tzone_region'].'</option>';
            }
            echo '</select><br />
			<small>Select the local timezone of your TCG.</small>
		</td>
	</tr>
	</table>

	<h2>File Paths</h2>
	<p>Kindly define the absolute file paths and URLs needed for your TCG below.</p>
	<table width="100%" cellspacing="0" cellpadding="5" border="0">
	<tr>
		<td width="20%" valign="top"><b>Absolute Path</b></td>
		<td width="2%">&nbsp;</td>
		<td width="78%" valign="top"><input type="text" name="tcg-abpath" placeholder="/home/user/public_html/tcgname/" size="30" /></td>
	</tr>
	<tr>
		<td valign="top"><b>Header File</b></td>
		<td>&nbsp;</td>
		<td valign="top">
			<input type="text" name="tcg-header" placeholder="/home/user/public_html/tcgname/theme/header.php" size="30" /><br />
			<small>The absolute path of your TCG\'s header.php file.</small>
		</td>
	</tr>
	<tr>
		<td valign="top"><b>Footer File</b></td>
		<td>&nbsp;</td>
		<td valign="top">
			<input type="text" name="tcg-footer" placeholder="/home/user/public_html/tcgname/theme/footer.php" size="30" /><br />
			<small>The absolute path of your TCG\'s footer.php file.</small>
		</td>
	</tr>
	<tr>
		<td colspan="3"><input type="submit" name="submit" class="btn-success" value="Install STACK" /></td>
	</tr>
	</table>
	</form>

	<p><small><b>Notice:</b> Please take note that there are more settings that you will need to fill up to get your TCG working. You can look on them from STACK\'s admin panel via Settings > Configuration.</p>';
}



/********************************************************
 * Action:			Import Database
 * Description:		Process database to create remaining tables
 */
if( $act == "import" ) {
	echo '<h1>Installed!</h1>
	<p>Your TCG settings has been created successfully! These are the list of the tables that were imported to the database and all must be marked with a <span class="fas fa-check" aria-hidden="true"></span>.</p>
	<p><font color="red">Don\'t forget to delete the <b>/install</b> folder and its files for security purposes.</font></p>';

	include('database.php');
}
?>

	</div><!-- /.box -->
</div>
</body>
</html>