<?php
#####################################
########## Add Form Action ##########
#####################################
if ( isset($_POST['submit']) ) {
    $cat = $sanitize->for_db($_POST['category']);
    $name = $sanitize->for_db($_POST['name']);

    $insert = $database->query("INSERT INTO `tcg_cards_cat` (`category`,`name`) VALUES ('$cat','$name')");

    if ($insert == TRUE) { $success[] = "The new category was successfully added to the database!"; }
    else { $error[] = "Sorry, there was an error and the category was not added. ".mysqli_error().""; }
}

echo '<h1>Categories <span class="fas fa-angle-right" aria-hidden="true"></span> Add a Card Category</h1>
<p>Use this form to add a new card category to the database. Use the <a href="index.php?page=categories">edit</a> form to update information for an existing card category.</p>
<center>';
if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }

echo '</center>
<form method="post" action="index.php?action=add&page=categories">
<table width="100%" cellpadding="5" cellspacing="3" border="0">
    <tr>
        <td width="10%" class="headSub">Category:</td><td width="40%" valign="middle"><input type="text" name="category" placeholder="numerical" style="width: 92%;" /></td>
        <td width="10%" class="headSub">Name:</td><td width="40%" valign="middle"><input type="text" name="name" placeholder="e.g. Puzzle" style="width: 92%;" /></td>
    </tr>
    <tr>
        <td colspan="4" valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Add Category" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td>
    </tr>
</table>
</form>';
?>
