<?php
if ($row['role'] == "Admin") {
    echo '<div id="sideBar">
        <center><a href="index.php"><span class="fas fa-tachometer-alt" aria-hidden="true"></span> Dashboard</a>
        <a id="s1"><span class="fas fa-home" aria-hidden="true"></span> Admin Panel</a>
        <span id="l1" class="slideable">
            <a href="index.php?action=add&page=blog"><span class="fas fa-pen-fancy" aria-hidden="true"></span> New Blog Post</a>
            <a href="index.php?action=add&page=cards"><span class="fas fa-image" aria-hidden="true"></span> Add Upcoming Deck</a>
            <a href="index.php?action=add&page=members"><span class="fas fa-user" aria-hidden="true"></span> Add New Member</a>
            <a href="index.php?action=add&page=affiliates"><span class="fas fa-globe" aria-hidden="true"></span> Add Affiliates</a>
            <a href="index.php?action=add&page=wishes"><span class="fas fa-star" aria-hidden="true"></span> Add Wishes</a>
        </span>
        <a id="s2"><span class="fas fa-pencil-alt" aria-hidden="true"></span> Content</a>
        <span id="l2" class="slideable">
            <a href="index.php?page=blog"><span class="fas fa-pen-fancy" aria-hidden="true"></span> Blog Posts</a>
            <a href="index.php?page=cards"><span class="fas fa-image" aria-hidden="true"></span> Card Decks</a>
            <a href="index.php?page=members"><span class="fas fa-user" aria-hidden="true"></span> Members</a>
            <a href="index.php?page=affiliates"><span class="fas fa-globe" aria-hidden="true"></span> Affiliates</a>	
            <a href="index.php?page=uploads"><span class="fas fa-upload" aria-hidden="true"></span> Uploads</a>
        </span>
        <a id="s3"><span class="fas fa-store" aria-hidden="true"></span> Collateral</a>
        <span id="l3" class="slideable">
            <a href="index.php?page=events"><span class="fas fa-calendar-alt" aria-hidden="true"></span> Event Cards</a>
            <a href="index.php?page=freebies"><span class="fas fa-gifts" aria-hidden="true"></span> Freebies</a>
            <a href="index.php?page=wishes"><span class="fas fa-star" aria-hidden="true"></span> Wishes</a>
        </span>
        <a id="s4"><span class="fas fa-cogs" aria-hidden="true"></span> Settings</a>
        <span id="l4" class="slideable">
            <a href="index.php?page=config"><span class="fas fa-cog" aria-hidden="true"></span> Configuration</a>
            <a href="index.php?page=levels"><span class="fas fa-chart-line" aria-hidden="true"></span> Levels</a>
            <a href="index.php?page=categories"><span class="fas fa-folder-open" aria-hidden="true"></span> Categories</a>
        </span>
        <div class="version">
            Core TCGMS &copy; <a href="https://www.reijou.net/" target="_blank">Aki</a><br />v1.0.2
        </div>
    </div><!-- /#sideBar -->';
} else if ($row['role'] == "Deck Maker") {
    echo '<div id="sideBar">
        <center><a href="index.php"><span class="fas fa-tachometer-alt" aria-hidden="true"></span> Dashboard</a>
        <a id="s1"><span class="fas fa-home" aria-hidden="true"></span> Admin Panel</a>
        <span id="l1" class="slideable">
            <a href="index.php?action=add&page=cards"><span class="fas fa-image" aria-hidden="true"></span> Add Upcoming Deck</a>
            <a href="index.php?page=cards"><span class="fas fa-image" aria-hidden="true"></span> Card Decks</a>
            <a href="index.php?page=uploads"><span class="fas fa-upload" aria-hidden="true"></span> Uploads</a>
        </span>
        <div class="version">
            Core TCGMS &copy; <a href="https://www.reijou.net/" target="_blank">Aki</a><br />v1.0.2
        </div>
    </div><!-- /#sideBar -->';
} else {
    echo '<div id="sideBar">
    <div class="version">You don\'t have a permission to access this page.</div>
    </div><!-- /#sideBar -->';
}
?>
