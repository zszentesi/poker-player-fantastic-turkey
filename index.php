<?php

require_once('player.php');

$state = json_decode($_POST['game_state'], true);
$player = new Player($state);

switch($_POST['action'])
{
    case 'bet_request':
        echo $player->betRequest();
        break;
    case 'showdown':
        $player->showdown();
        break;
    case 'version':
        echo Player::VERSION;
}
