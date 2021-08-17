<?php
include("../admin/class.lib.php");
include($header);
include('../theme/headers/acct-header.php');

if ( empty($login) ) {
	header("Location: /account.php?do=login");
}

// Get dynamic pages
$catalog = isset($_GET['catalog']) ? $_GET['catalog'] : null;
$category = isset($_GET['category']) ? $_GET['category'] : null;

// Get user items data
$item = $database->get_assoc("SELECT * FROM `user_items` WHERE `itm_name`='$player'");

// Show shoppe's default page
if ( empty($catalog) ) {
    // Explode bombs
    $curValue = explode(' | ', $general->getItem( 'itm_currency' ));
    $curName = explode(', ', $settings->getValue( 'tcg_currency' ));
    $curShop = explode(', ', $settings->getValue( 'shop_minimum' ));
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

        if( empty($curValue[$i]) ) { $arrayList .= '<b>0</b> '.$tn.', '; }
        else { $arrayList .= '<b>'.$curValue[$i].'</b> '.$tn.', '; }

        $arrayDiff .= $curShop[$i] - 1;
        $arrayName .= $tn.' and ';
        $cleanLI = substr_replace($arrayList,"",-2);
        if( $curValue[$i] <= $curShop[$i] - 1 ) {
            $msg = "You don't have enough ".$arrayName." to spend! Please play more games to earn more currencies.";
        } else {
            $msg = "You currently have ".$cleanLI." to spend!";
        }
    }
    $arrayName = substr_replace($arrayName,"",-5);

	echo '<h1>Shoppe</h1>
	<p>Welcome to the shop, '.$player.'! Here you can buy card packs that we are currently offering. Choose the product you want to purchase using your gained '.$arrayName.'!</p>

	<blockquote class="wish">
		<center>'.$msg.'</center>
	</blockquote>';

    $shopCatalogs = $database->query("SELECT * FROM `shop_catalog` ORDER BY `shop_id`");
    while( $row = mysqli_fetch_assoc($shopCatalogs) ) {
        echo '<div class="tableBody" style="width:46%; display:inline-block; margin:5px;" align="center">
        <h3>'.$row['shop_catalog'].'</h3>
        <a href="../shoppe/index.php?catalog='.$row['shop_slug'].'"><img src="/images/shop/catalog-'.$row['shop_slug'].'" /></a>
        </div>';
    }
} // end shop front


else {
    // Show shoppe catalog's main page
    if( empty($category) ) {
        $c1 = $database->get_assoc("SELECT * FROM `shop_catalog` WHERE `shop_slug`='$catalog'");
        $sql = $database->get_assoc("SELECT * FROM `shop_category` WHERE `shop_catalog`='".$c1['shop_id']."'");

        echo '<h1>'.$c1['shop_catalog'].'</h1>';
    } // end shop catalog front
}

include('../theme/headers/acct-footer.php');
include($footer);
?>