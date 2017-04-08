<?php
namespace Wargame\Mollwitz\Gadebusch1712;
use \Wargame\Battle;
    /*
Copyright 2012-2015 David Rodal

This program is free software; you can redistribute it
and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version

This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
   */
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 5/7/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */

class gadebusch1712VictoryCore extends \Wargame\Mollwitz\victoryCore
{
    public $wasIndecisive = false;
    public $isIndecisive = false;

    function __construct($data)
    {
        parent::__construct($data);
        if ($data) {
            $this->wasIndecisive = $data->victory->wasIndecisive;
            $this->isIndecisive = $data->victory->isIndecisive;
        }
    }

    public function save()
    {
        $ret = parent::save();
        $ret->wasIndecisive = $this->wasIndecisive;
        $ret->isIndecisive = $this->isIndecisive;
        return $ret;
    }

    public function reduceUnit($args)
    {
        $unit = $args[0];
        $mult = 1;
        $this->scoreKills($unit, $mult);
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;

        if (in_array($mapHexName, $battle->specialHexB)) {
            if ($forceId == Gadebusch1712::SWEDISH_FORCE) {
                $this->victoryPoints[Gadebusch1712::SWEDISH_FORCE] += 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='swedish'>+10 Swedish vp</span>";
            }
            if ($forceId == Gadebusch1712::DANISH_FORCE) {
                $this->victoryPoints[Gadebusch1712::SWEDISH_FORCE] -= 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='danish'>-10 Swedish vp</span>";
            }
        }
    }

    protected function checkVictory( $battle)
    {
        $battle = Battle::getBattle();

        $gameRules = $battle->gameRules;
        $scenario = $battle->scenario;
        $turn = $gameRules->turn;
        $danishWin = $swedishWin = $draw = false;

        $victoryReason = "";

        if (!$this->gameOver) {
            $winScore = 35;
            $highWinScore = 42;
            if ($this->victoryPoints[Gadebusch1712::DANISH_FORCE] >= $winScore && $turn <= 5) {
                $danishWin = true;
                $victoryReason .= "Over $winScore on or before turn 5";
            }
            if ($this->victoryPoints[Gadebusch1712::DANISH_FORCE] >= $highWinScore) {
                $danishWin = true;
                $victoryReason .= "Over $highWinScore ";
            }
            if ($this->victoryPoints[Gadebusch1712::SWEDISH_FORCE] >= $winScore && $turn <= 5) {
                $swedishWin = true;
                $victoryReason .= "Over $winScore on or before turn 5 ";
            }
            if ($this->victoryPoints[Gadebusch1712::SWEDISH_FORCE] >= $highWinScore) {
                $swedishWin = true;
                $victoryReason .= "Over $highWinScore ";
            }

            if ($danishWin && !$swedishWin) {
                $this->winner = Gadebusch1712::DANISH_FORCE;
                $gameRules->flashMessages[] = "Danish Win";
                $gameRules->flashMessages[] = $victoryReason;
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
            if ($swedishWin && !$danishWin) {
                $this->winner = Gadebusch1712::SWEDISH_FORCE;
                $gameRules->flashMessages[] = "Swedish Win";
                $gameRules->flashMessages[] = $victoryReason;
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
            if ($danishWin && $swedishWin) {
                $gameRules->flashMessages[] = "Tie Game";
                $gameRules->flashMessages[] = $victoryReason;
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
            if ($turn > $gameRules->maxTurn) {
                if($battle->mapData->getSpecialHex(1113)  == Gadebusch1712::DANISH_FORCE || $this->victoryPoints[Gadebusch1712::DANISH_FORCE] >= 15){
                    $this->winner = Gadebusch1712::DANISH_FORCE;
                    $gameRules->flashMessages[] = "Danish Win";
                    $gameRules->flashMessages[] = "Swedes Fail to Win";
                    $this->gameOver = true;
                    return true;
                }
                $gameRules->flashMessages[] = "Tie Game";
                $gameRules->flashMessages[] = $victoryReason;
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }

    public function preRecoverUnits()
    {
        parent::preRecoverUnits();

        $b = Battle::getBattle();
        $scenario = $b->scenario;
        if ($this->wasIndecisive || !empty($scenario->noIndecision)) {
            return;
        }
        $turn = $b->gameRules->turn;
        if ($turn <= 3 && $this->wasIndecisive === false && $b->gameRules->phase == RED_MOVE_PHASE) {
            $Die = floor(6 * (rand() / getrandmax()));
            /* 1 or 2 is 0 or 1 */
            if ($Die < 2) {

                $this->isIndecisive = true;
                $this->wasIndecisive = true;
                $b->gameRules->flashMessages[] = "No Danish Movement this turn.";

                return;
            }
        }
        if ($turn == 4 && $this->wasIndecisive === false && $b->gameRules->phase == RED_MOVE_PHASE) {
            $this->isIndecisive = true;
            $this->wasIndecisive = true;
        }
    }

    public function postRecoverUnits()
    {
//        parent::postRecoverUnits($args);
        $this->isIndecisive = false;
    }

    public function postRecoverUnit($args)
    {
        $unit = $args[0];
        $b = Battle::getBattle();
        $scenario = $b->scenario;
        $id = $unit->id;

        parent::postRecoverUnit($args);

        if ($this->isIndecisive && $unit->status == STATUS_READY) {
            $unit->status = STATUS_UNAVAIL_THIS_PHASE;
        }
    }
}
