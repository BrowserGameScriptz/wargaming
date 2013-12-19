<?php
// crt.js

// copyright (c) 2009-2011 Mark Butler
// This program is free software; you can redistribute it 
// and/or modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation;
// either version 2 of the License, or (at your option) any later version. 


class CombatResultsTable
{
//    use crtTraits
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
        $this->combatResultsHeader = array("1:1","2:1","3:1","4:1","5:1","6:1");
	    $this->combatResultsTable = array(
            array(DR2, DRL2, DE, DE, DE, DE),
            array(DR2, EX2, DRL2, DE, DE, DE),
            array(EX2, EX2, DRL2, DRL2, DE, DE),
            array(EX2, DR2, EX2, EX2, DE, DE),
            array(AL, DR2, DR2, DR2, DRL2, DE),
            array(AL, AL, DR2, DR2, EX, DE),
        );

        $this->combatOddsTable = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array()
        );

        $this->combatIndexCount = 6;
        $this->maxCombatIndex = $this->combatIndexCount - 1;
        $this->dieSideCount = 6;
        $this->combatResultCount = 5;

        $this->setCombatOddsTable();
    }

        function getCombatResults($Die, $index)
        {
            return $this->combatResultsTable[$Die][$index];
        }
    function getCombatIndex($attackStrength, $defenseStrength){
            $combatIndex = floor($attackStrength / $defenseStrength)-1;

        return $combatIndex;
    }
    function setCombatOddsTable()
    {
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
            $odds[5] = 0;


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
    function setCombatIndex($defenderId)
    {
        $battle = Battle::getBattle();
        $combatRules = $battle->combatRules;
        $terrain = $battle->terrain;
        $combats = $battle->combatRules->combats->$defenderId;
        /* @var Force $force */
        $force = $battle->force;
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

        $defenders = $combats->defenders;
        $attackStrength = 0;

//        $attackStrength = $this->force->getAttackerStrength($combats->attackers);
        $defenseStrength = 0;
        $defMarsh = false;
        $defArt = false;
        foreach ($defenders as $defId => $defender) {
            $unitStr = $force->getDefenderStrength($defId);
            $unit = $force->units[$defId];
            $unitHex = $unit->hexagon;
            if($unit->class == "artillery"){
                $defArt = true;
            }
            echo "Unitstr $unitStr ";
            if($terrain->terrainIsHex($unitHex->name, "rough") || $terrain->terrainIsHex($unitHex->name, "hills")){
                echo "Rough ";
                    $unitStr *= 2;
            }
            echo "about to";
            if($terrain->terrainIsHex($unitHex->name, "marsh")){
                echo "MARSH!!! ";
                echo $unit->class;
                $defMarsh = true;
                if($unit->class == "inf" || $unit->class == "cavalry"){
                        $unitStr *= 2;
                }
            }
                var_dump($defenders);
            $defenseStrength += $unitStr;
        }

        $defHex = $unitHex;
        foreach ($combats->attackers as $id => $v) {
            $unit = $force->units[$id];
            $unitStr = $unit->strength;
            if($unit->class != 'artillery' && $terrain->terrainIsHexSide($defHex->name,$unit->hexagon->name, "river")){
                $unitStr /= 2;
            }
            if($defMarsh && $force->units[$id]->class == 'mech'){
                $unitStr /= 2;
            }
            if($defArt && $force->units[$id]->class != 'artillery'){
                $unitStr *= 2;
            }
            $attackStrength += $unitStr;
        }

        $terrainCombatEffect = 0;

        $combatIndex = $this->getCombatIndex($attackStrength, $defenseStrength);
        /* Do this before terrain effects */
        if ($combatIndex >= $this->maxCombatIndex) {
            $combatIndex = $this->maxCombatIndex;
        }



        $combatIndex -= $terrainCombatEffect;

        $combats->attackStrength = $attackStrength;
        $combats->defenseStrength = $defenseStrength;
        $combats->terrainCombatEffect = $terrainCombatEffect;
        $combats->index = $combatIndex;
//    $this->force->storeCombatIndex($defenderId, $combatIndex);
    }
    function setCombatIndeqx($defenderId){
        $battle = Battle::getBattle();
        $combatRules = $battle->combatRules;
        $combats = $battle->combatRules->combats->$defenderId;
        $force = $battle->force;
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

        $defenders = $combats->defenders;
        $attackStrength = 0;

        foreach ($combats->attackers as $id => $v)
        {
            $attackStrength += $force->units[$id]->strength;
        }
//        $attackStrength = $this->force->getAttackerStrength($combats->attackers);
        $defenseStrength = 0;
        foreach ($defenders as $defId => $defender) {
            $defenseStrength += $force->getDefenderStrength($defId);
        }
        $combatIndex = $this->getCombatIndex($attackStrength, $defenseStrength);
        /* Do this before terrain effects */
        if ($combatIndex >= $this->maxCombatIndex) {
            $combatIndex = $this->maxCombatIndex;
        }



        $terrainCombatEffect = $combatRules->getDefenderTerrainCombatEffect($defenderId);

        $combatIndex -= $terrainCombatEffect;

        $combats->attackStrength = $attackStrength;
        $combats->defenseStrength = $defenseStrength;
        $combats->terrainCombatEffect = $terrainCombatEffect;
        $combats->index = $combatIndex;
//    $this->force->storeCombatIndex($defenderId, $combatIndex);
    }
    function getCombatOddsList($combatIndex)
    {
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