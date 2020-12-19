<?php
##########################################
########## Show Affiliates Page ##########
##########################################
echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Affiliates</h1>
<p>&raquo; Need to email <a href="index.php?action=email&page=all-affiliates">all affiliates</a>?</p>';
for($i=1; $i<=$num_affiliates; $i++) {
	$sql = $database->query("SELECT * FROM `tcg_affiliates` WHERE `status`='$affiliates[$i]' ORDER BY `owner`");
	$count = $database->num_rows("SELECT * FROM `tcg_affiliates` WHERE `status`='$affiliates[$i]' ORDER BY `owner`");
	echo "<h2>$affiliates[$i] Affiliates</h2>\n";
	
	if($count==0) {
		echo "There are currently no affiliates in this category.\n";
		echo "<br /><br />\n\n";
	}
	else {
		echo '<table width="100%" cellspacing="0">';
		echo '<tr><td width="35%" class="record-label">Owner</td><td width="15%" class="record-label">Affiliate</td><td width="30%" class="record-label">Action</td></tr>';
		while($row=mysqli_fetch_assoc($sql)) {
			echo '<tr><td class="player-list" align="center">'.$row['owner'].' of '.$row['subject'].' TCG</td>';
			echo '<td class="player-list"><a href="'.$row['url'].'" target="_blank"><img src="/images/aff/'.$row['subject'].'.png" title="'.$row['subject'].' TCG" alt="'.$row['subject'].' TCG"></a></td><td class="player-list">';
            if($row['status']=="Pending") {
				echo '<input type="button" onClick="window.location.href=\'index.php?action=approve&page=affiliates&id='.$row['id'].'\'" class="btn-success" value="Approve" /> ';
			}
            echo '<input type="button" onClick="window.location.href=\'index.php?action=email&page=affiliates&id='.$row['id'].'\'" class="btn-success" value="Send Email" /> <input type="button" onClick="window.location.href=\'index.php?action=edit&page=affiliates&id='.$row['id'].'\'" class="btn-success" value="Edit" /"> <input type="button" onClick="window.location.href=\'index.php?action=delete&page=affiliates&id='.$row['id'].'\'" class="btn-warning" value="Delete" /></td></tr>';
		}
		echo '</table>';
		echo '<br /><br />';
	}
}
?>