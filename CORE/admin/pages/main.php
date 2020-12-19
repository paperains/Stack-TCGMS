<?php
echo '<h1>Dashboard</h1>
<p>Welcome to your '.$tcgname.'\'s administration panel, cap\'n! What would you like to do today?</p>
<center>
	<div class="quickLink"><span class="fas fa-pencil-alt" aria-hidden="true"></span><br /><br /><a href="index.php?page=blog&action=add">Write an Update</a></div>
	<div class="quickLink"><span class="fas fa-image" aria-hidden="true"></span><br /><br /><a href="index.php?page=cards&action=add">Add a Deck</a></div>
	<div class="quickLink"><span class="fas fa-user" aria-hidden="true"></span><br /><br /><a href="index.php?page=members&action=add">Add a Member</a></div>
	<div class="quickLink"><span class="fas fa-upload" aria-hidden="true"></span><br /><br /><a href="index.php?page=uploads">Upload an Image</a></div>
</center><br />
<table width="100%" cellspacing="0" border="0">
    <tr><td class="table-header">TCG Overview</td></tr>
    <tr>
        <td class="table-body">
            <div class="flex">
            <div>
                <h2><span class="fas fa-user" aria-hidden="true"></span> '; $counts->numAll('user_list',''); echo '<div class="sub-title">Members</div></h2>
                <table width="100%" cellspacing="2" border="0">
                    <tr><td width="50%" align="left"><b>Active:</b></td><td width="50%" align="right">'; $counts->numAll('user_list','Active'); echo '</td></tr>
                    <tr><td align="left"><b>Pending:</b></td><td align="right">'; $counts->numAll('user_list','Pending'); echo '</td></tr>
                    <tr><td align="left"><b>Inactive:</b></td><td align="right">'; $counts->numAll('user_list','Inactive'); echo '</td></tr>
                    <tr><td align="left"><b>Hiatus:</b></td><td align="right">'; $counts->numAll('user_list','Hiatus'); echo '</td></tr>
                    <tr><td align="left"><b>Retired:</b></td><td align="right">'; $counts->numAll('user_list','Retired'); echo '</td></tr>
                </table>
            </div>

            <div>
                <h2><span class="fas fa-image" aria-hidden="true"></span> '; $counts->numCards('Active','1'); echo '<div class="sub-title">Released Decks</div></h2>
                <table width="100%" cellspacing="2" border="0">
                    <tr><td width="50%" align="left"><b>Upcoming:</b></td><td width="50%" align="right">'; $counts->numCards('Upcoming',''); echo '</td></tr>
                    <tr><td align="left"><b>Regular:</b></td><td align="right">'; $counts->numCards('','1'); echo '</td></tr>
                    <tr><td align="left"><b>Special:</b></td><td align="right">'; $counts->numCards('','2'); echo '</td></tr>
                    <tr><td align="left"><b>Cards:</b></td><td align="right">'; $counts->countCards(); echo '</td></tr>
                </table>
            </div>

            <div>
                <h2><span class="fas fa-globe-americas" aria-hidden="true"></span> '; $counts->numAll('tcg_affiliates','Active'); echo '<div class="sub-title">Affiliates</div></h2>
                <table width="100%" cellspacing="2" border="0">
                    <tr><td width="50%" align="left"><b>Active:</b></td><td width="50%" align="right">'; $counts->numAll('tcg_affiliates','Active'); echo '</td></tr>
                    <tr><td align="left"><b>Pending:</b></td><td align="right">'; $counts->numAll('tcg_affiliates','Pending'); echo '</td></tr>
                    <tr><td align="left"><b>Inactive:</b></td><td align="right">'; $counts->numAll('tcg_affiliates','Inactive'); echo '</td></tr>
                    <tr><td align="left"><b>Hiatus:</b></td><td align="right">'; $counts->numAll('tcg_affiliates','Hiatus'); echo '</td></tr>
                    <tr><td align="left"><b>Closed:</b></td><td align="right">'; $counts->numAll('tcg_affiliates','Closed'); echo '</td></tr>
                </table>
            </div>

            <div>
                <h2><span class="fas fa-rss" aria-hidden="true"></span> '; $counts->numAll('tcg_blog','Published'); echo '<div class="sub-title">Blog Posts</div></h2>
                <table width="100%" cellspacing="2" border="0">
                    <tr><td width="50%" align="left"><b>Published:</b></td><td width="50%" align="right">'; $counts->numAll('tcg_blog','Published'); echo '</td></tr>
                    <tr><td align="left"><b>Draft:</b></td><td align="right">'; $counts->numAll('tcg_blog','Draft'); echo '</td></tr>
                    <tr><td align="left"><b>Scheduled:</b></td><td align="right">'; $counts->numAll('tcg_blog','Scheduled'); echo '</td></tr>
                </table>
            </div>
            </div><!-- /.flex -->
        </td>
    </tr>
</table>

<br />

<table width="100%" cellspacing="0" border="0">
    <tr>
        <td width="49%" class="table-header"><span class="fas fa-users" aria-hidden="true"></span> Latest Members</td>
        <td width="2%">&nbsp;</td>
        <td width="49%" class="table-header"><span class="fas fa-user-clock" aria-hidden="true"></span> Online Members</td>
    </tr>
    <tr>
        <td class="table-body" valign="top">
            <table width="100%" cellspacing="0" border="0">
                <tr>
                    <td class="record-label" width="20%">Name</td>
                    <td class="record-label" width="40%">Email</td>
                    <td class="record-label" width="15%">Joined</td>
                </tr>';
                $ol_query = $database->query("SELECT * FROM `user_list` WHERE status='Active' ORDER BY regdate DESC LIMIT 5");
                while ($row = mysqli_fetch_assoc($ol_query)) {
                echo '<tr>
                    <td class="player-list"><b>'.$row['name'].'</b></td>
                    <td class="player-list"><a href="mailto:'.$row['email'].'" target="_blank">'.$row['email'].'</a></td>
                    <td class="player-list">'.date("Y/m/d", strtotime($row['regdate'])).'</td>
                </tr>';
                }
            echo '</table>
        </td>
        <td width="1%">&nbsp;</td>
        <td class="table-body" valign="top">
            <table width="100%" cellspacing="0" border="0">
                <tr>
                    <td class="record-label" width="25%">Name</td>
                    <td class="record-label" width="40%">Online since</td>
                </tr>';
                $ol_query = $database->query("SELECT * FROM `user_list` WHERE status='Active' AND TIMESTAMPDIFF(MINUTE, session, NOW()+60) LIMIT 5");
                while ($row = mysqli_fetch_assoc($ol_query)) {
                echo '<tr>
                    <td class="player-list"><b>'.$row['name'].'</b></td>
                    <td class="player-list">'.date("Y/m/d", strtotime($row['session'])).' at '.date("h:i:s A", strtotime($row['session'])).'</td>
                </tr>';
                }
            echo '</table>
        </td>
    </tr>
</table>

<br />

<table width="100%" cellspacing="0" border="0">
    <tr><td width="100%" class="table-header"><span class="fas fa-clock" aria-hidden="true"></span> Recent Activity (<a href="index.php?page=activities">View all?</a>)</td></tr>
    <tr>
        <td class="table-body" valign="top">
            <table width="100%" cellspacing="0" border="0">
                <tr>
                    <td class="record-label" width="85%">Activity</td>
                    <td class="record-label" width="15%">Date</td>
                </tr>';
                $ol_query = $database->query("SELECT * FROM `tcg_activities` ORDER BY `id` DESC LIMIT 5");
                while ($row = mysqli_fetch_assoc($ol_query)) {
                echo '<tr>
                    <td class="player-list">'.$row['activity'].'</td>
                    <td class="player-list">'.date("Y/m/d", strtotime($row['date'])).'</td>
                </tr>';
                }
            echo '</table>
        </td>
    </tr>
</table>
<br />
<h1>2020 Change Log</h1>
<li>Included cron jobs for automatic weekly updates.</li>
<li>Changed the beta primary theme to version 1.0.</li>
<li>All SQL connections are compiled to the <code>class.lib.php</code> file, and functions to call under the <code>class.call.php</code> file.</li>
<li>Combined all of the form actions to their main files.</li>
<li>Added a separate wishes list for wishes with restrictions and other notes, to be included in the updates function.</li>';
?>