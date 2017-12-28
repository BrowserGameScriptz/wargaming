<?php
namespace Wargame\TMCW\MartianCivilWar;
use Wargame\TMCW\UnitFactory;
use Wargame\ModernLandBattle;
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


global $force_name;
$force_name = array();
$force_name[0] = "Neutral Observer";
$force_name[1] = "Rebel";
$force_name[2] = "Loyalist";

global $phase_name, $mode_name, $event_name, $status_name, $results_name, $combatRatio_name;

class MartianCivilWar extends ModernLandBattle
{
    
    const REBEL_FORCE = 1;
    const LOYALIST_FORCE = 2;
    static function getPlayerData($scenario){
        $forceName = ["Neutral Observer", "Rebel", "Loyalist"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function terrainInit($terrainDoc){
        parent::terrainInit($terrainDoc);
    }

    function terrainGen($mapDoc, $terrainDoc){
        $this->terrain->addTerrainFeature("newrichmond", "New Richmond", "m", 0, 0, 1, false);
        $this->terrain->addTerrainFeature("eastedge", "East Edge", "m", 0, 0, 0, false);
        $this->terrain->addTerrainFeature("westedge", "West Edge", "m", 0, 0, 0, false);
        parent::terrainGen($mapDoc, $terrainDoc);
        $this->terrain->addNatAltTraverseCost("mine", "rebel", 'mech', 2);
        $this->terrain->addNatAltTraverseCost("mine", "rebel", 'inf', 1);
        $this->terrain->addNatAltTraverseCost("mine", "rebel", 'mountain', 1);
    }

    function save()
    {
        $data = parent::save();
        return $data;
    }

    public static function buildUnit($data = false){
        return UnitFactory::build($data);
    }

    public function oldInit()
    {

        UnitFactory::$injector = $this->force;

        $scenario = $this->scenario;

        UnitFactory::create("xx", RED_FORCE, 407, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 516, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 1515, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 1612, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 1316, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 2207, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 2210, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 208, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 508, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 512, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 1909, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 914, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');

        for ($i = 7; $i <= 10; $i += 2) {
            UnitFactory::create("xx", RED_FORCE, 1000 + $i, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist");

        }

        $loyalMechMove = 6;
        if (isset($scenario->loyalMechMove)) {
            $loyalMechMove = $scenario->loyalMechMove;
        }
        UnitFactory::create("xx", RED_FORCE, 2415, "multiRecon.png", 5, 2, 9, false, STATUS_READY, "B", 1, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, 2416, "multiRecon.png", 5, 2, 9, false, STATUS_READY, "B", 1, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, 2417, "multiRecon.png", 5, 2, 9, false, STATUS_READY, "B", 1, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, 2515, "multiMech.png", 9, 4, $loyalMechMove, true, STATUS_READY, "B", 1, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, 2516, "multiArmor.png", 7, 3, $loyalMechMove, true, STATUS_READY, "B", 1, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, 2517, "multiArmor.png", 7, 3, $loyalMechMove, true, STATUS_READY, "B", 1, 1, "loyalist", true, "mech");

        $bigLoyalist = false;
        if ($scenario && !empty($scenario->bigLoyal)) {
            $bigLoyalist = true;
        }
        if (isset($scenario->loyalExtraInf)) {
            UnitFactory::create("xx", RED_FORCE, "gameTurn2", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 2, 1, "loyalist", true, 'inf');
            UnitFactory::create("xx", RED_FORCE, "gameTurn2", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 2, 1, "loyalist", true, 'inf');
            UnitFactory::create("xx", RED_FORCE, "gameTurn2", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 2, 1, "loyalist", true, 'inf');
            UnitFactory::create("xx", RED_FORCE, "gameTurn2", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 2, 1, "loyalist", true, 'inf');
        }
        UnitFactory::create("xx", RED_FORCE, "gameTurn2", "multiArmor.png", 7, 3, $loyalMechMove, !$bigLoyalist, STATUS_CAN_REINFORCE, "B", 2, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, "gameTurn2", "multiArmor.png", 7, 3, $loyalMechMove, !$bigLoyalist, STATUS_CAN_REINFORCE, "B", 2, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, "gameTurn3", "multiMech.png", 9, 4, $loyalMechMove, !$bigLoyalist, STATUS_CAN_REINFORCE, "B", 3, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, "gameTurn3", "multiMech.png", 9, 4, $loyalMechMove, !$bigLoyalist, STATUS_CAN_REINFORCE, "B", 3, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, "gameTurn3", "multiMech.png", 9, 4, $loyalMechMove, !$bigLoyalist, STATUS_CAN_REINFORCE, "B", 3, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, "gameTurn4", "multiMech.png", 9, 4, $loyalMechMove, !$bigLoyalist, STATUS_CAN_REINFORCE, "B", 4, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, "gameTurn4", "multiMech.png", 9, 4, $loyalMechMove, !$bigLoyalist, STATUS_CAN_REINFORCE, "B", 4, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, "gameTurn5", "multiArmor.png", 7, 3, $loyalMechMove, !$bigLoyalist, STATUS_CAN_REINFORCE, "B", 5, 1, "loyalist", true, "mech");
        UnitFactory::create("xx", RED_FORCE, "gameTurn5", "multiArmor.png", 7, 3, $loyalMechMove, !$bigLoyalist, STATUS_CAN_REINFORCE, "B", 5, 1, "loyalist", true, "mech");

        if ($bigLoyalist) {
            UnitFactory::create("xx", RED_FORCE, "gameTurn2", "multiRecon.png", 5, 2, 9, $bigLoyalist, STATUS_CAN_REINFORCE, "B", 2, 1, "loyalist", true, "mech");
            UnitFactory::create("xx", RED_FORCE, "gameTurn4", "multiRecon.png", 5, 2, 9, $bigLoyalist, STATUS_CAN_REINFORCE, "B", 4, 1, "loyalist", true, "mech");
            UnitFactory::create("xx", RED_FORCE, "gameTurn4", "multiRecon.png", 5, 2, 9, $bigLoyalist, STATUS_CAN_REINFORCE, "B", 4, 1, "loyalist", true, "mech");
            UnitFactory::create("xx", RED_FORCE, "gameTurn3", "multiRecon.png", 5, 2, 9, $bigLoyalist, STATUS_CAN_REINFORCE, "B", 3, 1, "loyalist", true, "mech");
            UnitFactory::create("xx", RED_FORCE, "gameTurn5", "multiRecon.png", 5, 2, 9, $bigLoyalist, STATUS_CAN_REINFORCE, "B", 5, 1, "loyalist", true, "mech");
        }


        $i = 1;
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");

        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");


    }
    public function scenInit(){


        $scenario = $this->scenario;
        $unitSets = $scenario->units;

        foreach($unitSets as $unitSet) {
//            dd($unitSet);
            if($unitSet->forceId !== MartianCivilWar::LOYALIST_FORCE){
                continue;
            }
            for ($i = 0; $i < $unitSet->num; $i++) {
                    UnitFactory::create("xx", $unitSet->forceId, "deployBox", $unitSet->image, $unitSet->combat, $unitSet->combat/2, $unitSet->movement, false, STATUS_CAN_REINFORCE,  $unitSet->reinforce, 1, $unitSet->range, $unitSet->nationality, true, $unitSet->class);

            }
        }
        foreach($unitSets as $unitSet) {
//            dd($unitSet);
            if($unitSet->forceId !== MartianCivilWar::REBEL_FORCE){
                continue;
            }
            for ($i = 0; $i < $unitSet->num; $i++) {
                UnitFactory::create("xx", $unitSet->forceId, "deployBox", $unitSet->image, $unitSet->combat, $unitSet->combat/2, $unitSet->movement, false, STATUS_CAN_REINFORCE,  $unitSet->reinforce, 1, $unitSet->range, $unitSet->nationality, true, $unitSet->class);
            }
        }
    }

    public function init()
    {

        UnitFactory::$injector = $this->force;

        $scenario = $this->scenario;
        if(isset($scenario->units)){
            return $this->scenInit();
        }
        if(empty($scenario->hardCuneiform)){
            $this->oldInit();
            return;
        }

        UnitFactory::create("xx", self::LOYALIST_FORCE, 407, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 516, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 1515, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 1612, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 1316, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 1805, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 1706, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 208, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 608, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 512, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 1909, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 914, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 1505, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 2411, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 1704, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", self::LOYALIST_FORCE, 1803, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'inf');
        UnitFactory::create("xx", RED_FORCE, 909, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist");
        UnitFactory::create("xx", RED_FORCE, 1007, "multiInf.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist");

        UnitFactory::create("xxx", self::LOYALIST_FORCE, 2415, "multiHeavy.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", self::LOYALIST_FORCE, 2416, "multiHeavy.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", self::LOYALIST_FORCE, 2417, "multiHeavy.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", self::LOYALIST_FORCE, 2515, "multiHeavy.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", self::LOYALIST_FORCE, 2516, "multiHeavy.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", self::LOYALIST_FORCE, 2517, "multiHeavy.png", 2, 1, 4, true, STATUS_READY, "B", 1, 1, "loyalist", true, 'heavy');


        UnitFactory::create("xxx", RED_FORCE, "gameTurn2", "multiHeavy.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 2, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn2", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 2, 1, "loyalist", true, 'inf');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn3", "multiHeavy.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 3, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn3", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 3, 1, "loyalist", true, 'inf');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn4", "multiHeavy.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 4, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn4", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 4, 1, "loyalist", true, 'inf');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn5", "multiHeavy.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 5, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn5", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 5, 1, "loyalist", true, 'inf');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn6", "multiHeavy.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 6, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn6", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 6, 1, "loyalist", true, 'inf');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn7", "multiHeavy.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 7, 1, "loyalist", true, 'heavy');
        UnitFactory::create("xxx", RED_FORCE, "gameTurn7", "multiInf.png", 2, 1, 4, false, STATUS_CAN_REINFORCE, "B", 7, 1, "loyalist", true, 'inf');



        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mech");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMountain.png", 3, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mountain");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiMountain.png", 3, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "mountain");

        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");
        UnitFactory::create("xx", BLUE_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_REINFORCE, "A", 1, 1, "rebel", true, "inf");


    }

    function __construct($data = null, $arg = false, $scenario = false)
    {
//        die("const");
        parent::__construct($data, $arg, $scenario);

        $crt = new \Wargame\TMCW\MartianCivilWar\CombatResultsTable();
        $this->combatRules->injectCrt($crt);

        if ($data) {

        } else {
            $this->terrainName = "terrain-MartianCivilWar";
            $this->victory = new \Wargame\Victory("\\Wargame\\TMCW\\MartianCivilWar\\victoryCore");
            if (!empty($scenario->supplyLen)) {
                $this->victory->setSupplyLen($scenario->supplyLen);
            }
            if (!empty($scenario->supply)) {
                $this->moveRules->enterZoc = 2;
                $this->moveRules->exitZoc = 1;
                $this->moveRules->noZocZocOneHex = true;
            } else {
                $this->moveRules->enterZoc = "stop";
                $this->moveRules->exitZoc = 0;
                $this->moveRules->noZocZocOneHex = false;
            }
            $this->combatRules->crt->aggressorId = self::REBEL_FORCE;

            // game data
            $this->gameRules->setMaxTurn(7);
            $this->gameRules->setInitialPhaseMode(BLUE_MOVE_PHASE, MOVING_MODE);
            $this->gameRules->addPhaseChange(BLUE_REPLACEMENT_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_MOVE_PHASE, BLUE_COMBAT_PHASE, COMBAT_SETUP_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_COMBAT_PHASE, BLUE_MECH_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_MECH_PHASE, RED_REPLACEMENT_PHASE, REPLACING_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_REPLACEMENT_PHASE, RED_MOVE_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_MOVE_PHASE, RED_COMBAT_PHASE, COMBAT_SETUP_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_COMBAT_PHASE, RED_MECH_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_MECH_PHASE, BLUE_REPLACEMENT_PHASE, REPLACING_MODE, BLUE_FORCE, RED_FORCE, true);
        }
    }
}