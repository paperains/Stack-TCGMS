<?php
######################################
########## Edit Form Action ##########
######################################
if (isset($_POST['update'])) {
	$name = $sanitize->for_db($_POST['name']);
	$type = $sanitize->for_db($_POST['type']);
	$word = $sanitize->for_db($_POST['word']);
	$amnt = $sanitize->for_db($_POST['amount']);
	$color = $sanitize->for_db($_POST['color']);
    $series = $sanitize->for_db($_POST['series']);
    $wish = $sanitize->for_db($_POST['wish']);
    $stat = $sanitize->for_db($_POST['status']);
	$id = $sanitize->for_db($_POST['id']);
	
	$result = $database->query("UPDATE `user_wishes` SET `name`='$name', `type`='$type', `word`='$word', `amount`='$amnt', `color`='$color', `series`='$series', `wish`='$wish', `status`='$stat' WHERE id='$id'") or print ("Can't update wish.<br />" . mysqli_connect_error());

	header("Location: index.php?page=wishes");
}
	
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid entry ID."); }
else { $id = (int)$_GET['id']; }

$sql = $database->query("SELECT * FROM `user_wishes` WHERE `id`='$id'") or print ("Can't select entry.<br />" . $sql . "<br />" . mysqli_connect_error());

while ($row = mysqli_fetch_array($sql)) {
	$old_name = stripslashes($row['name']);
	$old_stat = stripslashes($row['status']);
	$old_wish = stripslashes($row['wish']);
	$old_type = stripslashes($row['type']);
    $old_word = stripslashes($row['word']);
	$old_amnt = stripslashes($row['amount']);
    $old_color = stripslashes($row['color']);
    $old_series = stripslashes($row['series']);
}

echo '<h1>Wishes <span class="fas fa-angle-right" aria-hidden="true"></span> Edit Wish</h1>
<form method="post" action="index.php?action=edit&page=wishes&id='.$id.'">
<input type="hidden" name="id" value="'.$id.'" />
<table width="100%" cellspacing="3">
<tr>
    <td class="headSub" width="15%">Wished by:</td><td valign="middle" width="35%"><input type="text" name="name" id="name" style="width:90%;" value="'.$old_name.'" /></td>
    <td class="headSub" width="15%">Type:</td><td valign="middle" width="35%"><select name="type" id="type" style="width:95%;" />
        <option value="'.$old_type.'">Current: '.$old_type.'</option>
        <option value="1">Spell Choice</option>
        <option value="2">Choice Pack</option>
        <option value="3">Random Pack</option>
        <option value="4">Color Choice</option>
        <option value="5">Deck Release</option>
        <option value="6">Game Rewards</option>
    </select></td>
</tr>
<tr>
    <td class="headSub">Word:</td><td valign="middle"><input type="text" name="word" id="word" style="width:90%;" value="'.$old_word.'" /></td>
    <td class="headSub">Amount:</td><td valign="middle"><input type="text" name="amount" id="amount" style="width:90%;" value="'.$old_amnt.'" /></td>
</tr>
<tr>
    <td class="headSub">Color:</td><td valign="middle"><select name="color" id="color" style="width:95%;" />';
    if ($old_color == 0) { echo '<option value="0">Current: None</option>'; }
    else {
        $get = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='".$old_color."'");
        echo '<option value="'.$get['category'].'">Current: '.$get['name'].'</option>';
    }
    $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
    for($i=1; $i<=$c; $i++) {
        $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
        echo '<option value="'.$i.'">'.$cat['name']."</option>\n";
    }
    echo '</select></td>
    <td class="headSub">Series:</td><td valign="middle"><input type="text" name="series" id="series" style="width:90%;" value="'.$old_series.'" /></td>
</tr>
<tr><td class="headSub">Wish:</td><td valign="middle" colspan="3"><textarea rows="3" style="width:95%;" name="wish" id="wish">'.$old_wish.'</textarea></td></tr>
<tr>
    <td class="headSub">Status:</td><td valign="middle"><select name="status" id="status" style="width:95%;">
        <option value="'.$old_stat.'">'.$old_stat.'</option>
        <option value="Pending">Pending</option>
        <option value="Granted">Granted</option>
    </select></td>
    <td valign="middle" colspan="2" align="center"><input type="submit" name="update" id="update" class="btn-success" value="Submit" /></td></tr>
</table>
</form>';
?>