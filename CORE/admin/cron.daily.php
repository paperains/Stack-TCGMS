<?php
include('class.lib.php');

$database = new Database;
$sanitize = new Sanitize;

// SET USER TO INACTIVE AFTER XX DAYS (default is 60 days)
$database->query("UPDATE `user_list` SET `status`='Inactive' WHERE `session` < CURDATE() - INTERVAL 60 DAY");
?>
