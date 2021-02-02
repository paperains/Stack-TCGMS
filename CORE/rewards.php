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
        if ($sql['x1'] == 0 && $sql['x2'] == 0 && $sql['mcard'] == "No") {
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.'</p></center>';
            $newSet = $rewards;
        }
        else if ($sql['x1'] != 0 && $sql['x2'] == 0 && $sql['mcard'] == "No") {
            echo '<img src="/images/'.$settings->getValue('x1').'" /> [x'.$sql['x1'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.', +'.$sql['x1'].' '.$x1.'s</p></center>';
            $newSet = $rewards.', +'.$sql['x1'].' '.$x1.'s';
        }
        else if ($sql['x1'] == 0 && $sql['x2'] != 0 && $sql['mcard'] == "No") {
            echo '<img src="/images/'.$settings->getValue('x2').'" /> [x'.$sql['x2'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.', +'.$sql['x2'].' '.$x2.'s</p></center>';
            $newSet = $rewards.', +'.$sql['x2'].' '.$x2.'s';
        }
        else if ($sql['x1'] != 0 && $sql['x2'] != 0 && $sql['mcard'] == "No") {
            echo '<img src="/images/'.$settings->getValue('x1').'" /> [x'.$sql['x1'].'] <img src="/images/'.$settings->getValue('x2').'" /> [x'.$sql['x2'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.', +'.$sql['x1'].' '.$x1.'s, +'.$sql['x2'].' '.$x2.'s</p></center>';
            $newSet = $rewards.', +'.$sql['x1'].' '.$x1.'s, +'.$sql['x2'].' '.$x2.'s';
        }
        else if ($sql['x1'] != 0 && $sql['x2'] == 0 && $sql['mcard'] == "Yes") {
            echo '<img src="/images/'.$settings->getValue('x1').'" /> [x'.$sql['x1'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> mc-'.$sql['name'].', '.$rewards.', +'.$sql['x1'].' '.$x1.'s</p></center>';
            $newSet = 'mc-'.$sql['name'].', '.$rewards.', +'.$sql['x1'].' '.$x1.'s';
        } else {
            echo '<img src="/images/'.$settings->getValue('x1').'" /> [x'.$sql['x1'].'] <img src="/images/'.$settings->getValue('x2').'" /> [x'.$sql['x2'].']';
            echo '<p><strong>'.$sql['type']; if ($sql['subtitle']!='') { echo ' '.$sql['subtitle']; } echo ':</strong> '.$rewards.', +'.$sql['x1'].' '.$x1.'s, +'.$sql['x2'].' '.$x2.'s</p></center>';
            $newSet = $rewards.', +'.$sql['x1'].' '.$x1.'s, +'.$sql['x2'].' '.$x2.'s';
        }
        $today = date("Y-m-d", strtotime("now"));
        $database->query("INSERT INTO `user_logs` (`name`,`type`,`title`,`subtitle`,`rewards`,`timestamp`) VALUES ('".$sql['name']."','Rewards','".$sql['type']."','".$sql['subtitle']."','$newSet','$today')");
        $database->query("UPDATE `user_items` SET `x1`=x1+'".$sql['x1']."', `x2`=x2+'".$sql['x2']."', `cards`=cards+'$cards' WHERE `email`='$login'");
        $database->query("DELETE FROM `user_rewards` WHERE `id`='".$sql['id']."'");
    }
}

include($footer);
?>
