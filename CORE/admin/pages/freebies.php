<?php
###################################
########## Freebies Main ##########
###################################
echo '<h1>Freebies</h1>
<p>&raquo; Do you want to <a href="index.php?action=add&page=freebies">add a freebie</a>?</p>';
$select = $database->query("SELECT * FROM `user_freebies` ORDER BY `timestamp`");
echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
echo '<tr><td width="15%" class="record-label">Action</td><td class="record-label" width="5%">Type</td><td class="record-label" width="10%">Word</td><td class="record-label" width="10%">Color</td><td class="record-label" width="10%">Amount</td><td class="record-label" width="10%">Date</td></tr>';
while($row=mysqli_fetch_assoc($select)) {
	echo '<tr><td class="player-list" align="center">
	<button onClick="window.location.href=\'index.php?action=edit&page=freebies&id='.$row['id'].'\'" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
	<button onClick="window.location.href=\'index.php?action=delete&page=freebies&id='.$row['id'].'\'" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button></td>
	<td class="player-list" align="center">'.$row['word'].'</td>
	<td class="player-list" align="center">'.$row['color'].'</td>
	<td class="player-list" align="center">'.$row['amount'].'</td>
	<td class="player-list" align="center">'.$row['timestamp'].'</td></tr>';
}
echo "</table>\n";
echo "<br /><br />\n\n";
?>