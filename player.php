<?php

class Player
{
    const VERSION = "shark with strategies 0.1";

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
                return $this->riverStrategy();
                break;
        }

        return $this->preFlopStrategy();
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

    public function calculateDistance($card1, $card2){
        $rank1 = $this->getRank($card1['rank']);
        $rank2 = $this->getRank($card2['rank']);

        $big = max($rank1,$rank2);
        $small = min($rank1,$rank2);

        return $big-$small;
    }
    public function isFaceCard($card1, $card2)
    {
        $faceArray = ['10', 'J', 'Q', 'K', 'A'];

        return in_array($card1['rank'], $faceArray) && in_array($card2['rank'], $faceArray);
    }

    private function preFlopStrategy()
    {
        if($this->isItGoodHand($this->myHand)){
            return $this->callMinRaise();
        }
        return $this->checkFold();
    }

    private function flopStrategy()
    {
        if($this->isItGoodHand($this->myHand)){
            return $this->callMinRaise();
        }
        return $this->checkFold();
    }

    private function turnStrategy()
    {
        if($this->isItGoodHand($this->myHand)){
            return $this->callMinRaise();
        }
        return $this->checkFold();
    }

    private function riverStrategy()
    {
        if($this->isItGoodHand($this->myHand)){
            return $this->callMinRaise();
        }
        return $this->checkFold();
    }

    private function getRank($rank)
    {
        $ranks = [
            'A' => 14,
            'K' => 13,
            'Q' => 12,
            'J' => 11,
            '10' => 10,
            '9' => 9,
            '8' => 8,
            '7' => 7,
            '6' => 6,
            '5' => 5,
            '4' => 4,
            '3' => 3,
            '2' => 2,
            'A1' => 1,
            ];

        return $ranks[$rank];
    }

}
