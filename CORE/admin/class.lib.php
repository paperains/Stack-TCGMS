<?php
/*
 * Class libraries for connections
 */

$db_server = 'localhost';						// The database server, usually localhost
$db_user = 'username';							// The username for your database
$db_password = 'password';						// The password for your database
$db_database = 'table';							// The database name
date_default_timezone_set('Asia/Manila');				// Local timezone of your TCG

/* Variables for affiliates */
$buttonsize = '100x35';							// The size for all affiliate buttons, ie. 88x31.
$buttonurl = 'https://www.domain.tld/images/aff/';			// The URL to your affiliate image folder WITH TRAILING SLASH
$num_affiliates = '3';							// The number of affiliate categories you have
$affiliates[1] = 'Pending';						// First affiliate category
$affiliates[2] = 'Active';						// Second affiliate category
$affiliates[3] = 'Hiatus';						// Third affiliate category

/* Set page strings for dynamic pages (DO NOT EDIT) */
$id = (isset($_GET['id']) ? $_GET['id'] : null);
$do = (isset($_GET['do']) ? $_GET['do'] : null);
$go = (isset($_GET['go']) ? $_GET['go'] : null);
$set = (isset($_GET['set']) ? $_GET['set'] : null);
$sub = (isset($_GET['sub']) ? $_GET['sub'] : null);
$msg = (isset($_GET['msg']) ? $_GET['msg'] : null);
$form = (isset($_GET['form']) ? $_GET['form'] : null);
$deck = (isset($_GET['deck']) ? $_GET['deck'] : null);
$view = (isset($_GET['view']) ? $_GET['view'] : null);
$page = (isset($_GET['page']) ? $_GET['page'] : null);
$stat = (isset($_GET['stat']) ? $_GET['stat'] : null);
$play = (isset($_GET['play']) ? $_GET['play'] : null);
$act = (isset($_GET['action']) ? $_GET['action'] : null);
$login = (isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null);

/********************************************************
** DO NOT EDIT BELOW UNLESS YOU KNOW WHAT YOU'RE DOING **
** YOU CAN ONLY EDIT THE COMMENTED PART IF YOU NEED TO **
********************************************************/

$users = array($user => md5($pass));
$salt = substr(md5(date("F")), 8);

/* Database config information */
class Config {
  const DB_SERVER = 'localhost',					// Default: localhost
	DB_USER = 'username',						// Database username
	DB_PASSWORD = 'password',					// Database password
	DB_DATABASE = 'table',						// Database table name
	DB_SALT = 'aEF#TGgs-!dgaw3324_WQ+';				// Your password salt and treat it like a high security password.
}

/* Your database functions to get your datas */
class Database {
  function connect () {
    $link = @mysqli_connect( Config::DB_SERVER , Config::DB_USER , Config::DB_PASSWORD, Config::DB_DATABASE )
    or die( "Couldn't connect to MYSQL: ".mysqli_error($link) );
    return $link;
  }

  function query ($query) {
    $link = $this->connect();
    $result = mysqli_query($link, $query);
    return $result;
  }

  function get_assoc ($query) {
    $link = $this->connect();
    $result = mysqli_query($link, $query);
    if ( !$result ) { die ( "Couldn't process query: ".mysqli_error($link) ); }
    $assoc = mysqli_fetch_assoc($result);
    return $assoc;
  }

  function get_array ($query) {
    $link = $this->connect();
    $result = mysqli_query($link, $query);
    if ( !$result ) { die ( "Couldn't process query: ".mysqli_error($link) ); }
    $array = mysqli_fetch_array($result);
    return $array;
  }

  function num_rows ($query) {
    $link = $this->connect();
    $result = mysqli_query($link, $query);
    if ( !$result ) { die ( "Couldn't process query: ".mysqli_error($link) ); }
    $num_rows = mysqli_num_rows($result);
    return $num_rows;
  }

  function data_seek ($query) {
    $link = $this->connect();
    $result = mysqli_query($link, $query);
    if ( !$result ) { die ( "Couldn't process query: ".mysqli_error($link) ); }
    $seek = mysqli_data_seek($result,rand($min,$max)-1);
    return $seek;
  }
}

/* Sanitize your form values before passing to the database */
class Sanitize {
  function clean ($data) {
    if ( get_magic_quotes_gpc() ) { $data = stripslashes($data); }
    $data = trim(htmlentities(strip_tags($data)));
    return $data;
  }

  function for_db ($data) {
    $database = new Database;
    $link = $database->connect();

    $data = $this->clean($data);
    $data = mysqli_real_escape_string($link, $data);
    return $data;
  }
}

/* Functions to use to count card and member datas through the dashboard */
class Count {
  function numCards( $stat, $worth ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $stat = $sanitize->for_db($stat);
    $worth = $sanitize->for_db($worth);

    if (empty($stat)) { $result = $database->num_rows("SELECT * FROM `tcg_cards` WHERE `worth`='$worth'"); }
    else if (empty($worth)) { $result = $database->num_rows("SELECT * FROM `tcg_cards` WHERE `status`='$stat'"); }
    else { $result = $database->num_rows("SELECT * FROM `tcg_cards` WHERE `status`='$stat' AND `worth`='$worth'"); }
    echo $result;
  }

  function countCards() {
    $database = new Database;
    $sanitize = new Sanitize;

    $result = $database->get_assoc("SELECT worth, SUM(count) FROM `tcg_cards`");
    $cardcount = $result['SUM(count)'];
    echo $cardcount;
  }

  function numAll( $table, $stat ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $table = $sanitize->for_db($table);
    $stat = $sanitize->for_db($stat);

    if (empty($stat)) { $result = $database->num_rows("SELECT * FROM `$table`"); }
    else { $result = $database->num_rows("SELECT * FROM `$table` WHERE `status`='$stat'"); }
    echo $result;
  }

  function numClaimed ( $type ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $type = $sanitize->for_db($type);

    $result = $database->num_rows("SELECT * FROM `tcg_donations` WHERE `type`='$type'");
    echo $result;
  }
  
  function numCurrency ( $item ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $item = $sanitize->for_db($item);

    $login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
    $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
    $player = $row['name'];

    $query = $database->query("SELECT * FROM `user_items` WHERE `name` = '$player'");
    $row = mysqli_fetch_assoc($query);
    return $row[$item];
  }
  
  function numRewards () {
    $database = new Database;
    $sanitize = new Sanitize;

    $login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
    $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
    $player = $row['name'];

    $result = $database->num_rows("SELECT * FROM `user_rewards` WHERE `name`='$player'");
    echo $result;
  }

  function numMail () {
    $database = new Database;
    $sanitize = new Sanitize;

    $login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
    $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
    $player = $row['name'];

    $result = $database->num_rows("SELECT * FROM `user_mbox` WHERE `recipient`='$player' AND `read_to`='1'");
    echo $result;
  }
}

/* Functions to use for manually checking the form datas and member listing */
function checkBots() {
  $exploits = "/(content-type|bcc:|cc:|document.cookie|onclick|onload|javascript|alert)/i";
  $profanity = "/(beastial|bestial|blowjob|clit|cum|cunilingus|cunillingus|cunnilingus|cunt|ejaculate|fag|felatio|fellatio|fuck|fuk|fuks|gangbang|gangbanged|gangbangs|hotsex|jism|jiz|kock|kondum|kum|kunilingus|orgasim|orgasims|orgasm|orgasms|phonesex|phuk|phuq|porn|pussies|pussy|spunk|xxx)/i";
  $spamwords = "/(viagra|phentermine|tramadol|adipex|advai|alprazolam|ambien|ambian|amoxicillin|antivert|blackjack|backgammon|texas|holdem|carisoprodol|ciara|ciprofloxacin|debt|dating|porn)/i";
  $bots = "/(Indy|Blaiz|Java|libwww-perl|Python|OutfoxBot|User-Agent|PycURL|AlphaServer)/i";

  if (preg_match($bots, $_SERVER['HTTP_USER_AGENT'])) { exit("<h1>Error</h1>\nKnown spam bots are not allowed.<br /><br />"); }
  return true;
}

class Check {
  function Value() {
    $database = new Database;
    $sanitize = new Sanitize;
    checkBots();
    foreach ($_POST as $key => $value) {
      $value = trim($value);
      if (empty($value)) {
        exit("<h1>Error</h1>\nAll fields are required. Please go back and complete the form.");
      }
      elseif (preg_match($exploits, $value)) {
        exit("<h1>Error</h1>\nExploits/malicious scripting attributes aren't allowed.");
      }
      elseif (preg_match($profanity, $value) || preg_match($spamwords, $value)) {
        exit("<h1>Error</h1>\nThat kind of language is not allowed through this form.");
      }
      $_POST[$key] = stripslashes(strip_tags($value));
    }
    return true;
  }

  function Password() {
    $database = new Database;
    $sanitize = new Sanitize;
    checkBots();
    foreach ($_POST as $key => $value) {
      $check1=mysqli_query($connect, "SELECT * FROM `user_list` WHERE id='$_POST[id]'");
      $row=mysqli_fetch_assoc($check1);
      $value = trim($value);
      if (empty($value)) {
        exit("<h1>Error</h1>\nYou must fill out all fields. Please go back and fill in the form properly.<br /><br />");
      }
      elseif (preg_match($exploits, $value)) {
        exit("<h1>Error</h1>\nExploits/malicious scripting attributes aren't allowed.<br /><br />");
      }
      elseif (preg_match($profanity, $value) || preg_match($spamwords, $value)) {
        exit("<h1>Error</h1>\nThat kind of language is not allowed through our form.<br /><br />");
      }				
      elseif ($_POST[password2]!=$_POST[password]) {
        exit("<h1>Error</h1>\nThe new passwords you entered do not match, please go back and make sure they are they same.");
      }
      elseif (md5($_POST[current])!=$row[password]) {
        exit("<h1>Error</h1>\nThe current password you entered does not match our records. Please go back and make sure you entered it correctly.");
      }
      $_POST[$key] = stripslashes(strip_tags($value));
    }
    return true;
  }

  function Member() {
    $database = new Database;
    $sanitize = new Sanitize;
    checkBots();
    foreach ($_POST as $key => $value) {
      $value = trim($value);
      $num_check1 = $database->num_rows("SELECT * FROM `user_list` WHERE `email`='$_POST[email]'");
      $num_check2 = $database->num_rows("SELECT * FROM `user_list` WHERE `name`='$_POST[name]'");

      if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['url']) || empty($_POST['collecting'])) {
        exit("<h1 class=\"title\">Error</h1>\n<div class=\"content\"><p>You must provide your name, email, url, and collecting deck. Please go back and fill in the form properly.</p>");
      }
      elseif (preg_match($exploits, $value)) {
        exit("<h1>Error</h1>\nExploits/malicious scripting attributes aren't allowed.<br /><br />");
      }
      elseif (preg_match($profanity, $value) || preg_match($spamwords, $value)) {
        exit("<h1>Error</h1>\nThat kind of language is not allowed through our form.<br /><br />");
      }
      elseif ($_POST['password2']!=$_POST['password']) {
        exit("<h1>Error</h1>\nThe passwords you entered do not match, please go back and make sure they are they same.");
      }
      elseif ($num_check1!=0) {
        exit("<h1>Error</h1>\nSomeone has already signed up with that email address. Please go back and use another email address. If you are a current member and have lost your password, please <a href=\"account.php?do=lostpass\">reset</a> your password.");
      }
      elseif ($num_check2!=0) {
        exit("<h1>Error</h1>\nSomeone has already joined $tcgname with that name. Please go back and use another name.");
      }
      $_POST[$key] = stripslashes(strip_tags($value));
    }
    return true;
  }

  function Donation() {
    $database = new Database;
    $sanitize = new Sanitize;
    checkBots();
    foreach ($_POST as $key => $value) {
      $num_chk = $database->num_rows("SELECT * FROM `tcg_donations` WHERE deckname='$_POST[deckname]'");
      $value = trim($value);
      if (empty($value)) {
        exit("<h1>Error</h1>\n<p>All fields are required. Please go back and complete the form.</p>");
      }
      elseif (preg_match($exploits, $value)) {
        exit("<h1>Error</h1>\n<p>Exploits/malicious scripting attributes aren't allowed.</p>");
      }
      elseif (preg_match($profanity, $value) || preg_match($spamwords, $value)) {
        exit("<h1>Error</h1>\n<p>That kind of language is not allowed through this form.</p>");
      }
      elseif ($num_chk!=0) {
        exit("<h1>Error</h1>\n<p>Someone has already claimed that deck! Please choose another nature-related subject to claim, thank you!</p>");
      }
      $_POST[$key] = stripslashes(strip_tags($value));
    }
    return true;
  }
}

/* General class for functions to use for the TCG */
class General {
  function randtype( $stat ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $stat = $sanitize->for_db($stat);

    $result = $database->get_assoc("SELECT * FROM `tcg_cards` WHERE `status`='$stat' ORDER BY RAND() LIMIT 1");
    $name = $result['filename'];
    $digits = rand(01,$result['count']);
    if ($digits < 10) { $_digits = "0$digits"; }
    else { $_digits = $digits; }
    $card = "$name$_digits";
    echo $card;
  }

  // NEED TO CHANGE CURRENCY VARIABLES THAT CAN BE MODIFIED VIA ADMIN PANEL
  function gamePrize( $set, $game, $sub, $rand, $choice, $x1, $x2, $x3 ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $set = $sanitize->for_db($set);
    $sub = $sanitize->for_db($sub);
    $game = $sanitize->for_db($game);
    $x1 = $sanitize->for_db($x1);
    $x2 = $sanitize->for_db($x2);
    $x3 = $sanitize->for_db($x3);
    $rand = $sanitize->for_db($rand);
    $choice = $sanitize->for_db($choice);

    $xn1 = $settings->getValue( 'x1' );
    $xn2 = $settings->getValue( 'x2' );
    $xn3 = $settings->getValue( 'x3' );

    $xn1 = substr_replace($xn1,"",-4);
    $xn2 = substr_replace($xn2,"",-4);
    $xn3 = substr_replace($xn3,"",-4);

    $cards = $rand + $choice;

    $login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
    $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
    $player = $row['name'];

    $c1 = $sanitize->for_db($_POST['choice1']);
    $c2 = $sanitize->for_db($_POST['choice2']);
    $n1 = $sanitize->for_db($_POST['num1']);
    $n2 = $sanitize->for_db($_POST['num2']);
    $choice1 = "$c1$n1";
    $choice2 = "$c2$n2";

    // SPIT OUT REWARDS
    $query = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active'");
    $min = 1; $max = mysqli_num_rows($query); $rewards = null;
    for($i=0; $i<$rand; $i++) {
      mysqli_data_seek($query,rand($min,$max)-1);
      $row = mysqli_fetch_assoc($query);
      $digits = rand(01,$row['count']);
      if ($digits < 10) { $_digits = "0$digits"; }
      else { $_digits = $digits; }
      $card = "$row[filename]$_digits";
      echo '<img src="/images/cards/'.$card.'.png" border="0" /> ';
      $rewards .= $card.", ";
    }
    $rewards = substr_replace($rewards,"",-2);
    if ($x1 != 0 && $x2 != 0) {
      echo '<img src="/images/'.$xn1.'.png"> [x'.$x1.'] <img src="/images/'.$xn2.'.png"> [x'.$x2.']';
      echo "<p><strong>$game"; if (!empty($sub)) { echo ' '.$sub; } echo ":</strong> $rewards, +$x1 $xn1(s), +$x2 $xn2(s)";
      $newSet = $rewards.', +'.$x1.' '.$xn1.'(s), +'.$x2.' '.$xn2.'(s)';
    } else if ($x1 != 0 && $x2 == 0) {
      echo '<img src="/images/'.$xn1.'.png"> [x'.$x1.']';
      echo "<p><strong>$game"; if (!empty($sub)) { echo ' '.$sub; } echo ":</strong> $rewards, +$x1 $xn1(s)";
      $newSet = $rewards.', +'.$x1.' '.$xn1.'(s)';
    } else if ($x1 != 0 && $x2 == 0 && $choice != 0) {
      echo '<img src="/images/'.$xn1.'.png"> [x'.$x1.']';
      echo "<p><strong>$game"; if (!empty($sub)) { echo ' '.$sub; } echo ":</strong> $choice1, $choice2, $rewards, +$x1 $xn1(s)";
      $newSet = $choice1.', '.$choice2.', '.$rewards.', +'.$x1.' '.$xn1.'(s)';
    } else {
      echo "<p><strong>$game"; if (!empty($sub)) { echo ' '.$sub; } echo ":</strong> $rewards";
      $newSet = $rewards;
    }
    echo "</p></center>";
    $today = date("Y-m-d", strtotime("now"));
    $database->query("UPDATE `user_items` SET `x1`=x1+'$x1', `x2`=x2+'$x2', `cards`=cards+'$cards', `timestamp`='$today' WHERE `name`='$player'");
    $database->query("INSERT INTO `user_logs` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('$player','$set','$game','$sub','$newSet','$today')");
  }

  function member( $stat ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $stat = $sanitize->for_db($stat);

    $get = $database->query("SELECT * FROM `user_list` WHERE `status`='$stat' ORDER BY `name` ASC");
    $count = mysqli_num_rows($get);
    if($count===0) { echo "<p><center>There are currently no members at this status.</center></p>"; }
    else {
      echo '<center>';
      while($row=mysqli_fetch_assoc($get)) {
        echo '<div class="memList">';
        if ($row['memcard']=="Yes") { echo '<a href="/members.php?id='.$row['name'].'"><img src="/images/cards/mc-'.$row['name'].'.png" /></a>'; }
        else { echo '<a href="/members.php?id='.$row['name'].'"><img src="/images/cards/filler.png" /></a>'; }
        echo '<div class="memName" align="center"><a href="/members.php?id='.$row['name'].'">'.$row['name'].'</a></div>';
        echo '<div class="socIcon">';
          $prejoin = $row['prejoiner'];
          if ($prejoin=="Yes") { echo '<li><font color="#c81b3c"><span class="fas fa-fire-alt" aria-hidden="true" title="Prejoiner"></span></font></li>'; }
          else { echo '<li><font color="#636363"><span class="fas fa-fire-alt" aria-hidden="true" title="Non-Prejoiner"></span></font></li>'; }
          echo '<li><a href="'.$row['url'].'" target="_blank" title="Visit Trade Post"><span class="fas fa-home" aria-hidden="true"></span></a></li>
          <li><span class="fas fa-gift" aria-hidden="true" title="Born on '.date("F d", strtotime($row['birthday'])).'"></span></li>
          <li><a href="/cards.php?view=released&deck='.$row['collecting'].'"><span class="fas fa-puzzle-piece" aria-hidden="true" title="Collecting '.$row['collecting'].'"></span></a></li>
        </div>
        </div>';
      }
      echo '</center>';
    }
    echo "<br /><br />\n\n";
  }

  function memList( $stat ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $stat = $sanitize->for_db($stat);

    $result = $database->num_rows("SELECT * FROM `user_list` WHERE `status`='$stat' ORDER BY `id` ASC");
    $sql = $database->query("SELECT * FROM `user_list` WHERE `status`='$stat' ORDER BY `id` ASC");

    if($result===0) {
      echo "<center>There are currently no members at this level.</center>\n";
      echo "<br /><br />\n\n";
    }
    else {
      echo '<table width="100%" class="table-body" cellpaddg="0" cellspacing="0">';
      echo '<tr><td width="25%" class="record-label">Action</td><td width="25%" class="record-label">Name</td><td width="25%" class="record-label">URL</td><td width="25%" class="record-label">Email</td></tr>';
      while($row = mysqli_fetch_assoc($sql)) {
        echo '<tr><td class="player-list"><button onClick="window.location.href=\'index.php?action=edit&page=members&id='.$row['id'].'\'" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> <button onClick="window.location.href=\'index.php?action=delete&page=members&id='.$row['id'].'" class="btn-warning"><span class="fas fa-times" aria-hidden="true"></span></button> <button onClick="window.location.href=\'index.php?action=approve&page=members&id='.$row['id'].'\'" class="btn-primary"><span class="fas fa-check" aria-hidden="true"></span></button></td><td class="player-list">'.$row['name'].'</td><td class="player-list"><a href="'.$row['url'].'" target="_blank">http://</a></td><td class="player-list"><a href="index.php?action=email&page=members&id='.$row['id'].'">Email?</a></td></tr>';
      }
      echo "</table>\n";
      echo "<br /><br />\n\n";
    }
  }

  function getItem( $item ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $item = $sanitize->for_db($item);

    $login = isset($_SESSION['USR_LOGIN']) ? $_SESSION['USR_LOGIN'] : null;
    $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `email`='$login'");
    $player = $row['name'];

    $query = $database->query("SELECT * FROM `user_items` WHERE `name` = '$player'");
    $row = mysqli_fetch_assoc($query);
    return $row[$item];
  }

  function cardSearch( $table, $stat ) {
    $database = new Database;
    $sanitize = new Sanitize;
    $table = $sanitize->for_db($table);
    $stat = $sanitize->for_db($stat);

    // BEGIN SEARCH FORM
    echo '<center><form method="post" action="">
    <input type="text" name="term" placeholder="Search released decks..." size="30" /> <input type="submit" name="search" value="   Search!   " />
    </form><br />';

    // DO SEARCH HERE
    if ( isset($_REQUEST['term']) ) {
      $term = $sanitize->for_db($_POST['term']);
      $sql = $database->query("SELECT * FROM `tcg_$table` WHERE `status`='$stat' AND (`deckname` LIKE '%".$term."%' OR `filename` LIKE '%".$term."%' OR `series` LIKE '%".$term."%' OR `donator` LIKE '%".$term."%' OR `maker` LIKE '%".$term."%') ORDER BY `deckname` ASC");
      if (mysqli_num_rows($sql) == 0) { echo '<div class="box-warning"><b>Error!</b> Your search query didn\'t match any data from the database.</div>'; }
      else {
        echo '<div class="box-success"><b>Success!</b> The data below shows any matches from your search query: <b>'.$term.'</b>.</div><br />
        <table width="80%" cellspacing="3" class="border">
        <tr><td class="headLineSmall" width="30%">Deckname</td><td class="headLineSmall" width="15%">Donator</td><td class="headLineSmall" width="15%">Maker</td></tr>';
        while ($search = mysqli_fetch_assoc($sql)) {
          echo '<tr><td class="tableBodySmall"><a href="/cards.php?view=released&deck='.$search['filename'].'">'.$search['deckname'].'</a></td><td class="tableBodySmall"><a href="/members.php?id="'.$search['donator'].'">'.$search['donator'].'</a></td><td class="tableBodySmall"><a href="/members.php?id='.$search['maker'].'">'.$search['maker'].'</a></td></tr>';
        }
      echo '</table><br />';
      }
    }
    echo '</center>';
    return true;
  }
}

/* Class for your file uploads which are dependent to your file paths and folders
 * Change absolute path according to your settings table data (will be added soon)
 */
class Uploads {
  function reArrayFiles($file) {
    $file_ary = array();
    $file_count = count($file['name']);
    $file_key = array_keys($file);

    for($i=0;$i<$file_count;$i++)
    {
      foreach($file_key as $val)
      {
        $file_ary[$i][$val] = $file[$val][$i];
      }
    }
    return $file_ary;
  }

  function folderPath($origin, $folder) {
    $settings = new Settings;
    $ab_path = $settings->getValue( 'file_path_absolute' );
	  
    global $img_desc;
    $file['name'] = null;
    foreach($img_desc as $val) {
      $newname = $file['name'];
      /* Change according to your own filepath */
      if (empty($origin)) {
        $path = $ab_path."".$folder."/";
      } else {
        $path = $ab_path."".$origin."/".$folder."/";
      }
      move_uploaded_file($val['tmp_name'],$path.$val['name']);
    }
  }
}

/* Class to fetch settings data from the database.
 * Must add a default config.php for database info to avoid repetition.
 * Tried doing it before but it wasn't working (not reading the file).
 * Tried using the first database info located at the beginning of this
 * file but it's not reading it as well.
 */
class Settings {
  function getValue( $setting ) {
    try {
      $pdo = new PDO("mysql:host=$GLOBALS[db_server];dbname=$GLOBALS[db_database]", $GLOBALS[db_user], $GLOBALS[db_password]);
      // set the PDO error mode to exception
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $result = $pdo->prepare("SELECT `value` FROM `tcg_settings` WHERE `settings` = :setting");
    $result->bindParam(':setting', $setting, PDO::PARAM_STR);
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $row = $result->fetch();
    return $row['value'];
  } // end of get_setting_value

  function getName( $setting ) {
    try {
      $pdo = new PDO("mysql:host=$GLOBALS[db_server];dbname=$GLOBALS[db_database]", $GLOBALS[db_user], $GLOBALS[db_password]);
      // set the PDO error mode to exception
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $result = $pdo->prepare("SELECT `settings` FROM `tcg_settings` WHERE `settings` = :setting");
    $result->bindParam(':setting', $setting, PDO::PARAM_STR);
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $row = $result->fetch();
    return $row['settings'];
  } // end of get_setting_description

  function getDesc( $setting ) {
    try {
      $pdo = new PDO("mysql:host=$GLOBALS[db_server];dbname=$GLOBALS[db_database]", $GLOBALS[db_user], $GLOBALS[db_password]);
      // set the PDO error mode to exception
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $result = $pdo->prepare("SELECT `description` FROM `tcg_settings` WHERE `settings` = :setting");
    $result->bindParam(':setting', $setting, PDO::PARAM_STR);
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $row = $result->fetch();
    return $row['description'];
  } // end of get_setting_description

  function getCatName( $setting ) {
    try {
      $pdo = new PDO("mysql:host=$GLOBALS[db_server];dbname=$GLOBALS[db_database]", $GLOBALS[db_user], $GLOBALS[db_password]);
      // set the PDO error mode to exception
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $result = $pdo->prepare("SELECT `category` FROM `tcg_cards_cat` WHERE `id` = :id");
    $result->bindParam(':id', $setting, PDO::PARAM_STR);
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $row = $result->fetch();
    return $row['category'];
  } // end of get_category_name

  function getCatID( $setting ) {
    try {
      $pdo = new PDO("mysql:host=$GLOBALS[db_server];dbname=$GLOBALS[db_database]", $GLOBALS[db_user], $GLOBALS[db_password]);
      // set the PDO error mode to exception
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    $result = $pdo->prepare("SELECT `id` FROM `tcg_cards_cat` WHERE `category` = :category");
    $result->bindParam(':category', $setting, PDO::PARAM_STR);
    $result->execute();
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $row = $result->fetch();
    return $row['id'];
  } // end of get_setting_description

  function update_setting( $settings, $value ) {
    try {
      $pdo = new PDO("mysql:host=$GLOBALS[db_server];dbname=$GLOBALS[db_database]", $GLOBALS[db_user], $GLOBALS[db_password]);
      // set the PDO error mode to exception
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }

    $query = "UPDATE `tcg_settings` SET `value` = :value WHERE " . " `settings` = :settings";
    $result = $pdo->prepare($query);
    $result->bindParam(':value', $value, PDO::PARAM_STR);
    $result->bindParam(':settings', $settings, PDO::PARAM_STR);
    $result->execute();
    if( !$result ) {
      log_error( __FILE__ . ':' . __LINE__,
      'Error executing query: <i>' . $result->errorInfo()[2] .
      '</i>; Query is: <code>' . $query . '</code>' );
      die( STANDARD_ERROR );
    }
  } // end of update_setting

  function update_settings( $settings ) {
    try {
      $pdo = new PDO("mysql:host=$GLOBALS[db_server];dbname=$GLOBALS[db_database]", $GLOBALS[db_user], $GLOBALS[db_password]);
      // set the PDO error mode to exception
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    foreach( $settings as $field => $value ) {
      $query = "UPDATE `tcg_settings` SET `value` = :value WHERE " . " `settings` = '$field'";
      if( $query != '' ) {
        $result = $pdo->prepare($query);
        $result->bindParam(':value', $value, PDO::PARAM_STR);
        $result->execute();
        if( !$result ) {
          log_error( __FILE__ . ':' . __LINE__,
          'Error executing query: <i>' . $result->errorInfo()[2] .
          '</i>; Query is: <code>' . $query . '</code>' );
          die( STANDARD_ERROR );
        }
      }
    }
  } // end of settings update function
}

define('VALID_INC', TRUE);
include('class.call.php');
?>
