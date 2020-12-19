<?php
########################################
########## Show Donated Decks ##########
########################################
echo '<h1>Cards <span class="fas fa-angle-right" aria-hidden="true"></span> Donated Decks</h1>';
$sql = $database->query("SELECT * FROM `tcg_donations` ORDER BY `date` DESC");
$count = mysqli_num_rows($sql);
if($count==0) {
    echo "There are currently no donated decks.\n";
    echo "<br /><br />\n\n";
} else {
    echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
    echo '<tr><td width="20%" class="record-label">Action</td>
    <td width="25%" class="record-label">Filename</td>
    <td width="10%" class="record-label">Maker</td>
    <td width="10%" class="record-label">Category</td>
    <td width="20%" class="record-label">Series</td>
    <td width="10%" class="record-label">Date</td></tr>';
    while($row = mysqli_fetch_assoc($sql)) {
        echo '<tr><td class="player-list" align="center">
        <button onClick="window.location.href=\'index.php?action=add&page=donated&id='.$row['id'].'\'" class="btn-success"><span class="fas fa-user-tag" aria-hidden="true"></span></button> 
        <button onClick="window.location.href=\''.$row['url'].'\'" target="_blank" class="btn-primary"><span class="fas fa-download" aria-hidden="true"></span></button> 
        <button onClick="window.location.href=\'index.php?action=delete&page=donated&id='.$row['id'].'\'" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button></td>
        <td class="player-list" align="center">'.$row['deckname'].'</td>
        <td class="player-list" align="center">'.$row['maker'].'</td>
        <td class="player-list" align="center">'.$row['category'].'</td>
        <td class="player-list" align="center">'.$row['series'].'</td>
        <td class="player-list" align="center">'.$row['date'].'</td></tr>';
    }
    echo "</table>\n";
}
?>