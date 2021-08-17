<?php
/* Other tables to create upon installation
 * If you want to add columns to a specific
 * table below, edit at your own risk!
 * It is highly suggested to edit the tables
 * once the installation has been done.
 */

// Establish database connection
require_once('/home/path/to/TCG/root/admin/class.lib.php');
$dateToday = date("Y-m-d", strtotime("now"));
?>

<center>
<table width="80%" cellpadding="0" cellspacing="5" class="customTable">
	<tr>
		<td width="60%" align="center"><b>Table</b></td>
		<td width="20%" align="center"><b>Status</b></td>
	</tr>

<?php
// Create table for TCG activities
$tcg_act = "CREATE TABLE IF NOT EXISTS `tcg_activities` (
	`act_id` int(11) NOT NULL AUTO_INCREMENT,
	`act_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`act_rec` text COLLATE utf8_unicode_ci NOT NULL,
	`act_slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`act_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`act_date` date NOT NULL,
	PRIMARY KEY (`act_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$activity = $database->query($tcg_act);

if( !$activity ) {
	echo '<tr>
		<td>The table <code>tcg_activities</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($activity));
}
else {
	echo '<tr>
		<td>The table <code>tcg_activities</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for TCG affiliates
$tcg_aff = "CREATE TABLE IF NOT EXISTS `tcg_affiliates` (
	`aff_id` int(11) NOT NULL AUTO_INCREMENT,
	`aff_owner` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`aff_subject` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`aff_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`aff_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`aff_button` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`aff_status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
	`aff_date` date NOT NULL,
	PRIMARY KEY (`aff_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$affiliate = $database->query($tcg_aff);

if( !$affiliate ) {
	echo '<tr>
		<td>The table <code>tcg_affiliates</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($affiliate));
}
else {
	echo '<tr>
		<td>The table <code>tcg_affiliates</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for TCG blog posts
$tcg_blog = "CREATE TABLE IF NOT EXISTS `tcg_blog` (
	`post_id` int(11) NOT NULL AUTO_INCREMENT,
	`post_auth` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`post_icon` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`post_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`post_status` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Draft',
	`post_member` text COLLATE utf8_unicode_ci NOT NULL,
	`post_referral` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'None',
	`post_affiliate` text COLLATE utf8_unicode_ci NOT NULL,
	`post_game` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'None',
	`post_master` text COLLATE utf8_unicode_ci NOT NULL,
	`post_level` text COLLATE utf8_unicode_ci NOT NULL,
	`post_deck` text COLLATE utf8_unicode_ci NOT NULL,
	`post_amount` int(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
	`post_wish` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'None',
	`post_entry` longtext COLLATE utf8_unicode_ci NOT NULL,
	`post_date` date NOT NULL,
	PRIMARY KEY (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$blog = $database->query($tcg_blog);

if( !$blog ) {
	echo '<tr>
		<td>The table <code>tcg_blog</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($blog));
}
else {
	// Insert placeholder post to new installation
	$date = date('Y-m-d', strtotime("now"));
	$entry = '<p>This is your first blog post, which you can edit later on! Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>';
	$database->query("INSERT INTO `tcg_blog` (`post_auth`,`post_icon`,`post_title`,`post_status`,`post_member`,`post_affiliate`,`post_master`,`post_level`,`post_deck`,`post_entry`,`post_date`) VALUES ('Admin','icon.png','Hello World!','Published','None','None','None','None','None','$entry','$date')");

	echo '<tr>
		<td>The table <code>tcg_blog</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for blog comments
$tcg_blog_comm = "CREATE TABLE IF NOT EXISTS `tcg_blog_comm` (
	`comm_id` int(20) NOT NULL AUTO_INCREMENT,
	`comm_post` int(5) NOT NULL,
	`comm_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`comm_text` longtext COLLATE utf8_unicode_ci NOT NULL,
	`comm_date` date NOT NULL,
	PRIMARY KEY (`comm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$blog_comm = $database->query($tcg_blog_comm);

if( !$blog_comm ) {
	echo '<tr>
		<td>The table <code>tcg_blog_comm</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($blog_comm));
}
else {
	// Insert placeholder comment to new installation
	$date = date('Y-m-d', strtotime("now"));
	$comm = 'This is a sample comment! You can use HTML tags here.<br /><br /><b>bold text</b>, <i>italized text</i>, <u>underlined text</u>, <a href="">linked text</a>';
	$database->query("INSERT INTO `tcg_blog_comm` (`comm_post`,`comm_name`,`comm_text`,`comm_date`) VALUES ('1','Commenter','$comm','$date')");

	echo '<tr>
		<td>The table <code>tcg_blog_comm</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for card decks
$tcg_card = "CREATE TABLE IF NOT EXISTS `tcg_cards` (
	`card_id` int(11) NOT NULL AUTO_INCREMENT,
	`card_filename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`card_deckname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`card_desc` text COLLATE utf8_unicode_ci NOT NULL,
	`card_maker` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`card_donator` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`card_color` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
	`card_status` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Upcoming',
	`card_count` int(2) NOT NULL DEFAULT '20',
	`card_worth` int(1) NOT NULL DEFAULT '1',
	`card_break` int(1) NOT NULL DEFAULT '5',
	`card_cat` int(2) NOT NULL,
	`card_set` int(5) NOT NULL,
	`card_mast` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Yes',
	`card_puzzle` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No',
	`card_masters` text COLLATE utf8_unicode_ci NOT NULL,
	`card_votes` int(5) NOT NULL DEFAULT '0',
	`card_released` date NOT NULL,
	PRIMARY KEY (`card_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$cards = $database->query($tcg_card);

if( !$cards ) {
	echo '<tr>
		<td>The table <code>tcg_cards</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($cards));
}
else {
	echo '<tr>
		<td>The table <code>tcg_cards</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for card categories
$tcg_card_cat = "CREATE TABLE IF NOT EXISTS `tcg_cards_cat` (
	`cat_id` int(2) NOT NULL AUTO_INCREMENT,
	`cat_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$card_cat = $database->query($tcg_card_cat);

if( !$card_cat ) {
	echo '<tr>
		<td>The table <code>tcg_cards_cat</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($card_cat));
}
else {
	echo '<tr>
		<td>The table <code>tcg_cards_cat</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for card sets
$tcg_card_set = "CREATE TABLE IF NOT EXISTS `tcg_cards_set` (
	`set_id` int(2) NOT NULL AUTO_INCREMENT,
	`set_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`set_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$card_set = $database->query($tcg_card_set);

if( !$card_set ) {
	echo '<tr>
		<td>The table <code>tcg_cards_set</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($card_set));
}
else {
	echo '<tr>
		<td>The table <code>tcg_cards_set</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for event cards
$tcg_card_event = "CREATE TABLE IF NOT EXISTS `tcg_cards_event` (
	`event_id` int(11) NOT NULL AUTO_INCREMENT,
	`event_filename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`event_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`event_group` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`event_date` date NOT NULL,
	PRIMARY KEY (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$card_event = $database->query($tcg_card_event);

if( !$card_event ) {
	echo '<tr>
		<td>The table <code>tcg_cards_event</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($card_event));
}
else {
	echo '<tr>
		<td>The table <code>tcg_cards_event</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for chatbox
$tcg_chatbox = "CREATE TABLE IF NOT EXISTS `tcg_chatbox` (
	`chat_id` int(11) NOT NULL AUTO_INCREMENT,
	`chat_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`chat_url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`chat_msg` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
	`chat_date` date NOT NULL,
	PRIMARY KEY (`chat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$chatbox = $database->query($tcg_chatbox);

if( !$chatbox ) {
	echo '<tr>
		<td>The table <code>tcg_chatbox</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($chatbox));
}
else {
	echo '<tr>
		<td>The table <code>tcg_chatbox</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for deck donations
$tcg_donate = "CREATE TABLE IF NOT EXISTS `tcg_donations` (
	`deck_id` int(11) NOT NULL AUTO_INCREMENT,
	`deck_donator` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`deck_cat` int(2) NOT NULL,
	`deck_set` int(5) NOT NULL,
	`deck_filename` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`deck_feature` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
	`deck_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`deck_maker` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`deck_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
	`deck_status` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Pending',
	`deck_pass` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`deck_date` date NOT NULL,
	PRIMARY KEY (`deck_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$donation = $database->query($tcg_donate);

if( !$donation ) {
	echo '<tr>
		<td>The table <code>tcg_donations</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($donation));
}
else {
	echo '<tr>
		<td>The table <code>tcg_donations</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for levels
$tcg_level = "CREATE TABLE IF NOT EXISTS `tcg_levels` (
	`lvl_id` int(2) NOT NULL AUTO_INCREMENT,
	`lvl_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`lvl_cards` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
	`lvl_interval` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`lvl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$level = $database->query($tcg_level);

if( !$level ) {
	echo '<tr>
		<td>The table <code>tcg_levels</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($level));
}
else {
	echo '<tr>
		<td>The table <code>tcg_levels</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for level badges
$tcg_level_badge = "CREATE TABLE IF NOT EXISTS `tcg_levels_badge` (
	`badge_id` int(5) NOT NULL AUTO_INCREMENT,
	`badge_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`badge_set` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`badge_level` int(3) NOT NULL,
	`badge_width` int(3) NOT NULL,
	`badge_height` int(3) NOT NULL,
	`badge_feature` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`badge_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$badge = $database->query($tcg_level_badge);

if( !$badge ) {
	echo '<tr>
		<td>The table <code>tcg_levels_badge</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($badge));
}
else {
	echo '<tr>
		<td>The table <code>tcg_levels_badge</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for games settings
$tcg_game = "CREATE TABLE IF NOT EXISTS `tcg_games` (
	`game_id` int(5) NOT NULL AUTO_INCREMENT,
	`game_set` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`game_slug` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`game_title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`game_subtitle` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`game_excerpt` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`game_desc` text COLLATE utf8_unicode_ci NOT NULL,
	`game_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`game_choice_array` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`game_random_array` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`game_currency_array` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`game_ques_array` longtext COLLATE utf8_unicode_ci NOT NULL,
	`game_pass_array` text COLLATE utf8_unicode_ci NOT NULL,
	`game_clue_array` text COLLATE utf8_unicode_ci NOT NULL,
	`game_current_array` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`game_multiple` int(1) NOT NULL,
	`game_updated` date NOT NULL,
	PRIMARY KEY (`game_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$game = $database->query($tcg_game);

if( !$game ) {
	echo '<tr>
		<td>The table <code>tcg_games</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($game));
}
else {
	// Insert games to database
	$database->query("INSERT INTO `tcg_games` (`game_id`, `game_set`, `game_slug`, `game_title`, `game_subtitle`, `game_excerpt`, `game_desc`, `game_status`, `game_choice_array`, `game_random_array`, `game_currency_array`, `game_ques_array`, `game_pass_array`, `game_clue_array`, `game_current_array`, `game_multiple`, `game_updated`) VALUES 
		(1, 'Monthly', 'birthdays', 'Birthdays', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '2', '10', '2 | 10', '', '', '', '0', '0', '$dateToday'),
		(2, 'Weekly', 'black-jack', 'Black Jack', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '4', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(3, 'Weekly', 'card-claim', 'Card Claim', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '0', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(4, 'Set A', 'coin-flip', 'Coin Flip', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '2', '0 | 2', '', '', '', '0', '0', '$dateToday'),
		(5, 'Weekly', 'freebies', 'Freebies', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '10', '0 | 5', '', '', '', '0', '0', '$dateToday'),
		(6, 'Set A', 'hangman-img', 'Hangman', 'Image', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '2', '0 | 4', '', '', '', '0', '0', '$dateToday'),
		(7, 'Set A', 'hangman-txt', 'Hangman', 'Text', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '2', '0 | 4', '', '', '', '0', '0', '$dateToday'),
		(8, 'Weekly', 'higher-lower', 'Higher or Lower', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '1', '0 | 2', '', '', '', '0', '0', '$dateToday'),
		(9, 'Set B', 'jan-ken-pon', 'Jan Ken Pon', 'Draw, Won', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0, 0', '2, 4', '0 | 2, 0 | 4', '', '', '', '0', '1', '$dateToday'),
		(10, 'Weekly', 'lottery', 'Lottery', 'One, Two, Three, Four, Jackpot', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0, 0, 0, 0, 0', '2, 4, 6, 8, 10', '0 | 4, 0 | 6, 0 | 8, 0 | 10, 0 | 12', '', '', '', '0', '1', '$dateToday'),
		(11, 'Set A', 'lucky-match', 'Lucky Match', 'Zero Match, One Match, Two Matches, Three Matches, Four Matches, Five Matches', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0, 0, 0, 0, 0, 0', '2, 4, 6, 8, 10, 12', '0 | 2, 0 | 4, 0 | 6, 0 | 8, 0 | 10, 0 | 12', '', '', '', '0', '1', '$dateToday'),
		(12, 'Weekly', 'melting-pot', 'Melting Pot', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '0', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(13, 'Set B', 'memory', 'Memory', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '5', '0 | 2', '', '', '', '0', '0', '$dateToday'),
		(14, 'Set A', 'peeptin', 'Peeptin', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '4', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(15, 'Set A', 'puzzle', 'Puzzle', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '2', '0 | 4', '', '', '', '0', '0', '$dateToday'),
		(16, 'Set B', 'reaction', 'Reaction', 'One, Two, Three, Four, Jackpot', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0, 0, 0, 0, 0', '2, 4, 6, 8, 10', '0 | 1, 0 | 2, 0 | 3, 0 | 4, 0 | 5', '', '', '', '0', '1', '$dateToday'),
		(17, 'Set B', 'slot-machine', 'Slot Machine', 'Token Type', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '5', '0 | 5', '', '', '', '0', '0', '$dateToday'),
		(18, 'Set B', 'slots', 'Slots', 'Image Type', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '5', '0 | 5', '', '', '', '0', '0', '$dateToday'),
		(19, 'Set A', 'telepathy', 'Telepathy', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '4', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(20, 'Set B', 'tic-tac-toe', 'Tic Tac Toe', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '2', '0 | 2', '', '', '', '0', '0', '$dateToday'),
		(21, 'Set B', 'toggler', 'Toggler', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '2', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(22, 'Set A', 'trasure-hunt', 'Treasure Hunt', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '3', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(23, 'Weekly', 'upcoming-vote', 'Upcoming Vote', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '4', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(24, 'Set B', 'vacation', 'Vacation', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '2', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(25, 'Set A', 'war', 'War', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '2', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(26, 'Set B', 'wheels', 'Wheels', 'Red Ball, Orange Ball, Yellow Ball, Green Ball, Blue Ball, Violet Ball, Black Ball', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0, 0, 0, 0, 0, 0, 0', '10, 9, 8, 7, 6, 5, 4', '2 | 6, 0 | 5, 0 | 4, 0 | 3, 0 | 2, 0 | 1, 0 | 0', '', '', '', '0', '1', '$dateToday'),
		(27, 'Monthly', 'wishes', 'Wishes', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '2', '0 | 2', '', '', '', '0', '0', '$dateToday'),
		(28, 'Monthly', 'motm-vote', 'Featured Member (Vote)', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '0', '1', '0 | 0', '', '', '', '0', '0', '$dateToday'),
		(29, 'Monthly', 'motm-reward', 'Featured Member (Reward)', '', '', '<p>Type your game description and/or mechanics here.</p>', 'Active', '2', '10', '5 | 20', '', '', '', '0', '0', '$dateToday')"
	);
	echo '<tr>
		<td>The table <code>tcg_games</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for games updater
$tcg_game_updater = "CREATE TABLE IF NOT EXISTS `tcg_games_updater` (
	`gup_id` int(5) NOT NULL AUTO_INCREMENT,
	`gup_set` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`gup_date` date NOT NULL,
	PRIMARY KEY (`gup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$game_update = $database->query($tcg_game_updater);

if( !$game ) {
	echo '<tr>
		<td>The table <code>tcg_games_updater</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($game_update));
}
else {
	echo '<tr>
		<td>The table <code>tcg_games_updater</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for page content
$tcg_page = "CREATE TABLE IF NOT EXISTS `tcg_pages` (
	`page_id` int(5) NOT NULL AUTO_INCREMENT,
	`page_parent` int(5) NOT NULL,
	`page_slug` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`page_title` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`page_content` longtext COLLATE utf8_unicode_ci NOT NULL,
	`page_status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
	`page_date` date NOT NULL,
	PRIMARY KEY (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$page = $database->query($tcg_page);

if( !$page ) {
	echo '<tr>
		<td>The table <code>tcg_pages</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($page));
}
else {
	echo '<tr>
		<td>The table <code>tcg_pages</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for services type
$tcg_service = "CREATE TABLE IF NOT EXISTS `tcg_services` (
	`service_id` int(5) NOT NULL AUTO_INCREMENT,
	`service_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`service_desc` text COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`service_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$service = $database->query($tcg_service);

if( !$service ) {
	echo '<tr>
		<td>The table <code>tcg_pages</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($service));
}
else {
	// Insert services to database
	$database->query("INSERT INTO `tcg_services` (`service_id`, `service_name`, `service_desc`) VALUES 
		(1, 'Rewards', 'Can be given to members such as gifts and the likes.'),
		(2, 'Pulls', 'Such as update wishes and freebies.'),
		(3, 'Releases', 'Your weekly new deck releases.'),
		(4, 'Exchanges', 'Typically the doubles exchange or coupon exchange.'),
		(5, 'Purchases', 'If your TCG have a shop, these are the shop items.'),
		(6, 'Service', 'Typical services such as masteries and level ups.'),
		(7, 'Monthly', 'Your monthly games for log purposes.'),
		(8, 'Weekly', 'Your weekly games for log purposes.'),
		(9, 'Set A', 'Your bi-weekly set A games for log purposes.'),
		(10, 'Set B', 'Your bi-weekly set B games for log purposes.'),
		(11, 'Special', 'Your special games for log purposes.'),
		(12, 'Paycheck', 'Your staff paycheck such as deck making and such.')"
	);
	echo '<tr>
		<td>The table <code>tcg_pages</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for shop catalog
$shop_catalog = "CREATE TABLE IF NOT EXISTS `shop_catalog` (
	`shop_id` int(5) NOT NULL AUTO_INCREMENT,
	`shop_slug` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`shop_catalog` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`shop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$catalog = $database->query($shop_catalog);

if( !$catalog ) {
	echo '<tr>
		<td>The table <code>shop_catalog</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($catalog));
}
else {
	echo '<tr>
		<td>The table <code>shop_catalog</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for shop categories
$shop_category = "CREATE TABLE IF NOT EXISTS `shop_category` (
	`shop_id` int(5) NOT NULL AUTO_INCREMENT,
	`shop_catalog` int(5) NOT NULL,
	`shop_slug` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`shop_category` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`shop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$shopcat = $database->query($shop_category);

if( !$shopcat ) {
	echo '<tr>
		<td>The table <code>shop_category</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($shopcat));
}
else {
	echo '<tr>
		<td>The table <code>shop_category</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for shop items
$shop_items = "CREATE TABLE IF NOT EXISTS `shop_items` (
	`shop_id` int(5) NOT NULL AUTO_INCREMENT,
	`shop_catalog` int(5) NOT NULL,
	`shop_category` int(5) NOT NULL,
	`shop_file` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`shop_item` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`shop_description` text COLLATE utf8_unicode_ci NOT NULL,
	`shop_usage` text COLLATE utf8_unicode_ci NOT NULL,
	`shop_currency` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`shop_quantity` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`shop_amount` int(2) NOT NULL,
	PRIMARY KEY (`shop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$shopitm = $database->query($shop_items);

if( !$shopitm ) {
	echo '<tr>
		<td>The table <code>shop_items</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($shopitm));
}
else {
	echo '<tr>
		<td>The table <code>shop_items</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for card claim
$game_cclaim = "CREATE TABLE IF NOT EXISTS `game_cclaim_cards` (
	`cclaim_cards` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$cclaim = $database->query($game_cclaim);

if( !$cclaim ) {
	echo '<tr>
		<td>The table <code>game_cclaim_cards</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($cclaim));
}
else {
	echo '<tr>
		<td>The table <code>game_cclaim_cards</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for card claim logs
$game_cclaim_logs = "CREATE TABLE IF NOT EXISTS `game_cclaim_logs` (
	`cclaim_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`cclaim_take` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`cclaim_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$cclaim_logs = $database->query($game_cclaim_logs);

if( !$cclaim_logs ) {
	echo '<tr>
		<td>The table <code>game_cclaim_logs</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($cclaim_logs));
}
else {
	echo '<tr>
		<td>The table <code>game_cclaim_logs</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for higher or lower
$game_hol = "CREATE TABLE IF NOT EXISTS `game_hol_cards` (
	`hol_filename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`hol_number` int(2) NOT NULL,
	`hol_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$hol = $database->query($game_hol);

if( !$hol ) {
	echo '<tr>
		<td>The table <code>game_hol_cards</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($hol));
}
else {
	echo '<tr>
		<td>The table <code>game_hol_cards</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for higher or lower logs
$game_hol_logs = "CREATE TABLE IF NOT EXISTS `game_hol_logs` (
	`hol_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`hol_guess` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
	`hol_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$hol_logs = $database->query($game_hol_logs);

if( !$hol_logs ) {
	echo '<tr>
		<td>The table <code>game_hol_logs</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($hol_logs));
}
else {
	echo '<tr>
		<td>The table <code>game_hol_logs</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for melting pot
$game_mpot = "CREATE TABLE IF NOT EXISTS `game_mpot_cards` (
	`mpot_cards` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$mpot = $database->query($game_mpot);

if( !$mpot ) {
	echo '<tr>
		<td>The table <code>game_mpot_cards</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($mpot));
}
else {
	echo '<tr>
		<td>The table <code>game_mpot_cards</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for melting pot logs
$game_mpot_logs = "CREATE TABLE IF NOT EXISTS `game_mpot_logs` (
	`mpot_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`mpot_take` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`mpot_give` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`mpot_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$mpot_logs = $database->query($game_mpot_logs);

if( !$mpot_logs ) {
	echo '<tr>
		<td>The table <code>game_mpot_logs</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($mpot_logs));
}
else {
	echo '<tr>
		<td>The table <code>game_mpot_logs</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for MOTM list
$game_motm_list = "CREATE TABLE IF NOT EXISTS `game_motm_list` (
	`motm_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`motm_vote` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$motm_list = $database->query($game_motm_list);

if( !$motm_list ) {
	echo '<tr>
		<td>The table <code>game_motm_list</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($motm_list));
}
else {
	echo '<tr>
		<td>The table <code>game_motm_list</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for MOTM records
$game_motm_logs = "CREATE TABLE IF NOT EXISTS `game_motm_logs` (
	`motm_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`motm_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$motm_logs = $database->query($game_motm_logs);

if( !$motm_logs ) {
	echo '<tr>
		<td>The table <code>game_motm_logs</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($motm_logs));
}
else {
	echo '<tr>
		<td>The table <code>game_motm_logs</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for user freebies
$user_freebies = "CREATE TABLE IF NOT EXISTS `user_freebies` (
	`free_id` int(11) NOT NULL AUTO_INCREMENT,
	`free_type` int(1) NOT NULL,
	`free_word` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`free_amount` int(2) NOT NULL,
	`free_cat` int(2) NOT NULL,
	`free_date` date NOT NULL,
	PRIMARY KEY (`free_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$freebies = $database->query($user_freebies);

if( !$freebies ) {
	echo '<tr>
		<td>The table <code>user_freebies</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($freebies));
}
else {
	echo '<tr>
		<td>The table <code>user_freebies</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for user activity logs
$user_logs = "CREATE TABLE IF NOT EXISTS `user_logs` (
    `log_id` int(11) NOT NULL AUTO_INCREMENT,
	`log_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`log_type` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`log_title` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`log_subtitle` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`log_rewards` varchar(3000) COLLATE utf8_unicode_ci NOT NULL,
	`log_date` date NOT NULL,
	PRIMARY KEY `log_id` (`log_id`),
	KEY `log_name` (`log_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$logs = $database->query($user_logs);

if( !$logs ) {
	echo '<tr>
		<td>The table <code>user_logs</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($logs));
}
else {
	echo '<tr>
		<td>The table <code>user_logs</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for user trade logs
$user_trades = "CREATE TABLE IF NOT EXISTS `user_trades` (
    `trd_id` int(11) NOT NULL AUTO_INCREMENT,
	`trd_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`trd_trader` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`trd_out` text COLLATE utf8_unicode_ci NOT NULL,
	`trd_inc` text COLLATE utf8_unicode_ci NOT NULL,
	`trd_date` date NOT NULL,
	PRIMARY KEY (`trd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$trades = $database->query($user_trades);

if( !$trades ) {
	echo '<tr>
		<td>The table <code>user_trades</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($trades));
}
else {
	echo '<tr>
		<td>The table <code>user_trades</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for user messages
$user_mbox = "CREATE TABLE IF NOT EXISTS `user_mbox` (
	`msg_id` int(11) NOT NULL AUTO_INCREMENT,
	`msg_sender` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`msg_recipient` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`msg_subject` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`msg_text` longtext COLLATE utf8_unicode_ci NOT NULL,
	`msg_box_from` int(1) NOT NULL,
	`msg_see_from` int(1) NOT NULL,
	`msg_del_from` int(1) NOT NULL,
	`msg_box_to` int(1) NOT NULL,
	`msg_see_to` int(1) NOT NULL,
	`msg_del_to` int(1) NOT NULL,
	`msg_origin` int(20) NOT NULL,
	`msg_date` datetime NOT NULL,
	PRIMARY KEY (`msg_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$mbox = $database->query($user_mbox);

if( !$mbox ) {
	echo '<tr>
		<td>The table <code>user_mbox</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($mbox));
}
else {
	echo '<tr>
		<td>The table <code>user_mbox</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for user notifications
$user_notif = "CREATE TABLE IF NOT EXISTS `user_notices` (
	`notif_id` int(11) NOT NULL AUTO_INCREMENT,
	`notif_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`notif_comm` int(11) NOT NULL,
	`notif_message` text COLLATE utf8_unicode_ci NOT NULL,
	`notif_read` int(1) NOT NULL,
	`notif_date` date NOT NULL,
	PRIMARY KEY (`notif_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$notif = $database->query($user_notif);

if( !$notif ) {
	echo '<tr>
		<td>The table <code>user_notices</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($notif));
}
else {
	echo '<tr>
		<td>The table <code>user_notices</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for user who quitted
$user_quit = "CREATE TABLE IF NOT EXISTS `user_list_quit` (
	`usr_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`usr_mcard` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`usr_joined` date NOT NULL,
	`usr_quit` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$quit = $database->query($user_quit);

if( !$quit ) {
	echo '<tr>
		<td>The table <code>user_list_quit</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($quit));
}
else {
	echo '<tr>
		<td>The table <code>user_list_quit</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for user rewards
$user_reward = "CREATE TABLE IF NOT EXISTS `user_rewards` (
	`rwd_id` int(11) NOT NULL AUTO_INCREMENT,
	`rwd_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`rwd_type` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`rwd_subtitle` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`rwd_mcard` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
	`rwd_mstone` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
	`rwd_cards` int(3) NOT NULL,
	`rwd_currency` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`rwd_date` datetime NOT NULL,
	PRIMARY KEY (`rwd_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$reward = $database->query($user_reward);

if( !$reward ) {
	echo '<tr>
		<td>The table <code>user_rewards</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($reward));
}
else {
	echo '<tr>
		<td>The table <code>user_rewards</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for user wishes
$user_wish = "CREATE TABLE IF NOT EXISTS `user_wishes` (
	`wish_id` int(11) NOT NULL AUTO_INCREMENT,
	`wish_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`wish_type` int(1) NOT NULL,
	`wish_word` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
	`wish_amount` int(2) NOT NULL,
	`wish_cat` int(1) NOT NULL,
	`wish_set` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`wish_text` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
	`wish_status` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
	`wish_date` datetime NOT NULL,
	PRIMARY KEY (`wish_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$wish = $database->query($user_wish);

if( !$wish ) {
	echo '<tr>
		<td>The table <code>user_wishes</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($wish));
}
else {
	echo '<tr>
		<td>The table <code>user_wishes</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}



// Create table for user wishlists
$user_wishlist = "CREATE TABLE IF NOT EXISTS `user_wishlist` (
	`wlist_id` int(11) NOT NULL AUTO_INCREMENT,
	`wlist_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
	`wlist_deck` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`wlist_date` datetime NOT NULL,
	PRIMARY KEY (`wlist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
$wishlist = $database->query($user_wishlist);

if( !$wishlist ) {
	echo '<tr>
		<td>The table <code>user_wishlist</code> was not created.</td>
		<td><span class="fas fa-times" aria-hidden="true"></span></td>
	</tr>';
	die("Error: ". mysqli_error($wishlist));
}
else {
	echo '<tr>
		<td>The table <code>user_wishlist</code> was successfully created.</td>
		<td align="center"><span class="fas fa-check" aria-hidden="true"></span></td>
	</tr>';
}
?>

</table>
</center>