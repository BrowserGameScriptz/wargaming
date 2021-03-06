<?php
namespace Wargame\Mollwitz\Hanau1813;
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

class hanau1813VictoryCore extends \Wargame\Mollwitz\victoryCore
{
    public $deadGuardInf = false;
    function __construct($data)
    {
        parent::__construct($data);
        if ($data) {

            $this->deadGuardInf = $data->victory->deadGuardInf;
        }
    }

    public function save()
    {
        $ret = parent::save();
        $ret->deadGuardInf = $this->deadGuardInf;
        return $ret;
    }
    public function reduceUnit($args)
    {
        $unit = $args[0];
        $mult = 1;
        if($unit->nationality == "Guard"){
            $mult = 1.5;
            if($unit->class == "infantry" && $unit->maxStrength == 9){
                $mult = 2.0;
                $this->deadGuardInf = true;
            }
        }
        $this->scoreKills($unit, $mult);
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;
        if (in_array($mapHexName, $battle->specialHexA)) {
            if ($forceId == Hanau1813::FRENCH_FORCE) {
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='french'>French Control Vital Objective</span>";
            }
            if ($forceId == Hanau1813::ALLIED_FORCE) {
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='prussian'>French Lose Vital Objective</span>";
            }
        }
    }

    protected function checkVictory( $battle)
    {
        $battle = Battle::getBattle();

        $gameRules = $battle->gameRules;
        $scenario = $battle->scenario;
        $turn = $gameRules->turn;
        $frenchWin = $alliedWin = $draw = false;

        $victoryReason = "";

        if (!$this->gameOver) {
            $specialHexes = $battle->mapData->specialHexes;
            $alliedWinScore = 55;
            $frenchLowWinScore = 60;
            $frenchHighWinScore = 70;
            /* end of allied turn */
            if($gameRules->attackingForceId === Hanau1813::ALLIED_FORCE && !$this->deadGuardInf){
                foreach($specialHexes as $specialHex){
                    if($specialHex === Hanau1813::FRENCH_FORCE){
                        $frenchWin = true;
                        $victoryReason .= "Occupy vital objective ";
                    }
                }
            }
            if ($this->victoryPoints[Hanau1813::FRENCH_FORCE] >= $frenchLowWinScore && $turn <= 5) {
                    $frenchWin = true;
                $victoryReason .= "Over $frenchLowWinScore on or before turn 5 ";
            }
            if($this->victoryPoints[Hanau1813::FRENCH_FORCE] >= $frenchHighWinScore){
                $frenchWin = true;
                $victoryReason .= "Over $frenchHighWinScore ";
            }
            if ($this->victoryPoints[Hanau1813::ALLIED_FORCE] >= $alliedWinScore) {
                $alliedWin = true;
                $victoryReason .= "Over $alliedWinScore ";
            }

            if ($frenchWin && !$alliedWin) {
                $this->winner = Hanau1813::FRENCH_FORCE;
                $gameRules->flashMessages[] = "French Win";
                $gameRules->flashMessages[] = $victoryReason;
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
            if ($alliedWin && !$frenchWin) {
                $this->winner = Hanau1813::ALLIED_FORCE;
                $gameRules->flashMessages[] = "Allies Win";
                $gameRules->flashMessages[] = $victoryReason;
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
            if($frenchWin && $alliedWin){
                $gameRules->flashMessages[] = "Tie Game";
                $gameRules->flashMessages[] = $victoryReason;
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
            if ($turn > $gameRules->maxTurn) {
                $this->winner = Hanau1813::ALLIED_FORCE;
                $gameRules->flashMessages[] = "Allies Win";
                $gameRules->flashMessages[] = "French Fail to Win";
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }

    public function postRecoverUnits()
    {
        $b = Battle::getBattle();
        $scenario = $b->scenario;

        if ($b->gameRules->turn == 1 && $b->gameRules->phase == RED_MOVE_PHASE) {
            if(!empty($scenario->earlyMovement)){
                $b->gameRules->flashMessages[] = "Allies south the the river Kinzig may not move.";
            }else{
                $b->gameRules->flashMessages[] = "No Allied movement this turn.";
            }
        }

        if (!isset($scenario->dayTwoMovement) && $b->gameRules->turn == 2 && $b->gameRules->phase == RED_MOVE_PHASE) {
            $b->gameRules->flashMessages[] = "Allies south the the river Kinzig may not move.";
        }
    }

    public function postRecoverUnit($args)
    {
        $unit = $args[0];
        $b = Battle::getBattle();
        $scenario = $b->scenario;
        $id = $unit->id;

        if($b->gameRules->turn <= 2 && !isset($this->southOfTheRiver)){
            $terrain = $b->terrain;
            $reinforceZones = $terrain->reinforceZones;
            $southOfTheRiver = [];
            foreach($reinforceZones as $reinforceZone){
                if($reinforceZone->name == 'D'){
                    $southOfTheRiver[$reinforceZone->hexagon->name] = true;
                }
            }
            $this->southOfTheRiver = $southOfTheRiver;
        }

        parent::postRecoverUnit($args);

        if ($b->gameRules->turn == 1 && $b->gameRules->phase == RED_MOVE_PHASE && $unit->status == STATUS_READY && $unit->forceId == Hanau1813::ALLIED_FORCE) {
            /* if early Movement set and unit is north of river they can move */
            if(!(isset($scenario->earlyMovement) && empty($this->southOfTheRiver[$unit->hexagon->name]))){
                $unit->status = STATUS_UNAVAIL_THIS_PHASE;
            }
        }

        if ($b->gameRules->turn == 2 && $b->gameRules->phase == RED_MOVE_PHASE){
          if($unit->status == STATUS_READY && $unit->forceId == Hanau1813::ALLIED_FORCE && !empty($this->southOfTheRiver[$unit->hexagon->name])) {
              $unit->status = STATUS_UNAVAIL_THIS_PHASE;
          }
        }
    }
}
use Wargame\Mollwitz\Hanau1813\Hanau1813;
