<?php
include('admin/class.lib.php');
include($header);

if (empty($page)) {
?>

<h1>Information</h1>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<div class="statLink">
    <a href="/site.php?page=affiliates">In and Out</a>
    <a href="">Level Badges</a>
    <a href="">Randomizers</a>
    <a href="/site.php?page=credits">Credits</a>
</div>
<?php
}

else if ($page == "affiliates") {
    echo '<h1>Link Buttons</h1>
    <p>Kindly feel free to use the buttons below to link back to the TCG. <u>We do not allow direct-linking here</u>, so if you can (and you must), please upload them to your own server.</p>
    <center><div style="display:block;width:600px;height:100px;overflow:auto;text-align:center;">
        <b>IMAGE CODE:</b><br />
        <code>&lt;a href="'.$tcgurl.'" target="_blank"&gt;&lt;img src="path/to/button.png" border="0"&gt;&lt;/a&gt;</code><br /><br />
        <b>TEXT CODE:</b><br />
        <code>&lt;a href="'.$tcgurl.'" target="_blank"&gt;I\'m a '.$tcgname.' member!&lt;/a&gt;</code>
    </div><br />
    <img src="/images/100x35A.png" /> <img src="/images/100x35B.png" /> <img src="/images/100x35C.png" /> <img src="/images/100x35D.png" /> <img src="/images/100x35E.png" /><br />
    <img src="/images/100x35F.png" /> <img src="/images/100x35G.png" /> <img src="/images/100x35H.png" /> <img src="/images/100x35I.png" /> <img src="/images/100x35J.png" /></center>
    
    <h2>Affiliates</h2>
    <p>If you want to become an affiliate of this TCG, kindly please fill up the form below. Make sure to upload your button that is <u>100x35 pixels</u> in size and name it after your TCG (e.g. <code>Elements.png</code>), otherwise your button will not be displayed.</p>
    <center>';
    $sql = $database->query("SELECT * FROM `tcg_affiliates` WHERE `status`='Active' ORDER BY `subject` ASC");
    $count = $database->num_rows("SELECT * FROM `tcg_affiliates` WHERE `status`='Active' ORDER BY `subject` ASC");
    if ($count == 0) { echo '<p>There are currently no affiliates, want to become one?</p>'; }
    else {
        while($row = mysqli_fetch_assoc($sql)) {
            echo '<a href="'.$row['url'].'" target="_blank" title="'.$row['subject'].' TCG by '.$row['owner'].'"><img src="/images/aff/'.$row['subject'].'.png" /></a> ';
        }
    }
    echo '</center><br />';
    if ( isset($_POST['submit']) ) {
        $check->Value();
        $owner = $sanitize->for_db($_POST['owner']);
        $email = $sanitize->for_db($_POST['email']);
        $url = $sanitize->for_db($_POST['url']);
        $subject = $sanitize->for_db($_POST['subject']);

        $img_desc = $uploads->reArrayFiles($img);
        $uploads->folderPath('images','aff');

        $date = date("Y-m-d", strtotime("now"));

        $insert = $database->query("INSERT INTO `tcg_affiliates` (`owner`,`email`,`subject`,`url`,`status`,`timestamp`) VALUES ('$owner','$email','$subject','$url','Pending','$date')");
        if ( !$insert ) { echo '<center><div class="box-error"><b>Error!</b> There was an error while processing your form.</div></center><br />'; }
        else { echo '<center><div class="box-success"><b>Success!</b> Your affiliation has been added and will be approved once checked.</div></center><br />'; }
    }
    echo '<form method="post" action="site.php?page=affiliates" multipart="" enctype="multipart/form-data">
    <table width="100%" cellspacing="3" class="border">
        <tr>
            <td class="headLine" width="15%">Owner:</td><td class="tableBody" width="35%"><input type="text" name="owner" placeholder="Jane Doe" style="width:90%;"></td>
            <td class="headLine" width="15%">Email:</td><td class="tableBody" width="35%"><input type="text" name="email" placeholder="username@domain.tld" style="width:90%;"></td>
        </tr>
        <tr>
            <td class="headLine">TCG Name:</td><td class="tableBody"><input type="text" name="subject" placeholder="e.g. Hanayaka" style="width:90%;"></td>
            <td class="headLine">TCG URL:</td><td class="tableBody"><input type="text" name="url" placeholder="http://" style="width:90%;"></td>
        </tr>
        <tr>
            <td class="headLine">Button:</td><td class="tableBody"><input type="file" name="img[]" style="width:90%;"></td>
            <td class="tableBody" colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Become an affiliate"> <input type="reset" name="reset" class="btn-cancel" value="Reset"></td>
        </tr>
    </table>
    </form>';
}

else if ($page == "credits") {
    echo '<h1>Credits</h1>
    <p>Below are the list of resources, ideas and help from some fellow TCG owners that made this TCG complete and wonderful as it is.</p>
    <table width="100%" cellspacing="0" class="table table-bordered table-striped">
        <tr><td colspan="3"><b>Resources</b></td></tr>
        <tr>
            <td width="30%" align="center">Aki & Rheanna</td>
            <td width="60%" align="center">CORE Trading Card Game Management System</td>
            <td width="10%" align="center">[ <a href="https://www.reijou.net/" target="_blank"><code>WWW</code></a> ]</td>
        </tr>
        <tr>
            <td align="center">In The Cards</td>
            <td align="center">Password gates and slots games <b>*</b></td>
            <td align="center">[ <a href="http://inthecards.neo-romance.net/" target="_blank"><code>WWW</code></a> ]</td>
        </tr>
        <tr>
            <td align="center">Taty</td>
            <td align="center">Puzzle and memory scripts</td>
            <td align="center">[ <a href="http://tcg.bombilate.net/scripts.php" target="_blank"><code>WWW</code></a> ]</td>
        </tr>
        <tr>
            <td align="center">Nina</td>
            <td align="center">Melting pot and card claim scripts <b>*</b></td>
            <td align="center">[ <a href="http://nation.magical-me.net/index.php?action=downloads" target="_blank"><code>WWW</code></a> ]</td>
        </tr>
        <tr>
            <td align="center">Christina</td>
            <td align="center">Broadcast (News System) <b>*</b></td>
            <td align="center">[ <a href="http://tcg-publicity.com/" target="_blank"><code>WWW</code></a> ]</td>
        </tr>
        <tr>
            <td align="center">The JavaScript Source</td>
            <td align="center">Game scripts <b>*</b></td>
            <td align="center">[ <a href="https://javascriptsource.com/snippet/games/" target="_blank"><code>WWW</code></a> ]</td>
        </tr>
        <tr><td colspan="3"><b>Coding Help</b></td></tr>
        <tr>
            <td align="center">Rizu</td>
            <td align="center">Automatic game updater, PHP ideas</td>
            <td align="center">[ <a href="http://www.haltfate.org/" target="_blank"><code>WWW</code></a> ]</td>
        </tr>
        <tr><td colspan="3"><i>Resources marked with an asterisk (*) are modified by Aki.</i></td></tr>
    </table>';
}
include($footer);
?>