<?php
####################################
########## Show Logs List ##########
####################################
if (empty($id)) {
	echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>
	<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
} else {
    $log = $database->query("SELECT * FROM `logs_$id` WHERE `name`='$id' ORDER BY `timestamp` DESC");
    echo '<h1>Members <span class="fas fa-angle-right" aria-hidden="true"></span> Activity Logs <span class="fas fa-angle-right" aria-hidden="true"></span> '.$row['name'].'</h1>
    <p>Below shows the detailed log of the user\'s activities.</p>
    <div style="padding-right: 20px; margin-top: 20px; line-height: 20px; font-size: 14px; overflow: auto; height: 300px;">';
    $timestamp = '';
    while($row=mysqli_fetch_assoc($log)){
        if ($row['timestamp'] != $timestamp){
            echo '<br /><b>'.date('F d, Y', strtotime($row['timestamp'])).' -----</b><br/>';
            $timestamp = $row['timestamp'];
        }
        echo '- <i>'.$row['title'];
            if (empty($row['subtitle'])) {}
            else { echo ' '.$row['subtitle']; }
        echo ':</i> '.$row['rewards'].'<br />';
    }
    echo '</div>';
}
?>