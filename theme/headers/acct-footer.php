<?php
    echo '</td>';
    if( !empty($login) ) {
        
        echo '<td width="2%"></td>
        
        <td width="20%" valign="top" class="box">
            <div class="gameUpdate">
                <a href="'.$tcgurl.'account.php">Overview</a>
                <a href="'.$tcgurl.'services.php?form=event-masteries" class="event">Event Masteries</a>
            </div><br />
            <h3>Services</h3>
            <div class="gameUpdate">
                <a href="'.$tcgurl.'services.php?form=masteries">Masteries</a>
                <a href="'.$tcgurl.'services.php?form=level-up">Level Up</a>
                <a href="'.$tcgurl.'services.php?form=trading-rewards">Trading Rewards</a>
                <a href="'.$tcgurl.'services.php?form=doubles">Doubles Exchange</a>
                <a href="'.$tcgurl.'services.php?form=deck-claims">Deck Claims</a>
                <a href="'.$tcgurl.'services.php?form=contact">Contact Admin</a>
            </div>
            <hr>
            <div class="gameUpdate">
                <a href="'.$tcgurl.'shoppe.php">Shoppe</a>
                <a href="'.$tcgurl.'rewards.php?name='.$player.'">Rewards ('; $count->numRewards(); echo ')</a>
                <a href="'.$tcgurl.'messages.php?id='.$player.'&page=inbox">Mailbox ('; $count->numMail(); echo ')</a>
            </div><br />
            <h3>Account</h3>
            <div class="gameUpdate">
                <a href="'.$tcgurl.'account.php?do=edit-information">Edit Profile</a>
                <a href="'.$tcgurl.'account.php?do=edit-items">Edit Items</a>
                <a href="'.$tcgurl.'account.php?do=reset-password">Reset Password</a>
            </div>
            <hr>
            <div class="gameUpdate">
                <a href="'.$tcgurl.'account.php?do=quit" class="quit">Quit '.$tcgname.'</a>
                <a href="'.$tcgurl.'account.php?do=logout" class="signout">Logout</a>
            </div>
        </td>';
        } else {}
    echo '</tr>
    </table>';