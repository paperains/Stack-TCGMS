<?php
include('admin/class.lib.php');
include($header);

$p = isset($_GET['p']) ? $_GET['p'] : null;

if ( empty($p) ) {
    $sql = $database->get_assoc("SELECT * FROM `tcg_pages` WHERE `page_id`='2' AND `page_status`='Published'");
    echo '<h1>'.$sql['page_title'].'</h1>';
    $con = $sql['page_content'];
    eval('?>'.$con.'<?');
}

else {
    if ( empty($sub) ) {
        $sql = $database->get_assoc("SELECT * FROM `tcg_pages` WHERE `page_slug`='$p' AND `page_parent`='2' AND `page_status`='Published'");
        echo '<h1>'.$sql['page_title'].'</h1>';
        $con = $sql['page_content'];
        eval('?>'.$con.'<?');
    } else {
        $row = $database->get_assoc("SELECT * FROM `tcg_levels_badge` WHERE `badge_set`='$sub'");
        echo '<h1>Level Badges - '.$row['badge_set'].'</h1>';
        echo '<center>';
        for($i=1;$i<=$row['badge_level'];$i++) {
            if( $i < 10 ) { $digit = '0'.$i; }
            else { $digit = $i; }
            echo '<img src="/images/badges/'.$row['badge_set'].'-'.$digit.'.png" /> ';
        }
        echo '</center>';
    }
}

include($footer);
?>