<?php
namespace Wargame\Mollwitz\Jakobovo1812;
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



class Jakobovo1812 extends \Wargame\Mollwitz\JagCore
{

    const RUSSIAN_FORCE = 1;
    const FRENCH_FORCE = 2;

    public $specialHexesMap = ['SpecialHexA'=>2, 'SpecialHexB'=>2, 'SpecialHexC'=>0];

    static function getPlayerData($scenario){
        $forceName = ['Observer', "Russian", "French"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function save()
    {
        $data = parent::save();
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
            for ($i = 0; $i < $unitSet->num; $i++) {
                UnitFactory::create("infantry-1", $unitSet->forceId, "deployBox", "", $unitSet->combat, $unitSet->combat, $unitSet->movement, true, STATUS_CAN_DEPLOY, $unitSet->reinforce, 1, $unitSet->range, $unitSet->nationality, false, $unitSet->class);
            }
        }
    }

    function __construct($data = null, $arg = false, $scenario = false, $game = false)
    {

        parent::__construct($data, $arg, $scenario, $game);

        if ($data) {
            $this->specialHexA = $data->specialHexA;
            $this->specialHexB = $data->specialHexB;
        } else {
            $this->victory = new \Wargame\Victory('\Wargame\Mollwitz\Jakobovo1812\VictoryCore');
            $this->gameRules->setMaxTurn(9);
            $this->deployFirstMoveSecond();
        }
    }
}
