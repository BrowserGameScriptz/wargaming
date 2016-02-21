<?php
namespace Wargame\Mollwitz\Malplaquet;
/*
Copyright 2012-2015 David Rodal

This program is free software; you can redistribute it
and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version

This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
   */

/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 5/7/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */
class malplaquetVictoryCore extends \Wargame\Mollwitz\victoryCore
{

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
    }
    protected function checkVictory($battle){
        $gameRules = $battle->gameRules;
        $turn = $gameRules->turn;
        $frenchWin = $angloMalplaquet =  $angloCities = $angloWin = false;

        if(!$this->gameOver){
            /* @var MapData $mapData */
            $mapData = $battle->mapData;
            /* End of the French player turn */
            if($gameRules->attackingForceId == FRENCH_FORCE) {

                $malplaquet = $battle->malplaquet[0];
                $otherCities = $battle->otherCities;
                if ($mapData->getSpecialHex($malplaquet) === ANGLO_FORCE) {
                    $angloMalplaquet = true;
                    foreach ($otherCities as $city) {
                        if ($mapData->getSpecialHex($city) === ANGLO_FORCE) {
                            $angloCities = true;
                        }
                    }
                }
            }

            if($angloCities && ($this->victoryPoints[ANGLO_FORCE] - ($this->victoryPoints[FRENCH_FORCE]) >= 10)){
                $angloWin = true;
            }

            if($turn == $gameRules->maxTurn+1){
                if(!$angloWin){
                    if($angloCities === false && $angloMalplaquet === false){
                        $frenchWin = true;
                    }
                }
                if(!$frenchWin && !$angloWin){
                    $this->winner = 0;
                    $angloWin = $frenchWin = false;
                    $gameRules->flashMessages[] = "Tie Game";
                    $gameRules->flashMessages[] = "Game Over";
                    $this->gameOver = true;
                    return true;
                }            }


            if($angloWin){
                $this->winner = ANGLO_FORCE;
                $gameRules->flashMessages[] = "Allies Win";
            }
            if($frenchWin){
                $this->winner = FRENCH_FORCE;
                $msg = "French Win Allies hold no cities";
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
