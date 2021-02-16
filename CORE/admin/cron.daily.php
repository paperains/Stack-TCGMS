<?php
include('class.lib.php');

$database = new Database;
$sanitize = new Sanitize;

// SET USER TO INACTIVE AFTER XX DAYS (default is 60 days)
$database->query("UPDATE `user_list` SET `status`='Inactive' WHERE `session` < CURDATE() - INTERVAL 60 DAY");

// PUBLISH ANY SCHEDULED POSTS
$count = $database->num_rows("SELECT *,DATE(timestamp) FROM `tcg_blog` WHERE DATE(`timestamp`) = CURDATE()");
$sql = $database->get_assoc("SELECT *,DATE(timestamp) FROM `tcg_blog` WHERE DATE(`timestamp`) = CURDATE()");
if ($count != 0) { $database->query("UPDATE `tcg_blog` SET `status`='Published' WHERE `id` = '".$sql['id']."'"); }
?>
