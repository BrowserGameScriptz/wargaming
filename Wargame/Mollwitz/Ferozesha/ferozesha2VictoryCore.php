<?php
namespace Wargame\Mollwitz\Ferozesha;
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


class ferozesha2VictoryCore extends \Wargame\Mollwitz\IndiaVictoryCore
{

    public function specialHexChange($args)
    {
        $battle = \Wargame\Battle::getBattle();
        if (!empty($battle->scenario->dayTwo)) {
            list($mapHexName, $forceId) = $args;

            if ($forceId == FerozeshaDayTwo::SIKH_FORCE) {
                $this->victoryPoints[FerozeshaDayTwo::SIKH_FORCE] += 5;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='beluchi'>+5 Sikh  vp</span>";
            }
            if ($forceId == FerozeshaDayTwo::BRITISH_FORCE) {
                $this->victoryPoints[FerozeshaDayTwo::SIKH_FORCE] -= 5;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='british'>-5 Sikh  vp</span>";
            }
        } else {

            list($mapHexName, $forceId) = $args;
            if ($mapHexName == $battle->moodkee) {
                if ($forceId == FerozeshaDayTwo::SIKH_FORCE) {
                    $this->victoryPoints[FerozeshaDayTwo::SIKH_FORCE] += 20;
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='beluchi'>+5 Sikh  vp</span>";
                }
                if ($forceId == FerozeshaDayTwo::BRITISH_FORCE) {
                    $this->victoryPoints[FerozeshaDayTwo::SIKH_FORCE] -= 20;
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='british'>-5 Sikh  vp</span>";
                }

            } else {
                if ($forceId == FerozeshaDayTwo::BRITISH_FORCE) {
                    $this->victoryPoints[FerozeshaDayTwo::BRITISH_FORCE] += 5;
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='british'>+5 British  vp</span>";
                }
                if ($forceId == FerozeshaDayTwo::SIKH_FORCE) {
                    $this->victoryPoints[FerozeshaDayTwo::BRITISH_FORCE] -= 5;
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='beluchi'>-5 British  vp</span>";
                }
            }
        }
    }


    protected function checkVictory( $battle)
    {
        $gameRules = $battle->gameRules;
        $scenario = $battle->scenario;
        $turn = $gameRules->turn;
        $britishWin = $sikhWin = false;

        if (!$this->gameOver) {
            $specialHexes = $battle->mapData->specialHexes;
            $sikhVic = 35;
            $britLead = 10;
            if ($this->victoryPoints[FerozeshaDayTwo::SIKH_FORCE] >= $sikhVic ) {
                $sikhWin = true;
            }
            if (($this->victoryPoints[FerozeshaDayTwo::BRITISH_FORCE] >= 40) && (($this->victoryPoints[FerozeshaDayTwo::BRITISH_FORCE] - $this->victoryPoints[FerozeshaDayTwo::SIKH_FORCE]) >= $britLead)) {
                $britishWin = true;
            }
            if ($turn == $gameRules->maxTurn + 1) {
                if (!$sikhWin) {
                    $britishWin = true;
                }
                if ($britishWin && $sikhWin) {
                    $this->winner = 0;
                    $sikhWin = $britishWin = false;
                    $gameRules->flashMessages[] = "Tie Game";
                    $gameRules->flashMessages[] = "Game Over";
                    $this->gameOver = true;
                    return true;
                }
            }

            if ($sikhWin) {
                $this->winner = FerozeshaDayTwo::SIKH_FORCE;
                $gameRules->flashMessages[] = "Sikh Win";
            }
            if ($britishWin) {
                $this->winner = FerozeshaDayTwo::BRITISH_FORCE;
                $msg = "British Win";
                $gameRules->flashMessages[] = $msg;
            }
            if ($sikhWin || $britishWin) {
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }
}
