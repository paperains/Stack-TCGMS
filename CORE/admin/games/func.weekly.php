<?php
/* This depends which games you'd like to use for your weekly set.
 * Lottery is put here for example to show you how it should be written.
 * Feel free to add more password gate games for your weekly set
 * and add them here accordingly. Don't forget to change the date it updates!
 */

switch ( true ) {
    // WEEKLY SETS
    case ($Weekly == '2020-12-05') :
        $Lottery = array( "g1" => "o", "g2" => "a", "g3" => "e", "g4" => "m", "g5" => "k");
    break;
		
    case ($Weekly == '2020-12-12') :
        $Lottery = array( "g1" => "j", "g2" => "c", "g3" => "f", "g4" => "n", "g5" => "l");
    break;
		
    case ($Weekly == '2020-12-19') :
        $Lottery = array( "g1" => "k", "g2" => "e", "g3" => "h", "g4" => "c", "g5" => "i");
    break;
		
    case ($Weekly == '2020-12-26') :
        $Lottery = array( "g1" => "m", "g2" => "o", "g3" => "l", "g4" => "a", "g5" => "e");
    break;
		
    case ($Weekly == '2021-01-02') :
        $Lottery = array( "g1" => "d", "g2" => "a", "g3" => "m", "g4" => "f", "g5" => "j");
    break;
		
    case ($Weekly == '2021-01-09') :
        $Lottery = array( "g1" => "f", "g2" => "i", "g3" => "c", "g4" => "o", "g5" => "n");
    break;
		
    case ($Weekly == '2021-01-16') :
        $Lottery = array( "g1" => "a", "g2" => "o", "g3" => "d", "g4" => "l", "g5" => "j");
    break;
		
    case ($Weekly == '2021-01-23') :
        $Lottery = array( "g1" => "b", "g2" => "g", "g3" => "m", "g4" => "k", "g5" => "a");
    break;
		
    case ($Weekly == '2021-01-30') :
        $Lottery = array( "g1" => "c", "g2" => "m", "g3" => "h", "g4" => "e", "g5" => "n");
    break;

    default: // What it'll show in case your rounds run out 
	$Lottery = array( "g1" => "o", "g2" => "a", "g3" => "e", "g4" => "m", "g5" => "k");
}
?>
