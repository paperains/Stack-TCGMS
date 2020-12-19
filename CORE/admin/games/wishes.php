<?php
/* Make sure to change the following according to your own setting:
 * 'GAME SET HERE' = e.g. 'Weekly'
 */

$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='GAME SET HERE' ORDER BY `id` DESC");
$logChk = $database->get_assoc("SELECT * FROM `logs_$player` WHERE `name`='$player' AND `title`='Wishes' AND `timestamp` >= '".$range['timestamp']."'");

if ($logChk['timestamp'] >= $range['timestamp']) {
    echo '<h1>Wishes : Halt!</h1>
    <p>You have already played this game! If you missed your rewards, here they are:</p>
    <center><b>'.$logChk['title'].':</b> '.$logChk['rewards'].'</center>';
} else {
    if( isset($_POST['spell']) ) {
        $check->Value();
        $type = $sanitize->for_db($_POST['type']);
        $name = $sanitize->for_db($_POST['name']);
        $word = $sanitize->for_db($_POST['word']);
        $today = date("Y-m-d", strtotime("now"));

        $wish = "I wish for choice cards spelling ".$word."!";
        $sql = $database->query("INSERT INTO `user_wishes` (`name`,`type`,`word`,`wish`,`status`,`timestamp`) VALUES ('$name','$type','$word','$wish','Pending','$today')");
        if ( !$sql ) { $error[] = "Sorry, there was an error and your wish was not submitted. ".mysqli_error().""; }
	else { $success[] = "Your wish has been received! Wait for to be granted~"; }

        echo '<h1>Wishes - Prize Pickup</h1>';
        echo '<center><p>Thank you for making a wish! Take everything you see below and don\'t forget to log it!</p>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        
	$getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wishes','','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wishes','','2','0','2','0','0'); }
    }
	
    if( isset($_POST['choice']) ) {
        $check->Value();
        $type = $sanitize->for_db($_POST['type']);
        $name = $sanitize->for_db($_POST['name']);
        $amnt = $sanitize->for_db($_POST['amount']);
        $today = date("Y-m-d", strtotime("now"));

        $wish = "I wish for a pack of ".$amnt." choice cards!";
        $sql = $database->query("INSERT INTO `user_wishes` (`name`,`type`,`amount`,`wish`,`status`,`timestamp`) VALUES ('$name','$type','$amnt','$wish','Pending','$today')");
        if ( !$sql ) { $error[] = "Sorry, there was an error and your wish was not submitted. ".mysqli_error().""; }
	else { $success[] = "Your wish has been received! Wait for to be granted~"; }

        echo '<h1>Wishes - Prize Pickup</h1>';
        echo '<center><p>Thank you for making a wish! Take everything you see below and don\'t forget to log it!</p>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        
	$getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wishes','','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wishes','','2','0','2','0','0'); }
    }
	
    if( isset($_POST['random']) ) {
        $check->Value();
        $type = $sanitize->for_db($_POST['type']);
        $name = $sanitize->for_db($_POST['name']);
        $amnt = $sanitize->for_db($_POST['amount']);
        $today = date("Y-m-d", strtotime("now"));

        $wish = "I wish for a pack of ".$amnt." random cards!";
        $sql = $database->query("INSERT INTO `user_wishes` (`name`,`type`,`amount`,`wish`,`status`,`timestamp`) VALUES ('$name','$type','$amnt','$wish','Pending','$today')");
        if ( !$sql ) { $error[] = "Sorry, there was an error and your wish was not submitted. ".mysqli_error().""; }
	else { $success[] = "Your wish has been received! Wait for to be granted~"; }

        echo '<h1>Wishes - Prize Pickup</h1>';
        echo '<center><p>Thank you for making a wish! Take everything you see below and don\'t forget to log it!</p>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        
	$getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wishes','','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wishes','','2','0','2','0','0'); }
    }
	
    if( isset($_POST['color']) ) {
        $check->Value();
        $type = $sanitize->for_db($_POST['type']);
        $name = $sanitize->for_db($_POST['name']);
        $colr = $sanitize->for_db($_POST['colr']);
        $today = date("Y-m-d", strtotime("now"));

        $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `category`='$colr'");
	$wish = "I wish for choice cards from any ".$cat['name']."-colored decks!";

        $sql = $database->query("INSERT INTO `user_wishes` (`name`,`type`,`color`,`wish`,`status`,`timestamp`) VALUES ('$name','$type','$colr','$wish','Pending','$today')");
        if ( !$sql ) { $error[] = "Sorry, there was an error and your wish was not submitted. ".mysqli_error().""; }
	else { $success[] = "Your wish has been received! Wait for to be granted~"; }

        echo '<h1>Wishes - Prize Pickup</h1>';
        echo '<center><p>Thank you for making a wish! Take everything you see below and don\'t forget to log it!</p>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        
	$getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wishes','','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wishes','','2','0','2','0','0'); }
    }
	
    if( isset($_POST['release']) ) {
        $check->Value();
        $type = $sanitize->for_db($_POST['type']);
        $name = $sanitize->for_db($_POST['name']);
        $amnt = $sanitize->for_db($_POST['amount']);
        $today = date("Y-m-d", strtotime("now"));

        $wish = "I wish for a double deck release!";
        $sql = $database->query("INSERT INTO `user_wishes` (`name`,`type`,`amount`,`wish`,`status`,`timestamp`) VALUES ('$name','$type','$amnt','$wish','Pending','$today')");
        if ( !$sql ) { $error[] = "Sorry, there was an error and your wish was not submitted. ".mysqli_error().""; }
	else { $success[] = "Your wish has been received! Wait for to be granted~"; }

        echo '<h1>Wishes - Prize Pickup</h1>';
        echo '<center><p>Thank you for making a wish! Take everything you see below and don\'t forget to log it!</p>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        
	$getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wishes','','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wishes','','2','0','2','0','0'); }
    }
	
    if( isset($_POST['rewards']) ) {
        $check->Value();
        $type = $sanitize->for_db($_POST['type']);
        $name = $sanitize->for_db($_POST['name']);
        $set = $sanitize->for_db($_POST['set']);
        $today = date("Y-m-d", strtotime("now"));

        $wish = "I wish for double rewards for the ".$set." set!";
        $sql = $database->query("INSERT INTO `user_wishes` (`name`,`type`,`set`,`wish`,`status`,`timestamp`) VALUES ('$name','$type','$set','$wish','Pending','$today')");
        if ( !$sql ) { $error[] = "Sorry, there was an error and your wish was not submitted. ".mysqli_error().""; }
	else { $success[] = "Your wish has been received! Wait for to be granted~"; }

        echo '<h1>Wishes - Prize Pickup</h1>';
        echo '<center><p>Thank you for making a wish! Take everything you see below and don\'t forget to log it!</p>';
        if ( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
        if ( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }
        
	$getWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."' AND `set`='GAME SET HERE'");
        if ($getWish['set'] == "GAME SET HERE") { $general->gamePrize('GAME SET HERE','Wishes','','4','0','4','0','0'); }
        else { $general->gamePrize('GAME SET HERE','Wishes','','2','0','2','0','0'); }
    }
?>

<h1>GAME SET HERE - Wishes</h1>
<img src="/admin/games/images/wishes.jpg" align="left" /> 
<p>Make a wish to the old oak and find out if your wish will be granted on the next update. The old oak will typically grant 3 wishes per official updates, but depending on the volume of wishes received, it may or may not grant any at all.</p>
<p>If you're running out of ideas what to wish for, take these for your reference:<br />
- Cards to be spell out as a title case word (e.g. SUMMER2020).<br />
- Choice cards based on deck colors (e.g. Decks from R/G/B colors).<br />
- Decks you want to be released next.<br />
- Double rewards for any game sets.<br />
- Random packs from any decks.</p>
<p>The old oak and our leaf fairy is looking forward to what you wish for! Choose the type of wish you want to submit via the tabs below:</p>
<ul class="tabs" data-persist="true">
    <li><a href="#spell">Spell</a></li>
    <li><a href="#choice">Choice</a></li>
    <li><a href="#random">Random</a></li>
    <li><a href="#color">Color</a></li>
    <li><a href="#release">Release</a></li>
    <li><a href="#rewards">Game Rewards</a></li>
</ul>
<div class="tabcontents">
    <div id="spell" align="center">
        <h2>Spell a Word</h2><p>Use this form to wish for spell choice cards!<br />Type only your <u>desired word</u>, nothing else.</p>
        <form method="post" action="/games.php?play=wishes">
        <input type="hidden" name="type" value="1" />
        <input type="hidden" name="name" value="<?php echo $player; ?>" />
        <input type="text" name="word" placeholder="Capital letters only (e.g. PREJOIN)" style="width:50%;" /> <input type="submit" name="spell" value="  Make a Wish!  " />
        </form>
    </div>
    <div id="choice" align="center">
        <h2>Choice Pack</h2><p>Use this form to wish for a certain amount of choice card packs!<br />You can only wish for a <u>minimum of 5 cards</u> and a <u>maximum of 10 cards</u>.</p>
        <form method="post" action="/games.php?play=wishes">
        <input type="hidden" name="type" value="2" />
        <input type="hidden" name="name" value="<?php echo $player; ?>" />
        <input type="number" name="amount" min="5" max="10" style="width:50%;" /> <input type="submit" name="choice" value="  Make a Wish!  " />
        </form>
    </div>
    <div id="random" align="center">
        <h2>Random Pack</h2><p>Use this form to wish for a certain amount of random card packs!<br />You can only wish for a <u>minimum of 5 cards</u> and a <u>maximum of 10 cards</u>.</p>
        <form method="post" action="/games.php?play=wishes">
        <input type="hidden" name="type" value="3" />
        <input type="hidden" name="name" value="<?php echo $player; ?>" />
        <input type="number" name="amount" min="5" max="10" style="width:50%;" /> <input type="submit" name="random" value="  Make a Wish!  " />
        </form>
    </div>
    <div id="color" align="center">
        <h2>Choice Color</h2><p>Use this form to wish for cards according to its deck color!<br />You can only wish for <u>one color</u> at a time for <u>3 cards</u>.</p>
        <form method="post" action="/games.php?play=wishes">
        <input type="hidden" name="type" value="4" />
        <input type="hidden" name="name" value="<?php echo $player; ?>" />
        <select name="colr" style="width:50%;">
        <?php
        $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
        for($i=1;$i<=$c;$i++) {
            $cat = $database->query("SELECT * FROM `tcg_cards_cat` WHERE `category`='$i'");
            while($row = mysqli_fetch_assoc($cat)) {
                echo '<option value="'.$i.'">'.$row['name'].'</option>';
            }
        }
        ?>
        </select> <input type="submit" name="color" value="  Make a Wish!  " />
        </form>
    </div>
    <div id="release" align="center">
        <h2>Double Release</h2><p>Click the button to wish for a <u>double</u> deck release!<br />We can only grant twice the amount of regular release for now.</p>
        <form method="post" action="/games.php?play=wishes">
        <input type="hidden" name="type" value="5" />
        <input type="hidden" name="amount" value="2" />
        <input type="hidden" name="name" value="<?php echo $player; ?>" />
        <input type="submit" name="release" value="  Make a Wish!  " />
        </form>
    </div>
    <div id="rewards" align="center">
        <h2>Game Rewards</h2><p>Choose a game set from the dropdown for <u>double rewards</u>!<br />We can only grant twice the amount of game rewards for now.</p>
        <form method="post" action="/games.php?play=wishes">
        <input type="hidden" name="type" value="6" />
        <input type="hidden" name="name" value="<?php echo $player; ?>" />
        <select name="set" style="width:50%;">
            <option value="Weekly">Weekly</option>
            <option value="Set A">Bi-weekly A</option>
            <option value="Set B">Bi-weekly B</option>
            <option value="Monthly">Monthly</option>
        </select> <input type="submit" name="rewards" value="  Make a Wish!  " />
        </form>
    </div>
</div>
<?php
}
?>
