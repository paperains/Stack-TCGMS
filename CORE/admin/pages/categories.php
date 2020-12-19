<?php
##########################################
########## Show Categories Page ##########
##########################################
echo '<h1>Categories</h1>
<p>Below is the list of current categories for your card decks. Feel free to edit or delete the items that suits your own TCG setup.</p>
<table width="100%" cellspacing="0">
  <tr>
    <td width="10%" class="record-label">ID</td>
    <td width="10%" class="record-label">Category</td>
    <td width="50%" class="record-label">Category Name</td>
    <td width="30%" class="record-label">Action</td>
 </tr>';

$sql = $database->query("SELECT * FROM `tcg_cards_cat` ORDER BY `id` ASC");
while ($row = mysqli_fetch_assoc($sql)) {
  echo '<tr>
    <td align="center">'.$row['id'].'</td>
    <td align="center">'.$row['category'].'</td>
    <td align="center">'.$row['name'].'</td>
    <td align="center">
      <input type="button" onClick="window.location.href=\'index.php?action=edit&page=categories&id='.$row['id'].'" class="btn-success" value="Edit" /> 
      <input type="button" onClick="window.location.href=\'index.php?action=delete&page=categories&id='.$row['id'].'" class="btn-warning" value="Delete" />
    </td>
  </tr>';
}

echo '</table>';
?>
