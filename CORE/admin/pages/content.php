<?php
#######################################
########## Page Content Main ##########
#######################################
echo '<h1>Page Content</h1>
<p>&raquo; Do you want to <a href="index.php?action=add&page=content">add a page</a>?</p>
<table width="100%" cellspacing="0">
<tr>
    <td class="record-label" width="20%">Action</td>
    <td class="record-label" width="5%">ID</td>
    <td class="record-label" width="45%">Title</td>
    <td class="record-label" width="20%">Posted on</td>
    <td class="record-label" width="10%">Status</td>
</tr>';
$sql = $database->query("SELECT * FROM `tcg_pages` ORDER BY id DESC");
while($row = mysqli_fetch_array($sql)) {
	$date  = date("F d, Y", strtotime($row['timestamp']));
	$id = $row['id'];
	$title = strip_tags(stripslashes($row['post_title']));
	if (mb_strlen($title) >= 30) {
		$title = substr($title, 0, 30);
		$title = $title . "...";
	}
	echo '<tr>
        <td class="player-list">
            <button onClick="window.location.href=\'index.php?action=edit&page=content&id='.$id.'\'" class="btn-success">Edit</button> 
            <button onClick="window.location.href=\'index.php?action=delete&page=content&id='.$id.'" class="btn-warning">Delete</button></td>
        <td class="player-list">'.$row['id'].'</td>
        <td class="player-list">'.$title.'</td>
        <td class="player-list">'.$date.'</td>
        <td class="player-list">'.$row['post_status'].'</td>
    </tr>';
}
echo '</table>';
?>
