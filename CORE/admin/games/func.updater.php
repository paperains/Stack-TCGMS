<?php
date_default_timezone_set("Asia/Manila"); // Timezone you're using

$dayoftheweek = date("l");
$todayis = date('Y-m-d');

if($dayoftheweek == 'Friday') { // The day it updates

	// Date to check for weekly updates
	$Weekly = $todayis;
	
	// Dates to check for biweekly (They're both for one set)
	$SetA = $todayis;
	$SetB = date("Y-m-d", strtotime("last Friday"));

} else {  

	// Date to check for weekly updates
	$Weekly = date("Y-m-d", strtotime("last Friday"));
	
	// Dates to check for biweekly (They're both for one set)
	$SetA = date("Y-m-d", strtotime("last Friday"));
	$SetB = date("Y-m-d", strtotime("-1 week last Friday"));

}

include('func.weekly.php'); // File for weekly set
include('func.biweekly.php'); // File for biweelky set
?>