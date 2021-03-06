<?php
namespace Wargame;
// crt.js

// Copyright (c) 2009-2011 Mark Butler
/*
Copyright 2012-2015 David Rodal

This program is free software; you can redistribute it
and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version->

This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
        $this->combatResultsHeader = array("1:1","2:1","3:1","4:1","5:1","6:1");
	    $this->crts = new stdClass();
        $this->crts->normal = array(
            array(DR, DRL, DE, DE, DE, DE),
            array(DR, EX, DRL, DE, DE, DE),
            array(EX, EX, DRL, DRL, DE, DE),
            array(EX, DR, EX, EX, DE, DE),
            array(AL, DR, DR, DR, DRL, DE),
            array(AL, AL, DR, DR, EX, DE),
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

    function getCombatIndex(){
        return 3;
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
