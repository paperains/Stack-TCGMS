<?php
include('class.lib.php');

$database = new Database;
$sanitize = new Sanitize;

$today = date("Y-m-d", strtotime("now"));
$dayoftheweek = date("l");

/* Update weekly and bi-weekly date first
 * Change the all the Saturdays to your TCG's wweekly update day
 */
if($dayoftheweek == 'Saturday') {
  $database->query("INSERT INTO `tcg_games` (`sets`,`timestamp`) VALUES ('Weekly','$today')");

  $getA = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Set A' ORDER BY `id` DESC LIMIT 1");
  $getB = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Set B' ORDER BY `id` DESC LIMIT 1");
  $weekly = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Weekly' ORDER BY `id` DESC LIMIT 1");

  $last = date("Y-m-d", strtotime("-1 week last Saturday"));

  if ($getA['timestamp'] == $last) {
    $database->query("INSERT INTO `tcg_games` (`sets`,`timestamp`) VALUES ('Set A','$today')");
  }

  else if ($getB['timestamp'] == $last) {
    $database->query("INSERT INTO `tcg_games` (`sets`,`timestamp`) VALUES ('Set B','$today')");
  }
}

// Grant wishes
$range = $database->get_assoc("SELECT * FROM `tcg_games` WHERE `sets`='Weekly' ORDER BY `id` DESC LIMIT 1");
$wish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Pending' AND `timestamp` <= '".$range['timestamp']."'");
if ($wish['timestamp'] <= $range['timestamp']) {
    $database->query("UPDATE `user_wishes` SET `status`='Granted', `timestamp`='$today' WHERE `status`='Pending' AND `timestamp` <= '".$range['timestamp']."' ORDER BY `id` ASC LIMIT 3");
}

/* Release decks
 * For some reason the double deck release isn't working properly.
 * Seems like it can't define which wish has the type 5.
 */
$eWish = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='$today' LIMIT 1");
$x = $eWish['type'];
if ($x == "5") {
	$database->query("UPDATE `tcg_cards` SET `status`='Active', `released`='$today', `votes`='0' WHERE `status`='Upcoming' AND `votes` >= 2 ORDER BY `votes` DESC LIMIT 8");
} else {
  $database->query("UPDATE `tcg_cards` SET `status`='Active', `released`='$today', `votes`='0' WHERE `status`='Upcoming' AND `votes` >= 2 ORDER BY `votes` DESC LIMIT 4");
}

// START CREATING THE AUTOMATIC WEEKLY UPDATE
$count = $database->num_rows("SELECT * FROM `tcg_cards` WHERE `released`='$today'");

/** GET RELEASED DECKS **/
$getDecks = $database->query("SELECT released, 
  GROUP_CONCAT(filename ORDER BY filename ASC SEPARATOR ', ')
  FROM tcg_cards
  WHERE released='$today'
  GROUP BY released");
$row = mysqli_fetch_array($getDecks);
$decks = $row['1'];

/** GET NEW MEMBERS **/
$dateToday = date("Y-m-d", strtotime($range['timestamp']));
$weekAgo = date("Y-m-d", strtotime("-1 week"));
$getUsers = $database->query("SELECT name,
  GROUP_CONCAT(name ORDER BY name ASC SEPARATOR ', ')
  FROM user_list
  WHERE status='Active' AND regdate BETWEEN '$weekAgo' AND '$dateToday'
  ORDER BY regdate ASC");
$row = mysqli_fetch_array($getUsers);
if (!empty($row['1'])) { $users = $row['1']; }
else { $users = "None"; }

/** GET REFERRALS **/
$getRefers = $database->query("SELECT GROUP_CONCAT(refer, ' (x', count SEPARATOR '), ') string
  FROM (SELECT refer,
    COUNT(refer) COUNT
    FROM user_list
    WHERE status='Active' AND regdate BETWEEN '$weekAgo' AND '$today'
    GROUP BY refer ASC) x");
$row = mysqli_fetch_array($getRefers);
if (!empty($row['string'])) { $refers = $row['string'].')'; }
else { $refers = "None"; }

/** GET MASTERIES **/
$getMasters = $database->query("SELECT name,
  GROUP_CONCAT(slug ORDER BY slug ASC SEPARATOR ', ') string
  FROM tcg_activities
  WHERE type='master' AND date BETWEEN '$weekAgo' AND '$today'
  GROUP BY name ASC");
if(!$getMasters) {
  echo 'There was an error pulling up the results';
} else {
  $mas = '';
  while($row = mysqli_fetch_assoc($getMasters)) {
    $mas .= $row['name'].' ('.$row['string'].'); ';
  }
  $mas = substr($mas, 0, -2);
  if (!empty($mas)) { $masters = $mas; }
  else { $masters = "None"; }
}

/** GET LEVEL UPS **/
$getLevels = $database->query("SELECT slug,
  GROUP_CONCAT(name ORDER BY name ASC SEPARATOR ', ') string
  FROM tcg_activities
  WHERE type='level' AND date BETWEEN '$weekAgo' AND '$today'
  GROUP BY slug ORDER BY name ASC");
if(!$getLevels) {
  echo 'There was an error pulling up the results';
}
else {
  $lvl = '';
  while($row = mysqli_fetch_assoc($getLevels)) {
    $lvl .= $row['string'].' ('.$row['slug'].'); ';
  }
  $lvl = substr($lvl, 0, -2);
  if (!empty($lvl)) { $levels = $lvl; }
  else { $levels = "None"; }
}

/** GET AFFILIATES **/
$getAff = $database->query("SELECT subject, url
  FROM tcg_affiliates
  WHERE timestamp BETWEEN '$weekAgo' AND '$today'
  ORDER BY subject ASC");
if(!$getAff) {
  echo 'There was an error pulling up the results';
} else {
  $aff = '';
  while($row = mysqli_fetch_assoc($getAff)) {
    $aff .= '<a href="'.$row['url'].'" target="_blank">'.$row['subject'].'</a>, ';
  }
  $aff = substr($aff, 0, -2);
  if (!empty($aff)) { $affiliates = $aff; }
  else { $affiliates = "None"; }
}

/* Change your update placeholder that will show up
 * if you cannot make a decent update at midnight.
 */
$entry = "<p>Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut. Vis nonumes feugait ex, vel fuisset denique moderatius ut, eu sit graeci nostrum accusata. Augue delicatissimi ei sit, eirmod dolorum vix ea. Vis soluta essent consectetuer in, eam ad velit ludus voluptaria. Vitae ornatus ad mei, te eos primis numquam perfecto. Eum id habemus feugait iracundia. Eos te malorum nominavi scribentur. Vel vidit detracto ea, an quaestio consequat vis. Cu per aliquando persequeris, vitae causae omittantur vix ne. Ei eam possit inermis.</p>";
$title = "Bloop bloop! ∑(ﾟﾛﾟ〃)";
$hashed = htmlentities($sanitize->for_db($title));

$chkWish = $database->num_rows("SELECT * FROM `user_wishes` WHERE `status`='Granted' AND `timestamp`='".$range['timestamp']."'");
if ($chkWish == 0) { $wishVar = 'None'; } else { $wishVar = 'Yes'; }

$database->query("INSERT INTO `tcg_blog` (`title`,`timestamp`,`members`,`masters`,`levels`,`affiliates`,`referrals`,`decks`,`amount`,`wish`,`entry`,`status`) VALUES ('$title','$today','$users','$masters','$levels','$affiliates','$refers','$decks','$count','$wishVar','$entry','Published')");

######################################
###### GAME UPDATES AND REWARDS ######
######################################
// Get new round for Higher or Lower
$query = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active'");
$min = 1; $max = mysqli_num_rows($query); $rewards = null;
for($i=0; $i<1; $i++) {
  mysqli_data_seek($query,rand($min,$max)-1);
  $row = mysqli_fetch_assoc($query);
  $digits = rand(01,$row['count']);
  if ($digits < 10) { $_digits = "0$digits"; }
  else { $_digits = $digits; }
  $file = "$row[filename]";
  $num = "$_digits";
}
$database->query("INSERT INTO `game_hol_cards` (`filename`,`number`,`timestamp`) VALUES ('$file','$num','$today')");

// Pass rewards for Higher or Lower
$get1 = $database->query("SELECT * FROM `game_hol_logs` WHERE `guess`='higher' AND `timestamp` BETWEEN '$weekAgo' AND '$today'");
$get2 = $database->query("SELECT * FROM `game_hol_logs` WHERE `guess`='lower' AND `timestamp` BETWEEN '$weekAgo' AND '$today'");
$from = $database->get_assoc("SELECT * FROM `game_hol_cards` WHERE `timestamp`='$weekAgo'");
$curr = $database->get_assoc("SELECT * FROM `game_hol_cards` WHERE `timestamp`='$today'");
if ($curr['number'] > $from['number']) {
  while ($row = mysqli_fetch_assoc($get1)) {
    $database->query("INSERT INTO `user_rewards` (`name`,`type`,`subtitle`,`mcard`,`cards`,`gold`,`vial`,`timestamp`) VALUES ('".$row['name']."','Games','(Higher or Lower)','No','2','1','0','".$today."')");
  }
} else if ($curr['number'] < $from['number']) {
  while ($row = mysqli_fetch_assoc($get2)) {
    $database->query("INSERT INTO `user_rewards` (`name`,`type`,`subtitle`,`mcard`,`cards`,`gold`,`vial`,`timestamp`) VALUES ('".$row['name']."','Games','(Higher or Lower)','No','2','1','0','".$today."')");
  }
}

/* Add a query for Higher or Lower that will delete old logs from two weeks ago.
 * So that the database won't get filled with old game logs.
 */

// Get new round for Card Claim
$database->query("DELETE FROM `game_cclaim_cards`");
$cclaim = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active'");
$min = 1; $max = mysqli_num_rows($cclaim); $rewards = null;
for($i=0; $i<60; $i++) {
  mysqli_data_seek($cclaim,rand($min,$max)-1);
  $row = mysqli_fetch_assoc($cclaim);
  $digits = rand(01,$row['count']);
  if ($digits < 10) { $_digits = "0$digits"; }
  else { $_digits = $digits; }
  $card = "$row[filename]$_digits";
  $claims .= "('".$card."'),";
}
$claims = substr_replace($claims,"",-1);
$database->query("INSERT INTO `game_cclaim_cards` (`cards`) VALUES $claims");

/* Add a query for Card Claim that will delete old logs from two weeks ago.
 * So that the database won't get filled with old game logs.
 */

// Get new round for Melting Pot
$database->query("DELETE FROM `game_mpot_cards`");
$mpot = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active'");
$min = 1; $max = mysqli_num_rows($mpot); $rewards = null;
for($i=0; $i<20; $i++) {
  mysqli_data_seek($mpot,rand($min,$max)-1);
  $row = mysqli_fetch_assoc($mpot);
  $digits = rand(01,$row['count']);
  if ($digits < 10) { $_digits = "0$digits"; }
  else { $_digits = $digits; }
  $card = "$row[filename]$_digits";
  $pots .= "('".$card."'),";
}
$pots = substr_replace($pots,"",-1);
$database->query("INSERT INTO `game_mpot_cards` (`card`) VALUES $pots");

/* Add a query for Melting Pot that will delete old logs from two weeks ago.
 * So that the database won't get filled with old game logs.
 */
?>
