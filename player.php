<?php

class Player
{
    const VERSION = "shark";

    public $me = [];
    public $my_hand = [];
    public $gameState = [];
    private $communityCards = [];

    /**
     * Player constructor.
     * @param array $me
     * @param array $my_hand
     * @param array $gameState
     */
    public function __construct(array $state)
    {
        $this->gameState = $state;
        $this->me = $state['players'][$state['in_action']];
        $this->my_hand = $this->me['hole_cards'];
        $this->communityCards = $state['community_cards'];
    }


    public function betRequest()
    {

        if ($this->isItGoodHand($this->my_hand)) {
            return $this->callMinRaise();
        }

        return $this->checkFold();
    }

    public function showdown()
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
        if ($this->isFaceCard($card1, $card2)) {
            return true;
        }

        return false;
    }

    public function call(){
        return $this->gameState['current_buy_in'] - $this->me['bet'];
    }

    public function minRaise(){
        return $this->gameState['current_buy_in'] - $this->me['bet'] + $this->gameState['minimum_raise'];
    }
    public function callMinRaise(){
        return $this->gameState['minimum_raise'];
    }

    public function checkFold(){
        return 0;
    }


    public function isFaceCard($card1, $card2)
    {
        $faceArray = ['10', 'J', 'Q', 'K', 'A'];

        return in_array($card1['rank'], $faceArray) && in_array($card2['rank'], $faceArray);
    }

}
