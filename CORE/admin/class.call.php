<?php
/* This is to call all classes needed for the pages
 * which includes shortened variables for specific
 * functions.
 */

if ( ! defined('VALID_INC') ) exit('No direct script access allowed');

$database = new Database;
$sanitize = new Sanitize;
$settings = new Settings;
$general = new General;
$uploads = new Uploads;
$counts = new Count;
$check = new Check;

$header = $settings->getValue('file_path_header');
$footer = $settings->getValue('file_path_footer');
$tcgurl = $settings->getValue('tcg_url');
$tcgname = $settings->getValue('tcg_name');
$tcgemail = $settings->getValue('tcg_email');
$tcgowner = $settings->getValue('tcg_owner');
$tcgcards = $settings->getValue('file_path_cards');
$tcgext = $settings->getValue('cards_file_type');
$tcgimg = $settings->getValue('file_path_img');

/* Feel free to change this according to your liking.
 * This is a blurb that will be displayed when a player access the game prize page directly.
 */
$ForbiddenAccess = '<h1>Hold it right there!</h1><p>Tough luck! It seems like you\'re trying to outwit the admin by secretly accessing a page through an underground method. That will surely anger the gods of the gods, sunshine! Please go back if you haven\'t played the game yet, or come back next week for a new round if you have already.</p>';

?>
