<?php
namespace Wargame\Mollwitz\Kolin1757;
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


class kolin1757VictoryCore extends \Wargame\Mollwitz\victoryCore
{
    public function reduceUnit($args)
    {
        $unit = $args[0];
        $mult = 1;
        if ($unit->nationality == "British") {
            $mult = 2;
        }
        $this->scoreKills($unit, $mult);
    }

    public function specialHexChange($args)
    {
        $battle = \Wargame\Battle::getBattle();

        list($mapHexName, $forceId) = $args;
        if (in_array($mapHexName, $battle->specialHexA)) {
            if ($forceId == Kolin1757::AUSTRIAN_FORCE) {
                $this->victoryPoints[Kolin1757::AUSTRIAN_FORCE] += 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='austrian'>+10 Austrian vp</span>";
            }
            if ($forceId == Kolin1757::PRUSSIAN_FORCE) {
                $this->victoryPoints[Kolin1757::AUSTRIAN_FORCE] -= 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='prussian'>-10 Prussian vp</span>";
            }
        }
        if (in_array($mapHexName, $battle->specialHexB)) {
            if ($forceId == Kolin1757::PRUSSIAN_FORCE) {
                $this->victoryPoints[Kolin1757::PRUSSIAN_FORCE] += 20;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='prussian'>+20 Prussian vp</span>";
            }
            if ($forceId == Kolin1757::AUSTRIAN_FORCE) {
                $this->victoryPoints[Kolin1757::PRUSSIAN_FORCE] -= 20;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='austrian'>-20 Austrian vp</span>";
            }
        }
    }

    protected function checkVictory( $battle)
    {
        $gameRules = $battle->gameRules;
        $scenario = $battle->scenario;
        $turn = $gameRules->turn;
        $prussianScore = $austrianWin = $prussianWin = $draw = false;

        if (!$this->gameOver) {
            $specialHexes = $battle->mapData->specialHexes;
            $winScore = 60;
            if (($this->victoryPoints[Kolin1757::PRUSSIAN_FORCE] >= $winScore && ($this->victoryPoints[Kolin1757::PRUSSIAN_FORCE] - ($this->victoryPoints[Kolin1757::AUSTRIAN_FORCE]) >= 10))) {
                if ($turn < 9) {
                    $prussianWin = true;
                }
                if ($turn < 12) {
                    $draw = true;
                }
                $prussianScore = true;
            }
            if ($this->victoryPoints[Kolin1757::AUSTRIAN_FORCE] >= $winScore && ($this->victoryPoints[Kolin1757::AUSTRIAN_FORCE] - ($this->victoryPoints[Kolin1757::PRUSSIAN_FORCE]))) {
                if ($turn < 13) {
                    $austrianWin = true;
                }
            }
            if ($turn == $gameRules->maxTurn + 1) {
                if (!$prussianScore) {
                    $austrianWin = true;
                }
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }

            if ($prussianWin) {
                $this->winner = Kolin1757::PRUSSIAN_FORCE;
                $gameRules->flashMessages[] = "Prussian Win";
            }
            if ($austrianWin) {
                $this->winner = Kolin1757::AUSTRIAN_FORCE;
                $msg = "Austrian Win";
                $gameRules->flashMessages[] = $msg;
            }
            if ($prussianWin || $austrianWin) {
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }
}
