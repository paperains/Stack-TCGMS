<?php
/********************************************************
 * Sub Page:        User Logs
 * Description:     Show page of a specific user's logs
 */
$name = isset($_GET['name']) ? $_GET['name'] : null;
if( $sub == "user-logs" ) {
    if( empty($act) ) {
        /*if( empty($name) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {*/
            if( isset($_POST['mass-delete']) ) {
                $getID = $_POST['id'];
                foreach( $getID as $id ) {
                    $delete = $database->query("DELETE FROM `user_logs` WHERE `log_id`='$id'");
                }
                if ( !$delete ) { $error[] = "Sorry, there was an error and the logs were not deleted from the database. ".mysqli_error().""; }
                else { $success[] = "The user logs were deleted successfully from the database."; }
            }

            $logs = $settings->getValue( 'item_per_page' );
            if( !isset($_GET['p']) ) { $p = 1; }
            else { $p = (int)$_GET['p']; }
            $from = (($p * $logs) - $logs);
            $log = $database->query("SELECT * FROM `user_logs` WHERE `log_name`='$name' ORDER BY `log_date` DESC");

            echo '<h1>User Logs <span class="fas fa-angle-right" aria-hidden="true"></span> '.$name.'</h1>
            <p>Below shows the detailed log of '.$name.'\'s activities.</p>

            <center>';
            if ( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if ( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-logs">
            <table width="100%" cellspacing="0" class="table table-bordered table-striped">
            <thead>
            <tr>
                <td width="5%"></td>
                <td width="5%">ID</td>
                <td width="10%">Date</td>
                <td width="30%">Log Title</td>
                <td width="30%">Rewards</td>
                <td width="20%">Action</td>
            </tr>
            </thead>
            <tbody>';
            while( $row = mysqli_fetch_assoc($log) ) {
                echo '<tr>
                <td align="center"><input type="checkbox" name="id[]" value="'.$row['id'].'" /></td>
                <td align="center">'.$row['log_id'].'</td>
                <td align="center">'.date("Y/m/d", strtotime($row['log_date'])).'</td>
                <td align="center">'.$row['log_title'];
                    if( empty($row['log_subtitle']) ) {}
                    else { echo ' '.$row['log_subtitle']; }
                echo '</td>
                <td align="center">';
                if (mb_strlen($row['log_rewards']) >= 90) {
                    $row['log_rewards'] = substr($row['log_rewards'], 0, 90);
                    $row['log_rewards'] = $row['log_rewards'] . "...";
                    echo $row['log_rewards'];
                } else { echo $row['log_rewards']; }
                echo '</td>
                <td align="center">
                    <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&action=edit&id='.$row['log_id'].'\';" class="btn-success" /><span class="fas fa-cog" aria-hidden="true"></span></button> 
                    <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&action=delete&id='.$row['log_id'].'\';" class="btn-cancel" /><span class="fas fa-trash-alt" aria-hidden="true"></span></button>
                    </td>
                </tr>';
            }
            echo '<tr>
                <td align="center"><span class="arrow-right">↳</span></td>
                <td colspan="5">With selected: <input type="submit" name="mass-delete" class="btn-cancel" value="Delete" /></td>
            <tr>
            <tbody>
            </table>
            </form><br /><br />';

            // Show activity pagination
            $total_results = mysqli_fetch_array($database->query("SELECT COUNT(*) as num FROM `user_logs` WHERE `log_name`='$id'"));
            if( isset($_GET['p']) && $_GET['p'] != "" ) { $page_no = $_GET['p']; }
            else { $page_no = 1; }

            $total_records_per_page = $settings->getValue( 'item_per_page' );
            $offset = ($page_no-1) * $total_records_per_page;
            $previous_page = $page_no - 1;
            $next_page = $page_no + 1;
            $adjacents = "2";

            $result_count = $database->query("SELECT COUNT(*) AS total_records FROM `user_logs`");
            $total_records = mysqli_fetch_array($result_count);
            $total_records = $total_records['total_records'];
            $total_no_of_pages = ceil($total_records / $total_records_per_page);
            $second_last = $total_no_of_pages - 1; // total pages minus 1

            echo '<div align="center">
            <small><strong>Page '.$page_no.' of '.$total_no_of_pages.'</strong></small><br />
            <ul class="pagination">
                <li '; if( $page_no <= 1 ) { echo 'class="disabled"'; } echo '>
                <a '; if( $page_no > 1 ) { echo 'href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$previous_page.'"'; } echo '>Previous</a>
                </li>';

                if( $total_no_of_pages <= 10 ){       
                    for( $counter = 1; $counter <= $total_no_of_pages; $counter++ ){
                        if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                        else { echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$counter.'">'.$counter.'</a></li>'; }
                    }
                }
                elseif( $total_no_of_pages > 10 ) {
                    if( $page_no <= 4 ) {         
                        for( $counter = 1; $counter < 11; $counter++ ) {
                            if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                            else { echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$counter.'">'.$counter.'</a></li>'; }
                        }
                        echo '<li><a>...</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$second_last.'">'.$second_last.'</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';
                    }
                    elseif( $page_no > 4 && $page_no < $total_no_of_pages - 4 ) {
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p=1">1</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p=2">2</a></li>';
                        echo '<li><a>...</a></li>';
                        for( $counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++ ) {
                            if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                            else { echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$counter.'">'.$counter.'</a></li>'; }
                        }
                        echo '<li><a>...</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$second_last.'">'.$second_last.'</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';
                    }
                    else {
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p=1">1</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p=2">2</a></li>';
                        echo '<li><a>...</a></li>';
                        for( $counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++ ) {
                            if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                            else { echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$counter.'">'.$counter.'</a></li>'; }
                        }
                    }
                }
                echo '<li '; if($page_no >= $total_no_of_pages) { echo 'class="disabled"'; } echo '>
                <a '; if($page_no < $total_no_of_pages) { echo 'href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$next_page; } echo '">Next</a>
                </li>';
                if( $page_no < $total_no_of_pages ) {
                    echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&p='.$total_no_of_pages.'">Last &rsaquo;&rsaquo;</a></li>';
                }
            echo '</ul>
            </div>';
        /*}*/
    }
    
    
    
    /********************************************************
     * Action:          Delete User Activity Logs
     * Description:     Show page for deleting user logs
     */
    if( $act == "delete" ) {
        if( isset($_POST['delete']) ) {
            $id = $_POST['id'];
            $delete = $database->query("DELETE FROM `user_logs` WHERE `log_id`='$id'");
            if ( !$delete ) { $error[] = "Sorry, there was an error and the log was not deleted from the database. ".mysqli_error().""; }
            else { $success[] = "The user log has been successfully deleted from the database."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $get = $database->get_assoc("SELECT * FROM `user_logs` WHERE `log_id`='$id'");
            echo '<h1>Delete a User Log</h1>
            <center>';
            if ( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if ( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-logs&name='.$name.'&action=delete&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <p>Are you sure you want to delete this log from '.$get['log_name.'].'? <b>This action can not be undone!</b><br />
            Click on the button below to delete the log:<br />
            <input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Edit User Activity Logs
     * Description:     Show page for editing user logs
     */
    if( $act == "edit" ) {
        if( isset($_POST['update']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $type = $sanitize->for_db($_POST['type']);
            $title = $sanitize->for_db($_POST['title']);
            $subtitle = $sanitize->for_db($_POST['subtitle']);
            $reward = $sanitize->for_db($_POST['rewards']);
            $date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['date'];

            $update = $database->query("UPDATE `user_logs` SET `log_type`='$type', `log_title`='$title', `log_subtitle`='$subtitle', `log_rewards`='$reward', `log_date`='$date' WHERE `log_id`='$id'");

            if ( !$update ) { $error[] = "Sorry, there was an error and the log was not updated from the database. ".mysqli_error().""; }
            else { $success[] = "The user log has been successfully updated from the database."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $row = $database->get_assoc("SELECT * FROM `user_logs` WHERE `log_id`='$id'");
            $old_month = date("F", strtotime($row['log_date']));
            $old_date = date("d", strtotime($row['log_date']));
            $old_year = date("Y", strtotime($row['log_date']));
            $oldm = date("m", strtotime($row['log_date']));

            echo '<h1>Edit a User Log</h1>
            <p>Edit this set of log from '.$row['log_name'].'\'s records.</p>

            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }

            echo '<form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-logs&action=edit&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <table width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td width="15%" valign="middle"><b>Date:</b></td>
                <td width="2%">&nbsp;</td>
                <td width="83%">
                    <select name="month" id="month" style="width:27%;">
                        <option value="'.$oldm.'">'.$old_month.'</option>';
                        for($m=1; $m<=12; $m++) {
                            if ($m < 10) { $_mon = "0$m"; }
                            else { $_mon = $m; }
                            echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                        }
                    echo '</select>
                    <input type="text" name="date" id="date" size="1" value="'.$old_date.'" />';
                    $start = date('Y');
                    $end = $start-40;
                    $yearArray = range($start,$end);
                    echo ' <select name="year" id="year">
                    <option value="'.$old_year.'">'.$old_year.'</option>';
                    foreach ($yearArray as $year) {
                        $selected = ($year == $start) ? 'selected' : '';
                        echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                    }
                    echo '</select>
                </td>
            </tr>
            <tr>
                <td valign="middle"><b>Log Type:</b></td>
                <td>&nbsp;</td>
                <td>
                    <select name="type" style="width:44%;">
                        <option value="'.$row['log_type'].'">Current: '.$row['log_type'].'</option>
                        <option value="Rewards">Rewards</option>
                        <option value="Pulls">Pulls</option>
                        <option value="Releases">Releases</option>
                        <option value="Exchanges">Exchanges</option>
                        <option value="Purchases">Purchases</option>
                        <option value="Service">Service</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Set A">Set A</option>
                        <option value="Set B">Set B</option>
                        <option value="Special">Special</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td valign="middle"><b>Title:</b></td>
                <td>&nbsp;</td>
                <td><input type="text" name="title" value="'.$row['log_title'].'" size="40" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>Subtitle:</b></td>
                <td>&nbsp;</td>
                <td><input type="text" name="subtitle" value="'.$row['log_subtitle'].'" size="40" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>Rewards:</b></td>
                <td>&nbsp;</td>
                <td><textarea name="rewards" rows="5" style="width:45%;" />'.$row['log_rewards'].'</textarea></td>
            </tr>
            <tr>
                <td colspan="3">
                    <input type="submit" name="update" class="btn-success" value="Edit User Log" /> 
                    <input type="reset" name="reset" class="btn-cancel" value="Reset" />
                </td>
            </tr>
            </table>
            </form>
            </center>';
        }
    }
} // end sub page (user logs)




/********************************************************
 * Sub Page:        User Trades
 * Description:     Show page of user's trade list
 */
else if( $sub == "user-trades" ) {
    if( empty($act) ) {
        if( empty($name) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            if( isset($_POST['mass-delete']) ) {
                $getID = $_POST['id'];
                foreach( $getID as $id ) {
                    $delete = $database->query("DELETE FROM `user_trades` WHERE `trd_id`='$id'");
                }
                if ( !$delete ) { $error[] = "Sorry, there was an error and the trade logs were not deleted from the database. ".mysqli_error().""; }
                else { $success[] = "The trade logs were deleted successfully from the database."; }
            }
            
            $trades = $settings->getValue( 'item_per_page' );
            if( !isset($_GET['p']) ) { $p = 1; }
            else { $p = (int)$_GET['p']; }
            $from = (($p * $trades) - $trades);
            $log = $database->query("SELECT * FROM `user_trades` WHERE `trd_name`='$name' ORDER BY `trd_date` DESC");

            echo '<h1>User Trade Logs</h1>
            <p>Below shows the detailed log of the user\'s activities.</p>';

            $sql = $database->get_assoc("SELECT * FROM `user_trades_rec` WHERE `trd_name`='$name'");
            echo '<p>'.$name.' has turned in a total of <b>'.$sql['turnins'].'</b> trade cards, has redeemed a total of <b>'.$sql['redeemed'].'</b> points and currently has <b>'.$sql['points'].'</b> unredeemed points.</p>

            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-trades">
            <table width="100%" cellspacing="0" class="table table-bordered table-striped">
            <thead>
            <tr>
                <td width="5%"></td>
                <td width="5%">ID</td>
                <td width="10%">Date</td>
                <td width="30%">Log</td>
                <td width="30%">Trader</td>
                <td width="20%">Action</td>
            </tr>
            </thead>
            <tbody>';
            while( $row = mysqli_fetch_assoc($log) ) {
                $tradelog = "Traded ".$row['trd_out']." for ".$row['trd_inc'];
                if (mb_strlen($tradelog) >= 60) {
                    $tradelog = substr($tradelog, 0, 60);
                    $tradelog = $tradelog . "...";
                }
                echo '<tr>
                <td align="center"><input type="checkbox" name="id[]" value="'.$row['trd_id'].'" /></td>
                <td align="center">'.$row['trd_id'].'</td>
                <td align="center">'.date("Y/m/d", strtotime($row['trd_date'])).'</td>
                <td align="center">'.$tradelog.'</td>
                <td align="center">With '.$row['trd_trader'].'</td>
                <td align="center">
                    <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-trades&action=edit&id='.$row['trd_id'].'\';" class="btn-success" /><span class="fas fa-cog" aria-hidden="true"></span></button> 
                    <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-trades&action=delete&id='.$row['trd_id'].';" class="btn-cancel" /><span class="fas fa-times" aria-hidden="true"></span></button>
                </td>
                </tr>';
            }
            echo '<tr>
                <td align="center"><span class="arrow-right">↳</span></td>
                <td colspan="5">With selected: <input type="submit" name="mass-delete" class="btn-cancel" value="Delete" /></td>
            <tr></tbody>
            </table>
            </form>';

            // Show trade pagination
            $total_results = mysqli_fetch_array($database->query("SELECT COUNT(*) as num FROM `user_trades` WHERE `trd_name`='$id'"));
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
                <a '; if( $page_no > 1 ) { echo 'href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$previous_page.'"'; } echo '>Previous</a>
                </li>';

                if( $total_no_of_pages <= 10 ){       
                    for( $counter = 1; $counter <= $total_no_of_pages; $counter++ ){
                        if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                        else { echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$counter.'">'.$counter.'</a></li>'; }
                    }
                }
                elseif( $total_no_of_pages > 10 ) {
                    if( $page_no <= 4 ) {         
                        for( $counter = 1; $counter < 11; $counter++ ) {
                            if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                            else { echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$counter.'">'.$counter.'</a></li>'; }
                        }
                        echo '<li><a>...</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$second_last.'">'.$second_last.'</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';
                    }
                    elseif( $page_no > 4 && $page_no < $total_no_of_pages - 4 ) {
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p=1">1</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p=2">2</a></li>';
                        echo '<li><a>...</a></li>';
                        for( $counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++ ) {
                            if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                            else { echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$counter.'">'.$counter.'</a></li>'; }
                        }
                        echo '<li><a>...</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$second_last.'">'.$second_last.'</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$total_no_of_pages.'">'.$total_no_of_pages.'</a></li>';
                    }
                    else {
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p=1">1</a></li>';
                        echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p=2">2</a></li>';
                        echo '<li><a>...</a></li>';
                        for( $counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++ ) {
                            if( $counter == $page_no ) { echo '<li class="active"><a>'.$counter.'</a></li>'; }
                            else { echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$counter.'">'.$counter.'</a></li>'; }
                        }
                    }
                }
                echo '<li '; if($page_no >= $total_no_of_pages) { echo 'class="disabled"'; } echo '>
                <a '; if($page_no < $total_no_of_pages) { echo 'href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$next_page; } echo '">Next</a>
                </li>';
                if( $page_no < $total_no_of_pages ) {
                    echo '<li><a href="'.$PHP_SELF.'?mod=members&sub=user-trades&p='.$total_no_of_pages.'">Last &rsaquo;&rsaquo;</a></li>';
                }
            echo '</ul>
            </div>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Delete User Trade Logs
     * Description:     Show page for deleting user logs
     */
    if( $act == "delete" ) {
        if( isset($_POST['delete']) ) {
            $id = $_POST['id'];
            $delete = $database->query("DELETE FROM `user_trades` WHERE `trd_id`='$id'");
            if ( !$delete ) { $error[] = "Sorry, there was an error and the trade log was not deleted from the database. ".mysqli_error().""; }
            else { $success[] = "The trade log has been successfully deleted from the database."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $get = $database->get_assoc("SELECT * FROM `user_trades` WHERE `trd_id`='$id'");
            echo '<h1>Delete a Trade Log</h1>
            <center>';
            if ( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if ( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-trades&action=delete&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <p>Are you sure you want to delete this log from '.$get['trd_name'].'? <b>This action can not be undone!</b><br />
            Click on the button below to delete the log:<br />
            <input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Edit User Trade Logs
     * Description:     Show page for editing user trade logs
     */
    if( $act == "edit" ) {
        if( isset($_POST['update']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $trader = $sanitize->for_db($_POST['trader']);
            $out = $sanitize->for_db($_POST['outgoing']);
            $inc = $sanitize->for_db($_POST['incoming']);
            $date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];

            $update = $database->query("UPDATE `user_trades` SET `trd_trader`='$trader', `trd_out`='$out', `trd_inc`='$inc', `trd_date`='$date' WHERE `trd_id`='$id'");

            if ( !$update ) { $error[] = "Sorry, there was an error and the trade log was not updated from the database. ".mysqli_error().""; }
            else { $success[] = "The user trade log has been successfully updated from the database."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $row = $database->get_assoc("SELECT * FROM `user_trades` WHERE `trd_id`='$id'");
            $old_month = date("F", strtotime($row['trd_date']));
            $old_date = date("d", strtotime($row['trd_date']));
            $old_year = date("Y", strtotime($row['trd_date']));
            $oldm = date("m", strtotime($row['trd_date']));
            $oldy = date("Y", strtotime($row['trd_date']));

            echo '<h1>Edit Trade Logs</h1>
            <p>Edit '.$row['trd_name'].'\'s trade logs.</p>

            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }

            echo '<form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-trades&action=edit&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <table width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td width="20" valign="middle"><b>Date:</b></td>
                <td width="2%">&nbsp;</td>
                <td width="78%">
                    <select name="month" id="month" style="width: 25%;">
                    <option value="'.$oldm.'">'.$old_month.'</option>';
                    for($m=1; $m<=12; $m++) {
                        if ($m < 10) { $_mon = "0$m"; }
                        else { $_mon = $m; }
                        echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                    }
                    echo '</select>
                    <input type="text" name="date" id="date" size="1" value="'.$old_date.'" />';
                    $start = date('Y');
                    $end = $start-40;
                    $yearArray = range($start,$end);
                    echo ' <select name="year" id="year">
                    <option value="'.$oldy.'">'.$old_year.'</option>';
                    foreach ($yearArray as $year) {
                        $selected = ($year == $start) ? 'selected' : '';
                        echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                    }
                    echo '</select>
                </td>
            </tr>
            <tr>
                <td valign="middle"><b>Traded With:</b></td>
                <td>&nbsp;</td>
                <td><input type="text" name="trader" value="'.$row['trd_trader'].'" size="40" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>Outgoing:</b></td>
                <td>&nbsp;</td>
                <td><input type="text" name="outgoing" value="'.$row['trd_out'].'" size="40" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>Incoming:</b></td>
                <td>&nbsp;</td>
                <td><input type="text" name="incoming" value="'.$row['trd_inc'].'" size="40" /></td>
            </tr>
            <tr>
                <td colspan="3">
                    <input type="submit" name="update" class="btn-success" value="Edit Trade Log" /> 
                    <input type="reset" name="reset" class="btn-cancel" value="Reset" />
                </td>
            </tr>
            </table>
            </form>
            </center>';
        }
    }
} // end sub page (user trades)




/********************************************************
 * Sub Page:        User Wishes
 * Description:     Show main page of user wishes list
 */
else if( $sub == "user-wishes" ) {
    if( empty($act) ) {
        date_default_timezone_set( $settings->getValue('tcg_timezone') );
        $timestamp = date('Y-m-d');
        
        if( isset($_POST['mass-grant']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $grant = $database->query("UPDATE `user_wishes` SET `wish_date`='$timestamp', `wish_status`='Granted' WHERE `wish_id`='$id'");
            }
            if ( !$grant ) { $error[] = "Sorry, there was an error and the wishes were not granted. ".mysqli_error().""; }
            else { $success[] = "The wishes has been granted successfully!"; }
        }

        if( isset($_POST['mass-delete']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $delete = $database->query("DELETE FROM `user_wishes` WHERE `wish_id`='$id'");
            }
            if ( !$delete ) { $error[] = "Sorry, there was an error and the wishes were not delete from the database. ".mysqli_error().""; }
            else { $success[] = "The wishes has been deleted successfully from the database!"; }
        }
        
        echo '<h1>User Wishes</h1>
        <p>&raquo; Do you want to <a href="'.$PHP_SELF.'?mod=wishes&action=add">add a wish</a>?</p>
        
        <center>';
        if( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }
        echo '</center>

        <ul class="tabs" data-persist="true">
            <li><a href="#pending">Pending</a></li>
            <li><a href="#granted">Granted</a></li>
        </ul>

        <div class="tabcontents" align="left">
            <div id="pending">
                <h2>Pending</h2>';
                $select = $database->query("SELECT * FROM `user_wishes` WHERE `wish_status`='Pending' ORDER BY `wish_id` ASC");
                $count = mysqli_num_rows($select);
                if( $count == 0 ) { echo '<p align="center">There are currently no wishes under this status.</p>'; }
                else {
                    echo '<form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-wishes">
                    <table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <td width="5%"></td>
                        <td width="5%">ID</td>
                        <td width="15%">Player</td>
                        <td width="45%">Wish</td>
                        <td width="10%">Date</td>
                        <td width="20%">Action</td>
                    </tr>
                    </thead>
                    <tbody>';
                    while( $row = mysqli_fetch_assoc($select) ) {
                        echo '<tr>
                        <td align="center"><input type="checkbox" name="id[]" value="'.$row['wish_id'].'" /></td>
                        <td align="center">'.$row['wish_id'].'</td>
                        <td align="center">'.$row['wish_name'].'</td>
                        <td align="center">'.$row['wish_text'].'</td>
                        <td align="center">'.$row['wish_date'].'</td>
                        <td align="center">
                            <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-wishes&action=approve&id='.$row['wish_id'].'\';" class="btn-default"><span class="fas fa-check" aria-hidden="true"></span></button> 
                            <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-wishes&action=edit&id='.$row['wish_id'].'\';" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
                            <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-wishes&action=delete&id='.$row['wish_id'].'\';" class="btn-cancel"><span class="fas fa-times" aria-hidden="true"></span></button>
                        </td>
                        </tr>';
                    }
                    echo '<tr>
                        <td align="center"><span class="arrow-right">↳</span></td>
                        <td colspan="5">With selected: 
                            <input type="submit" name="mass-grant" class="btn-success" value="Grant" />
                            <input type="submit" name="mass-delete" class="btn-cancel" value="Delete" />
                        </td>
                    <tr></tbody>
                    </table>
                    </form>';
                }
            echo '</div>

            <div id="granted">
                <h2>Granted</h2>';
                $select2 = $database->query("SELECT * FROM `user_wishes` WHERE `wish_status`='Granted' ORDER BY `wish_date` DESC");
                $count2 = mysqli_num_rows($select2);
                if( $count2 == 0 ) { echo '<p align="center">There are currently no wishes under this status.</p>'; }
                else {
                    echo '<form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-wishes">
                    <table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <td width="5%"></td>
                        <td width="5%">ID</td>
                        <td width="15%">Player</td>
                        <td width="45%">Wish</td>
                        <td width="10%">Date</td>
                        <td width="10%">Action</td>
                    </tr>
                    </thead>
                    </tbody>';
                    while( $row2 = mysqli_fetch_assoc($select2) ) {
                        echo '<tr>
                        <td align="center"><input type="checkbox" name="id[]" value="'.$row2['wish_id'].'" /></td>
                        <td align="center">'.$row2['wish_id'].'</td>
                        <td align="center">'.$row2['wish_name'].'</td>
                        <td align="center">'.$row2['wish_text'].'</td>
                        <td align="center">'.$row2['wish_date'].'</td>
                        <td align="center">
                            <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-wishes&action=edit&id='.$row2['wish_id'].'\';" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
                            <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-wishes&action=delete&id='.$row2['wish_id'].'\';" class="btn-cancel"><span class="fas fa-times" aria-hidden="true"></span></button>
                        </td>
                    </tr>';
                    }
                    echo '<tr>
                        <td align="center"><span class="arrow-right">↳</span></td>
                        <td colspan="5">With selected: <input type="submit" name="mass-delete" class="btn-cancel" value="Delete" /></td>
                    <tr></tbody>
                    </table>
                    </form>';
                }
            echo '</div>
        </div>';
    }
    
    
    
    /********************************************************
     * Action:          Approve Wishes
     * Description:     Show page for approving wishes
     */
    if( $act == "approve" ) {
        if( empty($id) ) {
            echo "<p>This page shouldn't be accessed directly! Please go back and try something else.</p>";
        } else {
            echo '<h1>Approve a User Wish</h1>';
            date_default_timezone_set( $settings->getValue('tcg_timezone') );
            $timestamp = date('Y-m-d');

            $update = "UPDATE `user_wishes` SET `wish_date`='$timestamp', `wish_status`='Granted' WHERE `wish_id`='$id'";

            if( !$update ) { echo '<p>Sorry, there was an error and the wish was not granted.</p>'; }
            else {
                $get = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_id`='$id'");
                echo '<p>You have just granted a wish submitted by '.$get['wish_name'].'.';
            }
        }
    }
    
    
    
    /********************************************************
     * Action:          Delete Wishes
     * Description:     Show page for deleting wishes
     */
    if( $act == "delete" ) {
        if( isset($_POST['delete']) ) {
            $id = $_POST['id'];
            $delete = $database->query("DELETE FROM `user_wishes` WHERE `wish_id`='$id'");
            if( !$delete ) { $error[] = "Sorry, there was an error and the wish was not deleted. ".mysqli_error().""; }
            else { $success[] = "The wish has been deleted from the database!"; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $getdata = $database->query("SELECT * FROM `user_wishes` WHERE `wish_id`='$id'");
            echo '<h1>Delete a User Wish</h1>
            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-wishes&action=delete">
            <input type="hidden" name="id" value="'.$id.'" />
            <p>Are you sure you want to delete this wish? <b>This action can not be undone!</b><br />
            Click on the button below to delete the wish:<br />
            <input type="submit" name="delete" class="btn-cancel" value="Delete"></p>
            </form>';
        }
    }
    
    
    
    /********************************************************
     * Action:          Add Wishes
     * Description:     Show page for adding wishes
     */
    if( $act == "add" ) {
        if( isset($_POST['add']) ) {
            $name = $sanitize->for_db($_POST['name']);
            $type = $sanitize->for_db($_POST['type']);
            $word = $sanitize->for_db($_POST['word']);
            $amnt = $sanitize->for_db($_POST['amount']);
            $cat = $sanitize->for_db($_POST['category']);
            $set = $sanitize->for_db($_POST['set']);
            $wish = $sanitize->for_db($_POST['wish']);
            $stat = $sanitize->for_db($_POST['status']);
            $date = date('Y-m-d', strtotime("now"));

            // Add wish blurbs for the database
            if ($type == "1" && !empty($word)) { $wish = "I wish for choice cards spelling ".$word."!"; }
            if ($type == "2" && !empty($amnt)) { $wish = "I wish for a pack of ".$amnt." choice cards!"; }
            if ($type == "3" && !empty($amnt)) { $wish = "I wish for a pack of ".$amnt." random cards!"; }
            if ($type == "4" && $color != "None") { $wish = "I wish for choice cards from any ".$cat." decks!"; }
            if ($type == "5" && $amnt == "2") { $wish = "I wish for a double deck release!"; }
            if ($type == "6" && $set == "None") { $wish = "I wish for double rewards for the ".$set." set!"; }

            $result = $database->query("INSERT INTO `user_wishes` ( `wish_name`,`wish_type`,`wish_word`,`wish_amount`,`wish_cat`,`wish__set`,`wish_text`,`wish_status`,`wish_date`) VALUES ('$name','$type','$word','$amnt','$cat','$set','$wish','Pending','$date')") or print ("Can't add wish.<br />" . mysqli_connect_error());

            if( !$result ) { $error[] = "Sorry, there was an error and the wish was not added to the database. ".mysqli_error().""; }
            else { $success[] = "You have successfully added a wish!"; }
        }

        echo '<h1>Add a User Wish</h1>
        <p>Make sure to only fill up the fields according to the wish type (e.g. Spell Choice should only have the Word field filled).</p>

        <center>';
        if( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }
        echo '</center>
        
        <form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-wishes&action=add">
        <table width="100%" cellspacing="0" cellpadding="5">
        <tr>
            <td width="49%" valign="top">
                <b>Wish Type:</b><br />
                <select name="type" id="type" style="width:95%;" />
                    <option value="1">Spell Choice</option>
                    <option value="2">Choice Pack</option>
                    <option value="3">Random Pack</option>
                    <option value="4">Category Choice</option>
                    <option value="5">Deck Release</option>
                    <option value="6">Game Rewards</option>
                </select>
            </td>

            <td width="2%">&nbsp;</td>

            <td width="49%" valign="top">
                <b>Spell Word:</b><br />
                <input type="text" name="word" id="word" style="width:90%;" placeholder="SUMMER2020" />
            </td>
        </tr>
        <tr>
            <td valign="top">
                <b>Card Amount:</b><br />
                <input type="text" name="amount" id="amount" style="width:90%;" placeholder="0" />
            </td>

            <td>&nbsp;</td>

            <td valign="top">
                <b>Choice Category:</b><br />
                <select name="category" id="category" style="width:95%;" />
                    <option value="0">Not applicable</option>';
                    $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                    for($i=1; $i<=$c; $i++) {
                        $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
                        echo '<option value="'.$i.'">'.$cat['cat_name'].'</option>';
                    }
                echo '</select>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <b>Game Set:</b><br />
                <select name="set" id="set" style="width:95%;" />
                    <option value="None">Not applicable</option>
                    <option value="Weekly">Weekly Set</option>
                    <option value="Set A">Bi-weekly A Set</option>
                    <option value="Set B">Bi-weekly B Set</option>
                    <option value="Monthly">Monthly Set</option>
                    <option value="Special">Special Set</option>
                </select>
            </td>

            <td>&nbsp;</td>

            <td valign="bottom" align="center">
                <input type="submit" name="add" class="btn-success" value="Add Wish" />
                <input type="reset" name="reset" class="btn-cancel" value="Reset" />
            </td>
        </tr>
        </table>
        </form>';
    }
    
    
    
    /********************************************************
     * Action:          Edit Wishes
     * Description:     Show page for editing wishes
     */
    if( $act == "edit" ) {
        if( isset($_POST['update']) ) {
            $name = $sanitize->for_db($_POST['name']);
            $type = $sanitize->for_db($_POST['type']);
            $word = $sanitize->for_db($_POST['word']);
            $amnt = $sanitize->for_db($_POST['amount']);
            $color = $sanitize->for_db($_POST['category']);
            $set = $sanitize->for_db($_POST['set']);
            $wish = $sanitize->for_db($_POST['wish']);
            $stat = $sanitize->for_db($_POST['status']);
            $id = $sanitize->for_db($_POST['id']);

            $result = $database->query("UPDATE `user_wishes` SET `wish_name`='$name', `wish_type`='$type', `wish_word`='$word', `wish_amount`='$amnt', `wish_cat`='$cat', `wish_set`='$set', `wish_text`='$wish', `wish_status`='$stat' WHERE `wish_id`='$id'") or print ("Can't update wish.<br />" . mysqli_connect_error());

            header("Location: index.php?mod=members&sub=user-wishes");
        }

        if( !isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id']) ) { die("Invalid entry ID."); }
        else { $id = (int)$_GET['id']; }

        $row = $database->get_assoc("SELECT * FROM `user_wishes` WHERE `wish_id`='$id'") or print ("Can't select entry.<br />" . $row . "<br />" . mysqli_connect_error());
        $old_name = stripslashes($row['wish_name']);
        $old_stat = stripslashes($row['wish_status']);
        $old_wish = stripslashes($row['wish_wish']);
        $old_type = stripslashes($row['wish_type']);
        $old_word = stripslashes($row['wish_word']);
        $old_amnt = stripslashes($row['wish_amount']);
        $old_cat = stripslashes($row['wish_cat']);
        $old_set = stripslashes($row['wish_set']);

        echo '<h1>Edit a User Wish</h1>
        <form method="post" action="'.$PHP_SELF.'?mod=members&sub=user-wishes&action=edit&id='.$id.'">
        <input type="hidden" name="id" value="'.$id.'" />
        <table width="100%" cellspacing="0" cellpadding="5">
        <tr>
            <td width="49%" valign="top">
                <b>Wished by:</b><br />
                <input type="text" name="name" style="width:96%;" value="'.$old_name.'" />
            </td>

            <td width="2%">&nbsp;</td>

            <td width="49%" valign="top">
                <b>Wish Type:</b><br />
                <select name="type" style="width:96%;" />
                    <option value="'.$old_type.'">Current: '.$old_type.'</option>
                    <option value="1">Spell Choice</option>
                    <option value="2">Choice Pack</option>
                    <option value="3">Random Pack</option>
                    <option value="4">Category Choice</option>
                    <option value="5">Deck Release</option>
                    <option value="6">Game Rewards</option>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <b>Spell Word:</b><br />
                <input type="text" name="word" style="width:96%;" value="'.$old_word.'" />
            </td>

            <td>&nbsp;</td>

            <td valign="top">
                <b>Card Amount:</b><br />
                <input type="text" name="amount" style="width:96%;" value="'.$old_amnt.'" />
            </td>
        </tr>
        <tr>
            <td valign="top">
                <b>Choice Category:</b><br />
                <select name="category" style="width:96%;" />';
                if( $old_cat == 0 ) { echo '<option value="0">Current: None</option>'; }
                else {
                    $get = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$old_cat'");
                    echo '<option value="'.$get['cat_id'].'">Current: '.$get['cat_name'].'</option>';
                }
                $c = $database->num_rows("SELECT * FROM `tcg_cards_cat`");
                for($i=1; $i<=$c; $i++) {
                    $cat = $database->get_assoc("SELECT * FROM `tcg_cards_cat` WHERE `cat_id`='$i'");
                    echo '<option value="'.$i.'">'.$cat['cat_name'].'</option>';
                }
                echo '</select>
            </td>

            <td>&nbsp;</td>

            <td valign="top">
                <b>Game Set:</b><br />
                <select name="set" style="width:96%;" />
                    <option value="'.$old_set.'">Current: '.$old_set.'</option>
                    <option value="None">Not applicable</option>
                    <option value="Weekly">Weekly Set</option>
                    <option value="Set A">Bi-weekly A Set</option>
                    <option value="Set B">Bi-weekly B Set</option>
                    <option value="Monthly">Monthly Set</option>
                    <option value="Special">Special Set</option>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top" colspan="3">
                <b>Wish Text:</b><br />
                <input type="text" name="wish" value="'.$old_wish.'" style="width:96%;">
            </td>
        </tr>
        <tr>
            <td valign="top">
                <b>Status:</b><br />
                <select name="status" style="width:96%;">
                    <option value="'.$old_stat.'">Current: '.$old_stat.'</option>
                    <option value="Pending">Pending</option>
                    <option value="Granted">Granted</option>
                </select>
            </td>

            <td>&nbsp;</td>

            <td valign="bottom" align="right"><input type="submit" name="update" class="btn-success" value="Edit Wish" /></td>
        </tr>
        </table>
        </form>';
    }
} // end sub page (user wishes)




/********************************************************
 * Moderation:      Members
 * Description:     Show main page of members list
 */
else {
    if( empty($act) ) {
        if( isset($_POST['mass-hiatus']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $hiatus = $database->query("UPDATE FROM `user_list` SET `usr_status`='Hiatus' WHERE `user_id`='$id'");
            }
            if( !$hiatus ) { $error[] = "Sorry, there was an error and the members were not put to Hiatus. ".mysqli_error().""; }
            else { $success[] = "The members were put to Hiatus successfully!"; }
        }

        if( isset($_POST['mass-inactive']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $inactive = $database->query("UPDATE FROM `user_list` SET `usr_status`='Inactive' WHERE `user_id`='$id'");
            }
            if( !$inactive ) { $error[] = "Sorry, there was an error and the members were not put to Inactive. ".mysqli_error().""; }
            else { $success[] = "The members were put to Inactive successfully!"; }
        }

        if( isset($_POST['mass-retired']) ) {
            $getID = $_POST['id'];
            $date = date("Y-m-d", strtotime("now"));
            foreach( $getID as $id ) {
                // Fetch data first and add to retired list
                $sql = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_id`='$id'");
                $insert = $database->query("INSERT INTO `user_list_quit` (`usr_name`,`usr_mcard`,`usr_joined`,`usr_quit`) VALUES ('".$sql['usr_name']."','mc-".$sql['usr_name']."','".$sql['usr_reg']."','$date'");

                // Delete from main user list table
                $retired = $database->query("DELETE FROM `user_list` WHERE `usr_id`='$id'");
            }
            if( !$insert && !$delete ) { $error[] = "Sorry, there was an error and the members were not deleted. ".mysqli_error().""; }
            else { $success[] = "The members were deleted successfully and has been put to the Retired list!"; }
        }

        if( isset($_POST['mass-delete']) ) {
            $getID = $_POST['id'];
            foreach( $getID as $id ) {
                $delete = $database->query("DELETE FROM `user_list` WHERE `usr_id`='$id'");
            }
            if( !$delete ) { $error[] = "Sorry, there was an error and the members were not deleted. ".mysqli_error().""; }
            else { $success[] = "The members were deleted successfully!"; }
        }

        echo '<h1>Members Administration</h1>
        <p>&raquo; Need to email <a href="'.$PHP_SELF.'?mod=members&action=email-all">all members</a>?</p>
        
        <center>';
        if ( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if ( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }
        echo '</center>

        <ul class="tabs" data-persist="true">
            <li><a href="#active">Active</a></li>
            <li><a href="#pending">Pending</a></li>
            <li><a href="#hiatus">Hiatus</a></li>
            <li><a href="#inactive">Inactive</a></li>
            <li><a href="#retired">Retired</a></li>
        </ul>

        <div class="tabcontents" align="left">
            <div id="active">
                <form method="post" action="'.$PHP_SELF.'?mod=members">';
                $l = $database->num_rows("SELECT * FROM `tcg_levels`");
                for($i=1; $i<=$l; $i++) {
                    $sql = $database->query("SELECT * FROM `user_list` WHERE `usr_level`='$i' AND `usr_status`='Active' ORDER BY `usr_id` ASC");
                    $count = $database->num_rows("SELECT * FROM `user_list` WHERE `usr_level`='$i' AND `usr_status`='Active' ORDER BY `usr_id` ASC");
                    $lvl = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `lvl_id`='$i'");

                    if( $count == 0 ) {}
                    else {
                        echo '<h2>'.$lvl['lvl_name'].' (Level '.$i.")</h2>\n";
                        echo '<table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <td width="5%"></td>
                            <td width="5%">ID</td>
                            <td width="15%">Name</td>
                            <td width="15%">Registered</td>
                            <td width="10%">Referral</td>
                            <td width="15%">Information</td>
                            <td width="20%">Action</td>
                        </tr>
                        </thead>
                        <tbody>';
                        while( $row = mysqli_fetch_assoc($sql) ) {
                            echo '<tr>
                            <td align="center"><input type="checkbox" name="id[]" value="'.$row['usr_id'].'" /></td>
                            <td align="center">'.$row['usr_id'].'</td>
                            <td align="center">'.$row['usr_name'].'</td>
                            <td align="center">'.date("F d, Y", strtotime($row['usr_reg'])).'</td>
                            <td align="center">'.$row['usr_refer'].'</td>
                            <td align="center">
                                <button type="button" onClick="window.location.href=\''.$row['usr_url'].'\';" target="_blank" title="Visit Trade Post" class="btn-default"><span class="fas fa-home" aria-hidden="true"></span></button>
                                <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-logs&name='.$row['usr_name'].'\';" title="View Activity Logs" class="btn-default"><span class="fas fa-file-import" aria-hidden="true"></span></button>
                                <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&sub=user-trades&name='.$row['usr_name'].'\';" title="View Trade Logs" class="btn-default"><span class="fas fa-file-export" aria-hidden="true"></span></button>
                            </td>
                            <td align="center">
                                <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&action=email&id='.$row['usr_id'].'\';" title="Send Email" class="btn-default"><span class="fas fa-envelope" aria-hidden="true"></span></button>
                                <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&action=rewards&id='.$row['usr_id'].'\';" title="Send Rewards" class="btn-default"><span class="fas fa-gift" aria-hidden="true"></span></button>
                                <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&action=edit&id='.$row['usr_id'].'\';" title="Edit Member" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button>
                                <button type="button" onClick="window.location.href=\''.$PHP_SELF.'?mod=members&action=delete&id='.$row['usr_id'].'\';" title="Delete Member" class="btn-cancel"><span class="fas fa-times" aria-hidden="true"></span></button>
                            </td>
                            </tr>';
                        }
                        echo '<tr>
                            <td align="center"><span class="arrow-right">↳</span></td>
                            <td colspan="6">With selected: <input type="submit" name="mass-hiatus" class="btn-default" value="Hiatus" /> 
                            <input type="submit" name="mass-inactive" class="btn-warning" value="Inactive" />
                            <input type="submit" name="mass-retired" class="btn-warning" value="Retired" /> 
                            <input type="submit" name="mass-delete" class="btn-cancel" value="Delete" /></td>
                        </tr>
                        </tbody>
                        </table>';
                    }
                }
                echo '</form>
            </div>

            <div id="pending">';
                if( isset($_POST['mass-approve']) ) {
                    $getID = $_POST['id'];
                    foreach( $getID as $id ) {
                        $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_id`='$id'");
                        $update = $database->query("UPDATE `user_list` SET `usr_status`='Active', `usr_level`='1', `usr_mcard`='Yes' WHERE `usr_id`='$id'");

                        $recipient = "$row[email]";
                        $subject = "$tcgname: Approved!";

                        $message = "Thank you for joining $tcgname! You have been approved by $tcgowner and can now begin to play games and take freebies. You should have received your starter pack already on the site and in your email. You do not need to reply to this email.\n\n";
                        $message .= "Now that you are an active member, there are lots of fun things to do. Check out the interactive section for the current game rounds or post your wishlists!\n\n";
                        $message .= "Don't forget that all members should be active at least once per two months. If you need to go on hiatus, please let us know! $tcgname does not delete members from the member list for inactivity, however any member who is inactive for two months will be listed as inactive and must submit an update form to reactive themselves.\n\n";
                        $message .= "That should be everything you need to know! Have any questions? Make sure to look through the Information page and if you still can't find the answer, shoot us an email! Thanks again for joining and happy trading!\n\n";
                        $message .= "-- $tcgowner\n";
                        $message .= "$tcgname: $tcgurl\n";

                        $headers = "From: $tcgname <$tcgemail> \n";
                        $headers .= "Reply-To: $tcgname <$tcgemail>";

                        if( mail($recipient,$subject,$message,$headers) ) {
                            $activity = '<span class="fas fa-user" aria-hidden="true"></span> <a href="'.$tcgurl.'/members.php?id='.$row['usr_name'].'">'.$row['usr_name'].'</a> became a member of '.$tcgname.'!';
                            $date = date("Y-m-d", strtotime("now"));
                            $database->query("INSERT INTO `tcg_activities` (`act_name`,`act_rec`,`act_date`) VALUES ('".$row['usr_name']."','$activity','$date')");
                            $database->query("INSERT INTO `user_rewards` (`rwd_name`,`rwd_type`,`rwd_mcard`,`rwd_cards`,`rwd_currency`,`rwd_date`) VALUES ('$name','Gift','Yes','".$settings->getValue('prize_start_reg')."','".$settings->getValue('prize_start_currency')."','$date')");
                        }
                    }
                    
                    if( !$update && !mail($recipient,$subject,$message,$headers) ) {
                        $error[] = 'Sorry, there was an error and the email could not be sent to the members. They also were not updated in the database. Please send them an email to let them know they have been approved and use the edit form from the <a href="'.$PHP_SELF.'?mod=members">members</a> page to update their status. '.mysqli_error().'';
                    } else if( !$update ) {
                        $error[] = 'The members has been successfully emailed but has not been updated in the database. Please use the edit form from the <a href="'.$PHP_SELF.'?mod=members">members</a> page to update their status. '.mysqli_error().'';
                    } else {
                        $success[] = 'The members has been successfully emailed and has been updated in the database!';
                    }
                }

                if( isset($_POST['mass-delete']) ) {
                    $getID = $_POST['id'];
                    foreach( $getID as $id ) {
                        $delete = $database->query("DELETE FROM `user_list` WHERE `usr_id`='$id'");
                    }
                    if( !$delete ) { $error[] = "Sorry, there was an error and the members were not deleted. ".mysqli_error().""; }
                    else { $success[] = "The members were successfully deleted."; }
                }

                echo '<h2>Pending</h2>
                <center>';
                if ( isset($error) ) {
                    foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
                }
                if ( isset($success) ) {
                    foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
                }
                echo '</center>';
                $admin->members('Pending');
            echo '</div>

            <div id="hiatus">';
                if( isset($_POST['mass-reactivate']) ) {
                    $getID = $_POST['id'];
                    foreach( $getID as $id ) {
                        $reactivate = $database->query("UPDATE `user_list` SET `usr_status`='Active' WHERE `usr_id`='$id' AND `usr_status`='Hiatus'");
                    }
                    if( !$reactivate ) { $error[] = "Sorry, there was an error and the members were not reactivated. ".mysqli_error().""; }
                    else { $success[] = "The members were reactivated successfully."; }
                }
                
                if( isset($_POST['mass-delete']) ) {
                    $getID = $_POST['id'];
                    foreach( $getID as $id ) {
                        $delete = $database->query("DELETE FROM `user_list` WHERE `usr_id`='$id'");
                    }
                    if( !$delete ) { $error[] = "Sorry, there was an error and the members were not deleted. ".mysqli_error().""; }
                    else { $success[] = "The members were successfully deleted."; }
                }
                
                echo '<h2>Hiatus</h2>
                <center>';
                if ( isset($error) ) {
                    foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
                }
                if ( isset($success) ) {
                    foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
                }
                echo '</center>';
                $admin->members('Hiatus');
            echo '</div>

            <div id="inactive">';
                if( isset($_POST['mass-reactivate']) ) {
                    $getID = $_POST['id'];
                    foreach( $getID as $id ) {
                        $reactivate = $database->query("UPDATE `user_list` SET `usr_status`='Active' WHERE `usr_id`='$id' AND `usr_status`='Inactive'");
                    }
                    if( !$reactivate ) { $error[] = "Sorry, there was an error and the members were not reactivated. ".mysqli_error().""; }
                    else { $success[] = "The members were reactivated successfully."; }
                }
                
                if( isset($_POST['mass-delete']) ) {
                    $getID = $_POST['id'];
                    foreach( $getID as $id ) {
                        $delete = $database->query("DELETE FROM `user_list` WHERE `usr_id`='$id'");
                    }
                    if( !$delete ) { $error[] = "Sorry, there was an error and the members were not deleted. ".mysqli_error().""; }
                    else { $success[] = "The members were successfully deleted."; }
                }
                
                echo '<h2>Inactive</h2>
                <center>';
                if ( isset($error) ) {
                    foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
                }
                if ( isset($success) ) {
                    foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
                }
                echo '</center>';
                $admin->members('Inactive');
            echo '</div>

            <div id="retired">
                <h2>Retired</h2>';
                $result = $database->num_rows("SELECT * FROM `user_list_quit` ORDER BY `usr_name`");
                if( $result === 0 ) { echo '<center>There are currently no retired members at the moment.</center>'; }
                else {
                    $get = $database->query("SELECT * FROM `user_list_quit` ORDER BY `usr_name`");
                    echo '<table width="100%" class="table table-bordered table-striped" cellpadding="0" cellspacing="0">
                    <thead>
                    <tr>
                        <td width="20%">Member Card</td>
                        <td width="30%">Name</td>
                        <td width="25%">Joined</td>
                        <td width="25%">Retired</td>
                    </tr>
                    </thead>
                    <tbody>';
                    while( $quit = mysqli_fetch_assoc($get) ) {
                        echo '<tr>
                        <td align="center"><img src="'.$settings->getValue('file_path_cards').''.$quit['usr_mcard'].'.'.$settings->getValue('cards_file_type').'" /></td>
                        <td align="center">'.$quit['usr_name'].'</td>
                        <td align="center">'.$quit['usr_joined'].'</td>
                        <td align="center">'.$quit['usr_quit'].'</td>
                        </tr>';
                    }
                    echo '</tbody>
                    </table>';
                }
            echo '</div>
        </div>';
    }



    /********************************************************
     * Action:          Add New Member
     * Description:     Show page for adding a new member
     */
    if( $act == "add" ) {
        if( isset($_POST['add']) ) {
            $check->Password();
            if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,6})$/",strtolower($_POST['email']))) {
                exit("<h1>Error</h1>\nThat e-mail address is not valid, please use another.");
            }

            $name = $sanitize->for_db($_POST['name']);
            $email = $sanitize->for_db($_POST['email']);
            $url = $sanitize->for_db($_POST['url']);
            $refer = $sanitize->for_db($_POST['refer']);
            $pass = md5($sanitize->for_db($_POST['password']));
            $pass2 = $sanitize->for_db($_POST['password2']);
            $stat = $sanitize->for_db($_POST['status']);
            $col = $sanitize->for_db($_POST['collecting']);
            $mc = $sanitize->for_db($_POST['memcard']);
            $birthday = $_POST['year']."-".$_POST['month']."-".$_POST['day'];
            $regdate = date("Y-m-d H:i:s", strtotime("now"));
            $date = date("Y-m-d", strtotime("now"));
            $bio = $_POST['about'];
            $bio = nl2br($bio);
            $bio = str_replace("'","\'",$bio);

            $insert = $database->query("INSERT INTO `user_list` (`usr_name`,`usr_email`,`usr_url`,`usr_refer`,`usr_bday`,`usr_pass`,`usr_status`,`usr_deck`,`usr_mcard`,`usr_bio`,`usr_level`,`usr_reg`) VALUES ('$name','$email','$url','$refer','$birthday','$pass','Active','$col','$mc','$bio','1','$regdate')");
    
            if( !$insert ) { $error[] = "Sorry, there was an error and the member was not added. ".mysqli_error().""; }
            else {
                $activity = '<span class="fas fa-user" aria-hidden="true"></span> <a href="'.$tcgurl.'/members.php?id='.$name.'">'.$name.'</a> became a member of '.$tcgname.'!';
                $database->query("INSERT INTO `user_items` (`itm_name`,`itm_masteries`,`itm_mcard`,`itm_ecard`,`itm_milestone`) VALUES ('$name','None','None','None','None')");
                $database->query("INSERT INTO `user_trades_rec` (`trd_name`,`trd_date`) VALUES ('$name','$date')");
                $success[] = "The member was successfully added to the database.";
            }
        }

        echo '<h1>Add a Member</h1>
        <p>Use this form to add a member to the database. <b>If the member has submitted a join form, they are already in the database!</b><br />
        Use the <a href="'.$PHP_SELF.'?mod=members">edit</a> form to update information for existing members.</p>

        <center>';
        if ( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if ( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }

        echo '<form method="post" action="'.$PHP_SELF.'?mod=members&action=add">
        <input type="hidden" name="memcard" value="No" />
        <input type="hidden" name="about" value="Coming Soon" />
        <table width="100%" cellpadding="5" cellspacing="0" border="0">
        <tr>
            <td width="15%" valign="middle"><b>Name:</b></td>
            <td width="2%">&nbsp;</td>
            <td width="83%"><input type="text" name="name" placeholder="Jane Doe" size="40" /></td>
        </tr>
        <tr>
            <td valign="middle"><b>Email Address:</b></td>
            <td>&nbsp;</td>
            <td><input type="text" name="email" placeholder="username@domain.tld" size="40" /></td>
        </tr>
        <tr>
            <td valign="middle"><b>Trade Post:</b></td>
            <td>&nbsp;</td>
            <td><input type="text" name="url" placeholder="https://" size="40" /></td>
        </tr>
        <tr>
            <td valign="top"><b>Password:</b><br /><small><i>(Type twice to verify)</i></small></td>
            <td>&nbsp;</td>
            <td>
                <input type="password" name="password" size="40" /><br /> 
                <input type="password" name="password2" size="40" />
            </td>
        </tr>
        <tr>
            <td valign="middle"><b>Collecting:</b></td>
            <td>&nbsp;</td>
            <td>
                <select name="collecting" style="width:38%;">';
                $sql = $database->query("SELECT * FROM `tcg_cards` WHERE `card_status`='Active' ORDER BY `card_deckname` ASC");
                while( $row = mysqli_fetch_assoc($sql) ) {
                    $name=stripslashes($row['card_filename']);
                    $deckname=stripslashes($row['card_deckname']);
                    echo '<option value="'.$name.'">'.$deckname."</option>\n";
                }
                echo '</select>
            </td>
        </tr>
        <tr>
            <td valign="middle"><b>Birthday:</b></td>
            <td>&nbsp;</td>
            <td>
                <select name="month" style="width:20%;">';
                for($m=1; $m<=12; $m++) {
                    if ($m < 10) { $_mon = "0$m"; }
                    else { $_mon = $m; }
                    echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                }
                echo '</select> <select name="day">';
                for($i=1; $i<=31; $i++) {
                    if ($i < 10) { $_days = "0$i"; }
                    else { $_days = $i; }
                    echo '<option value="'.$_days.'">'.$_days.'</option>';
                }
                echo '</select> ';
                $date = date('Y');
                $start = $date-10;
                $end = $start-40;
                $yearArray = range($start,$end);
                echo '<select name="year">';
                foreach ($yearArray as $year) {
                    $selected = ($year == $start) ? 'selected' : '';
                    echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                }
                echo '</select>
            </td>
        </tr>
        <tr>
            <td valign="middle"><b>Referral:</b></td>
            <td>&nbsp;</td>
            <td>
                <select name="refer" style="width:38%;" />';
                $mem = $database->query("SELECT * FROM `user_list` ORDER BY `usr_name` ASC");
                while( $row = mysqli_fetch_assoc($mem) ) {
                    $name = stripslashes($row['usr_name']);
                    echo '<option value="'.$name.'">'.$name."</option>\n";
                }
                echo '</select>
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td>
                <input type="submit" name="add" class="btn-success" value="Add Member" /> 
                <input type="reset" name="reset" class="btn-cancel" value="Reset" />
            </td>
        </tr>
        </table>
        </form>
        </center>';
    }



    /********************************************************
     * Action:          Approve Member
     * Description:     Show page for approving a member
     */
    if( $act == "approve" ) {
        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_id`='$id'");
            $update = $database->query("UPDATE `user_list` SET `usr_status`='Active', `usr_level`='1', `usr_mcard`='Yes' WHERE `usr_id`='".$row['id']."'");

            $recipient = "$row[usr_email]";
            $subject = "$tcgname: Approved!";

            $message = "Thank you for joining $tcgname! You have been approved by $tcgowner and can now begin to play games and take freebies. You should have received your starter pack already on the site and in your email. You do not need to reply to this email.\n\n";
            $message .= "Now that you are an active member, there are lots of fun things to do. Check out the interactive section for the current game rounds or post your wishlists!\n\n";
            $message .= "Don't forget that all members should be active at least once per two months. If you need to go on hiatus, please let us know! $tcgname does not delete members from the member list for inactivity, however any member who is inactive for two months will be listed as inactive and must submit an update form to reactive themselves.\n\n";
            $message .= "That should be everything you need to know! Have any questions? Make sure to look through the Information page and if you still can't find the answer, shoot us an email! Thanks again for joining and happy trading!\n\n";
            $message .= "-- $tcgowner\n";
            $message .= "$tcgname: $tcgurl\n";

            $headers = "From: $tcgname <$tcgemail> \n";
            $headers .= "Reply-To: $tcgname <$tcgemail>";

            if( mail($recipient,$subject,$message,$headers) ) {
                $activity = '<span class="fas fa-user" aria-hidden="true"></span> <a href="'.$tcgurl.'/members.php?id='.$row['usr_name'].'">'.$row['usr_name'].'</a> became a member of '.$tcgname.'!';
                $date = date("Y-m-d", strtotime("now"));
                $database->query("INSERT INTO `tcg_activities` (`act_name`,`act_rec`,`act_date`) VALUES ('".$row['usr_name']."','$activity','$date')");
                $database->query("INSERT INTO `user_rewards` (`rwd_name`,`rwd_type`,`rwd_mcard`,`rwd_cards`,`rwd_currency`,`rwd_date`) VALUES ('$name','Gift','Yes','".$settings->getValue('prize_start_bonus')."','".$settings->getValue('prize_start_cur')."','$date')");

                if( !$update ) { echo '<p>'.$row['usr_name'].' has been successfully emailed but has not be updated in the database. Please use the edit form from the <a href="'.$PHP_SELF.'?mod=members">members</a> page to update their status.</p>'; }
                else { echo '<p>'.$row['usr_name'].' has been successfully emailed and has been updated in the database.</p>'; }
            }

            else {
                if( !$update ) { echo '<p>Sorry, there was an error and the email could not be sent to '.$row['usr_name'].' @ '.$row['usr_email'].'. They also were not updated in the database. Please send them an email to let them know they have been approved and use the edit form from the <a href="'.$PHP_SELF.'?mod=members">members</a> page to update their status.</p>'; }
                else { echo '<p>'.$row['name'].' has been updated in the database but has not be emailed. Please send them an email to let them know they have been approved.</p>'; }
            }
        }
    }



    /********************************************************
     * Action:          Delete Member
     * Description:     Show page for deleting a member
     */
    if( $act == "delete" ) {
        if ( isset($_POST['delete']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $delete = $database->query("DELETE FROM `user_list` WHERE `usr_id`='$id'");

            if( !$delete ) { $error[] = "Sorry, there was an error and the member was not deleted. ".mysqli_error().""; }
            else { $success[] = "The member was successfully deleted."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            echo '<h1>Delete a Member</h1>
            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=members&action=delete&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <p>Are you sure you want to delete this member? <b>This action can not be undone!</b><br />
            Click on the button below to delete the member:<br />
            <input type="submit" name="delete" class="btn-cancel" value="Delete!"></p>
            </form>';
        }
    }



    /********************************************************
     * Action:          Edit Member
     * Description:     Show page for editing a member
     */
    if( $act == "edit" ) {
        if ( isset($_POST['update']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $name = $sanitize->for_db($_POST['name']);
            $email = $sanitize->for_db($_POST['email']);
            $url = $sanitize->for_db($_POST['url']);
            $refer = $sanitize->for_db($_POST['refer']);
            $status = $sanitize->for_db($_POST['status']);
            $prejoiner = $sanitize->for_db($_POST['prejoiner']);
            $level = $sanitize->for_db($_POST['level']);
            $collecting = $sanitize->for_db($_POST['collecting']);
            $memcard = $sanitize->for_db($_POST['memcard']);
            $mastered = $sanitize->for_db($_POST['mastered']);
            $mcard = $sanitize->for_db($_POST['mcard']);
            $ecard = $sanitize->for_db($_POST['ecard']);
            $role = $sanitize->for_db($_POST['role']);
            $cards = $sanitize->for_db($_POST['cards']);
            $money = $sanitize->for_db($_POST['money']);
            $mstone = $sanitize->for_db($_POST['milestone']);
            $trdp = $sanitize->for_db($_POST['trd_points']);
            $trdr = $sanitize->for_db($_POST['trd_redeems']);
            $trdt = $sanitize->for_db($_POST['trd_turnins']);
            $birthday = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
            $about = $_POST['about'];
            $about = nl2br($about);
            $about = str_replace("'","\'",$about);

            function trim_value(&$value) { $value = trim($value); }
            $mcard = explode(', ',$mcard);
            $ecard = explode(', ',$ecard);
            $mstone = explode(', ',$mstone);

            array_walk($mcard, 'trim_value');
            array_walk($ecard, 'trim_value');
            array_walk($mstone, 'trim_value');

            usort($mcard, 'strnatcasecmp');
            sort($ecard); sort($mstone);

            $mcard = implode(', ',$mcard);
            $ecard = implode(', ',$ecard);
            $mstone = implode(', ',$mstone);

            $update = $database->query("UPDATE `user_list` SET `usr_name`='$name', `usr_email`='$email', `usr_url`='$url', `usr_refer`='$refer', `usr_bday`='$birthday', `usr_status`='$status', `usr_pre`='$prejoiner', `usr_level`='$level', `usr_deck`='$collecting', `usr_mcard`='$memcard', `usr_bio`='$about', `usr_role`='$role' WHERE `usr_id`='$id'");

            if( !$update  ) { $error[] = "Sorry, there was an error and the member was not updated. ".mysqli_error().""; }
            else {
                $database->query("UPDATE `user_items` SET `itm_masteries`='$mastered', `itm_milestone`='$mstone', `itm_mcard`='$mcard', `itm_ecard`='$ecard', `itm_cards`='$cards', `itm_currency`='$money' WHERE `itm_id`='$id'");
                $database->query("UPDATE `user_trades_rec` SET `trd_points`='$trdp', `trd_redeems`='$trdr', `trd_turnins`='$trdt' WHERE `trd_name`='$name'");
                $success[] = "The member has been successfully updated!";
            }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $gal = $database->get_assoc("SELECT * FROM `user_items` WHERE `itm_id`='$id'");
            $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_id`='$id'");
            $trd = $database->get_assoc("SELECT * FROM `user_trades_rec` WHERE `trd_name`='".$row['usr_name']."'");
            echo '<h1>Edit a Member</h1>
            <p>Use this form to edit a member in the database.<br />
            Use the <a href="'.$PHP_SELF.'?mod=members&action=add">add</a> form to add new members.</p>

            <center>';
            if( isset($error) ) { foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; } }
            if( isset($success) ) { foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; } }

            echo '<form method="post" action="'.$PHP_SELF.'?mod=members&action=edit&id='.$id.'">
            <input type="hidden" name="id" value="'.$id.'" />
            <input type="hidden" name="about" value="'.$row['usr_bio'].'" />
            <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="66%" valign="top">
                    <table width="100%" cellpadding="5" cellspacing="0">
                    <tr>
                        <td width="20%" valign="middle"><b>Name:</b>
                        <td width="2%">&nbsp;</td>
                        <td width="78%"><input type="text" name="name" value="'.$row['usr_name'].'" style="width:95%;" /></td>
                    </tr>
                    <tr>
                        <td valign="middle"><b>Email Address:</b></td>
                        <td>&nbsp;</td>
                        <td><input type="text" name="email" value="'.$row['usr_email'].'" style="width:95%;" /></td>
                    </tr>
                    <tr>
                        <td valign="middle"><b>Trade Post:</b></td>
                        <td>&nbsp;</td>
                        <td><input type="text" name="url" value="'.$row['usr_url'].'" style="width:95%;" /></td>
                    </tr>
                    <tr>
                        <td valign="top"><b>Mastered Decks:</b></td>
                        <td>&nbsp;</td>
                        <td><textarea name="mastered" rows="10" style="width:95%;">'.$gal['itm_masteries'].'</textarea></td>
                    </tr>
                    <tr>
                        <td valign="top"><b>Milestones:</b></td>
                        <td>&nbsp;</td>
                        <td><textarea name="milestone" rows="7" style="width:95%;">'.$gal['itm_milestone'].'</textarea></td>
                    </tr>
                    <tr>
                        <td valign="top"><b>Member Cards:</b></td>
                        <td>&nbsp;</td>
                        <td><textarea name="mcard" rows="7" style="width:95%;">'.$gal['itm_mcard'].'</textarea></td>
                    </tr>
                    <tr>
                        <td valign="top"><b>Event Cards:</b></td>
                        <td>&nbsp;</td>
                        <td><textarea name="ecard" rows="7" style="width:95%;">'.$gal['itm_ecard'].'</textarea></td>
                    </tr>
                    </table>
                </td>

                <td width="4%">&nbsp;</td>

                <td width="30%" valign="top">
                    <b>Birthday:</b><br />
                    <select name="month" style="width:45%;">
                    <option value="'.date('m', strtotime($row['usr_bday'])).'">'.date('F', strtotime($row['usr_bday'])).'</option>';
                    for($m=1; $m<=12; $m++) {
                        if ($m < 10) { $_mon = "0$m"; }
                        else { $_mon = $m; }
                        echo '<option value="'.$_mon.'">'.date("F", strtotime("$_mon/12/20")).'</option>';
                    }
                    echo '</select> <select name="day">
                    <option value="'.date('d', strtotime($row['usr_bday'])).'">'.date('d', strtotime($row['usr_bday'])).'</option>';
                    for($i=1; $i<=31; $i++) {
                        if ($i < 10) { $_days = "0$i"; }
                        else { $_days = $i; }
                        echo '<option value="'.$_days.'">'.$_days.'</option>';
                    }
                    echo '</select> ';
                    $date = date('Y');
                    $start = $date-10;
                    $end = $start-40;
                    $yearArray = range($start,$end);
                    echo '<select name="year">
                    <option selected value="'.date('Y', strtotime($row['usr_bday'])).'">'.date('Y', strtotime($row['usr_bday'])).'</option>';
                    foreach ($yearArray as $year) {
                        $selected = ($year == $start) ? 'selected' : '';
                        echo '<option value="'.$year.'">'.$year.'</option>';
                    }
                    echo '</select><br /><br />

                    <b>Status:</b>
                    <select name="status" style="width:96%;">
                        <option value="'.$row['usr_status'].'">Current: '.$row['usr_status'].'</option>
                        <option value="Active">Active</option>
                        <option value="Pending">Pending</option>
                        <option value="Hiatus">Hiatus</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Retired">Retired</option>
                    </select><br /><br />

                    <b>Prejoiner?</b> ';
                    if( $row['usr_pre'] == "Beta" ) {
                        echo '<input type="radio" name="prejoiner" value="Beta" checked /> Beta &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="prejoiner" value="Yes" /> Yes &nbsp;&nbsp;&nbsp; 
                        <input type="radio" name="prejoiner" value="No" /> No';
                    } else if( $row['usr_pre'] == "Yes" ) {
                        echo '<input type="radio" name="prejoiner" value="Beta" /> Beta &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="prejoiner" value="Yes" checked /> Yes &nbsp;&nbsp;&nbsp; 
                        <input type="radio" name="prejoiner" value="No" /> No';
                    } else {
                        echo '<input type="radio" name="prejoiner" value="Beta" /> Beta &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="prejoiner" value="Yes" /> Yes &nbsp;&nbsp;&nbsp; 
                        <input type="radio" name="prejoiner" value="No" checked /> No';
                    }
                    echo '<br />

                    <b>With member card?</b> ';
                    if( $row['usr_mcard'] == "Yes" ) {
                        echo '<input type="radio" name="memcard" value="Yes" checked /> Yes &nbsp;&nbsp;&nbsp; 
                        <input type="radio" name="memcard" value="No" /> No';
                    } else {
                        echo '<input type="radio" name="memcard" value="Yes" /> Yes &nbsp;&nbsp;&nbsp; 
                        <input type="radio" name="memcard" value="No" checked /> No';
                    }
                    echo '<br /><br />

                    <b>Collecting:</b><br />
                    <select name="collecting" style="width:96%;">
                        <option value="'.$row['usr_deck'].'">Current: '.$row['usr_deck'].'</option>';
                        $row_collect = $database->query("SELECT * FROM `tcg_cards` ORDER BY `card_filename` ASC");
                        while( $col = mysqli_fetch_assoc($row_collect) ) {
                            echo '<option value="'.$col['card_filename'].'">'.$col['card_filename'].'</option>';
                        }
                    echo '</select><br /><br />

                    <b>Level:</b><br />
                    <select name="level" style="width:96%;">';
                    $l = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `lvl_id`='".$row['usr_level']."'");
                    echo '<option value="'.$row['usr_level'].'">Current: Level '.$l['lvl_id'].' - '.$l['lvl_name'].'</option>';
                    $l = $database->num_rows("SELECT * FROM `tcg_levels`");
                    for($i=1; $i<=$l; $i++) {
                        $lvl = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `lvl_id`='$i'");
                        echo '<option value="'.$i.'">'.$lvl['lvl_id'].' - '.$lvl['lvl_name']."</option>\n";
                    }
                    echo '</select><br /><br />

                    <b>Member Role:</b><br />
                    <select name="role" style="width:96%;">';
                    $role = $database->get_assoc("SELECT * FROM `user_role` WHERE `role_id`='".$row['usr_role']."'");
                        echo '<option value="'.$row['usr_role'].'">Current: '.$role['role_title'].'</option>';
                    $r = $database->num_rows("SELECT * FROM `user_role`");
                    for($i=1; $i<=$r; $i++) {
                        $role = $database->get_assoc("SELECT * FROM `user_role` WHERE `role_id`='$i'");
                        echo '<option value="'.$i.'">'.$role['role_title'].'</option>';
                    }
                    echo '</select><br /><br />

                    <b>Referral:</b><br />
                    <select name="refer" style="width:96%;" />
                    <option value="'.$row['usr_refer'].'">Current: '.$row['usr_refer'].'</option>';
                    $row_mem = $database->query("SELECT * FROM `user_list` ORDER BY `usr_name` ASC");
                    while( $mem = mysqli_fetch_assoc($row_mem) ) {
                        $name = stripslashes($mem['usr_name']);
                        echo '<option value="'.$name.'">'.$name."</option>\n";
                    }
                    echo '</select><br /><br />

                    <b>Card Worth & Currencies:</b><br />
                    <input type="text" name="cards" value="'.$gal['itm_cards'].'" style="width:40%;" />
                    <input type="text" name="money" value="'.$gal['itm_currency'].'" style="width:40%;" /><br /><br />

                    <hr><center><b>Trading Status</b></center>
                    <b>Points:</b> <input type="text" name="trd_points" value="'.$trd['trd_points'].'" style="width:70%;" /><br />
                    <b>Redeemed:</b> <input type="text" name="trd_redeems" value="'.$trd['trd_redeems'].'" style="width:60%;" /><br />
                    <b>Turn ins:</b> <input type="text" name="trd_turnins" value="'.$trd['trd_turnins'].'" style="width:67%;" /><br />

                    <div align="right" style="margin-top:20px;">
                        <input type="submit" name="update" class="btn-success" value="Edit Member" /> 
                        <input type="reset" name="reset" class="btn-cancel" value="Reset" />
                    </div>
                </td>
            </tr>
            </table>
            </form>
            </center>';
        }
    }



    /********************************************************
     * Action:          Email a Member
     * Description:     Show page for emailing a member
     */
    if( $act == "email" ) {
        if( isset($_POST['email']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_id`='$id'");

            $from = $sanitize->for_db($_POST['sender']);
            $to = $sanitize->for_db($_POST['recipient']);
            $date = date("Y-m-d H:i:s", strtotime("now"));
            $message = $_POST['message'];
            $message = nl2br($message);

            if ( !get_magic_quotes_gpc() ) {
                $message = addslashes($message);
            }

            $insert = $database->query("INSERT INTO `user_mbox` (`msg_subject`,`msg_text`,`msg_sender`,`msg_recipient`,`msg_box_from`,`msg_box_to`,`msg_see_from`,`msg_see_to`,`msg_del_from`,`msg_del_to`,`msg_origin`,`msg_date`) VALUES ('Admin Message','$message','$from','$to','Out','In','0','1','0','0','','$date')");

            if ( !$insert ) {
                $error[] = "Sorry, there was an error and the email could not be sent to ".$row['usr_name']." @ ".$row['usr_email']."<br />
                Send the message directly to ".$row['usr_email']." instead. ".mysqli_error()."";
            }
            else {
                $database->query("UPDATE `user_mbox` SET `msg_origin`=LAST_INSERT_ID() WHERE `msg_id`=LAST_INSERT_ID()");
                $success[] = "Your message has been sent to ".$to."! Kindly wait patiently for ".$to." to get back to you within the next few days.";
            }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_id`='$id'");
            echo '<h1>Email a Member</h1>
            <p>Use this form to send an email to '.$row['name'].'. <b>This is not the form for sending an email to all members.</b><br />
            If you need to send an email to all of the members of '.$tcgname.', please use <a href="'.$PHP_SELF.'?mod=members&action=email-all">this form</a>.</p>

            <center>';
            if( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }
            echo '</center>

            <form method="post" action="'.$PHP_SELF.'?mod=members&action=email&id='.$id.'">
            <input type="hidden" name="sender" value="'.$tcgowner.'" />
            <p><b>Recipient:</b><br />
            <input type="text" name="recipient" value="'.$row['usr_name'].'" style="width:50%;" readonly /></p>

            <p><b>Message:</b><br />
            <textarea name="message" rows="10" style="width:50%;"></textarea></p>

            <p><input type="submit" name="email" class="btn-success" value="Edit Member" /> 
                <input type="reset" name="reset" class="btn-cancel" value="Reset" /></p>
            </form>';
        }
    }



    /********************************************************
     * Action:          Email All Members
     * Description:     Show page for emailing all members
     */
    if( $act == "email-all" ) {
        if ( isset($_POST['email-all']) ) {
            echo '<p>Your email was sent to the following:</p>';
            $row = $database->get_assoc("SELECT * FROM `user_list` ORDER BY `usr_name`");

            $recipient = "$row[usr_email]";
            $subject = "$tcgname: Contact Form";
            $message = "$tcgowner at $tcgname has sent you the following message: \n";
            $message .= "{$_POST['message']} \n\n";
            $message .= "-- $tcgowner\n";
            $message .= "$tcgname: $tcgurl\n";
            $headers = "From: $tcgname <$tcgemail> \n";
            $headers .= "Reply-To: $tcgname <$tcgemail>";
            $sendmail = mail($recipient,$subject,$message,$headers);

            if( !$sendmail ) {
                $error[] = "Sorry, there was an error while sending the message to the following members:<br />
                ".$row['usr_name']." at ".$row['usr_email']."<br />".mysqli_error()."";
            } else {
                $success[] = "The message has been successfully sent to the following members:<br />
                ".$row['usr_name']." at ".$row['usr_email']."<br />";
            }
        }

        echo '<h1>Email All Members</h1>
        <p>Need to contact all of '.$tcgname.'\'s members? Use this form.<br />
        If you need to email one member, please use the contact form from <a href="'.$PHP_SELF.'?mod=members">this page</a>.</p>

        <center>';
        if ( isset($error) ) {
            foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
        }
        if ( isset($success) ) {
            foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
        }
        echo '</center>

        <form method="post" action="'.$PHP_SELF.'?mod=members&action=email-all">
        <h2>Message</h2>
        <p><textarea name="message" rows="10" style="width:50%;"></textarea></p>

        <p><input type="submit" name="email" class="btn-success" value="Edit Member" /> 
            <input type="reset" name="reset" class="btn-cancel" value="Reset" /></p>
        </form>';
    }



    /********************************************************
     * Action:          Send Rewards
     * Description:     Show page for sending a reward
     */
    if( $act == "rewards" ) {
        if ( isset($_POST['submit']) ) {
            $id = $sanitize->for_db($_POST['id']);
            $name = $sanitize->for_db($_POST['name']);
            $type = $sanitize->for_db($_POST['type']);
            $subt = $sanitize->for_db($_POST['subt']);
            $cards = $sanitize->for_db($_POST['cards']);
            $currency = $sanitize->for_db($_POST['currency']);
            $mcard = $sanitize->for_db($_POST['mcard']);
            $mstone = $sanitize->for_db($_POST['mstone']);
            $date = $sanitize->for_db($_POST['timestamp']);

            $insert = $database->query("INSERT INTO `user_rewards` (`rwd_name`,`rwd_type`,`rwd_subtitle`,`rwd_cards`,`rwd_mcard`,`rwd_mstone`,`rwd_currency`,`rwd_date`) VALUES ('$name','$type','$subt','$cards','$mcard','$mstone','$currency','$date')");

            if( !$insert ) { $error[] = "Sorry, there was an error and the rewards were not sent. ".mysqli_error().""; }
            else { $success[] = "The rewards were successfully sent to $name."; }
        }

        if( empty($id) ) {
            echo '<p>This page shouldn\'t be accessed directly! Please go back and try something else.</p>';
        } else {
            $row = $database->get_assoc("SELECT * FROM `user_list` WHERE `usr_id`='$id'");
            $date = date("Y-m-d", strtotime("now"));
            echo '<h1>Send Rewards</h1>
            <p>Use the form below to send rewards to a member.</p>
            
            <center>';
            if ( isset($error) ) {
                foreach ( $error as $msg ) { echo '<div class="box-error"><b>Error!</b> '.$msg.'</div><br />'; }
            }
            if ( isset($success) ) {
                foreach ( $success as $msg ) { echo '<div class="box-success"><b>Success!</b> '.$msg.'</div><br />'; }
            }

            echo '<form method="post" action="'.$PHP_SELF.'?mod=members&action=rewards&id='.$id.'">
            <input type="hidden" name="id" id="id" value="'.$id.'" />
            <input type="hidden" name="timestamp" id="timestamp" value="'.$date.'" />
            <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="15%" valign="middle"><b>Name:</b></td>
                <td width="2%">&nbsp;</td>
                <td width="83%"><input type="text" name="name" value="'.$row['usr_name'].'" readonly size="40" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>Rewarded for:</b></td>
                <td>&nbsp;</td>
                <td>
                    <select name="type" style="width:36%;">
                        <option value="Daily Bonus">Daily Bonus</option>
                        <option value="Donations">Donations</option>
                        <option value="Games">Games</option>
                        <option value="Gift">Gift</option>
                        <option value="Referrals">Referrals</option>
                        <option value="Starter Pack">Starter Pack</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td valign="middle"><b>Subtitle:</b> <small><i>(Leave blank if not applicable)</i></small></td>
                <td>&nbsp;</td>
                <td><input type="text" name="subt" placeholder="e.g. (deckname)" size="40" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>Member Card:</b> <small><i>(Select No if not applicable)</i></small></td>
                <td>&nbsp;</td>
                <td>
                    <input type="radio" name="mcard" value="Yes" /> Yes &nbsp;&nbsp; 
                    <input type="radio" name="mcard" value="No" /> No
                </td>
            </tr>
            <tr>
                <td valign="middle"><b>Milestones:</b> <small><i>(Leave blank if not applicable)</i></small></td>
                <td>&nbsp;</td>
                <td><input type="text" name="mstone" placeholder="achievement badge (e.g. ms-User-1120bday)" size="40" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>Cards:</b></td>
                <td>&nbsp;</td>
                <td><input type="text" name="cards" placeholder="amount of cards to reward" size="40" /></td>
            </tr>
            <tr>
                <td valign="middle"><b>Currency:</b></td>
                <td>&nbsp;</td>
                <td><input type="text" name="currency" placeholder="amount of currencies" size="40" /></td>
            </tr>
            <tr>
                <td colspan="3">
                    <input type="submit" name="submit" class="btn-success" value="Send Rewards" /> 
                    <input type="reset" name="reset" class="btn-cancel" value="Reset" />
                </td>
            </tr>
            </table>
            </form>
            </center>';
        }
    }
} // end main page (members)
?>