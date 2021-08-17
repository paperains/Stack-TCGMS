<?php
echo '<h1>Dashboard</h1>
<p>Welcome to your '.$tcgname.'\'s administration panel, cap\'n! What would you like to do today?</p>
<p><font color="red">Notice:</font> If your TCG is now open for prejoin, you can send out rewards to those who pre-prejoin donated decks and/or badges. <a href="index.php?mod=pre-prejoin">Click on this link</a> to manage the rewards for your potential members.</p>

<table width="100%" cellspacing="0" border="0">
	<tr>
	    <td width="25%" valign="top"><h2>Quick Links</h2></td>
        <td><h2>TCG Overview</h2></td>
    </tr>
	<tr>
        <td class="table-body" align="center">
            <div class="quickLink">
                <span class="fas fa-pencil-alt" aria-hidden="true"></span><br /><br />
                <a href="index.php?mod=blog&action=add">Write an Update</a>
            </div>
            <div class="quickLink">
                <span class="fas fa-image" aria-hidden="true"></span><br /><br />
                <a href="index.php?mod=cards&action=add-upcoming">Add a Deck</a>
            </div>
            <div class="quickLink">
                <span class="fas fa-user" aria-hidden="true"></span><br /><br />
                <a href="index.php?mod=members&action=add">Add a Member</a>
            </div>
            <div class="quickLink">
                <span class="fas fa-upload" aria-hidden="true"></span><br /><br />
                <a href="index.php?mod=uploads">Upload Images</a>
            </div>
        </td>
		<td class="table-body">
			<div class="flex">
				<div>
				<h2><span class="fas fa-user" aria-hidden="true"></span> '; $counts->numAll('user_list','','usr'); echo '<div class="sub-title">Members</div></h2>
				<table width="100%" cellspacing="2" border="0">
					<tr>
						<td width="50%" align="left"><b>Active:</b></td>
						<td width="50%" align="right">'; $counts->numAll('user_list','Active','usr'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Pending:</b></td>
						<td align="right">'; $counts->numAll('user_list','Pending','usr'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Inactive:</b></td>
						<td align="right">'; $counts->numAll('user_list','Inactive','usr'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Hiatus:</b></td>
						<td align="right">'; $counts->numAll('user_list','Hiatus','usr'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Retired:</b></td>
						<td align="right">'; $counts->numAll('user_list','Retired','usr'); echo '</td>
					</tr>
				</table>
				</div>

				<div>
				<h2><span class="fas fa-image" aria-hidden="true"></span> '; $counts->numCards('Active','1'); echo '<div class="sub-title">Released Decks</div></h2>
				<table width="100%" cellspacing="2" border="0">
 					<tr>
 						<td width="50%" align="left"><b>Upcoming:</b></td>
 						<td width="50%" align="right">'; $counts->numCards('Upcoming',''); echo '</td>
 					</tr>
					<tr>
						<td align="left"><b>Regular:</b></td>
						<td align="right">'; $counts->numCards('','1'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Special:</b></td>
						<td align="right">'; $counts->numCards('','2'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Cards:</b></td>
						<td align="right">'; $counts->countCards(); echo '</td>
					</tr>
				</table>
				</div>

				<div>
				<h2><span class="fas fa-globe-americas" aria-hidden="true"></span> '; $counts->numAll('tcg_affiliates','Active','aff'); echo '<div class="sub-title">Affiliates</div></h2>
				<table width="100%" cellspacing="2" border="0">
					<tr>
						<td width="50%" align="left"><b>Active:</b></td>
						<td width="50%" align="right">'; $counts->numAll('tcg_affiliates','Active','aff'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Pending:</b></td>
						<td align="right">'; $counts->numAll('tcg_affiliates','Pending','aff'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Inactive:</b></td>
						<td align="right">'; $counts->numAll('tcg_affiliates','Inactive','aff'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Hiatus:</b></td>
						<td align="right">'; $counts->numAll('tcg_affiliates','Hiatus','aff'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Closed:</b></td>
						<td align="right">'; $counts->numAll('tcg_affiliates','Closed','aff'); echo '</td>
					</tr>
				</table>
				</div>

				<div>
				<h2><span class="fas fa-rss" aria-hidden="true"></span> '; $counts->numAll('tcg_blog','Published','post'); echo '<div class="sub-title">Blog Posts</div></h2>
				<table width="100%" cellspacing="2" border="0">
					<tr>
						<td width="50%" align="left"><b>Published:</b></td>
						<td width="50%" align="right">'; $counts->numAll('tcg_blog','Published','post'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Draft:</b></td>
						<td align="right">'; $counts->numAll('tcg_blog','Draft','post'); echo '</td>
					</tr>
					<tr>
						<td align="left"><b>Scheduled:</b></td>
						<td align="right">'; $counts->numAll('tcg_blog','Scheduled','post'); echo '</td>
					</tr>
				</table>
				</div>
			</div><!-- /.flex -->
		</td>
	</tr>
</table>

<br />

<center>
<table width="100%" cellspacing="0" border="0">
	<tr>
		<td width="47%"><h2><span class="fas fa-users" aria-hidden="true"></span> Latest Members</h2></td>
		<td width="2%">&nbsp;</td>
		<td width="47%"><h2><span class="fas fa-user-clock" aria-hidden="true"></span> Online Members</h2></td>
	</tr>
	<tr>
		<td valign="top">
			<table width="100%" cellspacing="0" border="0" class="table table-bordered table-striped">
			<thead>
			<tr>
				<td width="20%">Name</td>
				<td width="40%">Email</td>
				<td width="15%">Joined</td>
			</tr>
			</thead>
			<tbody>';
			$ol_query = $database->query("SELECT * FROM `user_list` WHERE `usr_status`='Active' ORDER BY `usr_reg` DESC LIMIT 5");
			while ($row = mysqli_fetch_assoc($ol_query)) {
				echo '<tr>
				<td align="center"><b>'.$row['usr_name'].'</b></td>
				<td align="center"><a href="mailto:'.$row['usr_email'].'" target="_blank">'.$row['usr_email'].'</a></td>
				<td align="center">'.date("Y/m/d", strtotime($row['usr_reg'])).'</td>
				</tr>';
			}
			echo '</tbody>
			</table>
		</td>
		<td width="1%">&nbsp;</td>
		<td valign="top">
			<table width="100%" cellspacing="0" border="0" class="table table-bordered table-striped">
			<thead>
			<tr>
				<td width="25%">Name</td>
				<td width="40%">Online since</td>
			</tr>
			</thead>
			<tbody>';
			$ol_query = $database->query("SELECT * FROM `user_list` WHERE `usr_status`='Active' AND TIMESTAMPDIFF(MINUTE, usr_sess, NOW()+60) LIMIT 5");
			while ($row = mysqli_fetch_assoc($ol_query)) {
				echo '<tr>
				<td align="center"><b>'.$row['usr_name'].'</b></td>
				<td align="center">'.date("Y/m/d", strtotime($row['usr_sess'])).' at '.date("h:i:s A", strtotime($row['usr_sess'])).'</td>
				</tr>';
			}
			echo '</tbody>
			</table>
		</td>
	</tr>
</table>
</center>

<br />

<table width="100%" cellspacing="0" border="0">
	<tr><td width="100%"><h2><span class="fas fa-clock" aria-hidden="true"></span> Recent Activity (<a href="index.php?mod=activities">View all?</a>)</h2></td></tr>
	<tr>
		<td valign="top">
			<table width="100%" cellspacing="0" border="0" class="table table-bordered table-striped">
			<thead>
			<tr>
				<td width="85%">Activity</td>
				<td width="15%">Date</td>
			</tr>
			</thead>
			<tbody>';
			$ol_query = $database->query("SELECT * FROM `tcg_activities` ORDER BY `act_id` DESC LIMIT 5");
			while ($row = mysqli_fetch_assoc($ol_query)) {
				echo '<tr>
				<td>'.$row['act_rec'].'</td>
				<td align="center">'.date("Y/m/d", strtotime($row['act_date'])).'</td>
				</tr>';
			}
			echo '</tbody>
			</table>
		</td>
	</tr>
</table>
<br />

<h2><span class="fas fa-cogs" aria-hidden="true"></span> Stack Changelogs</h2>
<p>To view the full list of this version\'s changelogs and its previous version, visit our website.</p>';
?>