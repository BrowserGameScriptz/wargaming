<?php
namespace Wargame\Mollwitz\Aliwal1845;
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

class aliwal1845VictoryCore extends \Wargame\Mollwitz\IndiaVictoryCore
{

    public function specialHexChange($args)
    {
        $battle = \Wargame\Battle::getBattle();

        list($mapHexName, $forceId) = $args;
        if(in_array($mapHexName,$battle->specialHexB)){
            if ($forceId == Aliwal1845::BRITISH_FORCE) {
                $this->victoryPoints[Aliwal1845::BRITISH_FORCE]  += 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='sikh'>+10 British vp</span>";
            }
            if ($forceId == Aliwal1845::SIKH_FORCE) {
                $this->victoryPoints[Aliwal1845::BRITISH_FORCE]  -= 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='british'>-10 British vp</span>";
            }
        }
    }

    protected function checkVictory( $battle)
    {
        $gameRules = $battle->gameRules;
        $scenario = $battle->scenario;
        $turn = $gameRules->turn;
        $sikhWin = $britishWin = false;

        if (!$this->gameOver) {
            $specialHexes = $battle->mapData->specialHexes;
            $britVic = 40;
            if (($this->victoryPoints[Aliwal1845::BRITISH_FORCE] >= $britVic && ($this->victoryPoints[Aliwal1845::BRITISH_FORCE] - ($this->victoryPoints[Aliwal1845::SIKH_FORCE]) >= 15))) {
                $britishWin = true;
            }
            if (($this->victoryPoints[Aliwal1845::SIKH_FORCE] >= 40)) {
                $sikhWin = true;
            }
            if ($turn == $gameRules->maxTurn + 1) {
                if (!$britishWin) {
                    $sikhWin = true;
                }
                if ($sikhWin && $britishWin) {
                    $this->winner = 0;
                    $britishWin = $sikhWin = false;
                    $gameRules->flashMessages[] = "Tie Game";
                    $gameRules->flashMessages[] = "Game Over";
                    $this->gameOver = true;
                    return true;
                }
            }


            if ($britishWin) {
                $this->winner = Aliwal1845::BRITISH_FORCE;
                $gameRules->flashMessages[] = "British Win";
            }
            if ($sikhWin) {
                $this->winner = Aliwal1845::SIKH_FORCE;
                $msg = "Sikh Win";
                $gameRules->flashMessages[] = $msg;
            }
            if ($britishWin || $sikhWin) {
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }
}
