<?php
include('class.lib.php');

$database = new Database;
$sanitize = new Sanitize;

$month = date("Y-m", strtotime("now"));

// Update monthly game set first
if ($month === TRUE) {
    $database->query("INSERT INTO `tcg_games` (`sets`,`timestamp`) VALUES ('Monthly','$month-01')");
}
?>
