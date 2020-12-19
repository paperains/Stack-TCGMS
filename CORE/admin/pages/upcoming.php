<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

########################################
########## Show Upcoming Page ##########
########################################
echo '<h1>Upcoming Decks</h1>
<p>Do you want to <a href="index.php?action=add&page=cards">add an upcoming deck</a>?</p>';
$sql = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Upcoming' ORDER BY `votes` DESC");
$count = mysqli_num_rows($sql);

if($count==0) {
    echo "There are currently no upcoming decks.\n";
    echo "<br /><br />\n\n";
} else {
    echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
    echo '<tr><td width="20%" class="record-label">Action</td><td width="40%" class="record-label">Filename</td><td width="10%" class="record-label">Category</td><td width="10%" class="record-label">Votes</td></tr>';
    while($row=mysqli_fetch_assoc($sql)) {
        $c = $row['category'];
        $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$c'");
        echo '<tr>
        <td class="player-list" align="center">
            <button onClick="window.location.href=\'index.php?action=edit&page=upcoming&id='.$row['id'].'\'" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
            <button onClick="window.location.href=\'index.php?action=release&page=upcoming&id='.$row['id'].'\'" class="btn-primary"><span class="fas fa-check" aria-hidden="true"></span></button> 
            <button onClick="window.location.href=\'index.php?action=delete&page=upcoming&id='.$row['id'].'\'" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button>
        </td>
        <td class="player-list" align="center">'.$row['deckname'].' ('.$row['filename'].')</td>
        <td class="player-list" align="center">'.$cat['name'].'</td>
        <td class="player-list" align="center">'.$row['votes'].'</td></tr>';
    }
    echo "</table>\n";
    echo "<br /><br />\n";
}
?>