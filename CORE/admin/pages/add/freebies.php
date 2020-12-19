<?php
#####################################
########## Add Form Action ##########
#####################################
if (isset($_POST['submit'])) {
	$type = $sanitize->for_db($_POST['type']);
	$word = $sanitize->for_db($_POST['word']);
	$amnt = $sanitize->for_db($_POST['amount']);
	$color = $sanitize->for_db($_POST['color']);
    $date = date('Y-m-d', strtotime("now"));
	
	$result = $database->query("INSERT INTO `user_freebies` (`type`,`word`,`amount`,`color`,`timestamp`) VALUES ('$type','$word','$amnt','$color','$date')") or print ("Can't update freebies.<br />" . mysqli_connect_error());

	if ( !$result ) { $error[] = "Sorry, there was an error and the freebie was not added to the database. ".mysqli_error().""; }
	else { $success[] = "You have successfully added a freebie!"; }
}

echo '<h1>Freebies <span class="fas fa-angle-right" aria-hidden="true"></span> Add a Freebie</h1>
<center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
echo '</center>
<form method="post" action="index.php?action=add&page=freebies">
<table width="100%" cellspacing="3">
<tr>
    <td class="headSub" width="15%">Type:</td><td valign="middle" width="35%"><select name="type" id="type" style="width:95%;" />
        <option value="1">Spell Choice</option>
        <option value="2">Choice Pack</option>
        <option value="3">Random Pack</option>
        <option value="4">Color Choice</option>
    </select></td>
    <td class="headSub">Word:</td><td valign="middle"><input type="text" name="word" id="word" style="width:90%;" placeholder="SUMMER2020" /></td>
</tr>
<tr>
    <td class="headSub">Amount:</td><td valign="middle"><input type="text" name="amount" id="amount" style="width:90%;" placeholder="0" /></td>
    <td class="headSub">Color:</td><td valign="middle"><select name="color" id="color" style="width:95%;" />
    <option value="0">Not applicable</option>';
    $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
    for($i=1; $i<=$c; $i++) {
        $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
        echo '<option value="'.$i.'">'.$cat['name']."</option>\n";
    }
    echo '</select></td>
</tr>
<tr><td valign="middle" colspan="4" align="center"><input type="submit" name="submit" id="submit" class="btn-success" value="Add Freebie" /></td></tr>
</table>
</form>';
?>