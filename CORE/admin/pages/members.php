<?php
#######################################
########## Show Members Page ##########
#######################################
echo '<h1>Members</h1>
<p>&raquo; Need to email <a href="index.php?action=email&page=members-all">all members</a>?</p>
<h2>Pending</h2>';

$general->memList('Pending');

$l = $database->num_rows("SELECT * FROM `tcg_levels`");
for($i=1; $i<=$l; $i++) {
    $sql = $database->query("SELECT * FROM `user_list` WHERE `level`='$i' AND `status`='Active' ORDER BY `id` ASC");
    $count = $database->num_rows("SELECT * FROM `user_list` WHERE `level`='$i' AND `status`='Active' ORDER BY `id` ASC");
    $lvl = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `level`='$i'");
    //echo "<h2>$level[$i]</h2>\n";
    
    if($count==0) {
        //echo '<center>There are currently no members at this level.</center>';
        //echo '<br /><br />';
    }
    else {
        echo "<h2>".$lvl['name']." (Level ".$i.")</h2>\n";
        echo '<table width="100%" cellspacing="0" border="0">';
        echo '<tr><td width="5%" class="record-label">ID</td><td width="40%" class="record-label">Action</td><td width="15%" class="record-label">Name</td><td width="10%" class="record-label">Team</td><td width="15%" class="record-label">Registered</td><td width="10%" class="record-label">Referral</td></tr>';
        while($row = mysqli_fetch_assoc($sql)) {
            echo '<tr><td class="player-list">'.$row['id'].'</td><td class="player-list">
                <button onClick="window.location.href=\''.$row['url'].'\'" target="_blank" title="Visit Trade Post" class="btn-primary"><span class="fas fa-home" aria-hidden="true"></span></button>
                <button onClick="window.location.href=\'index.php?action=rewards&page=members&id='.$row['id'].'\'" title="Send Rewards" class="btn-primary"><span class="fas fa-gift" aria-hidden="true"></span></button>
                <button onClick="window.location.href=\'index.php?action=email&page=members&id='.$row['id'].'\'" title="Send Email" class="btn-primary"><span class="fas fa-envelope" aria-hidden="true"></span></button>
                <button onClick="window.location.href=\'index.php?page=logs&id='.$row['name'].'\'" title="View Trade Logs" class="btn-primary"><span class="fas fa-file" aria-hidden="true"></span></button>
                <button onClick="window.location.href=\'index.php?action=edit&page=members&id='.$row['id'].'\'" title="Edit Member" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button>
                <button onClick="window.location.href=\'index.php?action=delete&page=members&id='.$row['id'].'\'" title="Delete Member" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button>
            </td><td class="player-list">'.$row['name'].'</td><td class="player-list">'.$row['team'].'</td><td class="player-list">'.date("F d, Y", strtotime($row['regdate'])).'</td></td><td class="player-list">'.$row['refer'].'</td></tr>';
        }
        echo '</table>';
        echo '<br /><br />';
    }
}
?>

<h2>Inactive</h2>
<?php
$general->memList('Inactive');
?>

<h2>Hiatus</h2>
<?php
$general->memList('Hiatus');
?>

<h2>Retired</h2>
<?php
$general->memList('Retired');
?>