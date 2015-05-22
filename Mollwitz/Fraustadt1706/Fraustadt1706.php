<?php
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

define("SWEDISH_FORCE", 1);
define("SAXON_POLISH_FORCE", 2);

global $force_name;
$force_name[SAXON_POLISH_FORCE] = "Saxon Russian";
$force_name[SWEDISH_FORCE] = "Swedish";

require_once "JagCore.php";

class Fraustadt1706 extends JagCore
{
    public $specialHexesMap = ['SpecialHexA'=>2, 'SpecialHexB'=>1, 'SpecialHexC'=>0];

    public
    static function getHeader($name, $playerData, $arg = false)
    {
        @include_once "globalHeader.php";
        @include_once "header.php";
        @include_once "Fraustadt1706Header.php";

    }


    static function enterMulti()
    {
        @include_once "enterMulti.php";
    }

    static function playMulti($name, $wargame, $arg = false)
    {
        $deployTwo = $playerOne = "Swedish";
        $deployOne = $playerTwo = "Saxon Russian";
        @include_once "playMulti.php";
    }

    static function getView($name, $mapUrl, $player = 0, $arg = false, $scenario = false, $game = false)
    {
        global $force_name;
        $youAre = $force_name[$player];
        $deployTwo = $playerOne = "Swedish";
        $deployOne = $playerTwo = "Saxon Russian";
        @include_once "view.php";
    }

    function terrainGen($mapDoc, $terrainDoc){
        $this->terrain->addTerrainFeature("frozenswamp", "frozenswamp", "s", 2, 1, 0, true, false);

        parent::terrainGen($mapDoc, $terrainDoc);
        $this->terrain->addTerrainFeature("redoubtfront", "redoubtfront", "d", 0, 0, 0, false);
        $this->terrain->addAltTraverseCost('redoubtfront','cavalry',1);
    }

    function save()
    {
        $data = new stdClass();
        $data->mapData = $this->mapData;
        $data->mapViewer = $this->mapViewer;
        $data->moveRules = $this->moveRules->save();
        $data->force = $this->force;
        $data->gameRules = $this->gameRules->save();
        $data->combatRules = $this->combatRules->save();
        $data->players = $this->players;
        $data->display = $this->display;
        $data->victory = $this->victory->save();
        $data->terrainName = $this->terrainName;
        $data->arg = $this->arg;
        $data->scenario = $this->scenario;
        $data->game = $this->game;
        $data->roadHex = $this->roadHex;
        $data->specialHexA = $this->specialHexA;
        $data->specialHexB = $this->specialHexB;

        return $data;
    }

    public function init()
    {

        $scenario = $this->scenario;
        $unitSets = $scenario->units;


        foreach($unitSets as $unitSet) {
            for ($i = 0; $i < $unitSet->num; $i++) {
                $this->force->addUnit("infantry-1", $unitSet->forceId, "deployBox", "", $unitSet->combat, $unitSet->combat, $unitSet->movement, true, STATUS_CAN_DEPLOY, $unitSet->reinforce, 1, $unitSet->range, $unitSet->nationality, false, $unitSet->class);
            }
        }
    }

    function __construct($data = null, $arg = false, $scenario = false, $game = false)
    {

        parent::__construct($data, $arg, $scenario, $game);
        if ($data) {
            $this->roadHex = $data->roadHex;
            $this->specialHexA = $data->specialHexA;
            $this->specialHexB = $data->specialHexB;
        } else {
            $this->victory = new Victory("Mollwitz/Fraustadt1706/fraustadt1706VictoryCore.php");

            $this->mapData->blocksZoc->blocked = true;
            $this->mapData->blocksZoc->blocksnonroad = true;

            $this->moveRules->enterZoc = "stop";
            $this->moveRules->exitZoc = "stop";
            $this->moveRules->noZocZoc = true;
            $this->moveRules->zocBlocksRetreat = true;

            // game data

            $this->gameRules->setMaxTurn(6);
            $this->gameRules->setInitialPhaseMode(RED_DEPLOY_PHASE, DEPLOY_MODE);
            $this->gameRules->attackingForceId = RED_FORCE; /* object oriented! */
            $this->gameRules->defendingForceId = BLUE_FORCE; /* object oriented! */
            $this->force->setAttackingForceId($this->gameRules->attackingForceId); /* so object oriented */


            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_DEPLOY_PHASE, DEPLOY_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);

//            $this->gameRules->addPhaseChange(BLUE_REPLACEMENT_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_MOVE_PHASE, BLUE_COMBAT_PHASE, COMBAT_SETUP_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_COMBAT_PHASE, RED_MOVE_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);

//            $this->gameRules->addPhaseChange(RED_REPLACEMENT_PHASE, RED_MOVE_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_MOVE_PHASE, RED_COMBAT_PHASE, COMBAT_SETUP_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_COMBAT_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, true);

        }
    }
}