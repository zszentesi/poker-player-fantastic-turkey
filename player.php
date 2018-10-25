<?php

class Player
{
    const VERSION = "Default PHP folding player";

    public function betRequest($game_state)
    {
        foreach ($game_state['players'] as $player) {
            if (array_key_exists('hole_cards', $player)) {
                $me = $player;
                $my_hand = $me['hole_cards'];

                if ($this->isItGoodHand($my_hand)) {
                    return $game_state['minimum_raise'];
                }
            }
        }
        return 0;
    }

    public function showdown($game_state)
    {
    }

    public function isItGoodHand($hand)
    {
        $card1 = $hand[0];
        $card2 = $hand[1];

        if ($card1['suit'] == $card2['suit']) {
            return true;
        }
        if ($card1['rank'] == $card2['rank']) {
            return true;
        }
        return false;
    }

}
