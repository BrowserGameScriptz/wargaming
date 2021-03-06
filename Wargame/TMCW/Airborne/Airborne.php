<?php
namespace Wargame\TMCW\Airborne;
use Wargame\ModernGtMoveRules;
use Wargame\TMCW\Airborne\UnitFactory;
use Wargame\SupplyCombatRules;
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




class Airborne extends \Wargame\ModernLandBattle
{

    
    const REBEL_FORCE = 1;
    const LOYALIST_FORCE = 2;
    public $specialHexesMap = ['SpecialHexA'=>2, 'SpecialHexB'=>2, 'SpecialHexC'=>1];

    static function getPlayerData($scenario){
        $forceName = ["Neutral Observer", "Rebel", "Loyalist"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function terrainGen($mapDoc, $terrainDoc)
    {
        $this->terrain->addTerrainFeature("roughone", "roughone", "r", 3, 0, 1, true);
        parent::terrainGen($mapDoc, $terrainDoc);
        $this->terrain->addTerrainFeature("town", "town", "t", 0, 0, 1, false);
    }

    function save()
    {
        $data = parent::save();
        $data->specialHexA = $this->specialHexA;
        return $data;
    }

    public static function buildUnit($data = false){
        return UnitFactory::build($data);
    }

    public function init()
    {

        UnitFactory::$injector = $this->force;

        $scenario = $this->scenario;
        $baseValue = 6  ;
        $reducedBaseValue = 2;
        $i = 0;

        /* Loyalists units */
        UnitFactory::create("lll", self::LOYALIST_FORCE, "106", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "205", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "305", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "404", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "504", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "603", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "703", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "704", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);

        UnitFactory::create("lll", self::LOYALIST_FORCE, "802", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "902", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
//        UnitFactory::create("lll", self::LOYALIST_FORCE, "deployBox", "multiGor.png", $baseValue, 4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);

        UnitFactory::create("x", self::LOYALIST_FORCE, "1109", "multiHeavy.png", 10,  5,  STATUS_CAN_DEPLOY, "G", 1,  "loyalGuards",  'heavy', $i++);

        UnitFactory::create("lll", self::LOYALIST_FORCE, "1001", "multiGor.png", $baseValue,  4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "604", "multiGor.png", $baseValue,  4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "206", "multiGor.png", $baseValue,  4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "803", "multiGor.png", $baseValue,  4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "505", "multiGor.png", $baseValue,  4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "306", "multiGor.png", $baseValue,  4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "1002", "multiGor.png", $baseValue,  4,  STATUS_CAN_DEPLOY, "F", 1,  "loyalist",  'inf', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "1110", "multiInf.png", 7,  5,  STATUS_CAN_DEPLOY, "G", 1,  "loyalGuards",  'inf', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "1009", "multiInf.png", 7,  5,  STATUS_CAN_DEPLOY, "G", 1,  "loyalGuards",  'inf', $i++);



        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn2E", "multiArmor.png", 13,  8,  STATUS_CAN_REINFORCE, "E", 2,  "loyalGuards",  'mech', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn2E", "multiArmor.png", 13,  8,  STATUS_CAN_REINFORCE, "E", 2,  "loyalGuards",  'mech', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn2E", "multiMech.png", 12,  8,  STATUS_CAN_REINFORCE, "E", 2,  "loyalGuards",  'mech', $i++);




        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn2C", "multiInf.png", 7,  5,  STATUS_CAN_REINFORCE, "C", 2,  "loyalGuards",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "gameTurn2D", "multiInf.png", 6,  5,  STATUS_CAN_REINFORCE, "D", 2,  "loyalGuards",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "gameTurn2D", "multiInf.png", 6,  5,  STATUS_CAN_REINFORCE, "D", 2,  "loyalGuards",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "gameTurn2E", "multiInf.png", 6,  5,  STATUS_CAN_REINFORCE, "E", 2,  "loyalGuards",  'inf', $i++);
        UnitFactory::create("lll", self::LOYALIST_FORCE, "gameTurn2E", "multiInf.png", 6,  5,  STATUS_CAN_REINFORCE, "E", 2,  "loyalGuards",  'inf', $i++);


        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn3C", "multiArmor.png", 13,  8,  STATUS_CAN_DEPLOY, "C", 3,  "loyalGuards",  'mech', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn3D", "multiShock.png", 9,  5,  STATUS_CAN_DEPLOY, "D", 3, "loyalGuards",  'shock', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn3C", "multiPara.png", 7,  5,  STATUS_CAN_REINFORCE, "C", 3,  "loyalGuards",  'inf', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn3D", "multiPara.png", 7,  5,  STATUS_CAN_REINFORCE, "D", 3,  "loyalGuards",  'inf', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn3D", "multiInf.png", 6,  5,  STATUS_CAN_REINFORCE, "D", 3,  "loyalGuards",  'inf', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn4E", "multiInf.png", 6,  5,  STATUS_CAN_REINFORCE, "E", 4,  "loyalGuards",  'inf', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn4C", "multiShock.png", 9,  5,  STATUS_CAN_REINFORCE, "C", 4,  "loyalGuards",  'shock', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn4C", "multiShock.png", 9,  5,  STATUS_CAN_REINFORCE, "C", 4,  "loyalGuards",  'shock', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn4E", "multiShock.png", 9,  5,  STATUS_CAN_REINFORCE, "E", 4,  "loyalGuards",  'shock', $i++);

        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn5C", "multiArmor.png", 13, 8,  STATUS_CAN_REINFORCE, "C", 5,  "loyalGuards",  'mech', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn5C", "multiArmor.png", 13,  8,  STATUS_CAN_REINFORCE, "C", 5,  "loyalGuards",  'mech', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn5C", "multiMech.png", 12,  8,  STATUS_CAN_REINFORCE, "C", 5,  "loyalGuards",  'mech', $i++);
        UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn5C", "multiHeavy.png", 10,  5,  STATUS_CAN_REINFORCE, "C", 5, "loyalGuards",  'heavy', $i++);

        if(empty($scenario->weakerLoyalist)) {
            UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn6C", "multiArmor.png", 13,  8,  STATUS_CAN_REINFORCE, "C", 6,  "loyalGuards",  'mech', $i++);
            UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn6C", "multiArmor.png", 13,  8,  STATUS_CAN_REINFORCE, "C", 6,  "loyalGuards",  'mech', $i++);
            UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn6C", "multiMech.png", 12,  8,  STATUS_CAN_REINFORCE, "C", 6,  "loyalGuards",  'mech', $i++);
            UnitFactory::create("x", self::LOYALIST_FORCE, "gameTurn6C", "multiHeavy.png", 10,  5,  STATUS_CAN_REINFORCE, "C", 6,  "loyalGuards",  'heavy', $i++);
        }

        /*
         *
         *      <svg width="18" height="9" viewBox="0 0 20 10">
                        <line x1="1" x2="1" y1="0" y2="10" stroke-width="2"></line>
                        <line x1="0" x2="20" y1="9" y2="9" stroke-width="2"></line>
                        <line x1="19" x2="19" y1="0" y2="10" stroke-width="2"></line>
                        <line x1="0" x2="20" y1="1" y2="1" stroke-width="2"></line>
                        <line x1="1" x2="19" y1="1" y2="9" stroke-width="2"></line>
                        <line x1="1" x2="19" y1="9" y2="1" stroke-width="2"></line>
                    </svg>
         */
        /* Rebel Units */

        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiPara.png", 9,  5,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiPara.png", 9,  5,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiPara.png", 9,  5,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiPara.png", 9,  5,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiPara.png", 9,  5,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiPara.png", 9,  5,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "para", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiPara.png", 9,  5,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiPara.png", 9,  5,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiPara.png", 9,  5,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  1,  STATUS_CAN_DEPLOY, "A", 1,  "rebel",  "supply", $i++);


        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiArmor.png", 12,  8,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiArmor.png", 12,  8,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiArmor.png", 12,  8,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiArmor.png", 12,  8,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "mech", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiMech.png", 10,  8,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiMech.png", 10,  8,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiMech.png", 10,  8,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiMech.png", 10,  8,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiMech.png", 10,  8,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "mech", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  1,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  1,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  1,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  1,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  1,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  1,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  1,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "truck", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "truck", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "truck", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "deployBox", "multiGor.png", 1,  6,  STATUS_CAN_DEPLOY, "B", 1,  "rebel",  "truck", $i++);


        UnitFactory::create("lll", BLUE_FORCE, "gameTurn2B", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "B", 2,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn2B", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "B", 2,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn2A", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "A", 2,  "rebel",  "supply", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3B", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "B", 3,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3B", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "B", 3,  "rebel",  "supply", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "gameTurn4B", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "B", 4,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn4B", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "B", 4,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn4A", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "A", 4,  "rebel",  "supply", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "gameTurn5B", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "B", 5,  "rebel",  "supply", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "gameTurn1B", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "B", 1,  "rebel",  "supply", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn1A", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "A", 1,  "rebel",  "supply", $i++);


        UnitFactory::create("lll", BLUE_FORCE, "gameTurn7C", "multiGor.png", 1,  1,  STATUS_CAN_REINFORCE, "B", 7,  "rebel",  "supply", $i++);


        UnitFactory::create("lll", BLUE_FORCE, "gameTurn2", "multiGlider.png", 10,  5,  STATUS_CAN_REINFORCE, "A", 2,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn2", "multiGlider.png", 10,  5,  STATUS_CAN_REINFORCE, "A", 2,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn2", "multiPara.png", 9,  5,  STATUS_CAN_REINFORCE, "A", 2,  "rebel",  "para", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3", "multiGlider.png", 10,  5,  STATUS_CAN_REINFORCE, "A", 3,  "rebel",  "para", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3", "multiPara.png", 9,  5,  STATUS_CAN_REINFORCE, "A", 3,  "rebel",  "para", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "gameTurn4", "multiPara.png", 9,  5,  STATUS_CAN_REINFORCE, "A", 4,  "rebel",  "para", $i++);



        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3", "multiArmor.png", 12,  8,  STATUS_CAN_DEPLOY, "B", 3,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3", "multiArmor.png", 12,  8,  STATUS_CAN_DEPLOY, "B", 3,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3", "multiMech.png", 10,  8,  STATUS_CAN_DEPLOY, "B", 3,  "rebel",  "mech", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3", "multiArmor.png", 12,  8,  STATUS_CAN_DEPLOY, "B", 3,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3", "multiMech.png", 10,  8,  STATUS_CAN_DEPLOY, "B", 3,  "rebel",  "mech", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn3", "multiMech.png", 10,  8,  STATUS_CAN_DEPLOY, "B", 3,  "rebel",  "mech", $i++);

        UnitFactory::create("lll", BLUE_FORCE, "gameTurn5", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 5,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn5", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 5,  "rebel",  "inf", $i++);
        UnitFactory::create("lll", BLUE_FORCE, "gameTurn5", "multiInf.png", 8,  6,  STATUS_CAN_DEPLOY, "B", 5,  "rebel",  "inf", $i++);

        $symbol = new \stdClass();
        $symbol->type = 'RebelSupply';
        $symbol->image = '';
        $symbol->class = 'supply-hex  rebel fa fa-adjust';
        $symbols = new \stdClass();
        foreach([101] as $id){
            $symbols->$id = $symbol;
        }
        $this->mapData->setMapSymbols($symbols, "rebelsupply");

        $symbols = new \stdClass();

        $symbol = new \stdClass();
        $symbol->type = 'LoyalistSupply';
        $symbol->image = '';
        $symbol->class = 'supply-hex loyalist fa fa-adjust';

        $goal = array();
        for($row = 1;$row <= 22;$row++){
            $id = 2200+$row;
            $symbols->$id = $symbol;
        }
        /* Don't put lower right corner in twice! */
        for($col = 1;$col <= 21;$col++){
            if($col === 13){
                continue;
            }
            $id = ($col*100)+22;
            $symbols->$id = $symbol;
        }
        $this->mapData->setMapSymbols($symbols, "loyalistsupply");

    }

    function __construct($data = null, $arg = false, $scenario = false)
    {

        parent::__construct($data, $arg, $scenario);

        $crt = new \Wargame\TMCW\KievCorps\CombatResultsTable(self::REBEL_FORCE);

        if ($data) {
            $this->specialHexA = $data->specialHexA;

            $this->moveRules = new ModernGtMoveRules($this->force, $this->terrain, $data->moveRules);

            $this->combatRules = new SupplyCombatRules($this->force, $this->terrain, $data->combatRules);
            $this->gameRules->inject($this->moveRules, $this->combatRules, $this->force);

        } else {
            $this->victory = new \Wargame\Victory("\\Wargame\\TMCW\\Airborne\\airborneVictoryCore");
            if (!empty($scenario->supplyLen)) {
                $this->victory->setSupplyLen($scenario->supplyLen);
            }

            $this->moveRules = new ModernGtMoveRules($this->force, $this->terrain);

            $this->combatRules = new SupplyCombatRules($this->force, $this->terrain);
            $this->gameRules->inject($this->moveRules, $this->combatRules, $this->force);


            $this->moveRules->enterZoc = 2;
            $this->moveRules->exitZoc = 1;
            $this->moveRules->noZocZocOneHex = true;
            $this->moveRules->blockedRetreatDamages = true;

//            $this->moveRules->stacking = 3;
            $this->moveRules->friendlyAllowsRetreat = true;
            // game data
            $this->gameRules->setMaxTurn(7);

//            $this->gameRules->setInitialPhaseMode(RED_DEPLOY_PHASE, DEPLOY_MODE);

//            $this->gameRules->attackingForceId = RED_FORCE; /* object oriented! */
//            $this->gameRules->defendingForceId = BLUE_FORCE; /* object oriented! */
//            $this->force->setAttackingForceId($this->gameRules->attackingForceId); /* so object oriented */
//
//            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_DEPLOY_PHASE, DEPLOY_MODE, BLUE_FORCE, RED_FORCE, false);
//            $this->gameRules->addPhaseChange(BLUE_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);

            $this->gameRules->setInitialPhaseMode(BLUE_DEPLOY_PHASE, DEPLOY_MODE);

            $this->gameRules->attackingForceId = BLUE_FORCE; /* object oriented! */
            $this->gameRules->defendingForceId = RED_FORCE; /* object oriented! */
            $this->force->setAttackingForceId($this->gameRules->attackingForceId); /* so object oriented */

            $this->gameRules->addPhaseChange(BLUE_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);

            $this->gameRules->addPhaseChange(BLUE_MOVE_PHASE, BLUE_COMBAT_PHASE, COMBAT_SETUP_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_COMBAT_PHASE, BLUE_MECH_PHASE, MOVING_MODE,  BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_MECH_PHASE, RED_MOVE_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);

            $this->gameRules->addPhaseChange(RED_MOVE_PHASE, RED_COMBAT_PHASE, COMBAT_SETUP_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_COMBAT_PHASE, RED_MECH_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_MECH_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, true);
        }
        $this->combatRules->injectCrt($crt);
        $this->moveRules->stacking = function($mapHex, $forceId, $unit){
            if($unit->class == "truck"){
                return false;
            }

            $nonTruckCnt = 0;
            foreach($mapHex->forces[$forceId] as $mKey => $mVal){
                if($this->force->units[$mKey]->class !== "truck"){
                    $nonTruckCnt++;
                }
            }
            return $nonTruckCnt >= 2;
        };

    }
}