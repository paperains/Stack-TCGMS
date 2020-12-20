<?php
include('admin/class.lib.php');
include($header);
?>

<h1>Information</h1>
<p>Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut. Vis nonumes feugait ex, vel fuisset denique moderatius ut, eu sit graeci nostrum accusata. Augue delicatissimi ei sit, eirmod dolorum vix ea. Vis soluta essent consectetuer in, eam ad velit ludus voluptaria. Vitae ornatus ad mei, te eos primis numquam perfecto. Eum id habemus feugait iracundia. Eos te malorum nominavi scribentur. Vel vidit detracto ea, an quaestio consequat vis. Cu per aliquando persequeris, vitae causae omittantur vix ne. Ei eam possit inermis.</p>
<p>Anything else that you need to know are explained below, otherwise <a href="/services.php?form=contact">contact</a> us and we will answer your question ASAP.</p>

<table width="100%" cellspacing="3">
    <tr>
        <td width="49%" valign="top" class="post-body">
            <h2><span class="line-center">What is a TCG?</span></h2>
            <p>Online <b>T</b>rading <b>C</b>ard <b>G</b>ames are similar to the trading cards you knew as a child. Rather than playing in person and collecting cards from packs at the store, you collect everything online. The goal is to collect all of the cards you like, also know as mastering a deck. Online TCGs are free to play, a lot of fun, and very addictive! Interested in joining? Check out the rest of this section before moving on to the join form!</p>
        </td>
        <td width="2%">&nbsp;</td>
        <td width="49%" valign="top" class="post-body">
            <h2><span class="line-center">Trade Post</span></h2>
            <p>Before you join, you need a place to store your stuff, also known as a trade post. To make life easier for you and for your fellow TCG members, you should separate your cards&mdash;at least into groups of keeping and trading. You are also required to keep a detailed card log, showing how you got every card. The final thing you need on your site is a way to contact you, either a form or an email link, so you can trade.</p>
        </td>
    </tr>
</table>

<table width="100%" cellspacing="3">
    <tr>
        <td width="49%" valign="top">
            <h1>Levels & Level Ups</h1>
            <p>When you join <u><?php echo $tcgname; ?></u>, you begin at level one and work your way up through the levels. In order to level up, you will need to collect the amount of cards needed to progress as shown in the table below and it is based on card worth, not count. The rewards for leveling up are <?php
            if($settings->getValue('cards_level_choice')!=0) {
                echo "<b>".$settings->getValue('cards_level_choice')."</b> card(s) of choice";
            }
            if($settings->getValue('cards_level_reg')!=0) {
                echo ", <b>".$settings->getValue('cards_level_reg')."</b> random cards";
            }
            ?>, and <b>XX</b> CURRENCY.</p>
            <table width="100%" cellspacing="3" class="border">
            <tr><td width="60%" class="headLine">LEVEL</td><td width="40%" class="headLine">CARDS</td></tr>
            <?php
            $lvlcount = $database->num_rows("SELECT * FROM `tcg_levels`");
            for($i=1; $i<=$lvlcount; $i++) {
                $lvl = $database->get_assoc("SELECT * FROM `tcg_levels` WHERE `level`='$i'");
                echo '<tr class="rows"><td align="center">'.$i.'. '.$lvl['name'].'</td><td align="center">'.$lvl['cards'].'</td></tr>';
            }
            ?>
            </table>
            <p><i>* After level ten, you will level up every XXXX cards gained.</i></p>
            <p>Do keep in mind that no matter what level you ranked up, the set of rewards given will be the same even after level 10.</p>
        </td>
        <td width="2%">&nbsp;</td>
        <td width="49%" valign="top" class="post-body">
            <h1>Mastering</h1>
            <p>Once you have collecting all of the cards in a deck, you have <b>mastered</b> the deck. This means you cannot trade away any of these cards (except for doubles, of course) but you will receive some rewards for doing so. For every <b>card deck</b> you master, you receive <?php
    if($settings->getValue('cards_master_choice')!=0) { echo "<b>".$settings->getValue('cards_master_choice')."</b> card(s) of choice"; }
    if($settings->getValue('cards_master_reg')!=0) { echo ", <b>".$settings->getValue('cards_master_reg')."</b> random cards"; }
?> and <b>XX</b> CURRENCY.</p>
            <p align="center"><img src="/images/cards/filler.png" /> <img src="/images/cards/filler.png" /></p>
            <h2>Cards & Deck Types</h2>
            <p>Cards are the heart of every TCG and your goal is to complete these decks by gaining them through games or trade with other members. All decks have XX cards each and features CHANGE SUBJECT HERE, and all are worth <i>X</i>.</p>
            <p align="center"><img src="/images/cards/filler.png" /> <img src="/images/cards/filler.png" /> <img src="/images/cards/filler.png" /> <img src="/images/cards/filler.png" /></p>
            <p>Aside from the regular decks, we also have individual cards which are all worth 0 and can't be mastered. While the event cards are not tradeable, member cards are counted towards your trade count.</p>
            <p align="center"><img src="/images/cards/mc-filler.png" title="Member Card" /> <img src="/images/cards/ec-filler.png" title="Event Card" /></p>
        </td>
    </tr>
</table>

<!-- CHANGE ACCORDING TO YOUR OWN TCG NEEDS -->
<h1>Frequently Asked Questions</h1>
<ul>
    <li><b>When can I start playing?</b><br />
    - Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut.</li><br />
    <li><b>I got doubles in my starter pack, what should I do?</b><br />
    - Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut.</li><br />
    <li><b>I'm a prejoiner, what are the perks?</b><br />
    - Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut.</li><br />
    <li><b>What update freebies can I take?</b><br />
    - Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut.</li><br />
    <li><b>How many cards per deck can I take for each wishes or freebies?</b><br />
    - Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut.</li><br />
    <li><b>Do all update pulls expire?</b><br />
    - Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut.</li><br />
    <li><b>Do I only take specific colors for wishes?</b><br />
    - No. You can check out the <a href="https://en.wikipedia.org/wiki/Web_colors" target="_blank">X11 Colors</a> as reference for the main colors' variations.</li><br />
    <li><b>When can I donate my claims?</b><br />
    - Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut.</li><br />
    <li><b>Can I remaster decks that I've already mastered?</b><br />
    - Lorem ipsum dolor sit amet, commune gubergren vix id, perfecto adolescens interesset eam ea, cum tota detraxit theophrastus ut.</li>
</ul>

<?php
include($footer);
?>
