<?php
namespace Wargame\Mollwitz\HorseMusket;
use \Wargame\Mollwitz\UnitFactory;
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

class HorseMusket extends \Wargame\Mollwitz\JagCore
{


    public $specialHexesMap = [];

    static function getPlayerData($scenario){
        if(isset($scenario->playerInfo)){
            $forceName = $scenario->playerInfo;
            if($scenario->moveFirstDeploySecond){
                return \Wargame\Battle::register($forceName,
                    [$forceName[0], $forceName[2], $forceName[1]]);
            }else{
                return \Wargame\Battle::register($forceName, $forceName);
            }
        }
        $forceName = ['Observer', "Player One", "Player Two"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function save()
    {
        $data = parent::save();
        $data->specialHexesMap = $this->specialHexesMap;
        return $data;
    }


    function terrainGen($mapDoc, $terrainDoc){
        parent::terrainGen($mapDoc, $terrainDoc);
    }

    public function terrainInit($terrainDoc){
        parent::terrainInit($terrainDoc);
    }

    public function init()
    {
        $scenario = $this->scenario;
        $unitSets = $scenario->units;
        UnitFactory::$injector = $this->force;

        foreach($unitSets as $unitSet) {
            $reinforceTurn = 1;
            $unitHex = "deployBox";
            $status = STATUS_CAN_DEPLOY;
            if(isset($unitSet->reinforceTurn)){
                $reinforceTurn = $unitSet->reinforceTurn;
                $unitHex = "gameTurn$reinforceTurn";
                $status = STATUS_CAN_REINFORCE;
            }

            for ($i = 0; $i < $unitSet->num; $i++) {
                UnitFactory::create("infantry-1", $unitSet->forceId, $unitHex, "", $unitSet->combat, $unitSet->combat, $unitSet->movement, true, $status, $unitSet->reinforce ?? 1, $reinforceTurn, $unitSet->range, $unitSet->nationality, false, $unitSet->class);
            }
        }
    }

    function __construct($data = null, $arg = false, $scenario = false, $game = false)
    {

//        dd($scenario->victoryCore);
        parent::__construct($data, $arg, $scenario, $game);

        if ($data) {
            $this->specialHexA = $data->specialHexA;
            $this->specialHexB = $data->specialHexB;
            $this->specialHexesMap = $data->specialHexesMap;
        } else {
            $this->victory = new \Wargame\Victory($scenario->victoryCore);
            $this->gameRules->setMaxTurn($scenario->gameLength);
            $moveFirstDeploySecond = $scenario->moveFirstDeploySecond ?? false;
            if($moveFirstDeploySecond){
                $this->deployFirstMoveSecond();

            }else{
                $this->deployFirstMoveFirst();
            }
            if(isset( $scenario->specialHexesMap)){
                foreach($scenario->specialHexesMap as $k => $v){
                    $this->specialHexesMap[$k] = $v;
                }
            }
        }
    }
}
