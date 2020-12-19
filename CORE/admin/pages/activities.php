<?php
$mon = isset($_POST['month']) ? $_POST['month'] : null;

$activity = 40;
if(!isset($_GET['p'])) { $p = 1; }
else { $p = (int)$_GET['p']; }
$from = (($p * $activity) - $activity);

echo '<h1>Recent Activities</h1>
<p>Below is the complete list of the TCG\'s activities from the admins down to the members\' activities. They can also be sorted by year or by month, whichever you wish to view or check, cap\'n!</p>

<table width="100%" cellspacing="0" border="0">
    <tr>
        <td class="record-label" width="85%">Activity</td>
        <td class="record-label" width="15%">Date</td>
    </tr>';
    $sql = $database->query("SELECT * FROM `tcg_activities` ORDER BY `id` DESC LIMIT $from, $activity");
    while ($row = mysqli_fetch_assoc($sql)) {
        echo '<tr>
            <td class="player-list">'.$row['activity'].'</td>
            <td class="player-list" align="center">'.date("Y/m/d", strtotime($row['date'])).'</td>
        </tr>';
    }
echo '</table><br />';

    // SHOW BLOG PAGINATION
    $total_results = mysqli_fetch_array($database->query("SELECT COUNT(*) as num FROM `tcg_activities`"));
    if (isset($_GET['p']) && $_GET['p']!="") { $page_no = $_GET['p']; }
    else { $page_no = 1; }

    $total_records_per_page = 20;

    $offset = ($page_no-1) * $total_records_per_page;
    $previous_page = $page_no - 1;
    $next_page = $page_no + 1;
    $adjacents = "2";

    $result_count = $database->query("SELECT COUNT(*) AS total_records FROM `tcg_activities`");
    $total_records = mysqli_fetch_array($result_count);
    $total_records = $total_records['total_records'];
    $total_no_of_pages = ceil($total_records / $total_records_per_page);
    $second_last = $total_no_of_pages - 1; // total pages minus 1

    echo '<div align="center">';
    echo '<small><strong>Page '.$page_no.' of '.$total_no_of_pages.'</strong></small><br />';
    echo '<ul class="pagination">';
    echo '<li '; if($page_no <= 1) { echo 'class="disabled"'; } echo '>
        <a '; if($page_no > 1) { echo 'href="index.php?page=activities&p='.$previous_page.'"'; } echo '>Previous</a>
        </li>';

        if ($total_no_of_pages <= 10){       
            for ($counter = 1; $counter <= $total_no_of_pages; $counter++){
                if ($counter == $page_no) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                else { echo '<li><a href="index.php?page=activities&p='.$counter.'">'.$counter.'</a></li>'; }
            }
        }
        elseif($total_no_of_pages > 10){
            if($page_no <= 4) {         
                for ($counter = 1; $counter < 11; $counter++) {
                    if ($counter == $page_no) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                    else { echo '<li><a href="index.php?page=activities&p='.$counter.'">'.$counter.'</a></li>'; }
                }
                echo '<li><a>...</a></li>';
                echo '<li><a href="index.php?page=activities&p='.$second_last.'">'.$second_last.'</a></li>';
                echo '<li><a href="index.php?page=activities&p='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';
            }
            elseif($page_no > 4 && $page_no < $total_no_of_pages - 4) {
                echo '<li><a href="index.php?page=activities&p=1">1</a></li>';
                echo '<li><a href="index.php?page=activities&p=2">2</a></li>';
                echo '<li><a>...</a></li>';
                for ($counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++) {
                    if ($counter == $page_no) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                    else { echo '<li><a href="index.php?page=activities&p='.$counter.'">'.$counter.'</a></li>'; }                  
            }
            echo '<li><a>...</a></li>';
            echo '<li><a href="index.php?page=activities&p='.$second_last.'">'.$second_last.'</a></li>';
            echo '<li><a href="index.php?page=activities&p='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';      
            }
            else {
                echo '<li><a href="index.php?page=activities&p=1">1</a></li>';
                echo '<li><a href="index.php?page=activities&p=2">2</a></li>';
                echo '<li><a>...</a></li>';
                for ($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++) {
                    if ($counter == $page_no) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                    else { echo '<li><a href="index.php?page=activities&p='.$counter.'">'.$counter.'</a></li>'; }
                }
            }
        }
        echo '<li '; if($page_no >= $total_no_of_pages) { echo 'class="disabled"'; } echo '>
        <a '; if($page_no < $total_no_of_pages) { echo 'href="index.php?page=activities&p='.$next_page; } echo '">Next</a>
        </li>';
        if($page_no < $total_no_of_pages) { echo '<li><a href="index.php?page=activities&p='.$total_no_of_pages.'">Last &rsaquo;&rsaquo;</a></li>'; }
    echo '</ul>';
    echo '</div>';

?>