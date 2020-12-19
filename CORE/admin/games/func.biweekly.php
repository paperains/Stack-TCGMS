<?php
// CHANGE THE CARDS AND DATE IT UPDATES
// SET A & SET B DATES MUST BE THE SAME
switch ( true ) {
    case ($SetA == '2020-12-05' || $SetB == '2020-12-05') : 
    $LuckyMatch = array( "cards" => "dandelions07, mantis13, lakes10, edelweiss13, riversoca09", "last" => "harvestmice01, spring13, succulents01, redpandas12, deadsea16" );
    break;

    case ($SetA == '2020-12-12' || $SetB == '2020-12-12') : 
    $LuckyMatch = array( "cards" => "rubybonnets16, halloween202006, horses14, orangeclownfish01, stars15", "last" => "dandelions07, mantis13, lakes10, edelweiss13, riversoca09" );
    break;

    case ($SetA == '2020-12-19' || $SetB == '2020-12-19') : 
    $LuckyMatch = array( "cards" => "stars17, deserts08, alpacas16, silver17, spring07", "last" => "rubybonnets16, halloween202006, horses14, orangeclownfish01, stars15" );
    break;

    case ($SetA == '2020-12-26' || $SetB == '2020-12-26') : 
    $LuckyMatch = array( "cards" => "hummingbirds13, autumn04, sphynxcats02, rainbow16, dandelions11", "last" => "stars17, deserts08, alpacas16, silver17, spring07" );
    break;

    case ($SetA == '2021-01-02' || $SetB == '2021-01-02') : 
    $LuckyMatch = array( "cards" => "deserts14, mars14, pears02, croatia06, lapislazuli10", "last" => "hummingbirds13, autumn04, sphynxcats02, rainbow16, dandelions11" );
    break;

    case ($SetA == '2021-01-09' || $SetB == '2021-01-09') : 
    $LuckyMatch = array( "cards" => "mountains09, deadsea05, seaturtles06, rainbow12, giraffes07", "last" => "deserts14, mars14, pears02, croatia06, lapislazuli10" );
    break;

    case ($SetA == '2021-01-16' || $SetB == '2021-01-16') : 
    $LuckyMatch = array( "cards" => "lions08, koalas03, corals13, summer07, figs16", "last" => "mountains09, deadsea05, seaturtles06, rainbow12, giraffes07" );
    break;

    case ($SetA == '2021-01-23' || $SetB == '2021-01-23') : 
    $LuckyMatch = array( "cards" => "barnowls08, nebelungcats06, zionnationalpark04, amethyst10, grasses02", "last" => "lions08, koalas03, corals13, summer07, figs16" );
    break;

    case ($SetA == '2021-01-30' || $SetB == '2021-01-30') : 
    $LuckyMatch = array( "cards" => "gladiolus11, sunsets04, graywolves19, lakes01, philippines01", "last" => "barnowls08, nebelungcats06, zionnationalpark04, amethyst10, grasses02" );
    break;

    default: // What it'll show in case your rounds run out 
    $LuckyMatch = array( "cards" => "dandelions07, mantis13, lakes10, edelweiss13, riversoca09", "last" => "Free to play");
}
?>
