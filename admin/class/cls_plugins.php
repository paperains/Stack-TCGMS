<?php
/*
 * Class library for content plugins functions
 */
if ( ! defined('VALID_INC') ) exit('No direct script access allowed');


/********************************************************
 * Class:           Plugins
 * Description:     Functions to use for displaying complex code for page content
 */
class Plugins {
    function plugLevels() {
        $database = new Database;

        echo '<table width="100%" cellspacing="3" class="border">
        <tr>
            <td width="50%" class="headLine">Level</td>
            <td width="25%" class="headLine">Cards</td>
            <td width="25%" class="headLine">Difference</td>
        </tr>';

        $count = $database->num_rows("SELECT * FROM `tcg_levels`");
        for($i=1; $i<=$count; $i++) {
            $lvl = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `lvl_id`='$i'");
            echo '<tr><td align="center" class="tableBody">'.$lvl['lvl_name'].' (<i>Level '.$i.'</i>)</td>
            <td align="center" class="tableBody">'.$lvl['lvl_cards'].'</td>
            <td align="center" class="tableBody">'.$lvl['lvl_interval'].'</td>
            </tr>';
        }

        echo '</table>';
    } // end levels plugin function
    
    function plugCurrency( $plugged ) {
        $database = new Database;
        $settings = new Settings;

        // Explode all bombs
        $curValue = explode(' | ', $settings->getValue( $plugged ));
        $curName = explode(', ', $settings->getValue( 'tcg_currency' ));

        for($i=0; $i<count($curValue); $i++) {
            $tn = substr_replace($curName[$i],"",-4);
            if( $curValue[$i] > 1 ) {
                $var = substr($tn, -1);
                if( $var == "y" ) {
                    $tn = substr_replace($tn,"ies",-1);
                } else if( $var == "o" ) {
                    $tn = substr_replace($tn,"oes",-1);
                }
                else { $tn = $tn.'s'; }
            } else { $tn = $tn; }

            if( $curValue[$i] != 0 ) {
                $arrayCur .= "<b>" . $curValue[$i] . "</b> " . $tn . ", ";
            }
        }

        // Fix all bombs after explosions
        $cleanCur = substr_replace($arrayCur,"",-2);
        echo $cleanCur;
    }

    function plugAffiliates() {
        $database = new Database;

        echo '<center>';
        $sql = $database->query("SELECT * FROM `tcg_affiliates` WHERE `aff_status`='Active' ORDER BY `aff_subject` ASC");
        $count = $database->num_rows("SELECT * FROM `tcg_affiliates` WHERE `aff_status`='Active' ORDER BY `aff_subject` ASC");
        if ($count == 0) { echo '<p>There are currently no affiliates, want to become one?</p>'; }
        else {
            while( $row = mysqli_fetch_assoc($sql) ) {
                echo '<a href="'.$row['aff_url'].'" target="_blank" title="'.$row['aff_subject'].' TCG by '.$row['aff_owner'].'"><img src="/images/aff/'.$row['aff_button'].'" /></a> ';
            }
        }
        echo '</center>';
    } // end affiliates plugin function

    function plugAffiliatesForm() {
        $uploads = new Uploads;

        if ( isset($_POST['submit']) ) {
            $uploads->affiliates();
        }
        
        echo '<br /><form method="post" action="/site.php?p=affiliates" accept-charset="UTF-8" enctype="multipart/form-data">
        <table width="100%" class="table table-sliced table-striped">
        <tr>
            <td width="15%"><b>Owner:</b></td>
            <td width="35%"><input type="text" name="owner" placeholder="Jane Doe" style="width:86%;"></td>
            <td width="15%"><b>Email:</b></td>
            <td width="35%"><input type="text" name="email" placeholder="username@domain.tld" style="width:86%;"></td>
        </tr>
        <tr>
            <td><b>TCG Name:</b></td>
            <td><input type="text" name="subject" placeholder="e.g. Moonlight Legend" style="width:86%;"></td>
            <td><b>TCG URL:</b></td>
            <td><input type="text" name="url" placeholder="http://" style="width:86%;"></td>
        </tr>
        <tr>
            <td><b>Button:</b></td>
            <td><input type="file" name="file" style="width:86%;"></td>
            <td colspan="2" align="center"><input type="submit" name="submit" class="btn-success" value="Become an affiliate"> <input type="reset" name="reset" class="btn-danger" value="Reset"></td>
        </tr>
        </table>
        </form>';
    } // end affiliates form plugin function

    function plugBadges() {
        $database = new Database;

        echo '<center>';
        $sql = $database->query("SELECT * FROM `tcg_levels_badge` WHERE `badge_level`='10' ORDER by `badge_name`");
        while( $row = mysqli_fetch_assoc($sql) ) {
            echo '<div style="display: inline-block; padding: 2px;">';
            echo '<table class="border">
            <tr>
                <td class="headLine">'.$row['badge_name'].'</td>
            </tr>
            <tr>
                <td class="tableBody"><a href="/site.php?p=badges&sub='.$row['badge_set'].'"><img src="/images/badges/'.$row['badge_set'].'-01.png" border="0" title="'.$row['badge_feature'].'" /></a></td>
            </tr>
            <tr>
                <td class="tableBody" align="center">'.$row['badge_width'].' x '.$row['badge_height'].' pixels</td>
            </tr>
            </table>
            </div>';
        }
        echo '</center>';
    } // end level badges plugin function
}
?>