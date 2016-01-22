<?php
namespace Wargame\Mollwitz\Lesnaya1708;
use \Wargame\Mollwitz\UnitFactory;
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

define("SWEDISH_FORCE", 2);
define("RUSSIAN_FORCE", 1);

global $force_name;
$force_name[RUSSIAN_FORCE] = "Russian";
$force_name[SWEDISH_FORCE] = "Swedish";

class Lesnaya1708 extends \Wargame\Mollwitz\JagCore
{
    public $specialHexesMap = ['SpecialHexA'=>2, 'SpecialHexB'=>2, 'SpecialHexC'=>0];


    static function getPlayerData($scenario){
        return \Wargame\Battle::register(["Observer", "Russian", "Swedish"],
            ["Observer", "Russian", "Swedish" ]);
    }

    static function enterMulti()
    {
        @include_once "enterMulti.php";
    }

    static function playMulti($name, $wargame, $arg = false)
    {
        $deployOne = $playerOne = "Russian";
        $deployTwo = $playerTwo = "Swedish";
        @include_once "playMulti.php";
    }

    function terrainGen($mapDoc, $terrainDoc){
        parent::terrainGen($mapDoc, $terrainDoc);
        $this->terrain->addAltEntranceCost('forest', 'wagon', 'blocked');
    }

    function save()
    {
        $data = new \stdClass();
        $data->mapData = $this->mapData;
        $data->mapViewer = $this->mapViewer;
        $data->moveRules = $this->moveRules->save();
        $data->force = $this->force;
        $data->gameRules = $this->gameRules->save();
        $data->combatRules = $this->combatRules->save();
        $data->players = $this->players;
        $data->victory = $this->victory->save();
        $data->terrainName = $this->terrainName;
        $data->arg = $this->arg;
        $data->scenario = $this->scenario;
        $data->game = $this->game;
        $data->specialHexA = $this->specialHexA;
        $data->specialHexB = $this->specialHexB;

        return $data;
    }

    public function init()
    {

        $scenario = $this->scenario;
        $unitSets = $scenario->units;

        UnitFactory::$injector = $this->force;

        foreach($unitSets as $unitSet) {
            for ($i = 0; $i < $unitSet->num; $i++) {
                if(!empty($scenario->stepReduction) && isset($unitSet->reduced)){
                    UnitFactory::create("infantry-1", $unitSet->forceId, "deployBox", "", $unitSet->combat, $unitSet->reduced, $unitSet->movement, false, STATUS_CAN_DEPLOY, $unitSet->reinforce, 1, $unitSet->range, $unitSet->nationality, false, $unitSet->class);
                }else{
                    UnitFactory::create("infantry-1", $unitSet->forceId, "deployBox", "", $unitSet->combat, $unitSet->combat, $unitSet->movement, true, STATUS_CAN_DEPLOY, $unitSet->reinforce, 1, $unitSet->range, $unitSet->nationality, false, $unitSet->class);
                }
            }
        }
    }

    function __construct($data = null, $arg = false, $scenario = false, $game = false)
    {

        parent::__construct($data, $arg, $scenario, $game);
        if ($data) {
            $this->specialHexA = $data->specialHexA;
            $this->specialHexB = $data->specialHexB;
        } else {
            $this->victory = new \Victory("\\Wargame\\Mollwitz\\Lesnaya1708\\Lesnaya1708VictoryCore");

            $this->mapData->blocksZoc->blocked = true;
            $this->mapData->blocksZoc->blocksnonroad = true;

            $this->moveRules->enterZoc = "stop";
            $this->moveRules->exitZoc = "stop";
            $this->moveRules->noZocZoc = true;
            $this->moveRules->zocBlocksRetreat = true;

            // game data

            $this->gameRules->setMaxTurn(8);
            $this->gameRules->setInitialPhaseMode(BLUE_DEPLOY_PHASE, DEPLOY_MODE);
            $this->gameRules->attackingForceId = BLUE_FORCE; /* object oriented! */
            $this->gameRules->defendingForceId = RED_FORCE; /* object oriented! */
            $this->force->setAttackingForceId($this->gameRules->attackingForceId); /* so object oriented */


            $this->gameRules->addPhaseChange(BLUE_DEPLOY_PHASE, RED_DEPLOY_PHASE, DEPLOY_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);

            $this->gameRules->addPhaseChange(BLUE_MOVE_PHASE, BLUE_COMBAT_PHASE, COMBAT_SETUP_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_COMBAT_PHASE, RED_MOVE_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);

            $this->gameRules->addPhaseChange(RED_MOVE_PHASE, RED_COMBAT_PHASE, COMBAT_SETUP_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_COMBAT_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, true);

        }

        $this->moveRules->stacking = function($mapHex, $forceId, $unit){
            $wagon = false;
            if($unit->class === "wagon"){
                $wagon = true;
            }

            foreach($mapHex->forces[$forceId] as $mKey => $mVal){
                if($this->force->units[$mKey]->class === "wagon"){
                    if($wagon){
                        return true;
                    }
                    return count((array)$mapHex->forces[$forceId]) >= 2;
                }
            }
            if($wagon){
                return   count((array)$mapHex->forces[$forceId]) >= 2;
            }
            return count((array)$mapHex->forces[$forceId]) >= 1;
        };
    }
}