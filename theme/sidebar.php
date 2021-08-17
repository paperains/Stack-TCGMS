<h2 class="side-title">Statistics</h2>
<b>Owner:</b> <?php echo $tcgowner; ?><br />
<b>Status:</b> Upcoming<br />
<b>Prejoin:</b> Month 00, 0000<br />
<b>Opened:</b> Month 00, 0000<br />
<b>Members:</b> <?php echo $count->numAll('user_list','Active','usr'); ?><i>a</i> ( <?php echo $count->numAll('user_list','Pending','usr'); ?><i>p</i> / <?php echo $count->numAll('user_list','Hiatus','usr'); ?><i>h</i> / <?php echo $count->numAll('user_list','Inactive','usr'); ?><i>i</i> / <?php echo $count->numAll('user_list','Retired','usr'); ?><i>r</i> )<br />
<b># of Decks:</b> <?php echo $count->numCards('Active','1'); ?> (+<?php echo $count->numCards('Upcoming',''); ?> upcoming)

<?php
$b = $database->get_assoc("SELECT * FROM `tcg_blog` ORDER BY `post_date` DESC");
$blimit = $b['post_amount'];
if( $blimit = 0 ) {}
else {
    echo '<h2 class="side-title">New Releases</h2>
    <p align="center">Check out our <a href="<?php echo $tcgurl; ?>cards.php?view=upcoming">upcoming list</a> and vote which deck you want to be released next!</p>
    <marquee style="width:100%;" scrollamount="3" behavior="alternate">';
    $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active' ORDER BY `card_released` DESC LIMIT $blimit");
    while($new=mysqli_fetch_assoc($sql)) {
        $digits = rand(01,$new['count']);
        if ($digits < 10) { $_digits = "0$digits"; }
        else { $_digits = $digits; }
        $card = "$new[filename]$_digits";
        echo '<a href="'.$tcgurl.'cards.php?view=released&deck='.$new['card_filename'].'"><img src="'.$tcgcards.''.$card.'.'.$tcgext.'"></a>';
    }
    echo '</marquee>
    <p align="center">Also if you haven\'t yet, you can <a href="<?php echo $tcgurl; ?>services.php?form=deck-claims">donate</a> more unclaimed decks for this month.</p>';
}
?>

<?php if (!empty($login)) { ?>
<h2 class="side-title">Member Panel</h2>
<div class="post-game">
    <a href="<?php echo $tcgurl; ?>account.php">Account</a>
    <a href="<?php echo $tcgurl; ?>shoppe.php">Shoppe</a>
    <a href="<?php echo $tcgurl; ?>rewards.php?name=<?php echo $player; ?>">Rewards (<?php $count->numRewards(); ?>)</a>
    <a href="<?php echo $tcgurl; ?>messages.php?id=<?php echo $player; ?>&page=inbox">Messages (<?php echo $count->numMail(); ?>)</a>
    <a href="<?php echo $tcgurl; ?>account.php?do=logout">Logout</a>
</div>
<?php } else { ?>
<h2 class="side-title">Member Login</h2>
<p align="center">Kindly login your account in order to access the entire TCG.</p>
<form method="post" action="<?php echo $tcgurl; ?>account.php?do=login&action=loggedin" style="padding-bottom:1px;">
    <input type="text" name="username" placeholder="username@domain.tld" style="width:93%" /><br />
    <input type="password" name="password" placeholder="********" style="width:93%" /><br />
    <input type="submit" name="submit" value="Login" class="btn-success" />
    <?php if ( $settings->getValue( 'tcg_registration' ) == "0" ) {}
    else { echo '<input type="button" onClick="window.location.href=\'members.php?page=join\';" value="Register" class="btn-info" />'; } ?>
</form>
<?php } ?>

<?php
if( $settings->getValue( 'xtra_motm' ) == "0" ) {}
else {
?>
<h2 class="side-title">Member of the <?php echo $settings->getValue( 'xtra_motm_scope' ); ?></h2>
<p align="center">
    <?php
    $row = $database->get_assoc("SELECT * FROM `user_list_motm` WHERE `motm_date`='DEFINE CONDITION'");
    echo 'Congratulations, <em>'.$row['motm_name'].'</em>!<br /><br />';
    if( $row['motm_date'] == "DATE HERE" ) {
        echo '<img src="/images/cards/mc-'.$row['motm_name'].'.png" />';
    } else {
        echo '<img src="/images/cards/mc-filler.png" />';
    } ?>
    <br /><br />
    <?php if( $settings->getValue( 'xtra_motm_scope' ) == "Week" ) {
        echo 'You are the member for this week!<br />';
    } else {
        echo 'You are the member for the month of <u>'.date("F").'</u><br />';
    } ?>
    Do not forget to <a href="/games.php?play=motm">claim your rewards</a>!
</p>
<?php } ?>

<h2 class="side-title">Calendar</h2>
<?php include($tcgpath.'theme/calendar.php'); ?>
<!-- MAKE SURE TO CHANGE YOUR WEEKLY SCHEDULE -->
<p align="center">All deadlines are <?php echo $tcgname; ?>'s local time:
<iframe src="https://freesecure.timeanddate.com/clock/i7873tak/n145/fn16/fs11/fc58687d/tct/pct/ftb/tt0/tw1/tm3/td2/tb2" frameborder="0" width="100%" height="14" allowTransparency="true"></iframe>
</p>
<p align="center">Weekly updates every <i><?php echo $settings->getValues('update_scope'); ?></i> PHT!</p>

<?php
if( $settings->getValue( 'xtra_chatbox' ) == "0" ) {}
else {
?>
<h2 class="side-title">Chat Box</h2>
<center>Feel free to use the chatbox for quick inquiries ONLY if you don't have a Discord, otherwise use them for random discussions if you like.<br />
<iframe title="dwi-chat" src="<?php echo $tcgurl; ?>admin/chat.msg.php" width="100%" height="120" frameborder="0" scrolling="auto"></iframe>
<iframe title="dwi-form" src="<?php echo $tcgurl; ?>admin/chat.form.php" width="100%" height="80" frameborder="0" scrolling="no"></iframe>
</center>
<?php } ?>
