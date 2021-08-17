<?php
include('class.lib.php');
include($tcgpath.'admin/theme/header.php');

$PHP_SELF = "index.php";

if( empty($login) ) {
    header('Location: '.$tcgurl.'account.php?do=login');
}

if( $row['usr_role'] == 7 ) {
    echo '<h1>Access Denied!</h1>
    <p>You don\'t have the right permission to access this page.</p>';
} else {
    if( empty($mod) ) {
        include($tcgpath.'admin/inc/main.php');
    } else {
        include ($tcgpath.'admin/inc/'.$mod.'.php');
    }
}

include($tcgpath.'admin/theme/footer.php');
?>
