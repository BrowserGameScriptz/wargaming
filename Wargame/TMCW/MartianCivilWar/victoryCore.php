<?php
namespace Wargame\TMCW\MartianCivilWar;
use \stdClass;
use \Wargame\Battle;
/**
 *
 * Copyright 2012-2015 David Rodal
 * User: David Markarian Rodal
 * Date: 3/8/15
 * Time: 5:48 PM
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 5/7/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */
//include "supplyRulesTraits.php";

class victoryCore extends \Wargame\TMCW\victoryCore
{


    function __construct($data = false)
    {
        parent::__construct($data);
    }

    public function setSupplyLen($supplyLen){
        $this->supplyLen = $supplyLen[0];
    }
    public function save()
    {
        $ret = parent::save();
        return $ret;
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;
        if ($forceId == 1) {
            $this->victoryPoints[$forceId]++;
            $battle->mapData->specialHexesVictory->$mapHexName = "<span class='rebelVictoryPoints'>+1 vp</span>";
        }

    }

    public function postReinforceZones($args)
    {
        list($zones, $unit) = $args;

        $zones[] = new \Wargame\ReinforceZone(2414, 2414);
        return array($zones);
    }

    public function reduceUnit($args)
    {
        $unit = $args[0];
        if ($unit->strength == $unit->maxStrength) {
            if ($unit->status == STATUS_ELIMINATING || $unit->status == STATUS_RETREATING) {
                $vp = $unit->maxStrength;
            } else {
                $vp = $unit->maxStrength - $unit->minStrength;
            }
        } else {
            $vp = $unit->minStrength;
        }
        if ($unit->forceId == 1) {
            $victorId = 2;
            $this->victoryPoints[$victorId] += $vp;
            $hex = $unit->hexagon;
            $battle = Battle::getBattle();
            $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='loyalistVictoryPoints'>+$vp vp</span>";
        } else {
//            $victorId = 1;
//            $hex  = $unit->hexagon;
//            $battle = Battle::getBattle();
//            $battle->mapData->specialHexesVictory->{$hex->name} = "+$vp vp";
//            $this->victoryPoints[$victorId] += $vp;
        }
    }

    public function incrementTurn()
    {
        $battle = Battle::getBattle();

        $theUnits = $battle->force->units;
        foreach ($theUnits as $id => $unit) {

            if ($unit->status == STATUS_CAN_REINFORCE && $unit->reinforceTurn <= $battle->gameRules->turn && $unit->hexagon->parent != "deployBox") {
//                $theUnits[$id]->status = STATUS_ELIMINATED;
                $theUnits[$id]->hexagon->parent = "deployBox";
            }
        }
    }

    public function gameEnded(){
        $battle = Battle::getBattle();

        if($this->victoryPoints[REBEL_FORCE] > $this->victoryPoints[LOYALIST_FORCE]){
            $battle->gameRules->flashMessages[] = "Rebel Player Wins";
        }else{
            $battle->gameRules->flashMessages[] = "Loyalist Player Wins";
        }
        $this->gameOver = true;
        return true;
    }
    public function phaseChange()
    {

        /* @var $battle MartianCivilWar */
        $battle = Battle::getBattle();
        /* @var $gameRules GameRules */
        $gameRules = $battle->gameRules;
        $forceId = $gameRules->attackingForceId;
        $turn = $gameRules->turn;

        if ($gameRules->phase == RED_COMBAT_PHASE || $gameRules->phase == BLUE_COMBAT_PHASE) {
            $gameRules->flashMessages[] = "@hide deployWrapper";
        } else {
            $gameRules->flashMessages[] = "@hide crt";

            /* Restore all un-supplied strengths */
            $force = $battle->force;
            $this->restoreAllCombatEffects($force);
        }
        if ($gameRules->phase == BLUE_REPLACEMENT_PHASE || $gameRules->phase == RED_REPLACEMENT_PHASE) {
            $gameRules->flashMessages[] = "@show deadpile";
            $forceId = $gameRules->attackingForceId;
        }
        if ($gameRules->phase == BLUE_MOVE_PHASE || $gameRules->phase == RED_MOVE_PHASE) {
            $gameRules->flashMessages[] = "@hide deadpile";
            if (!empty($battle->force->reinforceTurns->$turn->$forceId)) {
                $gameRules->flashMessages[] = "@show deployWrapper";
                $gameRules->flashMessages[] = "Reinforcements have been moved to the Deploy/Staging Area";
            }
        }
    }
    public function preRecoverUnits(){
        /* @var unit $unit */

        $b = Battle::getBattle();

        if (!empty($b->scenario->supply) === true) {
            $bias = array(5 => true, 6 => true);
            $goal = $b->moveRules->calcRoadSupply(REBEL_FORCE, 401, $bias);
            $goal = array_merge($goal, array(101, 102, 103, 104, 201, 301, 401, 501, 601, 701, 801, 901, 1001));
            $this->rebelGoal = $goal;


            $bias = array(2 => true, 3 => true);
            $goal = $b->moveRules->calcRoadSupply(LOYALIST_FORCE, 3017, $bias);
            $goal = array_merge($goal, array(3014, 3015, 3016, 3017, 3018, 3019, 3020, 2620, 2720, 2820, 2920));
            $this->loyalistGoal = $goal;

        }

    }

    public function postRecoverUnit($args)
    {
        /* @var unit $unit */
        $unit = $args[0];

        $b = Battle::getBattle();
        $id = $unit->id;
        if ($unit->forceId != $b->gameRules->attackingForceId) {
//            return;
        }
        if (!empty($b->scenario->supply) === true) {
            if ($unit->forceId == REBEL_FORCE) {
                $bias = array(5 => true, 6 => true);
                $goal = $this->rebelGoal;
            } else {
                $bias = array(2 => true, 3 => true);
                $goal = $this->loyalistGoal;
            }
            $this->unitSupplyEffects($unit, $goal, $bias, $this->supplyLen);
        }
    }

    public function preCombatResults($args)
    {
        return $args;
        list($defenderId, $attackers, $combatResults, $dieRoll) = $args;
        $battle = Battle::getBattle();
        /* @var mapData $mapData */
        $mapData = $battle->mapData;
        $unit = $battle->force->getUnit($defenderId);
        $defendingHex = $unit->hexagon->name;
        if ($defendingHex == 407 || $defendingHex == 2415 || $defendingHex == 2414 || $defendingHex == 2515) {
            /* Cunieform */
            if ($unit->forceId == RED_FORCE) {
                if ($combatResults == DR2) {
                    $combatResults = NE;
                }
                if ($combatResults == DRL2) {
                    $combatResults = DL;
                }
            }
        }
        return array($defenderId, $attackers, $combatResults, $dieRoll);
    }

    public function preStartMovingUnit($arg)
    {
        $unit = $arg[0];
        $battle = Battle::getBattle();
        if (!empty($battle->scenario->supply) === true) {
            if ($unit->class != 'mech') {
                $battle->moveRules->enterZoc = "stop";
                $battle->moveRules->exitZoc = 0;
                $battle->moveRules->noZocZoc = true;
            } else {
                $battle->moveRules->enterZoc = 2;
                $battle->moveRules->exitZoc = 1;
                $battle->moveRules->noZocZoc = false;

            }
        }
    }

    public function playerTurnChange($arg)
    {
        $attackingId = $arg[0];
        $battle = Battle::getBattle();
        $mapData = $battle->mapData;
        $vp = $this->victoryPoints;
        $specialHexes = $mapData->specialHexes;
        $gameRules = $battle->gameRules;

        if ($gameRules->phase == BLUE_MECH_PHASE || $gameRules->phase == RED_MECH_PHASE) {
            $gameRules->flashMessages[] = "@hide crt";
        }
        if ($attackingId == REBEL_FORCE) {
            $gameRules->flashMessages[] = "Rebel Player Turn";
            $gameRules->replacementsAvail = 1;
        }
        if ($attackingId == LOYALIST_FORCE) {
            $gameRules->flashMessages[] = "Loyalist Player Turn";
            $gameRules->replacementsAvail = 10;
        }

        /*only get special VPs' at end of first Movement Phase */
        if ($specialHexes) {
            $scenario = $battle->scenario;
            if (!empty($scenario->supply) === true) {
                $inCity = false;
                $roadCut = false;
                foreach ($specialHexes as $k => $v) {
                    if ($v == REBEL_FORCE) {
                        $points = 1;
                        if ($k == 2414 || $k == 2415 || $k == 2515) {
                            $inCity = true;
                            $points = 5;
                        } elseif ($k >= 2416) {
                            /* Remember the first road Cut */
                            if ($roadCut === false) {
                                $roadCut = $k;
                            }
                            continue;
                        }
                        $vp[$v] += $points;
                        $battle->mapData->specialHexesVictory->$k = "<span class='rebelVictoryPoints'>+$points vp</span>";
                    } else {
                        //                    $vp[$v] += .5;
                    }
                }
                if ($roadCut !== false) {
                    $vp[REBEL_FORCE] += 3;
                    $battle->mapData->specialHexesVictory->$roadCut = "<span class='rebelVictoryPoints'>+3 vp</span>";
                }
                if (!$inCity) {
                    /* Cuneiform isolated? */
                    $cuneiform = 2515;
                    if (!$battle->moveRules->calcSupplyHex($cuneiform, array(3014, 3015, 3016, 3017, 3018, 3019, 3020, 2620, 2720, 2820, 2920), array(2 => true, 3 => true), RED_FORCE, $this->supplyLen)) {
                        $vp[REBEL_FORCE] += 5;

                        $battle->mapData->specialHexesVictory->$cuneiform = "<span class='rebelVictoryPoints'>+5 vp</span>";

                    }
                }
            } else {
                foreach ($specialHexes as $k => $v) {
                    if ($v == 1) {
                        $points = 1;
                        if ($k == 2414 || $k == 2415 || $k == 2515) {
                            $points = 5;
                        } elseif ($k >= 2416) {
                            $points = 3;
                        }
                        $vp[$v] += $points;
                        $battle = Battle::getBattle();
                        $battle->mapData->specialHexesVictory->$k = "<span class='rebelVictoryPoints'>+$points vp</span>";
                    } else {
                        //                    $vp[$v] += .5;
                    }
                }
            }
        }
        $this->victoryPoints = $vp;
    }
}