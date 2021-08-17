<div id="sideBar">
	<a href="index.php"><div class="logo"></div></a>
	<div class="main">
<?php
if( $row['usr_role'] == "1" || $row['usr_role'] == "2" ) {
?>
        <a id="s1" tabindex="1"><span class="fas fa-user-shield" aria-hidden="true"></span> Administration<span class="fas fa-caret-down" aria-hidden="true"></span></a>
		<span id="l1" class="slideable">
		<ul>
			<li><a href="index.php?mod=cards&action=add-upcoming"><span class="far fa-plus-square" aria-hidden="true"></span> Upcoming Deck</a></li>
			<li><a href="index.php?mod=blog&action=add"><span class="far fa-plus-square" aria-hidden="true"></span> Blog Post</a></li>
			<li><a href="index.php?mod=content&action=add"><span class="far fa-plus-square" aria-hidden="true"></span> Page Content</a></li>
			<li><a href="index.php?mod=members&action=add"><span class="far fa-plus-square" aria-hidden="true"></span> Member</a></li>
			<li><a href="index.php?mod=affiliates&action=add"><span class="far fa-plus-square" aria-hidden="true"></span> Affiliate</a></li>
			<li><a href="index.php?mod=games&action=add"><span class="far fa-plus-square" aria-hidden="true"></span> Game</a></li>
		</ul>
		</span>

		<a id="s2" tabindex="1"><span class="fas fa-book" aria-hidden="true"></span> Content<span class="fas fa-caret-down" aria-hidden="true"></span></a>
		<span id="l2" class="slideable">
		<ul>
			<li><a href="index.php?mod=cards"><span class="far fa-images" aria-hidden="true"></span> Cards</a></li>
			<li><a href="index.php?mod=blog"><span class="fas fa-rss" aria-hidden="true"></span> Blog Posts</a></li>
			<li><a href="index.php?mod=content"><span class="far fa-bookmark" aria-hidden="true"></span> Page Contents</a></li>
			<li><a href="index.php?mod=members"><span class="far fa-address-book" aria-hidden="true"></span> Members</a></li>
			<li><a href="index.php?mod=affiliates"><span class="fas fa-globe" aria-hidden="true"></span> Affiliates</a></li>
			<li><a href="index.php?mod=games"><span class="fas fa-gamepad" aria-hidden="true"></span> Games</a></li>
		</ul>
		</span>

		<a id="s3" tabindex="1"><span class="fas fa-folder-open" aria-hidden="true"></span> Categories<span class="fas fa-caret-down" aria-hidden="true"></span></a>
		<span id="l3" class="slideable">
		<ul>
            <?php
            $sql = $database->query("SELECT * FROM `tcg_cards_cat`");
            while( $get = mysqli_fetch_assoc($sql) ) {
                echo '<li><a href="index.php?mod=decks&id='.$get['cat_id'].'"><span class="fas fa-tag" aria-hidden="true"></span> '.$get['cat_name'].'</a></li>';
            }
			?>
			<li><a href="index.php?mod=decks"><span class="fas fa-tag" aria-hidden="true"></span> All</a></li>
		</ul>
		</span>
		
		<a id="s4" tabindex="1"><span class="fas fa-store" aria-hidden="true"></span> Shoppe <span class="fas fa-caret-down" aria-hidden="true"></span></a>
        <span id="l4" class="slideable">
            <ul>
                <li><a href="index.php?mod=shoppe"><span class="fas fa-clipboard-list" aria-hidden="true"></span> Inventory</a></li>
                <li><a href="index.php?mod=shoppe&sub=catalog"><span class="fas fa-file-invoice" aria-hidden="true"></span> Catalog</a></li>
                <li><a href="index.php?mod=shoppe&sub=category"><span class="fas fa-list" aria-hidden="true"></span> Categories</a></li>
                <li><a href="index.php?mod=shoppe&action=add-item"><span class="fas fa-clipboard-check" aria-hidden="true"></span> Add an Item</a></li>
            </ul>
        </span>

		<a id="s5" tabindex="1"><span class="fas fa-gifts" aria-hidden="true"></span> Collateral<span class="fas fa-caret-down" aria-hidden="true"></span></a>
		<span id="l5" class="slideable">
		<ul>
			<li><a href="index.php?mod=badges"><span class="fas fa-certificate" aria-hidden="true"></span> Level Badges</a></li>
			<li><a href="index.php?mod=cards&sub=event-cards"><span class="fas fa-heart" aria-hidden="true"></span> Event Cards</a></li>
			<li><a href="index.php?mod=freebies"><span class="fas fa-gift" aria-hidden="true"></span> Freebies</a></li>
			<li><a href="index.php?mod=members&sub=user-wishes"><span class="fas fa-star" aria-hidden="true"></span> Wishes</a></li>
			<li><a href="index.php?mod=uploads"><span class="fas fa-upload" aria-hidden="true"></span> Uploads</a></li>
			<?php
			$chk = $database->num_rows("SHOW TABLES LIKE 'tcg_chatbox'");
			if( $chk >= 1 ) {
				echo '<li><a href="index.php?mod=chatbox"><span class="fas fa-comments" aria-hidden="true"></span> Chat Box</a></li>';
			}
			?>
		</ul>
		</span>

		<a id="s6" tabindex="1"><span class="fas fa-tools" aria-hidden="true"></span> Settings<span class="fas fa-caret-down" aria-hidden="true"></span></a>
		<span id="l6" class="slideable">
		<ul>
			<li><a href="index.php?mod=settings"><span class="fas fa-user-cog" aria-hidden="true"></span> Configuration</a></li>
			<li><a href="index.php?mod=settings&sub=user-levels"><span class="fas fa-user-graduate" aria-hidden="true"></span> User Levels</a></li>
			<li><a href="index.php?mod=settings&sub=user-roles"><span class="fas fa-user-tie" aria-hidden="true"></span> User Roles</a></li>
			<li><a href="index.php?mod=settings&sub=card-categories"><span class="fas fa-folder-open" aria-hidden="true"></span> Card Categories</a></li>
            <li><a href="index.php?mod=settings&sub=card-sets"><span class="fas fa-box-open" aria-hidden="true"></span> Card Sets/Series</a></li>
			<li><a href="index.php?mod=settings&sub=plugins"><span class="fas fa-plug" aria-hidden="true"></span> Plugins</a></li>
		</ul>
		</span>
<?php
} else if( $row['usr_role'] == "3" || $row['usr_role'] == "4" ) {
?>
	<ul>
        <li><a href="<?php echo $tcgurl; ?>admin/"><span class="fas fa-home" aria-hidden="true"></span> Home</a></li>
        <li><span class="fas fa-edit" aria-hidden="true"></span> Content <span class="fas fa-angle-down" aria-hidden="true"></span></a>
            <ul>
                <li><a href="index.php?mod=blog">Blog Posts</a></li>
                <li><a href="index.php?mod=page">Page Contents</a></li>
                <li><a href="index.php?mod=cards">Card Decks</a></li>
                <li><a href="index.php?mod=members">Members</a></li>
                <li><a href="index.php?mod=affiliates">Affiliates</a></li>
                <li><a href="index.php?mod=uploads">Uploads</a></li>
            </ul>
        </li>
        <li><span class="fas fa-store" aria-hidden="true"></span> Collateral <span class="fas fa-angle-down" aria-hidden="true"></span></a>
            <ul>
                <li><a href="index.php?mod=events">Event Cards</a></li>
                <li><a href="index.php?mod=freebies">Freebies</a></li>
                <li><a href="index.php?mod=wishes">Wishes</a></li>
            </ul>
        </li>
    </ul>
<?php
} else if( $row['usr_role'] == "5" ) {
?>
	<ul>
        <li><a href="<?php echo $tcgurl; ?>admin/"><span class="fas fa-home" aria-hidden="true"></span> Home</a></li>
        <li><span class="fas fa-pencil-alt" aria-hidden="true"></span> Admin Panel <span class="fas fa-angle-down" aria-hidden="true"></span></a>
            <ul>
                <li><a href="index.php?mod=cards&action=add">Add Upcoming Deck</a></li>
                <li><a href="index.php?mod=cards">Card Decks</a></li>
                <li><a href="index.php?mod=uploads">Uploads</a></li>
            </ul>
        </li>
    </ul>
<?php
}
?>
	</div>

	<div class="credit">
		Stack 1.0.0 [Beta] &copy; 2016-<?php echo date("Y"); ?><br />
		<a href="https://design-with.in/resources/stack/" target="_blank">Visit Website</a>
	</div>
</div>