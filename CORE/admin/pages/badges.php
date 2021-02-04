<?php
############################################
########## Show Level Badges Page ##########
############################################
echo '<h1>Level Badges</h1>
<p>&raquo; Do you want to <a href="index.php?action=add&page=badges">add a level badge</a>?</p>';
$sql = $database->query("SELECT * FROM `tcg_levels_badge` ORDER BY `donator`");
echo '<table width="100%" cellspacing="0">';
echo '<tr><td width="35%" class="record-label">Donator</td><td width="15%" class="record-label">Size</td><td width="30%" class="record-label">Action</td></tr>';
while($row=mysqli_fetch_assoc($sql)) {
	echo '<tr><td class="player-list" align="center">'.$row['donator'].' ('.$row['set'].')</td>';
	echo '<td class="player-list">'.$row['width'].' x '.$row['height'].' pixels</td>
	<td class="player-list">
        <button onClick="window.location.href=\'index.php?action=edit&page=badges&id='.$row['id'].'\'" class="btn-success" />Edit</button> 
        <button onClick="window.location.href=\'index.php?action=delete&page=badges&id='.$row['id'].'\'" class="btn-warning" />Delete</button>
    </td></tr>';
}
echo '</table>';
?>
