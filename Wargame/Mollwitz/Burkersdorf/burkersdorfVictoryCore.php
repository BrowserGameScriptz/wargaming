<?php
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
namespace Wargame\Mollwitz\Burkersdorf;
use \Wargame\Battle;

class burkersdorfVictoryCore extends \Wargame\Mollwitz\victoryCore
{
    public $prussianEnterVictory = false;

    function __construct($data)
    {
        parent::__construct($data);
        if ($data) {
            $this->prussianEnterVictory = $data->victory->prussianEnterVictory;
        }
    }

    public function save()
    {
        $ret = parent::save();
        $ret->prussianEnterVictory = $this->prussianEnterVictory;
        return $ret;
    }

    public function reduceUnit($args)
    {
        $unit = $args[0];
        $mult = 1;
        if ($unit->class == "cavalry" || $unit->class == "artillery") {
            $mult = 2;
        }
        $this->scoreKills($unit, $mult);
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;
        if (in_array($mapHexName, $battle->cities)) {
            if ($forceId == Burkersdorf::PRUSSIAN_FORCE) {
                $this->prussianEnterVictory = true;
                $this->victoryPoints[Burkersdorf::PRUSSIAN_FORCE] += 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='prussian'>+10 Prussian vp</span>";
            }
            if ($forceId == Burkersdorf::AUSTRIAN_FORCE) {
                $this->victoryPoints[Burkersdorf::PRUSSIAN_FORCE] -= 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='austrian'>-10 Prussian vp</span>";
            }
        }
        if (in_array($mapHexName, $battle->loc)) {
            $vp = 50;
            if ($forceId == Burkersdorf::PRUSSIAN_FORCE) {
                $this->prussianEnterVictory = true;
                $this->victoryPoints[Burkersdorf::PRUSSIAN_FORCE] += $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='prussian'>+$vp Prussian vp</span>";
            }
            if ($forceId == Burkersdorf::AUSTRIAN_FORCE) {
                $this->victoryPoints[Burkersdorf::PRUSSIAN_FORCE] -= $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='austrian'>-$vp Prussian vp</span>";
            }
        }
    }

    protected function checkVictory( $battle)
    {
        $gameRules = $battle->gameRules;
        $turn = $gameRules->turn;
        if (!$this->gameOver) {
            $prussianWin = $austrianWin = false;
            if (($this->victoryPoints[Burkersdorf::AUSTRIAN_FORCE] >= 70) && ($this->victoryPoints[Burkersdorf::AUSTRIAN_FORCE] - ($this->victoryPoints[Burkersdorf::PRUSSIAN_FORCE]) >= 10)) {
                $austrianWin = true;
            }
            if (($this->victoryPoints[Burkersdorf::PRUSSIAN_FORCE] >= 70) && ($this->victoryPoints[Burkersdorf::PRUSSIAN_FORCE] - $this->victoryPoints[Burkersdorf::AUSTRIAN_FORCE] >= 10)) {
                $prussianWin = true;
            }

            $cities = $battle->cities;
            $loc = $battle->loc;
            $cities = array_merge($cities, $loc);
            $victoryHexes = 0;
            foreach ($cities as $city) {
                if ($battle->mapData->getSpecialHex($city) === Burkersdorf::PRUSSIAN_FORCE) {
                    $victoryHexes++;
                }
            }
            if ($prussianWin && $victoryHexes < 2) {
                $prussianWin = false;
            }
            if ($prussianWin && $austrianWin) {
                $this->winner = 0;
                $gameRules->flashMessages[] = "Tie Game";
            }
            if ($prussianWin) {
                $this->winner = Burkersdorf::PRUSSIAN_FORCE;
                $gameRules->flashMessages[] = "Prussian Win";
            }
            if ($austrianWin) {
                $this->winner = Burkersdorf::AUSTRIAN_FORCE;
                $gameRules->flashMessages[] = "Austrians Win";
            }
            if ($austrianWin || $prussianWin || $turn > 15) {
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }

    public function postRecoverUnits()
    {
        $b = Battle::getBattle();
        if ($b->gameRules->turn == 1 && $b->gameRules->phase == BLUE_MOVE_PHASE) {
            $b->gameRules->flashMessages[] = "Austrian Movement allowance 2 this turn.";
        }

    }

    public function postRecoverUnit($args)
    {
        $unit = $args[0];
        $b = Battle::getBattle();
        $id = $unit->id;

        parent::postRecoverUnit($args);
        if ($b->gameRules->turn == 1 && $b->gameRules->phase == BLUE_MOVE_PHASE && $unit->status == STATUS_READY) {
            $this->movementCache->$id = $unit->maxMove;
            $unit->maxMove = 2;
        }
        if ($b->gameRules->turn == 1 && $b->gameRules->phase == BLUE_COMBAT_PHASE && isset($this->movementCache->$id)) {
            $unit->maxMove = $this->movementCache->$id;
            unset($this->movementCache->$id);
        }
    }
}
