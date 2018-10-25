<?php

class Player
{
    const VERSION = "shark with strategies";

    public $me = [];
    public $myHand = [];
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
        $this->myHand = $this->me['hole_cards'];
        $this->communityCards = $state['community_cards'];
    }


    public function betRequest()
    {
        switch (count($this->communityCards)){
            case 0:
                return $this->preFlopStrategy();
                break;
            case 3:
                return $this->flopStrategy();
                break;
            case 4:
                return $this->turnStrategy();
                break;
            case 5:
                return $this->riverStrsategy();
                break;
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

    public function potRaise(){
        return $this->gameState['pot'];
    }

    public function allIn(){
        return $this->me['stack'];
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

    private function preFlopStrategy()
    {
        return $this->isItGoodHand($this->myHand);
    }

    private function flopStrategy()
    {
        return $this->isItGoodHand($this->myHand);
    }

    private function turnStrategy()
    {
        return $this->isItGoodHand($this->myHand);
    }

    private function riverStrsategy()
    {
        return $this->isItGoodHand($this->myHand);
    }

}
