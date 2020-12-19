<?php
######################################
########## Edit Form Action ##########
######################################
if (isset($_POST['update'])) {
	$type = $sanitize->for_db($_POST['type']);
	$word = $sanitize->for_db($_POST['word']);
	$amnt = $sanitize->for_db($_POST['amount']);
	$color = $sanitize->for_db($_POST['color']);
	$id = $sanitize->for_db($_POST['id']);
	
	$result = $database->query("UPDATE `user_freebies` SET `name`='$name', `type`='$type', `word`='$word', `amount`='$amnt', `color`='$color' WHERE id='$id'") or print ("Can't update freebies.<br />" . mysqli_connect_error());

	header("Location: index.php?page=freebies");
}
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid freebie ID."); }
else { $id = (int)$_GET['id']; }

$sql = $database->query("SELECT * FROM `user_freebies` WHERE `id`='$id'") or print ("Can't select freebie.<br />" . $sql . "<br />" . mysqli_connect_error());

while ($row = mysqli_fetch_array($sql)) {
	$old_type = stripslashes($row['type']);
    $old_word = stripslashes($row['word']);
	$old_amnt = stripslashes($row['amount']);
    $old_color = stripslashes($row['color']);
}
echo '<h1>Freebies <span class="fas fa-angle-right" aria-hidden="true"></span> Edit Freebie</h1>
<form method="post" action="index.php?action=edit&page=freebies&id='.$id.'">
<input type="hidden" name="id" value="'.$id.'" />
<table width="100%" cellspacing="3">
<tr>
    <td class="headSub" width="15%">Type:</td><td valign="middle" width="35%"><select name="type" id="type" style="width:95%;" />
        <option value="'.$old_type.'">Current: '.$old_type.'</option>
        <option value="1">Spell Choice</option>
        <option value="2">Choice Pack</option>
        <option value="3">Random Pack</option>
        <option value="4">Color Choice</option>
    </select></td>
    <td class="headSub">Word:</td><td valign="middle"><input type="text" name="word" id="word" style="width:90%;" value="'.$old_word.'" /></td>
</tr>
<tr>
    <td class="headSub">Amount:</td><td valign="middle"><input type="text" name="amount" id="amount" style="width:90%;" value="'.$old_amnt.'" /></td>
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
</tr>
<tr><td valign="middle" colspan="4" align="center"><input type="submit" name="update" id="update" class="btn-success" value="Edit Freebie" /></td></tr>
</table>
</form>';
?>