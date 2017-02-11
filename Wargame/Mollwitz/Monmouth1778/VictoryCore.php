<?php
namespace Wargame\Mollwitz\Monmouth1778;
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

class VictoryCore extends \Wargame\Mollwitz\victoryCore
{
    function __construct($data)
    {
        parent::__construct($data);
    }

    public function save()
    {
        $ret = parent::save();
        return $ret;
    }

    protected function checkVictory( $battle)
    {
        $battle = Battle::getBattle();

        $gameRules = $battle->gameRules;
        $scenario = $battle->scenario;
        $turn = $gameRules->turn;
        $frenchWin = $russianWin = $draw = false;

        $victoryReason = "";

        if (!$this->gameOver) {

            $pData = $battle::getPlayerData($battle->scenario)['forceName'];

            $russianWinScore = 25;
            $frenchWinScore = 25;

            if($this->victoryPoints[Monmouth1778::AMERICAN_FORCE] >= $frenchWinScore){
                $allHexes = true;
                foreach($battle->specialHexA as $specialHex){
                    if($battle->mapData->getSpecialHex($specialHex) !== Monmouth1778::AMERICAN_FORCE){
                        $allHexes = false;
                        break;
                    }
                }
                if($allHexes === true){
                    $frenchWin = true;
                    $victoryReason .= "Over $frenchWinScore and all hexes in Maloyaroslavets";
                }
            }
            if ($this->victoryPoints[Monmouth1778::BRITISH_FORCE] >= $russianWinScore) {
                $allHexes = true;
                foreach($battle->specialHexA as $specialHex){
                    if($battle->mapData->getSpecialHex($specialHex) !== Monmouth1778::BRITISH_FORCE){
                        $allHexes = false;
                        break;
                    }
                }
                if($allHexes === true) {
                    $russianWin = true;
                    $victoryReason .= "Over $russianWinScore and all hexes in Maloyaroslavets";
                }
            }

            if ($frenchWin && !$russianWin) {
                $this->winner = Monmouth1778::AMERICAN_FORCE;
                $winner = $pData[$this->winner];
                $gameRules->flashMessages[] = "$winner Win";
                $gameRules->flashMessages[] = $victoryReason;
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
            if ($russianWin && !$frenchWin) {
                $this->winner = Monmouth1778::BRITISH_FORCE;
                $winner = $pData[$this->winner];
                $gameRules->flashMessages[] = "$winner Win";
                $gameRules->flashMessages[] = $victoryReason;
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
            if ($turn > $gameRules->maxTurn) {
                $gameRules->flashMessages[] = "Tie Game";
                $this->winner = 0;
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }

    public function postRecoverUnit($args)
    {
        $unit = $args[0];
        $b = Battle::getBattle();
        $scenario = $b->scenario;
        $id = $unit->id;


        parent::postRecoverUnit($args);

    }


    public function phaseChange()
    {
        parent::phaseChange();
        /* @var $battle JagCore */
        $battle = Battle::getBattle();
        /* @var $gameRules GameRules */
        $gameRules = $battle->gameRules;
        $turn = $gameRules->turn;
        $forceId = $gameRules->attackingForceId;
        $theUnits = $battle->force->units;


        if ($gameRules->phase == BLUE_MOVE_PHASE || $gameRules->phase == RED_MOVE_PHASE) {
            $gameRules->flashMessages[] = "@hide deadpile";
            if (!empty($battle->force->reinforceTurns->$turn->$forceId)) {
                $gameRules->flashMessages[] = "@show deployWrapper";
                $gameRules->flashMessages[] = "Reinforcements have been moved to the Deploy/Staging Area";
            }

            foreach ($theUnits as $id => $unit) {

                if ($unit->status == STATUS_CAN_REINFORCE && $unit->reinforceTurn <= $battle->gameRules->turn && $unit->hexagon->parent != "deployBox") {
//                $theUnits[$id]->status = STATUS_ELIMINATED;
                    $theUnits[$id]->hexagon->parent = "deployBox";
                }
            }
        }
    }
}
