<?php
###########################################
########## Show Event Cards Page ##########
###########################################
echo '<h1>Event Cards</h1>
<p>Do you want to <a href="index.php?action=add&page=events">add an event card</a>?</p>';

$sql = $database->query("SELECT * FROM `tcg_cards_event` ORDER BY `released` DESC");
$count = $database->num_rows("SELECT * FROM `tcg_cards_event`");
if($count==0) {
    echo "There are currently no event cards added.\n";
    echo "<br /><br />\n\n";
} else {
    echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
    echo '<tr><td width="10%" class="record-label">Action</td><td width="30%" class="record-label">Filename</td><td width="10%" class="record-label">Made by</td><td width="10%" class="record-label">Released</td></tr>';
    while($row=mysqli_fetch_assoc($sql)) {
        echo '<tr>
        <td class="player-list"><button onClick="window.location.href=\'index.php?action=edit&page=events&id='.$row['id'].'\'" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
        <button onClick="window.location.href=\'index.php?action=delete&page=events&id='.$row['id'].'\'" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button></td>
        <td class="player-list">'.$row['title'].' ('.$row['filename'].')</td>
        <td class="player-list">'.$row['maker'].'</td><td class="player-list">'.$row['released'].'</td></tr>';
    }
    echo '</table>';
}
?>