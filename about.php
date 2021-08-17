<?php
include('admin/class.lib.php');
include($header);

$p = isset($_GET['p']) ? $_GET['p'] : null;

if (empty($p)) {
	$sql = $database->get_assoc("SELECT * FROM `tcg_pages` WHERE `page_id`='1' AND `page_status`='Published'");
	if( empty($sql) ) {
		echo '<h1>Missing Content!</h1>
		<p>It appears that you haven\'t created a content for this page yet. You can add a simple information page from the admin panel via Admin > New Page Content.</p>';
	} else {
		echo '<h1>'.$sql['page_title'].'</h1>';
		$con = $sql['page_content'];
		eval('?>'.$con.'<?');
	}
}

else {
	$sql = $database->get_assoc("SELECT * FROM `tcg_pages` WHERE `page_slug`='$p' AND `page_parent`='1' AND `page_status`='Published'");
	if( empty($sql) ) {
		echo '<h1>Missing Content!</h1>
		<p>It appears that you haven\'t created a content for this page yet. You can add a simple information page from the admin panel via Admin > New Page Content.</p>';
	} else {
		echo '<h1>'.$sql['page_title'].'</h1>';
		$con = $sql['page_content'];
		eval('?>'.$con.'<?');
	}
}

include($footer);
?>