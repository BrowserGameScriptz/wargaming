<?php
namespace Wargame\Mollwitz\Dubba1843;
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

//
//global $force_name;
//$force_name[Dubba1843::BRITISH_FORCE] = "British";
//$force_name[Dubba1843::BELUCHI_FORCE] = "Beluchi";

class Dubba1843 extends \Wargame\Mollwitz\IndiaCore
{


    const BRITISH_FORCE = 1;
    const BELUCHI_FORCE = 2;
    /* @var Mapdata */
    public $mapData;
    public $mapViewer;
    public $force;
    /* @var Terrain */
    public $terrain;
    /* @var MoveRules */
    public $moveRules;
    public $combatRules;
    public $gameRules;

    public $victory;


    public $players;

    static function enterMulti()
    {
        $deployTwo = $playerOne = "British";
        $deployOne = $playerTwo = "Beluchi";
        @include_once "enterMulti.php";
    }

    static function getPlayerData($scenario){
        $forceName = ["Observer", "British", "Beluchi"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function save()
    {
        $data = parent::save();

        return $data;
    }



    public function init()
    {


        $scenario = $this->scenario;
        $unitSets = $scenario->units;

        UnitFactory::$injector = $this->force;

        foreach ($unitSets as $unitSet) {
            if (empty($scenario->commandControl)) {
                if ($unitSet->class === 'hq'){
                    continue;
                }
            }
            for ($i = 0; $i < $unitSet->num; $i++) {
                UnitFactory::create("infantry-1", $unitSet->forceId, "deployBox", "", $unitSet->combat, $unitSet->combat, $unitSet->movement, true, STATUS_CAN_DEPLOY, $unitSet->reinforce, 1, $unitSet->range, $unitSet->nationality, false, $unitSet->class);
            }
        }

//        $artRange = 3;
//        $beluchiStrength = 2;
//
//        UnitFactory::$injector = $this->force;
//        if(!empty($this->scenario->commandControl)){
//            $beluchiStrength = 3;
//
//            for ($i = 0; $i < 2; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BELUCHI_FORCE, "deployBox", "SikhInfBadge.png", 1, 1, 5, true, STATUS_CAN_DEPLOY, "B", 1, 1, "Beluchi", false, 'hq');
//            }
//        }
//
//        for ($i = 0; $i < 25; $i++) {
//            UnitFactory::create("infantry-1", Dubba1843::BELUCHI_FORCE, "deployBox", "SikhInfBadge.png", $beluchiStrength, $beluchiStrength, 3, true, STATUS_CAN_DEPLOY, "B", 1, 1, "Beluchi", false, 'infantry');
//        }
//        for ($i = 0; $i < 15; $i++) {
//            UnitFactory::create("infantry-1", Dubba1843::BELUCHI_FORCE, "deployBox", "SikhCavBadge.png", 3, 3, 5, true, STATUS_CAN_DEPLOY, "B", 1, 1, "Beluchi", false, 'cavalry');
//        }
//        for ($i = 0; $i < 1; $i++) {
//            UnitFactory::create("infantry-1", Dubba1843::BELUCHI_FORCE, "deployBox", "SikhArtBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "B", 1, 2, "Beluchi", false, 'artillery');
//        }
//
//        if(!empty($this->scenario->toughBritish)){
//
//            for ($i = 0; $i < 4; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "BritInfBadge.png", 7, 7, 4, true, STATUS_CAN_DEPLOY, "A", 1, 1, "British", false, 'infantry');
//            }
//            for ($i = 0; $i < 5; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "NativeInfBadge.png", 6, 6, 4, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Native", false, 'infantry');
//            }
//            for ($i = 0; $i < 1; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "BritCavBadge.png", 7, 7, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "British", false, 'cavalry');
//            }
//            for ($i = 0; $i < 3; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "NativeCavBadge.png", 6, 6, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Native", false, 'cavalry');
//            }
//            for ($i = 0; $i < 2; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "BritArtBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "British", false, 'artillery');
//            }
//            for ($i = 0; $i < 1; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "BritHorArtBadge.png", 4, 4, 5, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "British", false, 'horseartillery');
//            }
//        }else{
//            for ($i = 0; $i < 4; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "BritInfBadge.png", 6, 6, 4, true, STATUS_CAN_DEPLOY, "A", 1, 1, "British", false, 'infantry');
//            }
//            for ($i = 0; $i < 5; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "NativeInfBadge.png", 5, 5, 4, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Native", false, 'infantry');
//            }
//            for ($i = 0; $i < 1; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "BritCavBadge.png", 6, 6, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "British", false, 'cavalry');
//            }
//            for ($i = 0; $i < 3; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "NativeCavBadge.png", 5, 5, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Native", false, 'cavalry');
//            }
//             for ($i = 0; $i < 2; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "BritArtBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "British", false, 'artillery');
//            }
//            for ($i = 0; $i < 1; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "BritHorArtBadge.png", 3, 3, 5, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "British", false, 'horseartillery');
//            }
//        }
//
//        if(!empty($this->scenario->commandControl)) {
//            for ($i = 0; $i < 4; $i++) {
//                UnitFactory::create("infantry-1", Dubba1843::BRITISH_FORCE, "deployBox", "BritInfBadge.png", 1, 1, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "British", false, 'hq');
//            }
//        }
    }

    function __construct($data = null, $arg = false, $scenario = false, $game = false)
    {
        parent::__construct($data, $arg, $scenario, $game);
        if ($data) {
        } else {
            $this->victory = new \Wargame\Victory("\\Wargame\\Mollwitz\\Dubba1843\\dubba1843VictoryCore");

            $this->mapData->blocksZoc->blocked = true;
            $this->mapData->blocksZoc->blocksnonroad = true;

            $this->moveRules->enterZoc = "stop";
            $this->moveRules->exitZoc = "stop";
            $this->moveRules->noZocZoc = true;
            $this->moveRules->zocBlocksRetreat = true;

            // game data
            $this->gameRules->setMaxTurn(12);
            $this->gameRules->setInitialPhaseMode(RED_DEPLOY_PHASE, DEPLOY_MODE);
            $this->gameRules->attackingForceId = RED_FORCE; /* object oriented! */
            $this->gameRules->defendingForceId = BLUE_FORCE; /* object oriented! */
            $this->force->setAttackingForceId($this->gameRules->attackingForceId); /* so object oriented */


            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_DEPLOY_PHASE, DEPLOY_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);

            $this->gameRules->addPhaseChange(BLUE_MOVE_PHASE, BLUE_COMBAT_PHASE, COMBAT_SETUP_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_COMBAT_PHASE, RED_MOVE_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);

            $this->gameRules->addPhaseChange(RED_MOVE_PHASE, RED_COMBAT_PHASE, COMBAT_SETUP_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_COMBAT_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, true);

            // force data



        }
    }
}