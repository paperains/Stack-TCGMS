<?php
#####################################
########## Add Form Action ##########
#####################################
if (isset($_POST['submit'])) {
	$name = $sanitize->for_db($_POST['name']);
	$type = $sanitize->for_db($_POST['type']);
	$word = $sanitize->for_db($_POST['word']);
	$amnt = $sanitize->for_db($_POST['amount']);
	$color = $sanitize->for_db($_POST['color']);
    $set = $sanitize->for_db($_POST['set']);
    $wish = $sanitize->for_db($_POST['wish']);
    $stat = $sanitize->for_db($_POST['status']);
    $date = date('Y-m-d', strtotime("now"));
    
    // Add wish blurbs for the database
    if ($type == "1" && !empty($word)) { $wish = "I wish for choice cards spelling ".$word."!"; }
    if ($type == "2" && !empty($amnt)) { $wish = "I wish for a pack of ".$amnt." choice cards!"; }
    if ($type == "3" && !empty($amnt)) { $wish = "I wish for a pack of ".$amnt." random cards!"; }
    if ($type == "4" && $color != "None") { $wish = "I wish for choice cards from any ".$color."-colored decks!"; }
    if ($type == "5" && $amnt == "2") { $wish = "I wish for a double deck release!"; }
    if ($type == "6" && $set == "None") { $wish = "I wish for double rewards for the ".$set." set!"; }
	
	$result = $database->query("INSERT INTO `user_wishes` ( `name`,`type`,`word`,`amount`,`color`,`game_set`,`wish`,`status`,`timestamp`) VALUES ('$name','$type','$word','$amnt','$color','$set','$wish','Pending','$date')") or print ("Can't add wish.<br />" . mysqli_connect_error());

	if ( !$result ) { $error[] = "Sorry, there was an error and the wish was not added to the database. ".mysqli_error().""; }
	else { $success[] = "You have successfully added a wish!"; }
}

echo '<h1>Wishes <span class="fas fa-angle-right" aria-hidden="true"></span> Add a Wish</h1>
<p>Make sure to only fill up the fields according to the wish type (e.g. Spell Choice should only have the Word field filled).</p>
<center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
echo '</center>
<form method="post" action="index.php?action=add&page=wishes">
<table width="100%" cellspacing="3">
<tr>
    <td class="headSub" width="15%">Type:</td><td valign="middle" width="35%"><select name="type" id="type" style="width:95%;" />
        <option value="1">Spell Choice</option>
        <option value="2">Choice Pack</option>
        <option value="3">Random Pack</option>
        <option value="4">Color Choice</option>
        <option value="5">Deck Release</option>
        <option value="6">Game Rewards</option>
    </select></td>
    <td class="headSub">Word:</td><td valign="middle"><input type="text" name="word" id="word" style="width:88%;" placeholder="SUMMER2020" /></td>
</tr>
<tr>
    <td class="headSub">Amount:</td><td valign="middle"><input type="text" name="amount" id="amount" style="width:88%;" placeholder="0" /></td>
    <td class="headSub">Category:</td><td valign="middle"><select name="category" id="category" style="width:95%;" />
    <option value="0">Not applicable</option>';
    $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
    for($i=1; $i<=$c; $i++) {
        $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
        echo '<option value="'.$i.'">'.$cat['name']."</option>\n";
    }
    echo '</select></td>
</tr>
<tr>
    <td class="headSub">Color:</td><td valign="middle"><select name="color" id="color" style="width:95%;" />
        <option value="None">Not applicable</option>
        <option value="Pink">Pink</option>
        <option value="Red">Red</option>
        <option value="Orange">Orange</option>
        <option value="Yellow">Yellow</option>
        <option value="Green">Green</option>
        <option value="Cyan">Cyan</option>
        <option value="Blue">Blue</option>
        <option value="Purple">Purple</option>
        <option value="Brown">Brown</option>
        <option value="Black">Black</option>
        <option value="White">White</option>
    </select></td>
    <td class="headSub">Game Set:</td><td valign="middle"><select name="set" id="set" style="width:95%;" />
        <option value="None">Not applicable</option>
        <option value="Weekly">Weekly Set</option>
        <option value="Set A">Bi-weekly A Set</option>
        <option value="Set B">Bi-weekly B Set</option>
        <option value="Monthly">Monthly Set</option>
    </select></td>
</tr>
<tr><td valign="middle" colspan="4" align="center"><input type="submit" name="submit" id="submit" class="btn-success" value="Add Wish" /></td></tr>
</table>
</form>';
?>