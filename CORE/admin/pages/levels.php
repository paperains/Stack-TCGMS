<?php
######################################
########## Show Levels Page ##########
######################################
echo '<h1>Levels</h1>
<p>Below is the list of current levels for your TCG. Feel free to edit or delete the items that suits your own TCG setup.</p>
<table width="100%" cellspacing="0">
  <tr>
    <td width="10%" class="record-label">ID</td>
    <td width="10%" class="record-label">Level</td>
    <td width="30%" class="record-label">Level Name</td>
    <td width="20%" class="record-label"># of Cards</td>
    <td width="30%" class="record-label">Action</td>
 </tr>';

$sql = $database->query("SELECT * FROM `tcg_levels` ORDER BY `id` ASC");
while ($row = mysqli_fetch_assoc($sql)) {
  echo '<tr>
    <td align="center">'.$row['id'].'</td>
    <td align="center">'.$row['level'].'</td>
    <td align="center">'.$row['name'].'</td>
    <td align="center">'.$row['cards'].'</td>
    <td align="center">
      <input type="button" onClick="window.location.href=\'index.php?action=edit&page=levels&id='.$row['id'].'" class="btn-success" value="Edit" /> 
      <input type="button" onClick="window.location.href=\'index.php?action=delete&page=levels&id='.$row['id'].'" class="btn-warning" value="Delete" />
    </td>
  </tr>';
}

echo '</table>';
?>
