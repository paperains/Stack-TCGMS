<?php
#################################
########## Wishes Main ##########
#################################
echo '<h1>Wishes</h1>
<p>&raquo; Do you want to <a href="index.php?action=add&page=wishes">add a wish</a>?</p>

<h1>Pending</h1>';
$select = $database->query("SELECT * FROM `user_wishes` WHERE `status`='Pending' ORDER BY `id` ASC");
$count = mysqli_num_rows($select);

if($count==0) {
	echo '<center>There are currently no wishes under this status.</center>';
	echo "<br /><br />\n\n";
} else {
	echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
	echo '<tr>
	    <td width="18%" class="record-label">Action</td>
	    <td width="15%" class="record-label">Player</td>
	    <td class="record-label" width="37%">Wish</td>
	    <td class="record-label" width="10%">Status</td>
	    <td class="record-label" width="10%">Date</td>
	</tr>';
	while($row=mysqli_fetch_assoc($select)) {
        echo '<tr>
            <td class="player-list" align="center">
                <button onClick="window.location.href=\'index.php?action=edit&page=wishes&id='.$row['id'].'\'" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
                <button onClick="window.location.href=\'index.php?action=delete&page=wishes&id='.$row['id'].'\'" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button> 
                <button onClick="window.location.href=\'index.php?action=approve&page=wishes&id='.$row['id'].'\'" class="btn-primary"><span class="fas fa-check" aria-hidden="true"></span></button>
            </td>
            <td class="player-list" align="center">'.$row['name'].'</td>
            <td class="player-list" align="center">'.$row['wish'].'</td>
            <td class="player-list" align="center">'.$row['status'].'</td>
            <td class="player-list" align="center">'.$row['timestamp'].'</td>
        </tr>';
	}
	echo "</table>\n";
	echo "<br /><br />\n\n";
}

echo '<h1>Granted</h1>';
$select2 = $database->query("SELECT * FROM `user_wishes` WHERE `status`='Granted' ORDER BY `timestamp` DESC");
$count2 = mysqli_num_rows($select2);

if($count2==0) {
	echo "<center>There are currently no wishes under this status.</center>\n";
	echo "<br /><br />\n\n";
}
else {
	echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
    echo '<tr>
        <td width="15%" class="record-label">Action</td>
        <td width="15%" class="record-label">Player</td>
        <td class="record-label" width="40%">Wish</td>
        <td class="record-label" width="10%">Status</td>
        <td class="record-label" width="10%">Date</td>
    </tr>';
	while($row2=mysqli_fetch_assoc($select2)) {
		echo '<tr>
            <td class="player-list" align="center">
                <button onClick="window.location.href=\'index.php?action=edit&page=wishes&id='.$row2['id'].'\'" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
                <button onClick="window.location.href=\'index.php?action=delete&page=wishes&id='.$row2['id'].'\'" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button>
            </td>
            <td class="player-list" align="center">'.$row2['name'].'</td>
            <td class="player-list" align="center">'.$row2['wish'].'</td>
            <td class="player-list" align="center">'.$row2['status'].'</td>
            <td class="player-list" align="center">'.$row2['timestamp'].'</td>
        </tr>';
	}
	echo "</table>\n";
	echo "<br /><br />\n\n";
}
?>