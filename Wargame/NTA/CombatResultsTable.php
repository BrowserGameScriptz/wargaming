<?php
namespace Wargame\NTA;
use stdClass;
use Wargame\Battle;
use Wargame\Hexpart;
// crt.js

// Copyright (c) 2009-2011 Mark Butler
// This program is free software; you can redistribute it 
// and/or modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation;
// either version 2 of the License, or (at your option) any later version. 

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
    
    function __construct(){
        $this->combatResultsHeader = array("1:5","1:4","1:3","1:2","1:1","2:1","3:1","4:1","5:1","6:1");
	    $this->crts = new stdClass();
        $this->crts->normal = array(
            array(AE, AR, AR, DR, DR, DR, DE, DE, DE, DE),
            array(AE, AE, AR, AR, DR, DR, DR, DE, DE, DE),
            array(AE, AE, AE, AR, DR, DR, DR, DR, DE, DE),
            array(AE, AE, AE, AR, AR, DR, DR, DR, DE, DE),
            array(AE, AE, AE, AR, AR, EX, DR, EX, EX, DE),
            array(AE, AE, AE, AE, AR, AR, EX, EX, EX, DE),
        );
        $this->crts->cavalry = array(
            array(AE, AR, AR, DR, DR, DR, DR, DR, DR, DR),
            array(AE, AE, AR, AR, DR, DR, DR, DR, DR, DR),
            array(AE, AE, AE, AR, DR, DR, DR, DR, DR, DR),
            array(AE, AE, AE, AR, AR, DR, DR, DR, DR, DR),
            array(AE, AE, AE, AR, AR, DR, DR, DR, DR, DR),
            array(AE, AE, AE, AE, AR, AR, DR, DR, DR, DR),
        );
        $this->crts->determined = array(
            array(AE, AE, AE, AR, AR, AR, DR, DR, DR, EX),
            array(AE, AE, AR, AR, AR, DR, DR, DR, EX, DE),
            array(AE, AE, AR, AR, DR, DR, EX, EX, DE, DE),
            array(AE, AE, NE, NE, DR, EX, EX, DE, DE, DE),
            array(AE, AR, NE, DR, EX, EX, DE, DE, DE, DE),
            array(AR, AR, DR, EX, EX, DE, DE, DE, DE, DE),
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
            if($combat->useAlt){
                return $this->crts->cavalry[$Die][$index];
            }else{
                return $this->crts->normal[$Die][$index];
            }
        }

    function getCombatDisplay(){
        return $this->combatResultsHeader;
    }

    public function setCombatIndex($defenderId){

        $combatLog = "";
        /* @var Jagersdorf $battle */
        $battle = Battle::getBattle();
        $combats = $battle->combatRules->combats->$defenderId;
        $hexagon = $battle->force->units[$defenderId]->hexagon;
        $hexpart = new Hexpart();
        $hexpart->setXYwithNameAndType($hexagon->name, HEXAGON_CENTER);

        if (count((array)$combats->attackers) == 0) {
            $combats->index = null;
            $combats->attackStrength = null;
            $combats->defenseStrength = null;
            $combats->terrainCombatEffect = null;
            return;
        }

        $isClear = $battle->terrain->terrainIs($hexpart,'clear');
        $isTown = $battle->terrain->terrainIs($hexpart,'town');
        $isHill = $battle->terrain->terrainIs($hexpart,'hill');
        $isForest = $battle->terrain->terrainIs($hexpart,'forest');

        $defenders = $combats->defenders;

        $attackers = $combats->attackers;
        $attackStrength = 0;
        $attackersCav = false;
        $combinedArms = ['infantry'=>0, 'artillery'=>0, 'cavalry'=>0];

        foreach($attackers as $attackerId => $attacker){
            $unit = $battle->force->units[$attackerId];
            $unitStrength = $unit->strength;

            if($unit->class == "infantry"){
//                if($unit->forceId == PRUSSIAN_FORCE && $isClear){
//                    $unitStrength++;
//                }
//                if($unit->forceId == RUSSIAN_FORCE && ($isTown || $isForest)){
//                    $unitStrength++;
//                }
                if($battle->combatRules->thisAttackAcrossRiver($defenderId,$attackerId)){
                    $unitStrength /= 2;
                }
            }

            if($unit->class == "cavalry"){
                $attackersCav = true;
                if($battle->combatRules->thisAttackAcrossRiver($defenderId,$attackerId) || !$isClear){
                    $unitStrength /= 2;
                }
            }
            $attackStrength += $unitStrength;
        }
//        $attackStrength = $battle->force->getAttackerStrength($combats->attackers);
        $defenseStrength = 0;
        $defendersAllCav = true;
        foreach($defenders as $defId => $defender){
            $unit = $battle->force->units[$defId];
            $class = $unit->class;
            $unitDefense = $unit->defStrength;
            if($unit->class != 'cavalry'){
                $defendersAllCav = false;
            }
//            if($unit->forceId == PRUSSIAN_FORCE && $class == "infantry" && $isClear){
//                $unitDefense += 1;
//            }
//            if($unit->forceId == RUSSIAN_FORCE  && $class == "infantry" && ($isTown || $isForest)){
//                $unitDefense += 1;
//            }

            $defenseStrength += $unitDefense * (($isTown && $class != 'cavalry') || $isHill ? 2 : 1);
        }


        if($attackStrength >= $defenseStrength){
            foreach($combats->attackers as $attackerId => $attacker){
                $combinedArms[$battle->force->units[$attackerId]->class]++;
            }
            if(!$isClear){
                unset($combinedArms['cavalry']);
            }
        }

        $armsShift = 0;
        $armsTypes = "";
        if ($attackStrength >= $defenseStrength) {
            foreach($combinedArms as $k => $arms){
                if($arms > 0){
                    $armsTypes  .= "$k ";
                    $armsShift++;
                }
            }
            $armsShift--;
        }

        if ($armsShift < 0) {
            $armsShift = 0;
        }
        if($armsShift > 0){
            $combatLog .= "Combined Arms Shift: +$armsShift ".$armsTypes;
        }

        $combatIndex = $this->getCombatIndex($attackStrength, $defenseStrength);
        /* Do this before terrain effects */
        $combatIndex += $armsShift;

        if ($combatIndex >= $this->maxCombatIndex) {
            $combatIndex = $this->maxCombatIndex;
        }

//        $terrainCombatEffect = $battle->combatRules->getDefenderTerrainCombatEffect($defenderId);

//        $combatIndex -= $terrainCombatEffect;

        $combats->attackStrength = $attackStrength;
        $combats->defenseStrength = $defenseStrength;
        $combats->terrainCombatEffect = $armsShift;
        $combats->index = $combatIndex;
        $combats->useAlt = false;
        if($defendersAllCav && !$attackersCav){
            $combats->useAlt = true;
        }
        $combats->combatLog = $combatLog;
    }

    function getCombatIndex($attackStrength, $defenseStrength){
        $ratio = $attackStrength / $defenseStrength;
        if($attackStrength >= $defenseStrength){
            $combatIndex = floor($ratio)+3;
        }else{
            $combatIndex = 5 - ceil($defenseStrength /$attackStrength );
        }
        return $combatIndex;
    }
    function setCombatOddsTable()
    {
        return;
        $odds = array();

    //    var Die;
    //    var combatIndex;
    //    var combatResultIndex;
    //    var numerator;
    //    var denominator;
    //    var percent;
    //    var intPercent;

        for ($combatIndex = 0; $combatIndex < $this->combatIndexCount; $combatIndex++)
        {

            $odds[0] = 0;
            $odds[1] = 0;
            $odds[2] = 0;
            $odds[3] = 0;
            $odds[4] = 0;

            for( $Die = 0; $Die < $this->dieSideCount; $Die++ )
            {
                $combatResultIndex = $this->combatResultsTable[$Die][$combatIndex];
                $odds[$combatResultIndex] = $odds[$combatResultIndex] + 1;
            }

            $list = "";

            $list += $odds[0] + ", ";
            $list += $odds[1] + ", ";
            $list += $odds[2] + ", ";
            $list += $odds[3] + ", ";
            $list += $odds[4];

            for( $combatResultIndex = 0; $combatResultIndex < $this->combatResultCount; $combatResultIndex++ )
            {
                $numerator = $odds[$combatResultIndex];
                $denominator = $this->dieSideCount;
                $percent = 100 * ($numerator/$denominator);
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
        for ( $i = 0; $i < $this->combatResultCount; $i++ )
        {
            //combatOddsList += "<br />";
            $combatOddsList .= $results_name[$i];
            $combatOddsList .= ":";
            $combatOddsList .= $this->combatOddsTable[$i][$combatIndex];
            $combatOddsList .= "% ";
        }

        return $combatOddsList;
    }

}
