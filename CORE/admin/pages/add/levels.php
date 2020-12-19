<?php
#####################################
########## Add Form Action ##########
#####################################
if ( isset($_POST['submit']) ) {
    $level = $sanitize->for_db($_POST['level']);
    $name = $sanitize->for_db($_POST['name']);
    $card = $sanitize->for_db($_POST['cards']);

    $insert = $database->query("INSERT INTO `tcg_levels` (`level`,`name`,`cards`) VALUES ('$level','$name','$card')");

    if ($insert == TRUE) { $success[] = "The new level set was successfully added to the database!"; }
    else { $error[] = "Sorry, there was an error and the level set was not added. ".mysqli_error().""; }
}

echo '<h1>Levels <span class="fas fa-angle-right" aria-hidden="true"></span> Add a New Level</h1>
<p>Use this form to add a new level set to the database. Use the <a href="index.php?page=levels">edit</a> form to update information for an existing level set.</p>
<center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }

echo '</center>
<form method="post" action="index.php?action=add&page=levels" multipart="" enctype="multipart/form-data">
<table width="100%" cellpadding="5" cellspacing="3" border="0">
    <tr>
        <td width="10%" class="headSub">Level:</td><td width="40%" valign="middle"><input type="text" name="level" placeholder="numerical" style="width: 92%;" /></td>
        <td width="10%" class="headSub">Name:</td><td width="40%" valign="middle"><input type="text" name="name" placeholder="e.g. Seedling" style="width: 92%;" /></td>
    </tr>
    <tr>
        <td class="headSub">Cards:</td><td valign="middle"><input type="text" name="card" style="width: 92%;" placeholder="amount of cards to gain" /></td>
        <td class="headSub">Proceed?</td><td valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Add Level" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td>
    </tr>
</table>
</form>';
?>
