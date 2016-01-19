<?php
namespace Wargame\Mollwitz\Minden;
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
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 5/7/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */
class mindenVictoryCore extends \Wargame\Mollwitz\victoryCore
{

    function __construct($data)
    {
        if($data) {
            $this->movementCache = $data->victory->movementCache;
            $this->victoryPoints = $data->victory->victoryPoints;
            $this->gameOver = $data->victory->gameOver;
        } else {
            $this->victoryPoints = array(0, 0, 0);
            $this->movementCache = new \stdClass();
            $this->gameOver = false;
        }
    }

    public function reduceUnit($args)
    {
        $unit = $args[0];
        $mult = 1;
        if($unit->class == "cavalry" || $unit->class == "artillery"){
            $mult = 2;
        }
        $this->scoreKills($unit, $mult);
    }

    public function specialHexChange($args)
    {
        $battle = \Wargame\Battle::getBattle();

        list($mapHexName, $forceId) = $args;
        if ($forceId == FRENCH_FORCE && in_array($mapHexName,$battle->angloSpecialHexes)) {
            $this->victoryPoints[FRENCH_FORCE]  += 5;
            $battle->mapData->specialHexesVictory->$mapHexName = "<span class='french'>+5 French vp</span>";
        }
        if ($forceId == ANGLO_FORCE && in_array($mapHexName,$battle->angloSpecialHexes)) {
            $this->victoryPoints[FRENCH_FORCE]  -= 5;
            $battle->mapData->specialHexesVictory->$mapHexName = "<span class='french'>-5 French vp</span>";
        }
        if ($forceId == ANGLO_FORCE && in_array($mapHexName,$battle->frenchSpecialHexes)) {
            $this->victoryPoints[ANGLO_FORCE]  += 10;
            $battle->mapData->specialHexesVictory->$mapHexName = "<span class='anglo'>+10 Anglo Allied vp</span>";
        }
        if ($forceId == FRENCH_FORCE && in_array($mapHexName,$battle->frenchSpecialHexes)) {
            $this->victoryPoints[ANGLO_FORCE]  -= 10;
            $battle->mapData->specialHexesVictory->$mapHexName = "<span class='anglo'>-10 Anglo Allied vp</span>";
        }
    }
    protected function checkVictory($battle){
        $gameRules = $battle->gameRules;
        $turn = $gameRules->turn;
        if(!$this->gameOver){
            $frenchWin = $angloWin = false;
            if(($this->victoryPoints[ANGLO_FORCE] >= 50) && ($this->victoryPoints[ANGLO_FORCE] - ($this->victoryPoints[FRENCH_FORCE]) >= 10)){
                $angloWin = true;
            }
            if(($this->victoryPoints[FRENCH_FORCE] >= 50) && ($this->victoryPoints[FRENCH_FORCE] - $this->victoryPoints[ANGLO_FORCE] >= 10)){
                $frenchWin = true;
            }
            if($frenchWin && $angloWin){
                $this->winner = 0;
                $angloWin = $frenchWin = false;
                $gameRules->flashMessages[] = "Tie Game";
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }

            if($angloWin){
                $this->winner = ANGLO_FORCE;
                $gameRules->flashMessages[] = "Anglo Allied Win 50 points or more";
            }
            if($frenchWin){
                $this->winner = FRENCH_FORCE;
                $msg = "French Win 50 points or more";
                $gameRules->flashMessages[] = $msg;
            }
            if($angloWin || $frenchWin){
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }
}
