<?php
/*
 * Class library for administrative functions
 */
if ( ! defined('VALID_INC') ) exit('No direct script access allowed');


/********************************************************
 * Class:           Admin
 * Description:     Functions to use for admin contents
 */
class Admin {
    function members( $stat ) {
        $database = new Database;
        $sanitize = new Sanitize;
        $settings = new Settings;
        $stat = $sanitize->for_db($stat);

        $result = $database->num_rows("SELECT * FROM `user_list` WHERE `usr_status`='$stat' ORDER BY `usr_id` ASC");
        $sql = $database->query("SELECT * FROM `user_list` WHERE `usr_status`='$stat' ORDER BY `usr_id` ASC");

        if( $result === 0 ) {
            echo '<center>There are currently no members at this status.</center>';
        }
        else {
            echo '<form method="post" action="index.php?mod=members">
            <table width="100%" class="table table-bordered table-striped" cellpaddg="0" cellspacing="0">
            <thead>
            <tr>
                <td width="5%"></td>
                <td width="5%">ID</td>
                <td width="20%">Name</td>
                <td width="25%">URL</td>
                <td width="25%">Email</td>
                <td width="20%">Action</td>
            </tr>
            </thead>
            <tbody>';
            while ( $row = mysqli_fetch_assoc($sql) ) {
                echo '<tr>
                <td align="center"><input type="checkbox" name="id[]" value="'.$row['usr_id'].'" /></td>
                <td align="center">'.$row['usr_id'].'</td>
                <td align="center">'.$row['usr_name'].'</td>
                <td align="center"><a href="'.$row['usr_url'].'" target="_blank">http://</a></td>
                <td align="center"><a href="index.php?mod=members&action=email&id='.$row['usr_id'].'">Email?</a></td>
                <td align="center">
                    <button type="button" onClick="window.location.href=\'index.php?mod=members&action=edit&id='.$row['usr_id'].'\';" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> ';
                    if( $stat == 'Pending' ) { echo '<button type="button" onClick="window.location.href=\'index.php?mod=members&action=approve&id='.$row['usr_id'].'\';" class="btn-primary"><span class="fas fa-check" aria-hidden="true"></span></button> '; }
                    else if( $stat == 'Hiatus' || $stat == 'Inactive' ) { echo '<button type="button" onClick="window.location.href=\'index.php?mod=members&action=reactivate&id='.$row['usr_id'].'\';" class="btn-primary"><span class="fas fa-check" aria-hidden="true"></span></button> '; }
                    echo '<button type="button" onClick="window.location.href=\'index.php?mod=members&action=reactivate&id='.$row['usr_id'].'\';" class="btn-warning"><span class="fas fa-trash-alt" aria-hidden="true"></span></button> 
                </td>
                </tr>';
            }
            echo '<tr>
                <td align="center"><span class="arrow-right">↳</span></td>
                <td colspan="5">With selected: ';
                if( $stat == 'Pending' ) { echo '<input type="submit" name="mass-approve" class="btn-success" value="Approve" />'; }
                else if( $stat == 'Hiatus' || $stat == 'Inactive' ) { echo '<input type="submit" name="mass-reactivate" class="btn-success" value="Reactivate" />'; }
                echo '<input type="submit" name="mass-delete" class="btn-cancel" value="Delete" />
            </tr>
            </tbody>
            </table>
            </form>';
        }
    }


    function affiliates( $stat ) {
        $database = new Database;
        $sanitize = new Sanitize;
        $settings = new Settings;
        $stat = $sanitize->for_db($stat);

        $result = $database->num_rows("SELECT * FROM `tcg_affiliates` WHERE `aff_status`='$stat' ORDER BY `aff_id` ASC");
        $sql = $database->query("SELECT * FROM `tcg_affiliates` WHERE `aff_status`='$stat' ORDER BY `aff_id` ASC");

        if( $result === 0 ) {
            echo '<center>There are currently no affiliates under this status.</center>';
        }
        else {
            echo '<form method="post" action="index.php?mod=affiliates">
            <table width="100%" cellspacing="0" class="table table-bordered table-striped">
            <thead>
            <tr>
                <td width="5%"></td>
                <td width="5%">ID</td>
                <td width="45%">Owner</td>
                <td width="15%">Affiliate</td>
                <td width="20%">Action</td>
            </tr>
            </thead>
            <tbody>';
            while( $row = mysqli_fetch_assoc($sql) ) {
                echo '<tr>
                <td align="center"><input type="checkbox" name="id[]" value="'.$row['aff_id'].'" /></td>
                <td align="center">'.$row['aff_id'].'</td>
                <td align="center"><b>'.$row['aff_owner'].'</b> of '.$row['aff_subject'].' TCG</td>
                <td align="center"><a href="'.$row['aff_url'].'" target="_blank"><img src="../images/aff/'.$row['aff_button'].'" title="'.$row['aff_subject'].' TCG" alt="'.$row['aff_subject'].' TCG"></a></td>
                <td align="center">
                    <button onClick="window.location.href=\''.$PHP_SELF.'?mod=affiliates&action=email&id='.$row['aff_id'].'\';" class="btn-success" /><span class="fas fa-envelope" aria-hidden="true"></span></button> ';
                    if( $stat == 'Pending' ) { echo '<button type="button" onClick="window.location.href=\'index.php?mod=affiliates&action=approve&id='.$row['aff_id'].'\';" class="btn-primary"><span class="fas fa-check" aria-hidden="true"></span></button> '; }
                    else if( $stat == 'Hiatus' || $stat == 'Inactive' ) { echo '<button type="button" onClick="window.location.href=\'index.php?mod=affiliates&action=reactivate&id='.$row['aff_id'].'\';" class="btn-primary"><span class="fas fa-check" aria-hidden="true"></span></button> '; }
                    echo '<button onClick="window.location.href=\''.$PHP_SELF.'?mod=affiliates&action=edit&id='.$row['aff_id'].'\';" class="btn-success" /><span class="fas fa-cog" aria-hidden="true"></span></button> 
                    <button onClick="window.location.href=\''.$PHP_SELF.'?mod=affiliates&action=delete&id='.$row['aff_id'].'\';" class="btn-cancel" /><span class="fas fa-trash-alt" aria-hidden="true"></span></button>
                </td>
                </tr>';
            }
            echo '<tr>
                <td align="center"><span class="arrow-right">↳</span></td>
                <td colspan="4">With selected: ';
                if( $stat == 'Pending' ) { echo '<input type="submit" name="mass-approve" class="btn-success" value="Approve" /> '; }
                else if( $stat == 'Hiatus' || $stat == 'Inactive' ) { echo '<input type="submit" name="mass-reactivate" class="btn-success" value="Reactivate" /> '; }
                else if( $stat == 'Active' ) {
                    echo '<input type="submit" name="mass-hiatus" class="btn-default" value="Hiatus" /> 
                    <input type="submit" name="mass-inactive" class="btn-warning" value="Inactive" /> ';
                }
                echo '<input type="submit" name="mass-closed" class="btn-warning" value="Closed" /> 
                <input type="submit" name="mass-delete" class="btn-cancel" value="Delete" />
            </tr>
            </tbody>
            </table>
            </form>';
        }
    }


    function shopItems() {
        $database = new Database;
        $sanitize = new Sanitize;
        $settings = new Settings;

        $c1 = $database->num_rows("SELECT * FROM `shop_catalog`");
        for( $i = 1; $i <= $c1; $i++ ) {
            $cat1 = $database->get_assoc("SELECT * FROM `shop_catalog` WHERE `shop_id`='$i'");
            echo '<h1>'.$cat1['shop_catalog'].'</h1>';

            $c2 = $database->num_rows("SELECT * FROM `shop_category` WHERE `shop_catalog`='$i'");
            $sql = $database->query("SELECT * FROM `shop_items` WHERE `shop_catalog`='$i' ORDER BY `shop_file`");
            $cat2 = $database->get_assoc("SELECT * FROM `shop_category` WHERE `shop_catalog`='$i'");

            if( count($sql) === 0 ) {
                echo '<center><i>You don\'t have any items in your inventory. <a href="index.php?mod=shoppe&action=add-item">Want to add one</a>?</i></center>';
            }
            else {
                echo '<h2>'.$cat2['shop_category'].'</h2>';
                echo '<table width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <td width="20%">Item Name</td>
                    <td width="25%">Item SKU</td>
                    <td width="15%">Price</td>
                    <td width="10%">Quantity</td>
                    <td width="20%">Action</td>
                </tr>
                </thead>
                <tbody>';
                while( $row = mysqli_fetch_assoc($sql) ) {
                    echo '<tr>
                    <td align="center">'.$row['shop_item'].'</td>
                    <td align="center">'.substr_replace($row['shop_file'],"",-4).'</td>
                    <td align="center">'.$row['shop_currency'].'</td>
                    <td align="center">'.$row['shop_quantity'].'</td>
                    <td align="center">
                        <button onClick="window.location.href=\''.$PHP_SELF.'?mod=shoppe&action=edit-item&id='.$row['shop_id'].'\';" title="Edit Item" class="btn-success"><span class="fas fa-cog" aria-hidden="true"></span></button> 
                        <button onClick="window.location.href=\''.$PHP_SELF.'?mod=shoppe&action=delete-item&id='.$row['shop_id'].'\';" title="Delete Item" class="btn-cancel"><span class="fas fa-times" aria-hidden="true"></span></button>
                    </td>
                    </tr>';
                }
                echo '</tbody></table>';
            }
        }
    }
}
?>