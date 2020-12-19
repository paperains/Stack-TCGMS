<?php
#####################################
########## Show Cards Page ##########
#####################################
echo '<h1>Card Decks</h1>
<p>Do you want to <a href="index.php?action=add&page=cards">add an upcoming deck</a>?</p>
<center><a href="index.php?page=upcoming">View Upcoming Decks?</a> | <a href="index.php?page=donated">View Donated Decks?</a></center>';

$c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
for($i=1; $i<=$c; $i++) {
    $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `category`='$i' AND `status`='Active' ORDER BY `filename`");
    $count = $database->num_rows("SELECT * FROM `tcg_cards` WHERE `category`='$i' AND `status`='Active' ORDER BY `filename`");
    $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
    echo "<h2>".$cat['name']."</h2>\n";
    
    if($count==0) {
        echo "There are currently no card decks in this category.\n";
        echo "<br /><br />\n\n";
    } else {
        echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
        echo '<tr><td width="10%" class="record-label">Action</td><td width="30%" class="record-label">Filename</td><td width="10%" class="record-label">Made/Donated by</td><td width="10%" class="record-label">Released</td><td width="5%" class="record-label"># / $</td></tr>';
        while($row=mysqli_fetch_assoc($sql)) {
            echo '<tr><td class="player-list">
                <button onClick="window.location.href=\'index.php?action=edit&page=cards&id='.$row['id'].'\'" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
                <button onClick="window.location.href=\'index.php?action=delete&page=cards&id='.$row['id'].'\'" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button>
            </td>
            <td class="player-list">'.$row['deckname'].' ('.$row['filename'].')</td>
            <td class="player-list">'.$row['maker'].' / '.$row['donator'].'</td>
            <td class="player-list">'.$row['released'].'</td>
            <td class="player-list">'.$row['count'].'/'.$row['worth'].'</td></tr>';
        }
        echo '</table>';
        echo '<br /><br />';
    }
}
?>