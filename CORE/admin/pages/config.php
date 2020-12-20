<?php
$setAction = ( isset( $_REQUEST["action"] ) ) ? $_REQUEST['action'] : '';

if( $setAction == 'edit' ) {
    $settings->update_settings( $_POST );
    echo '<p class="success">Settings updated.</p>';
}

else {
    echo '<h1>Settings <span class="fas fa-angle-right" aria-hidden="true"></span> Configuration</h1>
    <p>Change your TCG settings through this page.</p>
    <form action="index.php?page=config" method="post">
    <input type="hidden" name="action" value="edit" />
    <center>
    <table width="100%" cellspacing="3">
    <tr><td class="headSub" width="20%">TCG Name:</td>
        <td valign="middle" width="80%"><input type="text" name="'.$settings->getName( 'tcg_name' ).'" value="'.$settings->getValue( 'tcg_name' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'tcg_name' ).'</i></td></tr>

    <tr><td class="headSub">TCG Owner:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'tcg_owner' ).'" value="'.$settings->getValue( 'tcg_owner' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'tcg_owner' ).'</i></td></tr>
    
    <tr><td class="headSub">TCG Email:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'tcg_email' ).'" value="'.$settings->getValue( 'tcg_email' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'tcg_email' ).'</i></td></tr>

    <tr><td class="headSub">TCG URL:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'tcg_url' ).'" value="'.$settings->getValue( 'tcg_url' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'tcg_url' ).'</i></td></tr>
    
    <tr><td class="headSub">TCG Discord:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'tcg_discord' ).'" value="'.$settings->getValue( 'tcg_discord' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'tcg_discord' ).'</i></td></tr>
    
    <tr><td class="headSub">TCG Twitter:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'tcg_twitter' ).'" value="'.$settings->getValue( 'tcg_twitter' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'tcg_twitter' ).'</i></td></tr>
    
    <tr><td class="headSub">Registration:</td>
        <td valign="middle">';
        if ( $settings->getValue( 'tcg_registration' ) == "1") {
            echo '<input type="radio" name="'.$settings->getName( 'tcg_registration' ).'" value="'.$settings->getValue( 'tcg_registration' ).'" checked /> Open ';
            echo '<input type="radio" name="'.$settings->getName( 'tcg_registration' ).'" value="0" /> Close ';
        } else {
            echo '<input type="radio" name="'.$settings->getName( 'tcg_registration' ).'" value="'.$settings->getValue( 'tcg_registration' ).'" /> Open ';
            echo '<input type="radio" name="'.$settings->getName( 'tcg_registration' ).'" value="No" checked /> Close ';
        }
        echo '<i>'.$settings->getDesc( 'tcg_registration' ).'</i></td></tr>
    
    <tr><td class="headSub">Absolute Path:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'file_path_absolute' ).'" value="'.$settings->getValue( 'file_path_absolute' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'file_path_absolute' ).'</i></td></tr>
    
    <tr><td class="headSub">Layout Header:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'file_path_header' ).'" value="'.$settings->getValue( 'file_path_header' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'file_path_header' ).'</i></td></tr>
    
    <tr><td class="headSub">Layout Footer:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'file_path_footer' ).'" value="'.$settings->getValue( 'file_path_footer' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'file_path_footer' ).'</i></td></tr>
    
    <tr><td class="headSub">Images Folder:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'file_path_img' ).'" value="'.$settings->getValue( 'file_path_img' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'file_path_img' ).'</i></td></tr>

    <tr><td class="headSub">Cards Folder:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'file_path_cards' ).'" value="'.$settings->getValue( 'file_path_cards' ).'" style="width:50%;" required /> <i>'.$settings->getDesc( 'file_path_cards' ).'</i></td></tr>
        
    <tr><td class="headSub">File Type:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'cards_file_type' ).'" value="'.$settings->getValue( 'cards_file_type' ).'" style="width:30%;" required /> <i>'.$settings->getDesc( 'cards_file_type' ).'</i></td></tr>

    <tr><td class="headSub">Card Template:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'cards_size_height' ).'" value="'.$settings->getValue( 'cards_size_height' ).'" style="width:30%;" required /> <i>'.$settings->getDesc( 'cards_size_height' ).'</i><br />
            <input type="text" name="'.$settings->getName( 'cards_size_width' ).'" value="'.$settings->getValue( 'cards_size_width' ).'" style="width:30%;" required /> <i>'.$settings->getDesc( 'cards_size_width' ).'</i></td></tr>
    
    <tr><td class="headSub">Starter Pack:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'cards_start_choice' ).'" value="'.$settings->getValue( 'cards_start_choice' ).'" style="width:30%;" required /> <i>'.$settings->getDesc( 'cards_start_choice' ).'</i><br />
            <input type="text" name="'.$settings->getName( 'cards_start_reg' ).'" value="'.$settings->getValue( 'cards_start_reg' ).'" style="width:30%;" required /> <i>'.$settings->getDesc( 'cards_start_reg' ).'</i></td></tr>
    
    <tr><td class="headSub">Mastery Rewards:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'cards_master_choice' ).'" value="'.$settings->getValue( 'cards_master_choice' ).'" style="width:30%;" required /> <i>'.$settings->getDesc( 'cards_master_choice' ).'</i><br />
            <input type="text" name="'.$settings->getName( 'cards_master_reg' ).'" value="'.$settings->getValue( 'cards_master_reg' ).'" style="width:30%;" required /> <i>'.$settings->getDesc( 'cards_master_reg' ).'</i></td></tr>
    
    <tr><td class="headSub">Level Up Rewards:</td>
        <td valign="middle"><input type="text" name="'.$settings->getName( 'cards_level_choice' ).'" value="'.$settings->getValue( 'cards_level_choice' ).'" style="width:30%;" required /> <i>'.$settings->getDesc( 'cards_level_choice' ).'</i><br />
            <input type="text" name="'.$settings->getName( 'cards_level_reg' ).'" value="'.$settings->getValue( 'cards_level_reg' ).'" style="width:30%;" required /> <i>'.$settings->getDesc( 'cards_level_reg' ).'</i></td></tr>
    
    <tr><td valign="middle" colspan="3" align="center"><input type="submit" class="btn-success" value="Edit"></td></tr>
    
    </table>
    </center>
    </form>';
}
?>
