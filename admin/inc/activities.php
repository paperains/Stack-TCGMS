<?php
/********************************************************
 * Moderation:		Activities
 * Description:		Show main page of activities list
 */
if( isset($_POST['delete']) ) {
	$delete = $database->query("DELETE FROM `tcg_activities` WHERE `act_date` < DATE_SUB(NOW(), INTERVAL 14 DAY)");
	if( !$delete ) { $error[] = "Sorry, there was an error and activity data was not deleted. ".mysqli_error().""; }
	else { $success[] = "The activity data from the past 30 days has been deleted!"; }
}

if( isset($_POST['selection']) ) {
	$getID = $_POST['id'];
	foreach( $getID as $id ) {
		$selection = $database->query("DELETE FROM `tcg_activities` WHERE `act_id`='$id'");
	}
	if( !$selection ) { $error[] = "Sorry, there was an error and the selected activity datas were not deleted. ".mysqli_error().""; }
	else { $success[] = "The selected activity data has been deleted!"; }
}

$activity = $settings->getValue( 'item_per_page' );
if( !isset($_GET['p']) ) { $p = 1; }
else { $p = (int)$_GET['p']; }
$from = (($p * $activity) - $activity);

echo '<p>Below is the complete list of the TCG\'s activities from the admins down to the members that you can check, cap\'n!</p>

<center>';
if ( isset($error) ) {
	foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
}

if ( isset($success) ) {
	foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
}
echo '</center>

<form method="post" action="'.$PHP_SELF.'?mod=activities">
<table width="100%" cellspacing="0" border="0" class="table table-bordered table-striped">
<thead>
	<tr>
		<td width="5%"></td>
		<td width="5%">ID</td>
		<td width="75%">Activity</td>
		<td width="15%">Date</td>
	</tr>
</thead>
<tbody>';
$sql = $database->query("SELECT * FROM `tcg_activities` ORDER BY `act_id` DESC LIMIT $from, $activity");
while( $row = mysqli_fetch_assoc($sql) ) {
	echo '<tr>
	<td align="center"><input type="checkbox" name="id[]" value="'.$row['act_id'].'" /></td>
	<td align="center">'.$row['act_id'].'</td>
	<td>'.$row['act_rec'].'</td>
	<td align="center">'.date("Y/m/d", strtotime($row['act_date'])).'</td>
	</tr>';
}
echo '<tr>
	<td align="center"><span class="arrow-right">↳</span></td>
	<td colspan="3">With selected: <input type="submit" name="selection" class="btn-cancel" value="Delete" /></td>
<tr>
</tbody>
</table>
</form><br />';

// Show activity pagination
$total_results = mysqli_fetch_array($database->query("SELECT COUNT(*) as num FROM `tcg_activities`"));
if( isset($_GET['p']) && $_GET['p'] != "" ) { $page_no = $_GET['p']; }
else { $page_no = 1; }

$total_records_per_page = $settings->getValue( 'item_per_page' );

$offset = ($page_no-1) * $total_records_per_page;
$previous_page = $page_no - 1;
$next_page = $page_no + 1;
$adjacents = "2";

$result_count = $database->query("SELECT COUNT(*) AS total_records FROM `tcg_activities`");
$total_records = mysqli_fetch_array($result_count);
$total_records = $total_records['total_records'];
$total_no_of_pages = ceil($total_records / $total_records_per_page);
$second_last = $total_no_of_pages - 1; // total pages minus 1

echo '<div align="center">
<small><strong>Page '.$page_no.' of '.$total_no_of_pages.'</strong></small><br />
<ul class="pagination">
	<li '; if( $page_no <= 1 ) { echo 'class="disabled"'; } echo '>
	<a '; if( $page_no > 1 ) { echo 'href="'.$PHP_SELF.'?mod=activities&p='.$previous_page.'"'; } echo '>Previous</a>
	</li>';

	if( $total_no_of_pages <= 10 ){       
		for( $counter = 1; $counter <= $total_no_of_pages; $counter++ ){
			if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
			else { echo '<li><a href="'.$PHP_SELF.'?mod=activities&p='.$counter.'">'.$counter.'</a></li>'; }
		}
	}
	elseif( $total_no_of_pages > 10 ) {
		if( $page_no <= 4 ) {         
			for( $counter = 1; $counter < 11; $counter++ ) {
				if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
				else { echo '<li><a href="'.$PHP_SELF.'?mod=activities&p='.$counter.'">'.$counter.'</a></li>'; }
			}
			echo '<li><a>...</a></li>';
			echo '<li><a href="'.$PHP_SELF.'?mod=activities&p='.$second_last.'">'.$second_last.'</a></li>';
			echo '<li><a href="'.$PHP_SELF.'?mod=activities&p='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';
		}
		elseif( $page_no > 4 && $page_no < $total_no_of_pages - 4 ) {
			echo '<li><a href="'.$PHP_SELF.'?mod=activities&p=1">1</a></li>';
			echo '<li><a href="'.$PHP_SELF.'?mod=activities&p=2">2</a></li>';
			echo '<li><a>...</a></li>';
			for( $counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++ ) {
				if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
				else { echo '<li><a href="'.$PHP_SELF.'?mod=activities&p='.$counter.'">'.$counter.'</a></li>'; }
			}
			echo '<li><a>...</a></li>';
			echo '<li><a href="'.$PHP_SELF.'?mod=activities&p='.$second_last.'">'.$second_last.'</a></li>';
			echo '<li><a href="'.$PHP_SELF.'?mod=activities&p='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';
		}
		else {
			echo '<li><a href="'.$PHP_SELF.'?mod=activities&p=1">1</a></li>';
			echo '<li><a href="'.$PHP_SELF.'?mod=activities&p=2">2</a></li>';
			echo '<li><a>...</a></li>';
			for( $counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++ ) {
				if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
				else { echo '<li><a href="'.$PHP_SELF.'?mod=activities&p='.$counter.'">'.$counter.'</a></li>'; }
			}
		}
	}
	echo '<li '; if($page_no >= $total_no_of_pages) { echo 'class="disabled"'; } echo '>
	<a '; if($page_no < $total_no_of_pages) { echo 'href="'.$PHP_SELF.'?mod=activities&p='.$next_page; } echo '">Next</a>
	</li>';
	if( $page_no < $total_no_of_pages ) {
		echo '<li><a href="'.$PHP_SELF.'?mod=activities&p='.$total_no_of_pages.'">Last &rsaquo;&rsaquo;</a></li>';
	}
echo '</ul>
</div>

<p>Do you wish to delete all activity data? If you do so, any new quick updates such as masteries and level ups will be removed from the last 14 days. This cannot be undone!</p>
<form method="post" action="'.$PHP_SELF.'?mod=activities">
<p><input type="submit" name="delete" class="btn-cancel" value="Mass Data Deletion"></p>
</form>';
?>