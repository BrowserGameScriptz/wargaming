<?php
namespace Wargame\Mollwitz\Ferozesha;
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



//global $force_name;
//$force_name[SIKH_FORCE] = "Sikh";
//$force_name[Ferozesha::BRITISH_FORCE] = "British";

class FerozeshaDayTwo extends \Wargame\Mollwitz\IndiaCore
{

    const SIKH_FORCE = 1;
    const BRITISH_FORCE = 2;
    public $specialHexesMap = ['SpecialHexA'=>2, 'SpecialHexB'=>2, 'SpecialHexC'=>2];



    static function enterMulti()
    {
        @include_once "enterMulti.php";
    }

    static function playMulti($name, $wargame, $arg = false)
    {
        $deployTwo = $playerOne = "Sikh";
        $deployOne = $playerTwo = "British";
        @include_once "playMulti.php";
    }


    static function getPlayerData($scenario){
        $forceName = ["Observer", "Sikh", "British"];
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


//
//        $scenario = $this->scenario;
//        $artRange = 3;
//
//        UnitFactory::$injector = $this->force;
//
//
//        /* Sikh */
//
//        if (!empty($scenario->commandControl)) {
//            for ($i = 0; $i < 3; $i++) {
//                UnitFactory::create("infantry-1", self::SIKH_FORCE, "deployBox", "SikhInfBadge.png", 1, 1, 5, true, STATUS_CAN_DEPLOY, "B", 1, 1, "Sikh", false, 'hq');
//            }
//        }
//        for ($i = 0; $i < 20; $i++) {
//            UnitFactory::create("infantry-1", self::SIKH_FORCE, "deployBox", "SikhInfBadge.png", 4, 4, 3, true, STATUS_CAN_DEPLOY, "B", 1, 1, "Sikh", false, 'infantry');
//        }
//        for ($i = 0; $i < 13; $i++) {
//            UnitFactory::create("infantry-1", self::SIKH_FORCE, "deployBox", "SikhCavBadge.png", 4, 4, 5, true, STATUS_CAN_DEPLOY, "B", 1, 1, "Sikh", false, 'cavalry');
//        }
//        for ($i = 0; $i < 4; $i++) {
//            UnitFactory::create("infantry-1", self::SIKH_FORCE, "deployBox", "SikhArtBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "B", 1, 2, "Sikh", false, 'artillery');
//        }
//        for ($i = 0; $i < 2; $i++) {
//            UnitFactory::create("infantry-1", self::SIKH_FORCE, "deployBox", "SikhArtBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "B", 1, 3, "Sikh", false, 'artillery');
//        }
//        for ($i = 0; $i < 1; $i++) {
//            UnitFactory::create("infantry-1", self::SIKH_FORCE, "deployBox", "SikhArtBadge.png", 4, 4, 2, true, STATUS_CAN_DEPLOY, "B", 1, 5, "Sikh", false, 'artillery');
//        }
//
//
//        /* British */
//
//        /* British */
//        if (!empty($scenario->commandControl)) {
//            for ($i = 0; $i < 4; $i++) {
//                UnitFactory::create("infantry-1", self::BRITISH_FORCE, "deployBox", "BritInfBadge.png", 1, 1, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "British", false, 'hq');
//            }
//        }
//
//        $royalInf = 6;
//        $nativeInf = 15;
//        if(!empty($scenario->weakBritish)){
//            $royalInf -= 2;
//            $nativeInf -= 4;
//        }
//
//        for ($i = 0; $i < $royalInf; $i++) {
//            UnitFactory::create("infantry-1", self::BRITISH_FORCE, "deployBox", "BritInfBadge.png", 5, 5, 4, true, STATUS_CAN_DEPLOY, "A", 1, 1, "British", false, 'infantry');
//        }
//        for ($i = 0; $i < $nativeInf; $i++) {
//            UnitFactory::create("infantry-1", self::BRITISH_FORCE, "deployBox", "NativeInfBadge.png", 4, 4, 4, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Native", false, 'infantry');
//        }
//        for ($i = 0; $i < 1; $i++) {
//            UnitFactory::create("infantry-1", self::BRITISH_FORCE, "deployBox", "BritCavBadge.png", 5, 5, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "British", false, 'cavalry');
//        }
//        for ($i = 0; $i < 6; $i++) {
//            UnitFactory::create("infantry-1", self::BRITISH_FORCE, "deployBox", "NativeCavBadge.png", 4, 4, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Native", false, 'cavalry');
//        }
//        for ($i = 0; $i < 4; $i++) {
//            UnitFactory::create("infantry-1", self::BRITISH_FORCE, "deployBox", "BritArtBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 4, "British", false, 'artillery');
//        }
//        for ($i = 0; $i < 2; $i++) {
//            UnitFactory::create("infantry-1", self::BRITISH_FORCE, "deployBox", "BritHorArtBadge.png", 2, 2, 5, true, STATUS_CAN_DEPLOY, "A", 1, 3, "British", false, 'horseartillery');
//        }

    }

    function __construct($data = null, $arg = false, $scenario = false, $game = false)
    {
        parent::__construct($data, $arg, $scenario, $game);
        if ($data) {
        } else {
            $this->victory = new \Wargame\Victory("\\Wargame\\Mollwitz\\Ferozesha\\ferozesha2VictoryCore");


            $this->mapData->blocksZoc->blocked = true;
            $this->mapData->blocksZoc->blocksnonroad = true;

            $this->moveRules->enterZoc = "stop";
            $this->moveRules->exitZoc = "stop";
            $this->moveRules->noZocZoc = true;
            $this->moveRules->zocBlocksRetreat = true;

            // game data
            if(!empty($scenario->dayTwo)){
                $this->gameRules->setMaxTurn(14);
            }else{
                $this->gameRules->setMaxTurn(12);
            }
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

            $i = 0;

            // end unit data -------------------------------------------

        }
    }
}