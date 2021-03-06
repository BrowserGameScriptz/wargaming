<?php
namespace Wargame\Mollwitz\Mollwitz;
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
use \Wargame\Battle;

class mollwitzVictoryCore extends \Wargame\Mollwitz\victoryCore
{
    public function save()
    {
        $ret = parent::save();
        $ret->victoryPoints = $this->victoryPoints;
        $ret->movementCache = $this->movementCache;
        $ret->gameOver = $this->gameOver;
        return $ret;
    }

    public function phaseChange()
    {
    }

    protected function checkVictory( $battle){
        global $force_name;
        /* @var \GameRules $gameRules */
        $gameRules = $battle->gameRules;
        $attackingId = $gameRules->attackingForceId;
        $turn = $gameRules->turn;
        if(!$this->gameOver){
            $prussianWin = $austrianWin = false;
            if($this->victoryPoints[Mollwitz::AUSTRIAN_FORCE] >= 35){
                $austrianWin = true;
                $reason = "Win on kills";
            }
            if($this->victoryPoints[Mollwitz::PRUSSIAN_FORCE] >= 35){
                $prussianWin = true;
                $reason = "Win on kills";
            }
            if($turn > 1){
                if($attackingId == Mollwitz::PRUSSIAN_FORCE &&  $this->isMollwitz()){
                    $prussianWin = true;
                    $reason = " Occupy Mollwitz";
                }
                if($attackingId == Mollwitz::AUSTRIAN_FORCE &&  $this->isNeudorf()){
                    $austrianWin = true;
                    $reason = " Occupy Neudorf";
                }
            }
            if($austrianWin && $prussianWin){
                $this->winner = 0;
                $austrianWin = $prussianWin = false;
                $this->gameOver = true;
                $gameRules->flashMessages[] = "Tie Game";
            }
            $force_name = $battle::getPlayerData($battle->scenario)['forceName'];

            if($austrianWin){
                $this->winner = Mollwitz::AUSTRIAN_FORCE;
                $gameRules->flashMessages[] = $force_name[Mollwitz::AUSTRIAN_FORCE]." $reason";
            }
            if($prussianWin){
                $this->winner = Mollwitz::PRUSSIAN_FORCE;
                $gameRules->flashMessages[] = $force_name[Mollwitz::PRUSSIAN_FORCE]. " $reason";
            }
            if($austrianWin || $prussianWin){
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
            if($turn > $gameRules->maxTurn){
                $this->gameOver = true;
                $gameRules->flashMessages[] = "Tie Game";
                return true;
            }
        }
        return false;
    }
    private function isMollwitz(){
        $mollwitz = [602,702];
        $b = Battle::getBattle();
        $force = $b->force;
        $units = $force->units;
        foreach($units as $unit){
            if($unit->forceId == Mollwitz::PRUSSIAN_FORCE && in_array($unit->hexagon->name, $mollwitz) ){
                return true;
            }
        }
        return false;
    }
    private function isNeudorf(){
        $neudorf = [911];
        $b = Battle::getBattle();
        $force = $b->force;
        $units = $force->units;
        foreach($units as $unit){
            if($unit->forceId == Mollwitz::AUSTRIAN_FORCE && in_array($unit->hexagon->name, $neudorf) ){
                return true;
            }
        }
        return false;
    }

    public function playerTurnChange($arg){
        global $force_name;
        $attackingId = $arg[0];
        $battle = Battle::getBattle();

        /* @var GameRules $gameRules */
        $gameRules = $battle->gameRules;
        $turn = $gameRules->turn;
        $gameRules->flashMessages[] = "@hide crt";

        if($this->checkVictory($battle)){
            return;
        }

            $gameRules->flashMessages[] = $force_name[$attackingId]." Player Turn";


    }
    public function postCombatResults($args){
        list($defenderId, $attackers, $combatResults, $dieRoll) = $args;
        $b = Battle::getBattle();
        foreach ($attackers as $attackerId => $val) {
            $unit = $b->force->units[$attackerId];
            if ($unit->class == "artillery" && $unit->status == STATUS_CAN_ADVANCE) {
                $unit->status = STATUS_ATTACKED;
            }
        }
    }
    public function calcFromAttackers(){
        $mapData = \Wargame\MapData::getInstance();

        $battle = Battle::getBattle();
        /* @var CombatRules $cR */
        $cR = $battle->combatRules;
        /* @var Force $force */
        $force = $battle->force;
        $force->clearRequiredCombats();
        $defenderForceId = $force->defendingForceId;
        foreach($cR->attackers as $attackId => $combatId){
            $mapHex = $mapData->getHex($force->getUnitHexagon($attackId)->name);
            $neighbors = $mapHex->neighbors;
            foreach($neighbors as $neighbor){
                /* @var MapHex $hex */
                $hex = $mapData->getHex($neighbor);
                if($hex->isOccupied($defenderForceId)){
                    $units = $hex->forces[$defenderForceId];
                    foreach($units as $unitId=>$unitVal){
                        $requiredVal = true;
                        $combatId = isset($cR->defenders->$unitId) ? $cR->defenders->$unitId : null;
                        if($combatId !== null){
                            $attackers = $cR->combats->$combatId->attackers;
                            if($attackers){
                                if(count((array)$attackers) > 0){
                                    $requiredVal = false;
                                }
                            }

                        }

                        $force->requiredDefenses->$unitId = $requiredVal;
                    }
                }
            }
        }
    }
    public function postUnsetAttacker($args){
        $this->calcFromAttackers();
        list($unit) = $args;
        $id = $unit->id;
    }
    public function postUnsetDefender($args){
        $this->calcFromAttackers();

        list($unit) = $args;
    }
    public function postSetAttacker($args){
        $this->calcFromAttackers();

        list($unit) = $args;
    }
    public function postSetDefender($args){
        $this->calcFromAttackers();

    }
}