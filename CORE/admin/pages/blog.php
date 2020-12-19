<?php
##################################
########## Updates Main ##########
##################################
echo '<h1>Blog Posts</h1>
<p>&raquo; Do you want to <a href="index.php?action=add&page=blog">add an update</a>?</p>
<table width="100%" cellspacing="0">
<tr><td class="record-label" width="20%">Action</td><td class="record-label" width="50%">Title</td><td class="record-label" width="20%">Posted on</td><td class="record-label" width="10%">Status</td></tr>';
$sql = $database->query("SELECT * FROM `tcg_blog` ORDER BY id DESC");
while($row = mysqli_fetch_array($sql)) {
	$date  = date("F d, Y", strtotime($row['timestamp']));
	$id = $row['id'];
	$title = strip_tags(stripslashes($row['title']));
	if (mb_strlen($title) >= 30) {
		$title = substr($title, 0, 30);
		$title = $title . "...";
	}
	echo '<tr>
        <td class="player-list">
            <input type="button" onClick="window.location.href=\'index.php?action=edit&page=blog&id='.$id.'\'" class="btn-success" value="Edit"> 
            <input type="button" onClick="window.location.href=\'index.php?action=delete&page=blog&id='.$id.'" class="btn-warning" value="Delete"></td>
        <td class="player-list">'.$title.'</td>
        <td class="player-list">'.$date.'</td>
        <td class="player-list">'.$row['status'].'</td>
    </tr>';
}
echo '</table>';
?>