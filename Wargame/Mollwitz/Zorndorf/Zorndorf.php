<?php
namespace Wargame\Mollwitz\Zorndorf;
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



class Zorndorf extends \Wargame\Mollwitz\JagCore
{    
    const PRUSSIAN_FORCE = 1;
    const RUSSIAN_FORCE = 2;

    public $specialHexesMap = ['SpecialHexA'=>2, 'SpecialHexB'=>2, 'SpecialHexC'=>2];

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


    public $players;

    static function getPlayerData($scenario){
        return \Wargame\Battle::register(["Observer", "Prussian", "Russian"],
            ["Observer", "Russian", "Prussian" ]);
    }

    static function enterMulti()
    {
        @include_once "enterMulti.php";
    }

    function terrainGen($mapDoc, $terrainDoc){
        parent::terrainGen($mapDoc, $terrainDoc);
        $this->terrain->addTerrainFeature("river", "river", "v", 0, 2, 0, false);
    }

    function save()
    {
        $data = parent::save();

        return $data;
    }
    function init(){

        $artRange = 3;
        UnitFactory::$injector = $this->force;


        $coinFlip = $this->dieRolls->getEvent(2);

        $pruDeploy = $coinFlip == 1 ? "B": "C";

        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');

        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 5, 5, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 5, 5, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 5, 5, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 5, 5, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 5, 5, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 5, 5, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 5, 5, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruInfBadge.png", 5, 5, 3, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'infantry');

        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruArtBadge.png", 3, 3, 2, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, $artRange, "Prussian", false, 'artillery');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruArtBadge.png", 3, 3, 2, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, $artRange, "Prussian", false, 'artillery');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruArtBadge.png", 3, 3, 2, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, $artRange, "Prussian", false, 'artillery');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruArtBadge.png", 3, 3, 2, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, $artRange, "Prussian", false, 'artillery');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruArtBadge.png", 3, 3, 2, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, $artRange, "Prussian", false, 'artillery');

        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 3, 3, 6, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 3, 3, 6, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 3, 3, 6, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 3, 3, 6, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');

        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 3, 3, 5, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 3, 3, 5, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 3, 3, 5, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 3, 3, 5, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 3, 3, 5, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');

        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 5, 5, 5, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 5, 5, 5, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 5, 5, 5, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');
        UnitFactory::create("infantry-1", BLUE_FORCE, "deployBox", "PruCavBadge.png", 5, 5, 5, true, STATUS_CAN_DEPLOY, $pruDeploy, 1, 1, "Prussian", false, 'cavalry');






        if(!empty($this->scenario->bigRussian)){
            for($i = 0;$i < 15;$i++){
                UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            }
            for($i = 0;$i < 10;$i++){
                UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');

            }
            for($i = 0;$i < 7;$i++){
                UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 4, 4, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            }
            for($i = 0;$i < 5;$i++){
                UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusArtBadge.png", 4, 4, 2, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "Russian", false, 'artillery');
            }
            for($i = 0;$i < 6;$i++){
                UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 1, 1, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            }
            for($i = 0;$i < 5;$i++){
                UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 2, 2, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            }
            for($i = 0;$i < 4;$i++){
                UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 4, 4, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            }
        }else{
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 3, 3, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');

            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusInfBadge.png", 2, 2, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'infantry');

            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusArtBadge.png", 4, 4, 2, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "Russian", false, 'artillery');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusArtBadge.png", 4, 4, 2, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "Russian", false, 'artillery');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusArtBadge.png", 4, 4, 2, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "Russian", false, 'artillery');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusArtBadge.png", 4, 4, 2, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "Russian", false, 'artillery');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusArtBadge.png", 4, 4, 2, true, STATUS_CAN_DEPLOY, "A", 1, $artRange, "Russian", false, 'artillery');

            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 1, 1, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 1, 1, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 1, 1, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 1, 1, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 1, 1, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 1, 1, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');

            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 4, 4, 6, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');

            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 2, 2, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 2, 2, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 2, 2, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 2, 2, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 2, 2, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');

            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 4, 4, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 4, 4, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 4, 4, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
            UnitFactory::create("infantry-1", RED_FORCE, "deployBox", "RusCavBadge.png", 4, 4, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Russian", false, 'cavalry');
        }

    }
    function __construct($data = null, $arg = false, $scenario = false, $game = false)
    {
        parent::__construct($data, $arg, $scenario, $game);

        if ($data) {

        } else {
            $this->game = $game;
            $this->victory = new \Wargame\Victory("\\Wargame\\Mollwitz\\Zorndorf\\zorndorfVictoryCore");

            $this->mapData->blocksZoc->blocked = true;
            $this->mapData->blocksZoc->blocksnonroad = true;

            $this->moveRules->enterZoc = "stop";
            $this->moveRules->exitZoc = "stop";
            $this->moveRules->noZocZoc = true;
            $this->moveRules->stickyZoc = false;
            $this->players = array("", "", "");
            // game data


            if(!empty($scenario->deployForward)){
                $this->gameRules->setMaxTurn(14);
            }else{
                $this->gameRules->setMaxTurn(15);
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

        }
    }
}