<?php
namespace Wargame\TMCW\Nomonhan;
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

//include_once "supplyRulesTraits.php";

class nomonhanVictoryCore{

    public $victoryPoints;
    private $movementCache;
    private $combatCache;

    use \Wargame\TMCW\ModernSupplyRules;

    function __construct($data){
        if($data){
            $this->victoryPoints = $data->victory->victoryPoints;
            $this->movementCache = $data->victory->movementCache;
            $this->combatCache = $data->victory->combatCache;
        }else{
            $this->victoryPoints = array(0,0,0);
            $this->movementCache = new stdClass();
            $this->combatCache = new stdClass();
        }
    }
    public function save(){
        $ret = new stdClass();
        $ret->victoryPoints = $this->victoryPoints;
        $ret->movementCache = $this->movementCache;
        $ret->combatCache = $this->combatCache;
        return $ret;
    }

    public function preRecoverUnits()
    {
        $this->japaneseGoal = [101];

        $this->sovietGoal = [2020];
    }



    public function specialHexChange($args){
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;
        if($forceId == 1){
            $this->victoryPoints[$forceId]++;
            $battle->mapData->specialHexesVictory->$mapHexName = "<span class='rebelVictoryPoints'>+1 vp</span>";
        }

    }

//    public function postReinforceZones($args){
//        $battle = battle::getBattle();
//        /* @var $terrain terrain */
//        $terrain = $battle->terrain;
//        $zones = $terrain->getReinforceZones("W");
//
//        return array($zones);
//    }

    public function reinforceUnit($args){
        /* @var $unit unit
         * @var $hexagon Hexagon
         */
        list($unit, $hexagon) = $args;

        if($unit->forceId == Nomonhan::SOVIET_FORCE && $unit->reinforceTurn == 6){
            $victorId = Nomonhan::SOVIET_FORCE;
            $vp = 0 - $unit->maxStrength;
            $this->victoryPoints[$victorId] += $vp;
            $battle = Battle::getBattle();
            $battle->mapData->specialHexesVictory->{$hexagon->name} = "<span class='loyalistVictoryPoints'>$vp vp</span>";
        }
    }
    public function reduceUnit($args){
        $unit = $args[0];
        if($unit->strength == $unit->maxStrength){
            if($unit->status == STATUS_ELIMINATING || $unit->status == STATUS_RETREATING){
                $vp = $unit->maxStrength;
            }else{
                $vp = $unit->maxStrength - $unit->minStrength;
            }
        }else{
            $vp = $unit->minStrength;
        }
        if($unit->forceId == Nomonhan::JAPANESE_FORCE){
            $victorId = Nomonhan::SOVIET_FORCE;
            if($unit->class == "mech" || $unit->class == "artillery"){
                $vp += $vp;
            }
            $this->victoryPoints[$victorId] += $vp;
            $hex  = $unit->hexagon;
            $battle = Battle::getBattle();
            $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='loyalistVictoryPoints'>+$vp vp</span>";
        }else{
            $victorId = Nomonhan::JAPANESE_FORCE;
            $this->victoryPoints[$victorId] += $vp;
            $hex  = $unit->hexagon;
            $battle = Battle::getBattle();
            $battle->mapData->specialHexesVictory->{$hex->name} = "<span class='rebelVictoryPoints'>+$vp vp</span>";
        }
    }
    public function incrementTurn(){
        $battle = Battle::getBattle();

        $theUnits = $battle->force->units;
        foreach($theUnits as $id => $unit){

            if($unit->status == STATUS_CAN_REINFORCE && $unit->reinforceTurn <= $battle->gameRules->turn && $unit->hexagon->parent != "deployBox"){
                $theUnits[$id]->hexagon->parent = "deployBox";
            }
        }
    }
    public function phaseChange(){

        /* @var $battle MartianCivilWar */
        $battle = Battle::getBattle();
        /* @var $gameRules GameRules */
        $gameRules = $battle->gameRules;
        $turn = $gameRules->turn;


        if ($gameRules->phase == RED_COMBAT_PHASE || $gameRules->phase == BLUE_COMBAT_PHASE) {
            $gameRules->flashMessages[] = "@hide deployWrapper";
        } else {
            $gameRules->flashMessages[] = "@hide crt";

            /* Restore all un-supplied strengths */
            $force = $battle->force;
            foreach($this->combatCache as $id => $strength){
                $unit = $force->getUnit($id);
                $unit->removeAdjustment('supply');
//                $unit->strength = $strength;
                unset($this->combatCache->$id);
            }
        }
        if($gameRules->phase == BLUE_REPLACEMENT_PHASE || $gameRules->phase ==  RED_REPLACEMENT_PHASE){
            $gameRules->flashMessages[] = "@show deadpile";
            $forceId = $gameRules->attackingForceId;
            $reinforceTurns = $battle->force->reinforceTurns;
            if(isset($reinforceTurns) && isset($reinforceTurns->$turn) && isset($reinforceTurns->$turn->$forceId)){
                $gameRules->flashMessages[] = "Reinforcements have been moved to the dead pile";
            }
        }
        if($gameRules->phase == BLUE_MOVE_PHASE || $gameRules->phase ==  RED_MOVE_PHASE){
            $gameRules->flashMessages[] = "@hide deadpile";
            if($turn >= 6 && $gameRules->phase == RED_MOVE_PHASE){
                $gameRules->flashMessages[] = "@show deployWrapper";
            }
        }
    }
    public function postRecoverUnit($args)
    {
        /* @var unit $unit */
        $unit = $args[0];

        $b = Battle::getBattle();
        $id = $unit->id;
        if($unit->forceId != $b->gameRules->attackingForceId){
//            return;
        }
        if(!empty($b->scenario->supply) === true){
            if($unit->forceId == Nomonhan::JAPANESE_FORCE){
                for($i = 101;$i <= 3701;$i += 100){
                    $goal[] = $i;
                }
                $bias = array(1=>true, 5=>true,6=>true);
            }else{
                for($i = 125;$i <= 4025;$i += 100){
                    $goal[] = $i;
                }
                $bias =  array(2=>true,4=>true,3=>true);
            }
            if($b->gameRules->mode == REPLACING_MODE){
                if($unit->status == STATUS_CAN_UPGRADE){
                    $unit->supplied = $b->moveRules->calcSupply($unit->id,$goal,$bias);
                    if(!$unit->supplied){
                        /* TODO: make this not cry  (call a method) */
                        $unit->status = STATUS_STOPPED;
                    }
                }
                return;
            }
            if($b->gameRules->mode == MOVING_MODE){
                /* First Turn Japaneese Suprise */
//                if($b->gameRules->turn == 1 && $b->gameRules->phase == BLUE_MOVE_PHASE && $unit->status == STATUS_CAN_REINFORCE) {
//                    $this->movementCache->$id = $unit->maxMove;
//                    $unit->maxMove = $unit->maxMove * 2;
//                }
                 if($unit->status == STATUS_READY || $unit->status == STATUS_UNAVAIL_THIS_PHASE){
//                    $unit->supplied = $b->moveRules->calcSupply($unit,$goal,$bias);
                     $this->unitSupplyEffects($unit, $goal, $bias, false);
                }else{
                    return;
                }
                if(!$unit->supplied && !isset($this->movementCache->$id)) {
                    $this->movementCache->$id = $unit->maxMove;
                    $unit->maxMove = floor($unit->maxMove / 2);
                }
                if($unit->supplied && isset($this->movementCache->$id)) {
                    $unit->maxMove = $this->movementCache->$id;
                    unset($this->movementCache->$id);
                }
            }
            if($b->gameRules->mode == COMBAT_SETUP_MODE){
                if($b->gameRules->turn == 1 && $b->gameRules->phase == BLUE_COMBAT_PHASE && isset($this->movementCache->$id)) {
                    $unit->maxMove = $this->movementCache->$id;
                    unset($this->movementCache->$id);
                }
                if($unit->status == STATUS_READY || $unit->status == STATUS_DEFENDING || $unit->status == STATUS_UNAVAIL_THIS_PHASE){
                    $unit->supplied = $b->moveRules->calcSupply($unit->id,$goal, $bias);
                }else{
                    return;
                }
                if($unit->forceId == $b->gameRules->attackingForceId && !$unit->supplied && !isset($this->combatCache->$id)) {
                    $this->combatCache->$id = true;
                    $unit->addAdjustment('supply','floorHalf');
//                    $unit->strength = floor($unit->strength / 2);
                }
                if($unit->supplied && isset($this->combatCache->$id)) {
//                    $unit->strength = $this->combatCache->$id;
                    $unit->removeAdjustment('supply');
                    unset($this->combatCache->$id);
                }
                if($unit->supplied && isset($this->movementCache->$id)) {
                    $unit->maxMove = $this->movementCache->$id;
                    unset($this->movementCache->$id);
                }
            }
        }
    }
    public function preCombatResults($args){
        return $args;
        list($defenderId, $attackers, $combatResults, $dieRoll) = $args;
        $battle = Battle::getBattle();
        /* @var mapData $mapData */
        $mapData = $battle->mapData;
        $unit = $battle->force->getUnit($defenderId);
        $defendingHex = $unit->hexagon->name;
        if($defendingHex == 407 || $defendingHex == 2415 || $defendingHex == 2414 || $defendingHex == 2515){
            /* Cunieform */
            if($unit->forceId == RED_FORCE){
                if($combatResults == DR2){
                    $combatResults = NE;
                }
                if($combatResults == DRL2){
                    $combatResults = DL;
                }
            }
        }
        return array($defenderId, $attackers, $combatResults, $dieRoll);
    }
    public function preStartMovingUnit($arg){
        $unit = $arg[0];
        $battle = Battle::getBattle();
        if($unit->class != 'mech'){
            $battle->moveRules->enterZoc = "stop";
            $battle->moveRules->exitZoc = 0;
            $battle->moveRules->noZocZoc = true;
        }else{
            $battle->moveRules->enterZoc = 2;
            $battle->moveRules->exitZoc = 1;
            $battle->moveRules->noZocZoc = false;

        }
    }
    public function playerTurnChange($arg){
        $attackingId = $arg[0];
        $battle = Battle::getBattle();
        $mapData = $battle->mapData;
        $vp = $this->victoryPoints;
        $specialHexes = $mapData->specialHexes;
        $gameRules = $battle->gameRules;

        if($gameRules->phase == BLUE_MECH_PHASE || $gameRules->phase == RED_MECH_PHASE){
            $gameRules->flashMessages[] = "@hide crt";
        }
        if($attackingId == Nomonhan::SOVIET_FORCE){
            $gameRules->flashMessages[] = "Soviet Player Turn";
            $gameRules->replacementsAvail = 2;
        }
        if($attackingId  == Nomonhan::JAPANESE_FORCE){
            $gameRules->flashMessages[] = "Japanese Player Turn";
//            $gameRules->replacementsAvail = 10;
        }

           /*only get special VPs' at end of first Movement Phase */
        if($specialHexes){
            $arg = $battle->arg;
            if($arg == "Supply"){
                $inCity = false;
                $roadCut = false;
                foreach($specialHexes as $k=>$v){
                    if($v == REBEL_FORCE){
                        $points = 1;
                        if($k == 2414 || $k == 2415 || $k == 2515){
                            $inCity = true;
                            $points = 5;
                        }elseif($k >= 2416){
                            /* Remember the first road Cut */
                            if($roadCut === false){
                                $roadCut = $k;
                            }
                            continue;
                        }
                        $vp[$v] += $points;
                        $battle->mapData->specialHexesVictory->$k = "<span class='rebelVictoryPoints'>+$points vp</span>";
                    }else{
    //                    $vp[$v] += .5;
                    }
                }
                if($roadCut !== false){
                    $vp[REBEL_FORCE] += 3;
                    $battle->mapData->specialHexesVictory->$roadCut = "<span class='rebelVictoryPoints'>+3 vp</span>";
                }
                if(!$inCity){
                    /* Cuneiform isolated? */
                    $cuneiform = 2515;
                    if(!$battle->moveRules->calcSupplyHex($cuneiform, array(3014,3015,3016,3017,3018,3019,3020,2620,2720,2820,2920),array(2=>true,3=>true),RED_FORCE)){
                        $vp[REBEL_FORCE] += 5;

                        $battle->mapData->specialHexesVictory->$cuneiform = "<span class='rebelVictoryPoints'>+5 vp</span>";

                    }
                }
            }else{
                foreach($specialHexes as $k=>$v){
                    if($v == 1){
                        $points = 1;
                        if($k == 2414 || $k == 2415 || $k == 2515){
                            $points = 5;
                        }elseif($k >= 2416){
                            $points = 3;
                        }
                        $vp[$v] += $points;
                        $battle = Battle::getBattle();
                        $battle->mapData->specialHexesVictory->$k = "<span class='rebelVictoryPoints'>+$points vp</span>";
                    }else{
                        //                    $vp[$v] += .5;
                    }
                }            }
        }
       $this->victoryPoints = $vp;
    }
}
