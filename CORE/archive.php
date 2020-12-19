<?php include('admin/class.lib.php');
include($header);

echo '<h1>News Archive</h1>
<p>Below is the complete list of our news archive sorted by the recent one.</p>';
$sql = $database->query("SELECT * FROM `tcg_blog` WHERE `status`='Published' ORDER BY `timestamp` DESC");
while($row=mysqli_fetch_assoc($sql)) {
    echo '<li><b>'.date("ymd", strtotime($row['timestamp'])).':</b> '.$row['title'].' &mdash; ';
    if (!empty($row['comm'])) { echo '<a href="/news.php?id='.$row['id'].'">View Comments</a>'; }
    else { echo '<a href="/news.php?id='.$row['id'].'">Leave a comment?</a>'; }
    echo '</li>';
}
include($footer);
?>