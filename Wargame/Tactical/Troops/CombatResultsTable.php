<?php
namespace Wargame\Tactical\Troops;
use \stdClass;
use \Wargame\Battle;
use \Wargame\Hexpart;
use \Wargame\Los;
// crt.js

// Copyright (c) 2009-2011 Mark Butler
// This program is free software; you can redistribute it 
// and/or modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation;
// either version 2 of the License, or (at your option) any later version. 

/**
 *
 * Copyright 2012-2015 David Rodal
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

class CombatResultsTable
{
    public $combatIndexCount;
    public $maxCombatIndex;
    public $dieSideCount;
    public $dieMaxValue;
    public $combatResultCount;

    public $combatResultsTable;
    public $combatResultsHeader;
    public $combatOddsTable;

    //     combatIndexeCount is 6; maxCombatIndex = 5
    //     index is 0 to 5;  dieSidesCount = 6

    function __construct()
    {
        $this->combatResultsHeader = array("1:4", "1:3", "1:2", "1:1", "2:1", "3:1", "4:1", "5:1", "6:1", "7:1");
        $this->crts = new stdClass();

        $this->crts->normal = array(
            array(DD, DD, DD, DD, DE, DE, DE, DE, DE, DE),
            array(NE, DD, DD, DD, DD, DE, DE, DE, DE, DE),
            array(NE, NE, DD, DD, DD, DD, DE, DE, DE, DE),
            array(NE, NE, NE, DD, DD, DD, DD, DE, DE, DE),
            array(NE, NE, NE, NE, DD, DD, DD, DD, DE, DE),
            array(NE, NE, NE, NE, NE, DD, DD, DD, DD, DE),
        );
        $this->combatOddsTable = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array()
        );

        $this->combatIndexCount = 10;
        $this->maxCombatIndex = $this->combatIndexCount - 1;
        $this->dieSideCount = 6;
        $this->combatResultCount = 5;

        $this->setCombatOddsTable();
    }

    function getCombatResults($Die, $index, $combat)
    {
        if ($combat->useAlt) {
            return $this->crts->normal[$Die][$index];
        } else {
            if($combat->useDetermined){
                return $this->combatResultsTableDetermined[$Die][$index];
            }
            return $this->crts->normal[$Die][$index];
        }
    }

    function getCombatDisplay()
    {
        return $this->combatResultsHeader;
    }

    public function setCombatIndex($defenderId)
    {

        $combatLog = "";
        /* @var JagCore $battle */
        $battle = Battle::getBattle();
        $scenario = $battle->scenario;
        $combats = $battle->combatRules->combats->$defenderId;

        if (count((array)$combats->attackers) == 0) {
            $combats->index = null;
            $combats->attackStrength = null;
            $combats->defenseStrength = null;
            $combats->terrainCombatEffect = null;
            return;
        }

        $defenders = $combats->defenders;
        $isCavalry = $isTown = $isHill = $isForest = $isSwamp = $attackerIsSunkenRoad = $isRedoubt = $isElevated = false;


        foreach ($defenders as $defId => $defender) {
            $defUnit = $battle->force->units[$defId];
            $hexagon = $defUnit->hexagon;
            if($defUnit->class === 'cavalry'){
                $isCavalry = true;
            }
            $hexpart = new Hexpart();
            $hexpart->setXYwithNameAndType($hexagon->name, HEXAGON_CENTER);
            $isTown |= $battle->terrain->terrainIs($hexpart, 'town');
            $isForest |= $battle->terrain->terrainIs($hexpart, 'forest');
            if($battle->terrain->terrainIs($hexpart, 'elevation')){
                $isElevated = 1;
            }
            if($battle->terrain->terrainIs($hexpart, 'elevation2')){
                $isElevated = 2;
            }
        }
        $isClear = true;
        if ($isTown || $isForest || $isHill || $isSwamp) {
            $isClear = false;
        }

        $attackers = $combats->attackers;
        $attackStrength = 0;
        $attackersCav = false;
        $combinedArms = array();

        $combatLog .= "Attackers<br>";
        foreach ($attackers as $attackerId => $attacker) {
            $terrainReason = "";
            $unit = $battle->force->units[$attackerId];
            $unitStrength = $unit->strength;

            $hexagon = $unit->hexagon;
            $hexpart = new Hexpart();
            $hexpart->setXYwithNameAndType($hexagon->name, HEXAGON_CENTER);

            $los = new Los();

            $los->setOrigin($battle->force->getUnitHexagon($attackerId));
            $los->setEndPoint($battle->force->getUnitHexagon($defenderId));
            $range = $los->getRange();
            $combatLog .= $unit->strength ." ".$unit->class." ";

            if($isCavalry){
                $combatLog .= "defender is cavalry, doubled ";
                $unitStrength *= 2;
            }
            if($unit->class === "infantry" && $range == 1){
                $combatLog .= "infantry at range 1, doubled ";
                $unitStrength *= 2;
            }
            if($unit->class === "mg" && $range <= 3){
                $combatLog .= "machine gun at range 3 or less, doubled ";

                $unitStrength *= 2;
            }
            if($isTown || $isForest){
                if($range > 1){
                    $combatLog .= "using observation fire, halved ";
                    $unitStrength /= 2;
                }
            }

            $attackerIsElevated = false;
            if($battle->terrain->terrainIs($hexpart, 'elevation')){
                $attackerIsElevated = 1;
            }

            if($battle->terrain->terrainIs($hexpart, 'elevation2')){
             $attackerIsElevated = 2;
            }


            $combatLog .= "<br>";
            $attackStrength += $unitStrength;
        }

        $combatLog .= " = $attackStrength<br>Defenders<br>";
        $defenseStrength = 0;

        foreach ($defenders as $defId => $defender) {
            $unitStrength = 2;
            $terrain = '';

            $unit = $battle->force->units[$defId];
            $class = $unit->class;
            $clearHex = false;
            $notClearHex = false;
            $hexagon = $unit->hexagon;
            $hexpart = new Hexpart();
            $hexpart->setXYwithNameAndType($hexagon->name, HEXAGON_CENTER);
            $isTown = $battle->terrain->terrainIs($hexpart, 'town');
//            $isHill = $battle->terrain->terrainIs($hexpart, 'hill');
            $isForest = $battle->terrain->terrainIs($hexpart, 'forest');
            $isSwamp = $battle->terrain->terrainIs($hexpart, 'swamp');
            $terran = "";
            if($attackerIsElevated < $isElevated){
                $unitStrength = 4;
                $terrain = "uphill ";
            }
            if($isTown){
                $terrain = "in town ";
                $unitStrength = 8;
            }
            if($isForest){
                $terrain = "in forest ";
                $unitStrength = 5;
            }
            if($unit->isImproved){
                $unitStrength *= 2;
                $terrain .= "Improved ";
            }
            $combatLog .= "$unitStrength ".$unit->class." $terrain";


            $defenseStrength += $unitStrength;
            $combatLog .= "<br>";
        }

        $combatLog .= " = $defenseStrength";

        $combatIndex = $this->getCombatIndex($attackStrength, $defenseStrength);
        /* Do this before terrain effects */

        if ($combatIndex >= $this->maxCombatIndex) {
            $combatIndex = $this->maxCombatIndex;
        }

//        $terrainCombatEffect = $battle->combatRules->getDefenderTerrainCombatEffect($defenderId);

//        $combatIndex -= $terrainCombatEffect;

        $combats->attackStrength = $attackStrength;
        $combats->defenseStrength = $defenseStrength;
        $combats->terrainCombatEffect = 0;
//
//        if($combats->pinCRT !== false){
//            $pinIndex = $combats->pinCRT;
//            if($combatIndex > $pinIndex){
//                $combatLog .= "<br>Pinned to {$this->combatResultsHeader[$pinIndex]} ";
//            }else{
//                $combats->pinCRT = false;
//            }
//        }
        $combats->index = $combatIndex;
        $combats->useAlt = false;
        $combats->combatLog = $combatLog;
    }

    function getCombatIndex($attackStrength, $defenseStrength)
    {
        $ratio = $attackStrength / $defenseStrength;
        if ($attackStrength >= $defenseStrength) {
            $combatIndex = floor($ratio) + 2;
        } else {
            $combatIndex = 4 - ceil($defenseStrength / $attackStrength);
        }
        return $combatIndex;
    }

    function setCombatOddsTable()
    {
        return;
        $odds = array();

        for ($combatIndex = 0; $combatIndex < $this->combatIndexCount; $combatIndex++) {

            $odds[0] = 0;
            $odds[1] = 0;
            $odds[2] = 0;
            $odds[3] = 0;
            $odds[4] = 0;

            for ($Die = 0; $Die < $this->dieSideCount; $Die++) {
                $combatResultIndex = $this->combatResultsTable[$Die][$combatIndex];
                $odds[$combatResultIndex] = $odds[$combatResultIndex] + 1;
            }

            $list = "";

            $list += $odds[0] + ", ";
            $list += $odds[1] + ", ";
            $list += $odds[2] + ", ";
            $list += $odds[3] + ", ";
            $list += $odds[4];

            for ($combatResultIndex = 0; $combatResultIndex < $this->combatResultCount; $combatResultIndex++) {
                $numerator = $odds[$combatResultIndex];
                $denominator = $this->dieSideCount;
                $percent = 100 * ($numerator / $denominator);
                $intPercent = (int)floor($percent);
                $this->combatOddsTable[$combatResultIndex][$combatIndex] = $intPercent;
            }
        }
    }

    function getCombatOddsList($combatIndex)
    {
        die("sad");
        global $results_name;
        $combatOddsList = "";
        //  combatOddsList  += "combat differential: " + combatIndex;

        //    var i;
        for ($i = 0; $i < $this->combatResultCount; $i++) {
            //combatOddsList += "<br />";
            $combatOddsList .= $results_name[$i];
            $combatOddsList .= ":";
            $combatOddsList .= $this->combatOddsTable[$i][$combatIndex];
            $combatOddsList .= "% ";
        }

        return $combatOddsList;
    }

}
