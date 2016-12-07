<?php
namespace Wargame\Mollwitz\Minden;
use \Wargame\Mollwitz\UnitFactory;
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
define("FRENCH_FORCE", 1);
define("ANGLO_FORCE", 2);

class Minden extends \Wargame\Mollwitz\JagCore
{
    public $specialHexesMap = ['SpecialHexA'=>2, 'SpecialHexB'=>1, 'SpecialHexC'=>1];
    /* @var Mapdata */
    public $mapData;
    public $mapViewer;
    public $force;
    /* @var Terrain */
    public $terrain;
    public $moveRules;
    public $combatRules;
    public $gameRules;
    public $victory;
    public $angloSpecialHexes;
    public $frenchSpecialHexes;


    public $players;

    static function enterMulti()
    {
        @include_once "enterMulti.php";
    }

    static function getPlayerData($scenario){
        return \Wargame\Battle::register(["Observer", "French", "Anglo Allied"],
            ["Observer", "Anglo Allied", "French" ]);
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
        $data->angloSpecialHexes = $this->angloSpecialHexes;
        $data->frenchSpecialHexes = $this->frenchSpecialHexes;

        return $data;
    }

    public function terrainInit($terrainDoc){
        parent::terrainInit($terrainDoc);
        $specialHexes = $this->mapData->specialHexes;
        foreach($specialHexes as $hexId => $forceId){
            if($forceId == ANGLO_FORCE){
                $this->angloSpecialHexes[] = $hexId;
            }else{
                $this->frenchSpecialHexes[] = $hexId;
            }
        }
    }
    public function terrainGen($mapDoc, $terrainDoc){
        parent::terrainGen($mapDoc, $terrainDoc);

        for ($col = 2200; $col <= 2500; $col += 100) {
            for ($row = 1; $row <= 18; $row++) {
                $this->terrain->addReinforceZone($col + $row, 'B');
            }
        }

        for ($col = 100; $col <= 700; $col += 100) {
            for ($row = 1; $row <= 18; $row++) {
                $this->terrain->addReinforceZone($col + $row, 'A');
            }
        }


    }

    public function init()
    {
        UnitFactory::$injector = $this->force;

        $artRange = 3;

        for ($i = 0; $i < 19; $i++) {
            UnitFactory::create("infantry-1", FRENCH_FORCE, "deployBox", "FrenchInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "B", 1, 1, "French", false, 'infantry');
        }
        for ($i = 0; $i < 3; $i++) {
            UnitFactory::create("infantry-1", FRENCH_FORCE, "deployBox", "FrenchInfBadge.png", 4, 4, 3, true, STATUS_CAN_DEPLOY, "B", 1, 1, "French", false, 'infantry');
        }
        for ($i = 0; $i < 12; $i++) {
            UnitFactory::create("infantry-1", FRENCH_FORCE, "deployBox", "FrenchCavBadge.png", 3, 3, 5, true, STATUS_CAN_DEPLOY, "B", 1, 1, "French", false, 'cavalry');
        }
        for ($i = 0; $i < 3; $i++) {
            UnitFactory::create("infantry-1", FRENCH_FORCE, "deployBox", "FrenchCavBadge.png", 5, 5, 5, true, STATUS_CAN_DEPLOY, "B", 1, 1, "French", false, 'cavalry');
        }
        for ($i = 0; $i < 5; $i++) {
            UnitFactory::create("infantry-1", FRENCH_FORCE, "deployBox", "FrenchArtBadge.png", 2, 2, 2, true, STATUS_CAN_DEPLOY, "B", 1, $artRange, "French", false, 'artillery');
        }
        UnitFactory::create("infantry-1", FRENCH_FORCE, "deployBox", "FrenchArtBadge.png", 5, 5, 2, true, STATUS_CAN_DEPLOY, "B", 1, $artRange, "French", false, 'artillery');


        for ($i = 0; $i < 20; $i++) {
            UnitFactory::create("infantry-1", ANGLO_FORCE, "deployBox", "AngInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "AngloAllied", false, 'infantry');
        }
        for ($i = 0; $i < 5; $i++) {
            UnitFactory::create("infantry-1", ANGLO_FORCE, "deployBox", "AngInfBadge.png", 4, 4, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "AngloAllied", false, 'infantry');
        }
        for ($i = 0; $i < 6; $i++) {
            UnitFactory::create("infantry-1", ANGLO_FORCE, "deployBox", "AngCavBadge.png", 3, 3, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "AngloAllied", false, 'cavalry');
        }
        for ($i = 0; $i < 2; $i++) {
            UnitFactory::create("infantry-1", ANGLO_FORCE, "deployBox", "AngCavBadge.png", 5, 5, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "AngloAllied", false, 'cavalry');
        }
        UnitFactory::create("infantry-1", ANGLO_FORCE, "deployBox", "AngCavBadge.png", 4, 4, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "AngloAllied", false, 'cavalry');
        for ($i = 0; $i < 3; $i++) {
            UnitFactory::create("infantry-1", ANGLO_FORCE, "deployBox", "AngArtBadge.png", 2, 2, 2, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "AngloAllied", false, 'artillery');
        }

        for ($i = 0; $i < 2; $i++) {
            UnitFactory::create("infantry-1", ANGLO_FORCE, "deployBox", "AngArtBadge.png", 4, 4, 2, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "AngloAllied", false, 'artillery');
        }
    }

    function __construct($data = null, $arg = false, $scenario = false, $game = false)
    {
        parent::__construct($data, $arg, $scenario, $game);
        if ($data) {
            $this->arg = $data->arg;
            $this->angloSpecialHexes = $data->angloSpecialHexes;
            $this->frenchSpecialHexes = $data->frenchSpecialHexes;
        } else {
            $this->victory = new \Wargame\Victory("\\Wargame\\Mollwitz\\Minden\\mindenVictoryCore");

            $this->mapData->blocksZoc->blocked = true;
            $this->mapData->blocksZoc->blocksnonroad = true;


            $this->moveRules->enterZoc = "stop";
            $this->moveRules->exitZoc = "stop";
            $this->moveRules->noZocZoc = true;

            // game data
            $this->gameRules->setMaxTurn(14);
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

        }
    }
}