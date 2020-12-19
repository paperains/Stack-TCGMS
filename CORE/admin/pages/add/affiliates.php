<?php
$img = (isset($_FILES['img']) ? $_FILES['img'] : null);
$file = (isset($_GET['name']) ? $_GET['name'] : null);

#####################################
########## Add Form Action ##########
#####################################
if ($stat == "added") {
    if (!isset($_POST['submit']) || $_SERVER['REQUEST_METHOD'] != "POST") { exit("<p>You did not press the submit button; this page should not be accessed directly.</p>"); }
    else {
        $check->Value();
        $name = $sanitize->for_db($_POST['owner']);
        $email = $sanitize->for_db($_POST['email']);
        $tcg = $sanitize->for_db($_POST['subject']);
        $url = $sanitize->for_db($_POST['url']);
        $status = $sanitize->for_db($_POST['status']);
        
        $img_desc = $uploads->reArrayFiles($img);
        $uploads->folderPath('images','aff');
        
        $insert = $database->query("INSERT INTO `tcg_affiliates` (`owner`, `email`, `subject`, `url`, `status`) VALUES ('$name', '$email', '$tcg', '$url', '$status')");
        
        if ($insert == TRUE) {
            $activity = '<span class="fas fa-globe" aria-hidden="true"></span> <a href="'.$url.'" target="_blank">'.$tcg.' TCG</a> has been added as Shizen\'s new affiliate.';
            $date = date("Y-m-d", strtotime("now"));
            $database->query("INSERT INTO `tcg_activities` (`name`,`activity`,`date`) VALUES ('$name','$activity','$date')");
            echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Success</h1>';
            echo '<p>The affiliate was successfully added to the database.</p>';
            echo "<p>Want to <a href=\"index.php?action=add&page=affiliates\">add</a> another?</p>";
        } else {
            echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Error</h1>';
            echo '<p>Sorry, there was an error and the affiliate was not added to the database.</p>';
            die("Error:". mysqli_connect_error());
        }
    }
} else {
    echo '<h1>Affiliates <span class="fas fa-angle-right" aria-hidden="true"></span> Add an Affiliate</h1>
    <p>Use this form to add an affiliate to the database. <b>If they have sent in a request, they are already in the database!</b> Use the <a href="index.php?page=affiliates">edit</a> form to edit an affiliate\'s information.</p>
    <form method="post" action="index.php?action=add&page=affiliates&stat=added" multipart="" enctype="multipart/form-data">
    <input type="hidden" name="status" value="Active" />
    <table width="100%" cellspacing="3">
    <tr>
        <td class="headSub" width="15%">Owner:</td>
        <td valign="middle" width="35%"><input type="text" name="owner" placeholder="Jane Doe" style="width:90%;" /></td>
        <td class="headSub" width="15%">Email:</td>
        <td valign="middle" width="35%"><input type="text" name="email" placeholder="username@domain.tld" style="width:90%;" /></td>
    </tr>
    <tr>
        <td class="headSub">Subject:</td>
        <td valign="middle"><input type="text" name="subject" placeholder="Name of the TCG" style="width:90%;" /></td>
        <td class="headSub">URL:</td>
        <td valign="middle"><input type="text" name="url" placeholder="http://" style="width:90%;" /></td>
    </tr>
    <tr>
        <td class="headSub">Upload Button:</td>
        <td valign="middle"><input type="file" name="img[]"></td>
        <td class="headSub">Proceed?</td>
        <td valign="middle" align="center"><input type="submit" name="submit" class="btn-success" value="Add Affiliate" /> <input type="reset" name="reset" class="btn-warning" value="Reset" /></td>
    </tr>
    </table>
    </form>';
} // END SHOW ADD FORM
$uploads->reArrayFiles($file);
?>