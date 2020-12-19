<?php include('admin/class.lib.php');
include($header);

if (empty($login)) {
    header("Location: account.php?do=login");
}

$name = isset($_GET['name']) ? $_GET['name'] : null;
$id = isset($_GET['id']) ? $_GET['id'] : null;
$count = $database->num_rows("SELECT * FROM `user_rewards` WHERE `name`='".$row['name']."'");

if ($name == $row['name']) {
    if (empty($id)) {
        echo '<h1>Rewards : '.$row['name'].'</h1>
        <p>Hey, '.$row['name'].'! You currently have <b>'.$count.'</b> unclaimed rewards right now. Click on the rewards link to get them!</p>';
        $sql = $database->query("SELECT * FROM `user_rewards` WHERE `name`='".$row['name']."'");
        while($get = mysqli_fetch_assoc($sql)) {
            echo '<li><a href="/rewards.php?name='.$row['name'].'&id='.$get['id'].'"><b>'.$get['type'].'</b></a> - received on '.$get['timestamp'].'</li>';
        }
    } else {
        $sql = $database->get_assoc("SELECT * FROM `user_rewards` WHERE `name`='".$row['name']."'");
        echo '<h1>Rewards : '.$sql['type'].'</h1>
        <p>Claim your '.$sql['type'].' rewards below and don\'t forget to log them on your trade post.</p>
        <center>';
        $query = $database->query("SELECT * FROM `tcg_cards` WHERE `status`='Active'");
        $min = 1; $max = mysqli_num_rows($query); $rewards = null; $cards = $sql['cards'];
        for($i=0; $i<$cards; $i++) {
            mysqli_data_seek($query,rand($min,$max)-1);
            $row = mysqli_fetch_assoc($query);
            $digits = rand(01,$row['count']);
            if ($digits < 10) { $_digits = "0$digits"; }
            else { $_digits = $digits; }
            $card = "$row[filename]$_digits";
            echo '<img src="'.$tcgcards.''.$card.'.png" border="0" /> ';
            $rewards .= $card.", ";
        }
        if ($sql['mcard'] == "No") { }
        else { echo '<img src="/images/cards/mc-'.$sql['name'].'.png" /> '; }
        $rewards = substr_replace($rewards,"",-2);
        if ($sql['cake'] == 0 && $sql['ticket'] == 0 && $sql['mcard'] == "No") {
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.'</p></center>';
            $newSet = $rewards;
        }
        else if ($sql['cake'] != 0 && $sql['ticket'] == 0 && $sql['mcard'] == "No") {
            echo '<img src="/images/cake.png" /> [x'.$sql['cake'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.', +'.$sql['cake'].' cakes</p></center>';
            $newSet = $rewards.', +'.$sql['cake'].' cakes';
        }
        else if ($sql['cake'] == 0 && $sql['ticket'] != 0 && $sql['mcard'] == "No") {
            echo '<img src="/images/ticket.png" /> [x'.$sql['ticket'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.', +'.$sql['ticket'].' tickets</p></center>';
            $newSet = $rewards.', +'.$sql['ticket'].' tickets';
        }
        else if ($sql['cake'] != 0 && $sql['ticket'] != 0 && $sql['mcard'] == "No") {
            echo '<img src="/images/cake.png" /> [x'.$sql['cake'].'] <img src="/images/ticket.png" /> [x'.$sql['ticket'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.', +'.$sql['cake'].' cakes, +'.$sql['ticket'].' tickets</p></center>';
            $newSet = $rewards.', +'.$sql['cake'].' cakes, +'.$sql['ticket'].' tickets';
        }
        else if ($sql['cake'] != 0 && $sql['ticket'] == 0 && $sql['mcard'] == "Yes") {
            echo '<img src="/images/cake.png" /> [x'.$sql['cake'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> mc-'.$sql['name'].', '.$rewards.', +'.$sql['cake'].' cakes</p></center>';
            $newSet = 'mc-'.$sql['name'].', '.$rewards.', +'.$sql['cake'].' cakes';
        } else {
            echo '<img src="/images/cake.png" /> [x'.$sql['cake'].'] <img src="/images/ticket.png" /> [x'.$sql['ticket'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.', +'.$sql['gold'].' golds, +'.$sql['vial'].' vials</p></center>';
            $newSet = $rewards.', +'.$sql['cake'].' cakes, +'.$sql['ticket'].' tickets';
        }
        $today = date("Y-m-d", strtotime("now"));
        $database->query("INSERT INTO `logs_".$sql['name']."` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('".$sql['name']."','Rewards','".$sql['type']."','".$sql['subtitle']."','$newSet','$today')");
        $database->query("UPDATE `user_items` SET `cake`=cake+'".$sql['cake']."', `ticket`=ticket+'".$sql['ticket']."', `cards`=cards+'$cards' WHERE `email`='$login'");
        $database->query("DELETE FROM `user_rewards` WHERE `id`='".$sql['id']."'");
    }
}

include($footer);
?>