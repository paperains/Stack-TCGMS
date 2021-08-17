<?php
/*
 * Class libraries for connections
 */

$db_server = 'localhost';							// The database server, usually localhost
$db_user = 'username';								// The username for your database
$db_password = 'password';							// The password for your database
$db_database = 'table name';						// The database name



/********************************************************
 * Class:         	Config
 * Description:		Set database connection for the TCG
 */
class Config {
	const DB_SERVER = 'localhost',					// Default: localhost
		DB_USER = 'username',						// Database username
		DB_PASSWORD = 'password',					// Database password
		DB_DATABASE = 'table name',					// Database table name
		DB_SALT = 'aEF#TGgs-!dgaw3324_WQ+';			// Your password salt and treat it like a high security password.
}



/********************************************************
** DO NOT EDIT BELOW UNLESS YOU KNOW WHAT YOU'RE DOING **
** YOU CAN ONLY EDIT THE COMMENTED PART IF YOU NEED TO **
 -------------------------------------------------------
 * Include class library files
 */
define('VALID_INC', TRUE);
include('class/cls_database.php');
include('class/cls_check.php');
include('class/cls_count.php');
include('class/cls_games.php');
include('class/cls_general.php');
include('class/cls_plugins.php');
include('class/cls_settings.php');
include('class/cls_uploads.php');
include('class/cls_admin.php');


// Shortened variables for settings
$database = new Database;
$sanitize = new Sanitize;
$general = new General;
$uploads = new Uploads;
$plugins = new Plugins;
$counts = new Count;
$games = new Games;
$check = new Check;
$admin = new Admin;

// Check if any tables exists in the database
$db_name = $db_database;
$sql = $database->num_rows("SHOW TABLES FROM $db_name LIKE 'tcg_settings'");
if( $sql == 0 ) {}
else {
	$settings = new Settings;

	$header = $settings->getValue('file_path_header');
	$footer = $settings->getValue('file_path_footer');
	$tcgurl = $settings->getValue('tcg_url');
	$tcgname = $settings->getValue('tcg_name');
	$tcgemail = $settings->getValue('tcg_email');
	$tcgowner = $settings->getValue('tcg_owner');
	$tcgcards = $settings->getValue('file_path_cards');
	$tcgext = $settings->getValue('cards_file_type');
	$tcgimg = $settings->getValue('file_path_img');
	$tcgpath = $settings->getValue('file_path_absolute');
	$tcgdiscord = $settings->getValue('tcg_discord');
	$tcgtwitter = $settings->getValue('tcg_twitter');
}


// Set page strings for dynamic pages (DO NOT EDIT)
$id = (isset($_GET['id']) ? $_GET['id'] : null);
$do = (isset($_GET['do']) ? $_GET['do'] : null);
$go = (isset($_GET['go']) ? $_GET['go'] : null);
$set = (isset($_GET['set']) ? $_GET['set'] : null);
$sub = (isset($_GET['sub']) ? $_GET['sub'] : null);
$msg = (isset($_GET['msg']) ? $_GET['msg'] : null);
$mod = (isset($_GET['mod']) ? $_GET['mod'] : null);
$item = (isset($_GET['item']) ? $_GET['item'] : null);
$form = (isset($_GET['form']) ? $_GET['form'] : null);
$deck = (isset($_GET['deck']) ? $_GET['deck'] : null);
$view = (isset($_GET['view']) ? $_GET['view'] : null);
$page = (isset($_GET['page']) ? $_GET['page'] : null);
$stat = (isset($_GET['stat']) ? $_GET['stat'] : null);
$play = (isset($_GET['play']) ? $_GET['play'] : null);
$act = (isset($_GET['action']) ? $_GET['action'] : null);
$login = (isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null);

$credits = '&copy; '.date('Y').' '.$tcgname.' by <a href="mailto:'.$tcgemail.'">'.$tcgowner.'</a> &bull; Online TCG concept &copy; Calico &bull; Powered by <a href="https://design-with.in/resources/stack/" target="_blank">Stack 1.0.0 [Beta]</a>';
?>