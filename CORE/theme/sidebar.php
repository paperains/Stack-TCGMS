<h2 class="side-title">Statistics</h2>
<b>Owner:</b> <?php echo $tcgowner; ?><br />
<b>Status:</b> Upcoming<br />
<b>Prejoin:</b> Month 00, 0000<br />
<b>Opened:</b> Month 00, 0000<br />
<b>Members:</b> <?php echo $counts->numAll('user_list','Active'); ?><i>a</i> ( <?php echo $counts->numAll('user_list','Pending'); ?><i>p</i> / <?php echo $counts->numAll('user_list','Hiatus'); ?><i>h</i> / <?php echo $counts->numAll('user_list','Inactive'); ?><i>i</i> / <?php echo $counts->numAll('user_list','Retired'); ?><i>r</i> )
<b># of Decks:</b> <?php echo $counts->numCards('Active','1'); ?> (+<?php echo $counts->numCards('Upcoming',''); ?> upcoming)

<h2 class="side-title">New Releases</h2>
<p align="center">Check out our <a href="/cards.php?view=upcoming">upcoming list</a> and vote which deck you want to be released next!</p>
<marquee style="width:100%;" scrollamount="3" behavior="alternate">
<?php
$b = $database->get_assoc("SELECT * FROM `tcg_blog` ORDER BY `timestamp` DESC");
$blimit = $b['amount'];
$sql = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active' ORDER BY `released` DESC LIMIT $blimit");
while($new=mysqli_fetch_assoc($sql)) {
    $digits = rand(01,$new['count']);
    if ($digits < 10) { $_digits = "0$digits"; }
    else { $_digits = $digits; }
    $card = "$new[filename]$_digits";
    echo '<a href="/cards.php?view=released&deck='.$new['filename'].'"><img src="/images/cards/'.$card.'.png"></a>';
}
?>
</marquee>
<p align="center">Also if you haven't yet, you can <a href="/services.php?form=deck-claims">donate</a> more unclaimed decks for this month.</p>

<?php if (!empty($login)) { ?>
<h2 class="side-title">Member Panel</h2>
<div class="post-game">
	<a href="/account.php">Account</a>
	<a href="/shoppe.php">Shoppe</a>
	<a href="/rewards.php?name=<?php echo $player; ?>">Rewards (<?php $count->numRewards(); ?>)</a>
	<a href="/messages.php?id=<?php echo $player; ?>&page=inbox">Messages (<?php $count->numMail(); ?>)</a>
	<a href="/account.php?do=logout">Logout</a>
</div>
<?php } else { ?>
<h2 class="side-title">Member Login</h2>
<p align="center">Kindly login your account in order to access the entire TCG.</p>
<form method="post" action="account.php?do=login&action=loggedin" style="padding-bottom:1px;">
	<input type="text" name="username" placeholder="username@domain.tld" style="width:93%" /><br />
	<input type="password" name="password" placeholder="********" style="width:93%" /><br />
	<input type="submit" name="submit" value="Login" class="btn-success" />
	<input type="button" onClick="window.location.href='members.php?page=join';" value="Register" class="btn-info" />
</form>
<?php } ?>

<h2 class="side-title">Calendar</h2>
<?php include('/theme/calendar.php'); ?>
<!-- MAKE SURE TO CHANGE YOUR WEEKLY SCHEDULE -->
<p align="center">All deadlines are <?php echo $tcgname; ?>'s local time:
<iframe src="https://freesecure.timeanddate.com/clock/i7873tak/n145/fn16/fs11/fc58687d/tct/pct/ftb/tt0/tw1/tm3/td2/tb2" frameborder="0" width="100%" height="14" allowTransparency="true"></iframe>
</p>
<p align="center">Weekly updates every <i>Fridays</i> PHT!</p>

<h2 class="side-title">Chat Box</h2>
<center>Feel free to use the chatbox for quick inquiries ONLY if you don't have a Discord, otherwise use them for random discussions if you like.<br />
<iframe title="dwi-chat" src="<?php echo $tcgurl; ?>admin/chat.msg.php" width="100%" height="120" frameborder="0" scrolling="auto"></iframe>
<iframe title="dwi-form" src="<?php echo $tcgurl; ?>admin/chat.form.php" width="100%" height="80" frameborder="0" scrolling="no"></iframe>
</center>