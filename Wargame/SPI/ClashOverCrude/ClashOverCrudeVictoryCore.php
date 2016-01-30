<?php
namespace Wargame\SPI\ClashOverCrude;
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

class ClashOverCrudeVictoryCore extends \Wargame\SPI\victoryCore
{
    public $victoryPoints;
    protected $movementCache;
    protected $combatCache;
    protected $supplyLen = false;
    private $landingZones;
    private $airdropZones;
    public $gameOver = false;
    public $winner = false;

    public $airXferPts = 30;


    function __construct($data)
    {
        if ($data) {
            $this->victoryPoints = $data->victory->victoryPoints;
            $this->movementCache = $data->victory->movementCache;
            $this->combatCache = $data->victory->combatCache;
            $this->supplyLen = $data->victory->supplyLen;
            $this->landingZones = $data->victory->landingZones;
            $this->airdropZones = $data->victory->airdropZones;
            $this->gameOver = $data->victory->gameOver;
        } else {
            $this->victoryPoints = array(0, 0, 25);
            $this->movementCache = new stdClass();
            $this->combatCache = new stdClass();
            $this->landingZones = [];
            $this->airdropZones = [];
        }
    }

    public function setSupplyLen($supplyLen)
    {
        $this->supplyLen = $supplyLen[0];
    }

    public function save()
    {
        $ret = new stdClass();
        $ret->victoryPoints = $this->victoryPoints;
        $ret->movementCache = $this->movementCache;
        $ret->combatCache = $this->combatCache;
        $ret->supplyLen = $this->supplyLen;
        $ret->landingZones = $this->landingZones;
        $ret->airdropZones = $this->airdropZones;
        $ret->gameOver = $this->gameOver;
        return $ret;
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;

        if ($forceId == LOYALIST_FORCE) {
            $newLandings = [];
            foreach ($this->landingZones as $landingZone) {
                if ($landingZone == $mapHexName) {
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='loyalistVictoryPoints'>Beachhead Destroyed</span>";
                    $battle->mapData->removeSpecialHex($mapHexName);
                    unset($battle->mapData->specialHexesChanges->$mapHexName);
                    continue;
                }
                $newLandings[] = $landingZone;
            }
            $this->landingZones = $newLandings;

            $newAirdrops = [];
            foreach ($this->airdropZones as $airdropZone) {
                if ($airdropZone == $mapHexName) {
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='loyalistVictoryPoints'>Airdrop zone Destroyed</span>";
                    $battle->mapData->removeSpecialHex($mapHexName);
                    unset($battle->mapData->specialHexesChanges->$mapHexName);
                    continue;
                }
                $newAirdrops[] = $airdropZone;
            }
            $this->airdropZones = $newAirdrops;
        }

//        if(in_array($mapHexName,$battle->specialHexA)){
//            $vp = 25;
//
//            $prevForceId = $battle->mapData->specialHexes->$mapHexName;
//            if ($forceId == REBEL_FORCE) {
//                $this->victoryPoints[REBEL_FORCE]  += $vp;
//                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='rebel'>+$vp Rebel vp</span>";
//                $this->victoryPoints[LOYALIST_FORCE] -= $vp;
//                $battle->mapData->specialHexesVictory->$mapHexName .= "<span class='rebel'> -$vp Loyalist vp</span>";
//            }
//            if ($forceId == LOYALIST_FORCE) {
//                $this->victoryPoints[LOYALIST_FORCE]  += $vp;
//                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='loyalist'>+$vp Loyalist vp</span>";
//                $this->victoryPoints[REBEL_FORCE] -= $vp;
//                $battle->mapData->specialHexesVictory->$mapHexName .= "<span class='loyalist'> -$vp Rebel vp</span>";
//            }
//        }

    }

    public function postReinforceZones($args)
    {
        list($zones, $unit) = $args;
        if ($unit->forceId == BLUE_FORCE) {
            $zone = $unit->reinforceZone;
            if ($zone == "O") {
                $inverse = [];
                for($row = 1; $row <= 29; $row++){
                    for($col = 1; $col <= 21;$col++){
                        $hexNum = sprintf("%04d", "0000" . ($col * 100 + $row));
                        $inverse[$hexNum] = true;
                    }
                }
                foreach($zones as $zone){
                    $hexNum = $zone->hexagon->name;
                    $hexNum = sprintf("%04d", "0000" . $hexNum);
                    unset($inverse[$hexNum]);
                }
                $zones = [];
                foreach($inverse as $key=>$val){
                    $zones[] = new \Wargame\ReinforceZone($key, "O");
                }
            }
        }
        return array($zones);
    }


    public function postReinforceZoneNames($args)
    {
        list($zoneNames, $unit) = $args;
        if ($unit->forceId == BLUE_FORCE) {
            $zone = $unit->reinforceZone;
            if ($zone == "O") {
                if(in_array("O",$zoneNames)){
                    return [];
                }
                return [["O"]];
            }
        }
        return array($zoneNames);
    }

    public function reduceUnit($args)
    {
        $unit = $args[0];

        $vp = $unit->damage;

        if ($unit->forceId == 1) {
            $victorId = 2;
            $this->victoryPoints[$victorId] += $vp;
            $hex = $unit->hexagon;
            $battle = Battle::getBattle();
            $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='loyalistVictoryPoints'>+$vp vp</span>";
        } else {
            $victorId = 1;
            $hex  = $unit->hexagon;
            $battle = Battle::getBattle();
            $battle->mapData->specialHexesVictory->{$hex->name} = "+$vp vp";
            $this->victoryPoints[$victorId] += $vp;
        }
    }

    public function incrementTurn()
    {
        $battle = Battle::getBattle();

        $theUnits = $battle->force->units;
        foreach ($theUnits as $id => $unit) {

            if ($unit->status == STATUS_CAN_REINFORCE && $unit->reinforceTurn <= $battle->gameRules->turn && $unit->hexagon->parent != "deployBox" && $unit->hexagon->parent != "germany" && $unit->hexagon->parent != "israel") {
//                $theUnits[$id]->status = STATUS_ELIMINATED;
                $theUnits[$id]->hexagon->parent = "oman";
            }
        }
    }

    public function gameEnded()
    {
        $battle = Battle::getBattle();
        if ($this->victoryPoints[LOYALIST_FORCE] > $this->victoryPoints[REBEL_FORCE]) {
            $battle->gameRules->flashMessages[] = "Loyalist Player Wins";
            $this->winner = LOYALIST_FORCE;
        }
        if ($this->victoryPoints[REBEL_FORCE] > $this->victoryPoints[LOYALIST_FORCE]) {
            $battle->gameRules->flashMessages[] = "Rebel Player Wins";
            $this->winner = REBEL_FORCE;
        }
        if ($this->victoryPoints[LOYALIST_FORCE] == $this->victoryPoints[REBEL_FORCE]) {
            $battle->gameRules->flashMessages[] = "Tie Game";
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
        $force = $battle->force;

        if ($turn == 1 && $gameRules->phase == BLUE_MOVE_PHASE) {
            /* first 4 units gaga */
            $supply = [];
            $battle->terrain->reinforceZones = [];
            $units = $force->units;
            $num = count($units);
            for ($i = 0; $i < $num; $i++) {
                $unit = $units[$i];
                if ($unit->forceId == BLUE_FORCE && $unit->hexagon->parent === "gameImages") {
                    $supply[$unit->hexagon->name] = BLUE_FORCE;
                    if ($unit->class === "para") {
                        $this->airdropZones[] = $unit->hexagon->name;
                    } else {
                        $this->landingZones[] = $unit->hexagon->name;
                    }
                }
            }
            $battle->mapData->setSpecialHexes($supply);
        }
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
            if ($battle->force->reinforceTurns->$turn->$forceId) {
                $gameRules->flashMessages[] = "@show deployWrapper";
                $gameRules->flashMessages[] = "Reinforcements have been moved to the Deploy/Staging Area";
            }
        }
    }

    public function preRecoverUnits()
    {
        $b = Battle::getBattle();

        $goal = array_merge($this->landingZones, $this->airdropZones);
        $this->rebelGoal = $goal;

        $goal = array();
        $goal = array_merge($goal, array(110, 210, 310, 410, 510, 610, 710, 810, 910, 1010, 1110, 1210, 1310, 1410, 1510, 1610, 1710, 1810, 1910, 2010));
        $this->loyalistGoal = $goal;
    }

    function isExit($args)
    {
        list($unit) = $args;
        if ($unit->forceId == BLUE_FORCE && in_array($unit->hexagon->name, $this->landingZones)) {
            return true;
        }
        return false;
    }


    public function postRecoverUnit($args)
    {
        /* @var unit $unit */
        $unit = $args[0];

        $b = Battle::getBattle();
        $id = $unit->id;
        if($unit->class === "air" && $unit->forceId == $b->force->attackingForceId && ($b->gameRules->phase == RED_COMBAT_PHASE || $b->gameRules->phase == BLUE_COMBAT_PHASE )) {
            if($unit->hexagon && $unit->hexagon->name){

                /* Air units can only attack units in same hex */
                $mapHex = $b->mapData->getHex($unit->hexagon->name);
                if($mapHex->isOccupied($b->force->defendingForceId,1)){
                    $unit->setStatus(STATUS_READY);
                }else{
                    $unit->status = STATUS_UNAVAIL_THIS_PHASE;
                }
            }
            /* @var MapHex $mapHex */
        }

        if($unit->class !== "air" &&  ($b->gameRules->phase == RED_AIR_COMBAT_PHASE || $b->gameRules->phase == BLUE_AIR_COMBAT_PHASE )) {
            if($unit->hexagon && $unit->hexagon->name){
                $unit->status = STATUS_UNAVAIL_THIS_PHASE;
            }
            /* @var MapHex $mapHex */
        }

        if ($unit->forceId != $b->gameRules->attackingForceId) {
//            return;
        }
        if (!empty($b->scenario->supply) === true) {
            if ($unit->forceId == REBEL_FORCE) {
                $bias = array(5 => true, 6 => true, 1 => true);
                $goal = $this->rebelGoal;
            } else {
                $bias = array(2 => true, 3 => true, 4 => true);
                $goal = $this->loyalistGoal;
            }
            $this->unitSupplyEffects($unit, $goal, $bias, $this->supplyLen);
        }
    }

    public function preStartMovingUnit($arg)
    {
        $unit = $arg[0];
        $battle = Battle::getBattle();
        if ($unit->class != 'air') {
            $battle->moveRules->enterZoc = "stop";
            $battle->moveRules->exitZoc = 0;
            $battle->moveRules->noZocZoc = true;
        } else {
            $battle->moveRules->enterZoc = 0;
            $battle->moveRules->exitZoc = 0;
            $battle->moveRules->noZocZoc = false;

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
        $this->victoryPoints = $vp;
    }
}