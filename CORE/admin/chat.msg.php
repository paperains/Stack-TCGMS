<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i|Work+Sans:400,700" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/theme/general.css">
<style type="text/css">
body {
    background-color: transparent !important;
    background-image: none !important;
    color: #999999 !important;
}
a:link, a:active, a:visited {
	-webkit-transition: all 0.6s ease;
	-moz-transition: all 0.6s ease;
	-o-transition: all 0.6s ease;
	transition: all 0.6s ease;
	text-decoration: none;
	font-weight: 600;
	color: #dbada3;
}
a:hover { color: #e36973; }
</style>
<?php
include('class.lib.php');
$database = new Database;
$sql = $database->query("SELECT * FROM `tcg_chatbox` ORDER BY `timestamp` DESC");
$count = $database->num_rows("SELECT * FROM `tcg_chatbox`");

if ($count == 0) {
    echo '<div align="center" style="font-family: Open Sans; font-size: 14px;">Your chatbox is empty!<br />Be the first one to chat.</div>';
}

else {
    echo '<table width="100%" class="table table-bordered table-striped" border="0">';
    while ($row = mysqli_fetch_assoc($sql)) {
        echo '<tr><td width="100%" style="font-family: Open Sans; font-size: 14px;">
            <div align="right" style="font-size:11px;">'.
                date("F d, Y", strtotime($row['timestamp'])).' at '.date("h:i A", strtotime($row['timestamp'])).'
            </div>
            <b>';
                if ($row['url'] == "") { echo $row['name']; }
                else { echo '<a href="'.$row['url'].'" target="_blank">'.$row['name'].'</a>'; }
            echo ':</b> '.$row['msg'].'
        </td></tr>';
    }
    echo '</table>';
}
?>