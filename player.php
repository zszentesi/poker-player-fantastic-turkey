<?php

class Player
{
    const VERSION = "Daniel Negreanu";
    public $me = [];
    public $myHand = [];
    public $gameState = [];

    private $communityCards = [];
    public $visibleCards;

    const STRAIGHT_FLUSH = 11;
    const POKER = 10;
    const FULL = 9;
    const FLUSH = 8;
    const STRAIGHT = 7;
    const DRILL = 6;
    const TWO_PAIR = 5;
    const PAIR = 4;

    public function __construct(array $state)
    {
        $this->gameState = $state;
        $this->me = $state['players'][$state['in_action']];
        $this->myHand = $this->me['hole_cards'];
        $this->communityCards = $state['community_cards'];
        $this->visibleCards = array_merge($this->communityCards, $this->myHand);
    }


    public function betRequest()
    {
        switch (count($this->communityCards)) {
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

    public function call()
    {
        return $this->gameState['current_buy_in'] - $this->me['bet'];
    }

    public function minRaise()
    {
        return $this->gameState['current_buy_in'] - $this->me['bet'] + $this->gameState['minimum_raise'];
    }

    public function potRaise()
    {
        return $this->gameState['pot'];
    }

    public function allIn()
    {
        return $this->me['stack'];
    }

    public function callMinRaise()
    {
        return $this->gameState['minimum_raise'];
    }

    public function checkFold()
    {
        return 0;
    }

    public function calculateDistance($card1, $card2)
    {
        $rank1 = $this->getRank($card1['rank']);
        $rank2 = $this->getRank($card2['rank']);

        $big = max($rank1, $rank2);
        $small = min($rank1, $rank2);

        return $big - $small;
    }

    public function isFaceCard($card1, $card2)
    {
        $faceArray = ['10', 'J', 'Q', 'K', 'A'];

        return in_array($card1['rank'], $faceArray) && in_array($card2['rank'], $faceArray);
    }

    private function preFlopStrategy()
    {
        if ($this->isItGoodHand($this->myHand)) {
            return $this->callMinRaise();
        }
        return $this->checkFold();
    }

    private function flopStrategy()
    {
        $hand = $this->getHand();
        switch ($hand) {
            case self::POKER:
            case self::FULL:
                return $this->allIn();
            case self::DRILL:
                return $this->potRaise();
            case self::TWO_PAIR:
                return $this->minRaise();
            case self::PAIR:
                return $this->callMinRaise();
        }

        return $this->checkFold();
    }

    private function turnStrategy()
    {
        return $this->flopStrategy();
    }

    private function riverStrategy()
    {
        return $this->flopStrategy();
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


    private function getHand()
    {
        $matches = $this->matchingCards();
        $straight = $this->getStraight();
        $flush = $this->getFlush();

        if($straight && $flush) {
            return self::STRAIGHT_FLUSH;
        }

        return max($matches, $straight, $flush);
    }

    /**
     * @return int
     */
    private function matchingCards()
    {


        $match = [];


        foreach ($this->visibleCards as $cards) {
            if (isset($match[$cards['rank']])) {
                $match[$cards['rank']]++;
            } else {
                $match[$cards['rank']] = 0;
            }
        }

        array_filter($match, function ($card) {
            return $card > 1;
        });

        if (count($match) === 1 && $match[0] === 4) {
            return self::POKER;
        } // poker
        if (count($match) === 2 && max($match) === 3) {
            return self::FULL;
        } // full house
        if (count($match) === 1 && max($match) === 3) {
            return self::DRILL;
        } // drill
        if (count($match) === 2) {
            return self::TWO_PAIR;
        } // 2 pair
        if (count($match) === 1) {
            return self::PAIR;
        } // 1 pair


        return 0;
    }

    private function getStraight()
    {
        $numericRanks = [];
        foreach ($this->visibleCards as $card){
            $numericRanks[]= $this->getRank($card['rank']);
        }
        if(in_array(14, $numericRanks,true)){
            $numericRanks[] = 1;
        }

        $numericRanksUnique = array_unique($numericRanks);

        foreach ($numericRanksUnique as $num_rank) {
            $straight = range($num_rank, $num_rank + 4);
            if(count(array_intersect($straight, $numericRanksUnique)) > 4){
                return self::STRAIGHT;
            }
        }

        return 0;
    }

    private function getFlush()
    {
        return 0;
        //return self::FLUSH;
    }

}
