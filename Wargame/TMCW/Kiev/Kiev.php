<?php
namespace Wargame\TMCW\Kiev;
use \Wargame\TMCW\UnitFactory;
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

define("GERMAN_FORCE", 1);
define("SOVIET_FORCE", 2);

class Kiev extends \ModernLandBattle
{

    public $specialHexesMap = ['SpecialHexA'=>1, 'SpecialHexB'=>2, 'SpecialHexC'=>2];

    static function getPlayerData($scenario){
        $forceName = ["Neutral Observer", "German", "Soviet"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function terrainGen($mapDoc, $terrainDoc)
    {
        $this->terrain->addTerrainFeature("swamp", "swamp", "s", 2, 0, 1, true);
        $this->terrain->addAltEntranceCost('swamp', 'mech', 3);
        parent::terrainGen($mapDoc, $terrainDoc);
        $this->terrain->addTerrainFeature("road", "road", "r", 1, 0, 0, true);
        $this->terrain->addNatAltEntranceCost('road','soviet','inf',1);


    }

    public static function buildUnit($data = false){
        return UnitFactory::build($data);
    }

    function terrainInit($terrainDoc){
        parent::terrainInit($terrainDoc);
        UnitFactory::$injector = $this->force;

        if(!empty($this->scenario->preDeploy)) {

            $list = $terrainDoc->terrain->reinforceZones;
            $cnt = 0;
            $unitsDeployed = 0;
            do {
                foreach ($list as $item) {
                    if ($item->name != 'A') {
                        continue;
                    }
                    $cnt++;
                    if ($cnt & 1) {
                        UnitFactory::create("xxx", SOVIET_FORCE, $item->hexagon->number, "multiInf.png", 2, 1, 4, false, STATUS_CAN_DEPLOY, "A", 1, 1, "soviet", true, 'inf', $unitsDeployed+1);
                        $unitsDeployed++;
                    }
                    if ($unitsDeployed >= 60) {
                        break;
                    }

                }
            } while ($unitsDeployed < 60);


            $A = $B = $C = $D = $E = $F = $G = [];
            $cnt = 0;
            foreach ($list as $item) {
                ${$item->name}[] = $item->hexagon->number;
            }
            $i = 0;
            /* Second panzer army */
            /* 21 corp */
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiArmor.png", 6, 3, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "3");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiArmor.png", 6, 3, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "4");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "10");

            /* 47 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "17");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "18");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "29");

            /* 48 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "9");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "16");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiMech.png", 4, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "25");

            /* 35 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiCav.png", 2, 1, 6, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "1 Cav");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "95");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "262");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "293");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "296");

            /* 34 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "45");
            UnitFactory::create("xx", GERMAN_FORCE, $B[$i++], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "134");

            $i = 0;
            /* Second Army */
            /* 13 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $C[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "17");
            UnitFactory::create("xx", GERMAN_FORCE, $C[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "260");

            /* 53 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $C[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "31");
            UnitFactory::create("xx", GERMAN_FORCE, $C[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "56");
            UnitFactory::create("xx", GERMAN_FORCE, $C[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "167");

            /* 42 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $C[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "52");
            UnitFactory::create("xx", GERMAN_FORCE, $C[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "131");

            /* army reserve */
            UnitFactory::create("xx", GERMAN_FORCE, $C[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "112");


            $i = 0;

            /*  Sixth Army */
            /* 17'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "56");
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "62");

            /* 29'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "44");
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "111");
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "299");

            /* 44'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "9");
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "297");

            /* 55'th Corps  */
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "75");
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "57");
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "168");
            UnitFactory::create("xx", GERMAN_FORCE, $D[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "298");


            $i = 0;

            /* First panzer army */
            /* 3 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $E[$i += 2], "multiArmor.png", 6, 3, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "13");
            UnitFactory::create("xx", GERMAN_FORCE, $E[$i += 2], "multiArmor.png", 6, 3, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "14");
            UnitFactory::create("xx", GERMAN_FORCE, $E[$i += 2], "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "25");

            /* 14 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $E[$i += 2], "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "9");
            UnitFactory::create("xx", GERMAN_FORCE, $E[$i += 2], "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "SS AH");
            UnitFactory::create("xx", GERMAN_FORCE, $E[$i += 2], "multiMech.png", 4, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "SS W");

            /* 48 Corps ? */
            UnitFactory::create("xx", GERMAN_FORCE, $E[$i += 2], "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "11");
            UnitFactory::create("xx", GERMAN_FORCE, $E[$i += 2], "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "16");
            UnitFactory::create("xx", GERMAN_FORCE, $E[$i += 2], "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "16");


            $i = 0;

            /* Seventeenth Army */
            /* 4'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "24");
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "71");
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "262");
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "295");
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "296");

            /* 49'th Mountain Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "69");
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "257");
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiMountain.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "mountain", "1 M");

            /* 59'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "101 L");
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "97 L");
            UnitFactory::create("xx", GERMAN_FORCE, $F[$i += 3], "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "100 L");


        }
    }

    function save()
    {
        $data = parent::save();

        $data->specialHexA = $this->specialHexA;
        $data->specialHexB = $this->specialHexB;
        $data->specialHexC = $this->specialHexC;

        return $data;
    }

    public function init()
    {
        UnitFactory::$injector = $this->force;


        $scenario = $this->scenario;


        for($i = 0; $i < 18;$i++){
            UnitFactory::create("xxx", SOVIET_FORCE, "deadpile", "multiInf.png", 2, 1, 4, true, STATUS_ELIMINATED, "A", 1, 1, "soviet", true, 'inf');
        }


        if(empty($scenario->preDeploy)) {


            for ($i = 0; $i < 60; $i++) {
                UnitFactory::create("xxx", SOVIET_FORCE, "deployBox", "multiInf.png", 2, 1, 4, false, STATUS_CAN_DEPLOY, "A", 1, 1, "soviet", true, 'inf');
            }


            /* Second panzer army */
            /* 21 corp */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "3");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "4");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "10");

            /* 47 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "17");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "18");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "29");

            /* 48 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "9");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "16");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "mech", "25");

            /* 35 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiCav.png", 2, 1, 6, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "1 Cav");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "95");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "262");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "293");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "296");

            /* 34 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "45");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "B", 1, 1, "german", true, "inf", "134");

            /* Second Army */
            /* 13 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "17");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "260");

            /* 53 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "31");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "56");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "167");

            /* 42 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "52");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "131");

            /* army reserve */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "C", 1, 1, "secondArmy", true, "inf", "112");

            /* First panzer army */
            /* 3 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "13");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 6, 3, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "14");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "25");

            /* 14 Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "9");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "SS AH");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiMech.png", 4, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "SS W");

            /* 48 Corps ? */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "11");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiArmor.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "16");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiMech.png", 5, 2, 8, false, STATUS_CAN_DEPLOY, "E", 1, 1, "firstPanzerArmy", true, "mech", "16");

            /*  Sixth Army */
            /* 17'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "56");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "62");

            /* 29'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "44");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "111");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "299");

            /* 44'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "9");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "297");

            /* 55'th Corps  */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "75");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "57");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "168");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "D", 1, 1, "sixthArmy", true, "inf", "298");


            /* Seventeenth Army */
            /* 4'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "24");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "71");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "262");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "295");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "296");

            /* 49'th Mountain Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "69");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "257");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiMountain.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "mountain", "1 M");

            /* 59'th Corps */
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "101 L");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "97 L");
            UnitFactory::create("xx", GERMAN_FORCE, "deployBox", "multiInf.png", 2, 1, 5, false, STATUS_CAN_DEPLOY, "F", 1, 1, "seventeenthArmy", true, "inf", "100 L");

        }

    }

    function __construct($data = null, $arg = false, $scenario = false)
    {

        parent::__construct($data, $arg, $scenario);

        $crt = new \Wargame\TMCW\Kiev\CombatResultsTable();
        $this->combatRules->injectCrt($crt);

        if ($data) {
            $this->specialHexA = $data->specialHexA;
            $this->specialHexB = $data->specialHexB;
            $this->specialHexC = $data->specialHexC;

        } else {
            $this->victory = new \Wargame\Victory("\\Wargame\\TMCW\\Kiev\\kievVictoryCore");
            if (!empty($scenario->supplyLen)) {
                $this->victory->setSupplyLen($scenario->supplyLen);
            }
            $this->moveRules->enterZoc = 3;
            $this->moveRules->exitZoc = 2;
            $this->moveRules->noZocZocOneHex = true;
            $this->moveRules->stacking = 3;

            $this->moveRules->friendlyAllowsRetreat = true;
            $this->moveRules->blockedRetreatDamages = true;
            $this->gameRules->legacyExchangeRule = false;

            // game data
            $this->gameRules->setMaxTurn(11);
            $this->gameRules->setInitialPhaseMode(RED_DEPLOY_PHASE, DEPLOY_MODE);

            $this->gameRules->attackingForceId = RED_FORCE; /* object oriented! */
            $this->gameRules->defendingForceId = BLUE_FORCE; /* object oriented! */
            $this->force->setAttackingForceId($this->gameRules->attackingForceId); /* so object oriented */

            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_DEPLOY_PHASE, DEPLOY_MODE, GERMAN_FORCE, SOVIET_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, GERMAN_FORCE, SOVIET_FORCE, false);

            $this->gameRules->addPhaseChange(BLUE_REPLACEMENT_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, GERMAN_FORCE, SOVIET_FORCE, false);

            $this->gameRules->addPhaseChange(BLUE_MOVE_PHASE, BLUE_COMBAT_PHASE, COMBAT_SETUP_MODE, GERMAN_FORCE, SOVIET_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_COMBAT_PHASE, BLUE_MECH_PHASE, MOVING_MODE, GERMAN_FORCE, SOVIET_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_MECH_PHASE, RED_REPLACEMENT_PHASE, REPLACING_MODE, SOVIET_FORCE, GERMAN_FORCE, false);
            $this->gameRules->addPhaseChange(RED_REPLACEMENT_PHASE, RED_MOVE_PHASE, MOVING_MODE, SOVIET_FORCE, GERMAN_FORCE, false);
            $this->gameRules->addPhaseChange(RED_MOVE_PHASE, RED_COMBAT_PHASE, COMBAT_SETUP_MODE, SOVIET_FORCE, GERMAN_FORCE, false);
            $this->gameRules->addPhaseChange(RED_COMBAT_PHASE, RED_MECH_PHASE, MOVING_MODE, SOVIET_FORCE, GERMAN_FORCE, false);
            $this->gameRules->addPhaseChange(RED_MECH_PHASE, BLUE_REPLACEMENT_PHASE, REPLACING_MODE, GERMAN_FORCE, SOVIET_FORCE, true);
        }
    }
}