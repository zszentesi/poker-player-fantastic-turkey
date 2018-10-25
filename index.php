<?php

require_once('player.php');

$state = json_decode($_POST['game_state'], true);

switch($_POST['action'])
{
    case 'bet_request':
        $player = new Player($state);
        echo $player->betRequest();
        break;
    case 'showdown':
        $player = new Player($state);
        $player->showdown();
        break;
    case 'version':
        echo Player::VERSION;
}
