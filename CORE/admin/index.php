<?php
require_once('class.lib.php');
include('theme/header.php');

if (empty($login)) {
    header("Location: /account.php?do=login");
}

if ($row['role'] == "Member") {
    echo '<h1>Access Denied!</h1>
    <p>You don\'t have the right permission to access this page.</p>';
}

else if (empty($page)) {
    include('pages/main.php');
} else {
    include ("pages/$act/$page.php");
}

include('theme/footer.php');
?>
