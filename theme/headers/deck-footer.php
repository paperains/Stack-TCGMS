<?php
        echo '</td>
        
        <td width="2%"></td>
        
        <td width="20%" valign="top" class="box">
            <div class="gameUpdate">
                <a href="'.$tcgurl.'cards.php">View Sets</a>
            </div>
            <hr>
            <h3>Decks</h3>
            <div class="gameUpdate">
                <a href="'.$tcgurl.'cards.php?view=released">Released: '; $count->numCards('Active','1'); echo' decks</a>
                <a href="'.$tcgurl.'cards.php?view=upcoming">Upcoming: '; $count->numCards('Upcoming',''); echo ' decks</a>
                <a href="'.$tcgurl.'cards.php?view=claimed">Claimed: '; $count->numClaimed('Claims'); echo ' decks</a>
                <a href="'.$tcgurl.'cards.php?view=donated">Donated: '; $count->numClaimed('Donations'); echo ' decks</a>
            </div>
            <hr>
            <div class="gameUpdate">
                <a href="'.$tcgurl.'cards.php?view=zips">Weekly ZIPs</a>
            </div>
        </td>
    </tr>
</table>';
?>