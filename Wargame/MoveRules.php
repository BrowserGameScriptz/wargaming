<?php
namespace Wargame;
use \stdClass;
// moveRules.js

// Copyright (c) 2009-2011 Mark Butler
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

use \Wargame\Battle;
$numWalks = 0;
class MoveRules
{
    /* @var Force */
    public $force;
    /* @var Terrain */
    public $terrain;

    /* @var MapData */
    public $mapData;
    // local variables
    public $movingUnitId;
    public $anyUnitIsMoving;
    public $railMove;
    public $storm;
    protected $moves;
    protected $path;
    protected $moveQueue;
    public $stickyZoc;
    public $enterZoc = "stop";
    public $exitZoc = 0;
    public $noZocZoc = false;
    public $noZocZocOneHex = true;
    public $oneHex = true;
    public $zocBlocksRetreat = true;
    public $friendlyAllowsRetreat = false;
    public $stacking = 1;
    public $blockedRetreatDamages = false;
    public $noZoc = false;
    public $retreatCannotOverstack = false;
    public $moveCannotOverstack = false;
    public $turnHex = false;
    public $turnFacing = false;
    public $turnId = false;
    public $transitStacking = 1;
    /* usually used for a closure, it's the amount of enemies or greater you CANNOT stack with
     * so 1 means you can't stack with even 1 enemy. Use a closure here to allow for air units stacking with
     * enemy land units only, for example. and vice a versa.
     */
    public $enemyStackingLimit = 1;

    function save()
    {
        $data = new StdClass();
        foreach ($this as $k => $v) {
            if (is_object($v) && $k != "path" && $k != "moves") {
                continue;
            }
            $data->$k = $v;
        }
        return $data;
    }

    function __construct($Force, $Terrain, $data = null)
    {
        // Class references

        $this->mapData = MapData::getInstance();
        $this->moves = new stdClass();
        $this->path = new stdClass();
        $this->force = $Force;
        $this->terrain = $Terrain;

        if ($data) {
            foreach ($data as $k => $v) {
                $this->$k = $v;
            }
        } else {
            $this->movingUnitId = NONE;
            $this->anyUnitIsMoving = false;
            $this->storm = false;
            $this->railMove = true;
            $this->stickyZoc = false;
        }
    }

    public function inject($force, $terrain){
        $this->force = $force;
        $this->terrain = $terrain;
    }

    public function movesLeft(){
        return false;
    }

    function turnLeft($isDeploy = false){
        if ($this->anyUnitIsMoving) {
            $movingUnit = $this->force->units[$this->movingUnitId];
            $movesLeft = $movingUnit->maxMove - $movingUnit->moveAmountUsed;
            $turnCost = 1;
            $origFacing = $movingUnit->facing;
            if($isDeploy || $movesLeft >= $turnCost){


                if($isDeploy){
                    $movingUnit->facing--;
                    if($movingUnit->facing < 0){
                        $movingUnit->facing += 6;
                    }
                    return true;
                }
                $battle = Battle::getBattle();
                $mapHex = $battle->mapData->getHex($movingUnit->hexagon->name);
                if($movingUnit->hexagon->name === $this->turnHex && $this->rightOf($origFacing, $this->turnFacing)){
                    $turnCost =  0 - $turnCost;
                }

                $movingUnit->updateFacingStatus(-1, $turnCost);
                if($this->turnHex === false || $movingUnit->hexagon->name !== $this->turnHex || $movingUnit->id !== $this->turnId){
                    $this->turnHex = $movingUnit->hexagon->name;
                    $this->turnFacing = $origFacing;
                    $this->turnId = $movingUnit->id;
                }

                if ($mapHex->isZoc($this->force->defendingForceId) == true) {
                    if ($this->enterZoc === "stop") {
                        $this->stopMove($movingUnit);
                        return true;
                    }
                }
                if($movingUnit->moveAmountUsed >= $movingUnit->maxMove){
                    $this->stopMove($movingUnit);
                    return true;
                }
                $this->calcMove($this->movingUnitId, false);
                return true;
            }
            return false;
        }
        return false;
    }

    function turnAbout($isDeploy = false){
        if ($this->anyUnitIsMoving) {
            $movingUnit = $this->force->units[$this->movingUnitId];
            $movesLeft = $movingUnit->maxMove - $movingUnit->moveAmountUsed;
            $turnCost = 2;
            if($isDeploy || $movesLeft >= $turnCost){

                if($isDeploy){
                    $movingUnit->facing += 3;
                    if($movingUnit->facing >= 6){
                        $movingUnit->facing -= 6;
                    }
                    return true;
                }
                $battle = Battle::getBattle();
                $mapHex = $battle->mapData->getHex($movingUnit->hexagon->name);
                $movingUnit->updateFacingStatus(3, $turnCost);
                if ($mapHex->isZoc($this->force->defendingForceId) == true) {
                    if ($this->enterZoc === "stop") {
                        $this->stopMove($movingUnit);
                        return true;
                    }
                }
                if($movingUnit->moveAmountUsed >= $movingUnit->maxMove){
                    $this->stopMove($movingUnit);
                    return true;
                }
                $this->calcMove($this->movingUnitId, false);
                return true;
            }
            return false;
        }
        return false;

    }

    function leftOf($newCourse, $prevCourse){

        if(($newCourse + 1) % 6 === $prevCourse ){
            return true;
        }
        if(($newCourse + 2) % 6 === $prevCourse ){
            return true;
        }
        return false;
    }

    function rightOf($newCourse, $prevCourse){

        if(($prevCourse + 1) % 6 === $newCourse ){
            return true;
        }
        if(($prevCourse + 2) % 6 === $newCourse ){
            return true;
        }
        return false;
    }

    function turnRight($isDeploy = false){
        if ($this->anyUnitIsMoving) {
            $movingUnit = $this->force->units[$this->movingUnitId];
            $movesLeft = $movingUnit->maxMove - $movingUnit->moveAmountUsed;
            $origFacing = $movingUnit->facing;
            $turnCost = 1;
            if($isDeploy || $movesLeft >= $turnCost){

                if($isDeploy){
                    $movingUnit->facing++;
                    if($movingUnit->facing >= 6){
                        $movingUnit->facing -= 6;
                    }
                    return true;
                }

                if($movingUnit->hexagon->name === $this->turnHex && $this->leftOf($origFacing, $this->turnFacing)){
                    $turnCost =  0 - $turnCost;
                }
                $movingUnit->updateFacingStatus(1, $turnCost);
                if($this->turnHex === false || $movingUnit->hexagon->name !== $this->turnHex || $movingUnit->id !== $this->turnId){
                    $this->turnHex = $movingUnit->hexagon->name;
                    $this->turnFacing = $origFacing;
                    $this->turnId = $movingUnit->id;
                }

                $battle = Battle::getBattle();
                $mapHex = $battle->mapData->getHex($movingUnit->hexagon->name);
                if ($mapHex->isZoc($this->force->defendingForceId) == true) {
                    if ($this->enterZoc === "stop") {
                        $this->stopMove($movingUnit);
                        return true;
                    }
                }
                if($movingUnit->moveAmountUsed >= $movingUnit->maxMove){
                    $this->stopMove($movingUnit);
                    return true;
                }
                $this->calcMove($this->movingUnitId, false);
                return true;
            }
            return false;
        }
        return false;

    }
    public function loadUnit(){
        if ($this->anyUnitIsMoving) {

            $unit = $this->force->getUnit($this->moveRules->movingUnitId);
            if($unit->canBeTransported()){
                $unit->status = STATUS_LOADING;
            }

        }
    }



// id will be map if map event, id will be unit id if counter event
    function moveUnit($eventType, $id, $hexagon, $turn)
    {
        $dirty = false;
        $this->turnHex = false;
        $this->turnFacing = false;
        if ($eventType == SELECT_MAP_EVENT) {
            if ($this->anyUnitIsMoving) {
                // click on map, so try to move
                /* @var Unit $movingUnit */
                $movingUnit = $this->force->units[$this->movingUnitId];
                if ($movingUnit->unitIsMoving() == true) {
                    $newHex = $hexagon;

                    if($movingUnit->airMovement){
                        $ret = $this->airMove($movingUnit, $newHex);
                        if($ret){
                            $this->stopMove($movingUnit);
                            return true;
                        }
                        return false;
                    }
                    if ($this->moves->$newHex) {
                        $this->path = $this->moves->$newHex->pathToHere;

                        foreach ($this->path as $moveHex) {
                            $this->move($movingUnit, $moveHex);
                        }
                        $movesLeft = $this->moves->$newHex->pointsLeft;
                        if(isset($this->moves->$newHex->facing)) {
                            $facing = $this->moves->$newHex->facing;
                            $movingUnit->facing = $facing;
                        }
                        $this->moves = new stdClass();

                        $this->move($movingUnit, $newHex);
                        $this->path = array();
                        if ($this->anyUnitIsMoving) {
                            $this->moveQueue = array();
                            $hexPath = new HexPath();
                            $hexPath->name = $newHex; //$startHex->name;
                            $hexPath->pointsLeft = $movesLeft;
                            $hexPath->pathToHere = array();
                            $hexPath->firstHex = false;
                            $hexPath->isOccupied = true;
                            if(isset($facing)){
                                $hexPath->facing = $facing;
                            }

                            $this->moveQueue[] = $hexPath;
                            $this->bfsMoves();

                            $movesAvail = 0;
                            foreach ($this->moves as $move) {
                                if ($move->isOccupied || !$move->isValid) {
                                    continue;
                                }
                                $movesAvail++;
                            }

                            if ($movesAvail === 0) {
                                $this->stopMove($movingUnit);
                            }
                        }
                        $dirty = true;
                    }
                }
                if ($movingUnit->unitIsReinforcing($this->movingUnitId) == true) {
                    $this->reinforce($movingUnit, new Hexagon($hexagon));
                    $this->calcMove($id);
                    $dirty = true;
                }
                if ($movingUnit->unitIsDeploying() == true) {
                    $this->deploy($movingUnit, new Hexagon($hexagon));
                    $dirty = true;
                }
            }
        } else // click on a unit
        {
            if ($this->anyUnitIsMoving == true) {
                if ($id == $this->movingUnitId) {
                    $movingUnit = $this->force->getUnit($id);
                    // clicked on moving or reinforcing unit
                    /* @var Unit $movingUnit */
                    if ($movingUnit->unitIsMoving() == true) {
                        $this->stopMove($movingUnit);
                        $dirty = true;
                    }
                    if ($movingUnit->unitIsReinforcing($id) == true) {
                        $this->stopReinforcing($movingUnit);
                        $dirty = true;
                    }
                    if ($movingUnit->unitIsDeploying() == true) {
                        $this->stopDeploying($movingUnit);
                        $dirty = true;
                    }
                } else {
                    /* @var Unit $movingUnit */
                    $movingUnit = $this->force->getUnit($this->movingUnitId);
                    $movingUnitId = $this->movingUnitId;
                    if ($movingUnit->unitIsMoving() == true) {
                        $this->stopMove($movingUnit);
                        $dirty = true;
                    }
                    if ($movingUnit->unitIsReinforcing($movingUnitId) == true) {
                        $this->stopReinforcing($movingUnit);
                        $dirty = true;
                    }
                    if ($movingUnit->unitIsDeploying() == true) {
                        $this->stopDeploying($movingUnit);
                        $dirty = true;
                    }

                    if ($eventType == KEYPRESS_EVENT) {
                        if ($this->force->unitCanMove($movingUnitId) == true) {
                            $this->startMoving($movingUnitId);
                            $this->calcMove($movingUnitId);
                            $dirty = true;
                        }
                    } else {
                        if ($this->force->unitCanMove($id) == true) {
                            $this->startMoving($id);
                            $this->calcMove($id);
                            $dirty = true;
                        }
                        if ($this->force->unitCanReinforce($id) == true) {
                            $this->startReinforcing($id, $turn);
                            $dirty = true;
                        }
                        if ($this->force->unitCanDeploy($id) == true) {
                            $this->startDeploying($id, $turn);
                            $dirty = true;
                        }
                    }
                    // clicked on another unit
                    return $dirty;
//                    $this->moveOver($this->movingUnitId, $id, $hexagon);
                }
            } else {
                // no one is moving, so start new move
                if ($this->force->unitCanMove($id) == true) {
                    $this->startMoving($id);
                    $this->calcMove($id);
                    $dirty = true;
                }
                if ($this->force->unitCanReinforce($id) == true) {
                    $this->startReinforcing($id, $turn);
                    $dirty = true;
                }
                if ($this->force->unitCanDeploy($id) == true) {
                    $this->startDeploying($id, $turn);
                    $dirty = true;
                }
            }
        }
        return $dirty;
    }
    function selectUnit($eventType, $id, $hexagon, $turn)
    {
        $dirty = false;
        if ($eventType == SELECT_MAP_EVENT) {
           return false;
        } else // click on a unit
        {
            if ($this->anyUnitIsMoving == true) {
                if ($id == $this->movingUnitId) {
                    $movingUnit = $this->force->getUnit($id);
                    // clicked on moving or reinforcing unit
                    /* @var Unit $movingUnit */
                    if ($movingUnit->unitIsMoving() == true) {
                        $this->stopMove($movingUnit);
                        $dirty = true;
                    }
                    if ($movingUnit->unitIsReinforcing($id) == true) {
                        $this->stopReinforcing($movingUnit);
                        $dirty = true;
                    }
                    if ($movingUnit->unitIsDeploying() == true) {
                        $this->stopDeploying($movingUnit);
                        $dirty = true;
                    }
                } else {
                    /* @var Unit $movingUnit */
                    $movingUnit = $this->force->getUnit($this->movingUnitId);
                    $movingUnitId = $this->movingUnitId;
                    if ($movingUnit->unitIsMoving() == true) {
                        $this->stopMove($movingUnit);
                        $dirty = true;
                    }
                    if ($this->force->unitCanMove($id) == true) {
                        $this->startMoving($id);
                        $dirty = true;
                    }


                    // clicked on another unit
                    return $dirty;
//                    $this->moveOver($this->movingUnitId, $id, $hexagon);
                }
            } else {
                // no one is moving, so start new move
                if ($this->force->unitCanMove($id) == true) {
                    $this->startMoving($id);
                    $dirty = true;
                }

            }
        }
        return $dirty;
    }

    function calcSupplyHex($startHex, $goal, $bias = array(), $attackingForceId = false, $maxHex = false)
    {
        $this->moves = new stdClass();
        $this->moveQueue = array();
        $hexPath = new HexPath();
        $hexPath->name = $startHex;
        $hexPath->pathToHere = array();
        $hexPath->firstHex = true;
        $hexPath->isOccupied = true;
        if($maxHex !== false){
            $hexPath->pointsLeft = $maxHex;
            $maxHex = true;
        }
        $this->moveQueue[] = $hexPath;
        $ret = $this->bfsCommunication($goal, $bias, $attackingForceId, $maxHex);
        $this->moves = new stdClass();
        $this->moveQueue = array();
        return $ret;
    }

    function calcRoadSupply($forceId, $goal, $bias = array())
    {
        $attackingForceId = $forceId;

        $this->moves = new stdClass();
        $this->moveQueue = array();
        if(!is_array($goal)){
            $goals = [$goal];
        }else{
            $goals = $goal;
        }
        foreach($goals as $aGoal){
            $hexPath = new HexPath();
            $hexPath->name = $aGoal;
            $hexPath->pathToHere = array();
            $hexPath->firstHex = true;
            $hexPath->isOccupied = true;
            $this->moveQueue[] = $hexPath;
        }
        $ret = $this->bfsRoadTrace($goal, $bias, $attackingForceId);
        $moves = $this->moves;
        $goal = [];
        foreach($moves as $hex => $move){
            $goal[] = $hex;
        }
        $this->moves = new stdClass();
        $this->moveQueue = array();
        return $goal;
    }
    function calcSupply($id, $goal, $bias = array(), $maxHex = false)
    {
        global $numWalks;
        global $numBangs;
        $attackingForceId = $this->force->units[$id]->forceId;
        $startHex = $this->force->units[$id]->hexagon;
        return $this->calcSupplyHex($startHex->name, $goal, $bias, $attackingForceId, $maxHex);
    }

    function calcMove($id, $firstHex = true)
    {
        global $numWalks;
        global $numBangs;
        $unit = $this->force->units[$id];
        $numWalks = 0;
        $numBangs = 0;
        $startHex = $unit->hexagon;
        $movesLeft = $unit->getMaxMove() - $unit->moveAmountUsed;
        $this->moves = new stdClass();
        $this->moveQueue = array();
        $hexPath = new HexPath();
        $hexPath->name = $startHex->name;
        $hexPath->pointsLeft = $movesLeft;
        $hexPath->pathToHere = array();
        $hexPath->firstHex = $firstHex;
        $hexPath->isOccupied = true;
        if(isset($this->force->units[$id]->facing)){
            $hexPath->facing = $this->force->units[$id]->facing;
        }
        $this->moveQueue[] = $hexPath;
        $this->bfsMoves();

    }

    function calcRetreat($id)
    {
        global $numWalks;
        global $numBangs;
        $numWalks = 0;
        $numBangs = 0;
        $done = false;
        $startHex = $this->force->units[$id]->hexagon;
        $movesLeft = $this->force->units[$id]->retreatCountRequired;
        do{
            $this->moves = new stdClass();
            $this->moveQueue = array();
            $hexPath = new HexPath();
            $hexPath->name = $startHex->name;
            $hexPath->pointsLeft = $movesLeft;
            $hexPath->pathToHere = array();
            $hexPath->firstHex = true;
            $hexPath->isOccupied = true;
            $this->moveQueue[] = $hexPath;
            $this->bfsRetreat();
            $moves = $this->moves;
            $validCount = 0;
            foreach($moves as $key => $val){
                if($moves->$key->pointsLeft){
                    unset($moves->$key);
                    continue;
                }
                if($moves->$key->isOccupied === false){
                    $validCount++;
                }
            }
            /* no possible retreats */
            if(count((array)$this->moves) === 0){
                $this->force->addToRetreatHexagonList($id, $startHex);
                $this->movingUnitId = NONE;
                $this->anyUnitIsMoving = false;
                $this->moves = new stdClass();
                if($this->blockedRetreatDamages){
                    if($this->force->units[$id]->damageUnit()) {
                        $this->force->eliminateUnit($id);
                    }else{
                        $this->force->units[$id]->setStatus(STATUS_STOPPED);
                        $this->force->clearAdvancing();
                    }
                }else{
                    $this->force->eliminateUnit($id);
                }

                $done = true;
                return;
            }

            if($validCount > 0){
                $done = true;
            }else{
                $movesLeft++;
                /* fail safe for strange things */
                if($this->retreatCannotOverstack || $movesLeft > 12){
                    $this->force->addToRetreatHexagonList($id, $startHex);
                    $this->movingUnitId = NONE;
                    $this->anyUnitIsMoving = false;
                    $this->moves = new stdClass();
                    if($this->blockedRetreatDamages){
                        if($this->force->units[$id]->damageUnit()) {
                            $this->force->eliminateUnit($id);
                        }else{
                            $this->force->units[$id]->setStatus(STATUS_STOPPED);
                            $this->force->clearAdvancing();
                        }
                    }else{
                        $this->force->eliminateUnit($id);
                    }
                    $done = true;
                }
            }

        }while($done === false);


    }

    function unlimitedMoves(){
        if($this->moveQueue[0]->pointsLeft === 0){
            return;
        }
        $unit = $this->force->units[$this->movingUnitId];

        for($x = 1; $x <= 21; $x++){
            for($y=1;$y <= 29;$y++){
                $hexNum = sprintf("%02d%02d", $x, $y);

                $hexPath = new HexPath();
                $hexPath->name = $hexNum;
                $hexPath->pathToHere = [];
                $hexPath->pointsLeft = 0;

                /* @var MapHex $mapHex */
                $mapHex = $this->mapData->getHex($hexNum);

                if ($mapHex->isOccupied($this->force->attackingForceId, $this->stacking, $unit)) {
//                    $this->moves->$hexNum->isOccupied = true;
                    continue;
                }

                if ($mapHex->isOccupied($this->force->defendingForceId,$this->enemyStackingLimit, $unit)) {
//                    $this->moves->$hexNum->isValid = false;
                    continue;
                }
                $this->moves->$hexNum = $hexPath;



            }
        }


    }
    function bfsMoves()
    {
        $hist = array();
        $cnt = 0;
        $unit = $this->force->units[$this->movingUnitId];
        if($unit->airMovement){
            $this->unlimitedMoves();
//            $this->airMoves();
            return;
        }
        while (count($this->moveQueue) > 0) {
            $cnt++;
            $hexPath = array_shift($this->moveQueue);
            $hexNum = $hexPath->name;
            $movePoints = $hexPath->pointsLeft;
            if (!$hexNum) {
                continue;
            }
            if (!isset($this->moves->$hexNum)) {
                /* first time here */
                $this->moves->$hexNum = $hexPath;
            } else {
                /* invalid hex */
                if ($this->moves->$hexNum->isValid === false) {
                    continue;
                }
                /* already been here with more points */
                if ($this->moves->$hexNum->pointsLeft >= $movePoints) {
                    continue;

                }
            }
            /* @var MapHex $mapHex */
            $mapHex = $this->mapData->getHex($hexNum);

            if ($mapHex->isOccupied($this->force->attackingForceId, $this->stacking, $unit)) {
                $this->moves->$hexNum->isOccupied = true;
            }

            if ($mapHex->isOccupied($this->force->defendingForceId,$this->enemyStackingLimit, $unit)) {
                $this->moves->$hexNum->isValid = false;
                continue;
            }
            $this->moves->$hexNum->pointsLeft = $movePoints;
            $this->moves->$hexNum->pathToHere = $hexPath->pathToHere;

            if ($this->moves->$hexNum->isZoc == NULL) {
                $this->moves->$hexNum->isZoc = $this->force->mapHexIsZOC($mapHex);
            }
            $exitCost = 0;
            if ($this->moves->$hexNum->isZoc) {
                if (is_numeric($this->exitZoc)) {
                    $exitCost += $this->exitZoc;
                }
                if (!$hexPath->firstHex) {
                    if ($this->enterZoc === "stop") {
                        continue;
                    }
                }

            }
            $path = $hexPath->pathToHere;
            $path[] = $hexNum;

            $neighbors = $mapHex->neighbors;
            $backupHexNum = false;
            $behind = false;
            if(isset($hexPath->facing)){
                $newFacing = $hexPath->facing;
                /*
                 * Front 3 hexes kept just in case game designer chnages his mind.
                 * $neighbors = array_slice(array_merge($mapHex->neighbors,$mapHex->neighbors), ($hexPath->facing + 6 - 1)%6, 3);
                 */

                /*
                 * Just the front facing hex
                 */
                $neighbors = array_slice($mapHex->neighbors, $hexPath->facing, 1);
                /* first hex can do backup move */
                if($hexPath->firstHex === true){
                    $behind = $hexPath->facing + 3;
                    $behind %= 6;
                    $backupHexNum = $neighbors[] = $mapHex->neighbors[$behind];
                }

            }
            $curHex = Hexagon::getHexPartXY($hexNum);

            foreach ($neighbors as $neighbor) {
                $newHexNum = $neighbor;
                $gnuHex = Hexagon::getHexPartXY($newHexNum);
                if (!$gnuHex) {
                    continue;
                }

                /* This can and should be dealt with by the "blocked" moveAmount below
                 * History, can't live with it can't live with it
                 */
                if ($this->terrain->terrainIsHexSide($hexNum, $newHexNum, "blocked")) {
                    continue;
                }
                if (!$unit->forceMarch && $this->terrain->terrainIsHexSide($hexNum, $newHexNum, "blocksnonroad")) {
                    continue;
                }
                if ($this->terrain->terrainIsXY($gnuHex[0], $gnuHex[1], "offmap")) {

                    continue;
                }
                $moveAmount = $this->terrain->getTerrainMoveCostXY($curHex[0], $curHex[1], $gnuHex[0], $gnuHex[1], $unit->forceMarch, $unit);
                if ($moveAmount === "blocked") {
                    continue;
                }
                $moveAmount += $exitCost;
                $newMapHex = $this->mapData->getHex($newHexNum);

                if ($newMapHex->isOccupied($this->force->defendingForceId, $this->enemyStackingLimit, $unit)) {
                    continue;
                }

                if ($this->moveCannotOverstack  && $newMapHex->isOccupied($this->force->attackingForceId, $this->transitStacking, $unit)) {
                    continue;
                }

                $isZoc = $this->force->mapHexIsZOC($newMapHex);
                if($isZoc && $this->noZoc){
                    continue;
                }
                if ($isZoc && is_numeric($this->enterZoc)) {
                    $moveAmount += (int)$this->enterZoc;
                }
                if ($moveAmount <= 0) {
                    $moveAmount = 1;
                }
                if ($this->noZocZoc && $isZoc && $hexPath->isZoc) {
                    continue;
                }
                /*
                 * TODO order is important in if statement check if doing zoc zoc move first then if just one hex move.
                 * Then check if oneHex and firstHex
                 */
                if($moveAmount <= $movePoints && $behind !== false && $newHexNum === $backupHexNum){
                    $moveAmount = $movePoints;
                }
                if ($movePoints - $moveAmount >= 0 || (($isZoc && $hexPath->isZoc && !$this->noZocZocOneHex) && $hexPath->firstHex === true) || ($hexPath->firstHex === true && $this->oneHex === true && !($isZoc && $hexPath->isZoc && !$this->noZocZoc))) {
                    $head = false;
                    if (isset($this->moves->$newHexNum)) {
                        if ($this->moves->$newHexNum->pointsLeft > ($movePoints - $moveAmount)) {
                            continue;
                        }
                        $head = true;
                    }
                    $newPath = new HexPath();
                    $newPath->name = $newHexNum;
                    $newPath->pathToHere = $path;
                    $newPath->pointsLeft = $movePoints - $moveAmount;

                    if(isset($newFacing)){
                        $newPath->facing = $newFacing;
                    }

                    if ($newPath->pointsLeft < 0) {
                        $newPath->pointsLeft = 0;
                    }
                    if ($this->exitZoc === "stop" && $hexPath->isZoc) {
                        $newPath->pointsLeft = 0;
                    }
                     if ($head) {
                        array_unshift($this->moveQueue, $newPath);
                    } else {
                        $this->moveQueue[] = $newPath;

                    }
                }
            }

        }
        return;
    }

    function airMoves()
    {
        $hist = array();
        $cnt = 0;
        $unit = $this->force->units[$this->movingUnitId];
        while (count($this->moveQueue) > 0) {
            $cnt++;
            $hexPath = array_shift($this->moveQueue);
            $hexNum = $hexPath->name;
            $movePoints = $hexPath->pointsLeft;
            if (!$hexNum) {
                continue;
            }
            if (!isset($this->moves->$hexNum)) {
                /* first time here */
                $this->moves->$hexNum = $hexPath;
            } else {
                /* invalid hex */
                if ($this->moves->$hexNum->isValid === false) {
                    continue;
                }
                /* already been here with more points */
                if ($this->moves->$hexNum->pointsLeft >= $movePoints) {
                    continue;

                }
            }
            /* @var MapHex $mapHex */
            $mapHex = $this->mapData->getHex($hexNum);

            if ($mapHex->isOccupied($this->force->attackingForceId, $this->stacking, $unit)) {
                $this->moves->$hexNum->isOccupied = true;
            }

            if ($mapHex->isOccupied($this->force->defendingForceId,$this->enemyStackingLimit, $unit)) {
                $this->moves->$hexNum->isValid = false;
                continue;
            }
            $this->moves->$hexNum->pointsLeft = $movePoints;
            $this->moves->$hexNum->pathToHere = $hexPath->pathToHere;

            $path = $hexPath->pathToHere;
            $path[] = $hexNum;


            $neighbors = $mapHex->neighbors;

            foreach ($neighbors as $neighbor) {
                $newHexNum = $neighbor;
                $gnuHex = Hexagon::getHexPartXY($newHexNum);
                if (!$gnuHex) {
                    continue;
                }

//                if ($this->terrain->terrainIsXY($gnuHex[0], $gnuHex[1], "offmap")) {
//                    continue;
//                }

                $newMapHex = $this->mapData->getHex($newHexNum);

                if ($newMapHex->isOccupied($this->force->defendingForceId, $this->enemyStackingLimit, $unit)) {
                    continue;
                }

                $moveAmount = 1;

                /*
                 * TODO order is important in if statement check if doing zoc zoc move first then if just one hex move.
                 * Then check if oneHex and firstHex
                 */
                if ($movePoints - $moveAmount >= 0 ) {
                    $head = false;
                    if (isset($this->moves->$newHexNum)) {
                        if ($this->moves->$newHexNum->pointsLeft > ($movePoints - $moveAmount)) {
                            continue;
                        }
                        $head = true;
                    }
                    $newPath = new HexPath();
                    $newPath->name = $newHexNum;
                    $newPath->pathToHere = $path;
                    $newPath->pointsLeft = $movePoints - $moveAmount;

                    if ($newPath->pointsLeft < 0) {
                        $newPath->pointsLeft = 0;
                    }

                    if ($head) {
                        array_unshift($this->moveQueue, $newPath);
                    } else {
                        $this->moveQueue[] = $newPath;

                    }
                }
            }

        }
        return;
    }

    function bfsRetreat()
    {

        $id = $this->movingUnitId;
        $unit = $this->force->units[$this->movingUnitId];

        if($unit->forceId == $this->force->attackingForceId){
            /* attacker retreating leave sides normal */
            $attackingForceId = $this->force->attackingForceId;
            $defendingForceId = $this->force->defendingForceId;
        }else{
            /* Reverse attack and defender for defender retreats (retreating units are moving) */
            /* Reverse attack and defender for defender retreats (retreating units are moving) */
            $defendingForceId = $this->force->attackingForceId;
            $attackingForceId = $this->force->defendingForceId;
        }


        $cnt = 0;
        while (count($this->moveQueue) > 0) {


            $cnt++;
            $hexPath = array_shift($this->moveQueue);
            $hexNum = $hexPath->name;
            $movePoints = $hexPath->pointsLeft;

            if (!$hexNum) {
                continue;
            }

            if (!isset($this->moves->$hexNum)) {
                /* first time here */
                $this->moves->$hexNum = $hexPath;

            } else {
                /* invalid hex */
                if ($this->moves->$hexNum->isValid === false) {
                    continue;
                }
                /* been here, done that */
                continue;
            }
            /* @var MapHex $mapHex */
            $mapHex = $this->mapData->getHex($hexNum);

            if ($mapHex->isOccupied($attackingForceId, $this->stacking, $unit)) {
                $this->moves->$hexNum->isOccupied = true;
            }
            if ($mapHex->isOccupied($defendingForceId,$this->enemyStackingLimit, $unit)) {
                $this->moves->$hexNum->isValid = false;
                continue;
            }
            $this->moves->$hexNum->pointsLeft = $movePoints;
            $this->moves->$hexNum->pathToHere = $hexPath->pathToHere;

//            if ($this->moves->$hexNum->isZoc == NULL) {
//                $this->moves->$hexNum->isZoc = $this->force->mapHexIsZOC($mapHex, $defendingForceId);
//            }
//            if ((!$hexPath->firstHex) && $this->moves->$hexNum->isZoc) {
//                continue;
//            }
            $path = $hexPath->pathToHere;
            $path[] = $hexNum;

            for ($i = 1; $i <= 6; $i++) {
                $newHexNum = $mapHex->neighbors[$i - 1];
                if($this->hexagonBlocksRetreat($id, new Hexagon($hexNum), new Hexagon($newHexNum))){
                    continue;
                }
//                $newMapHex = $this->mapData->getHex($newHexNum);
//
//                if ($this->force->mapHexIsZOC($newMapHex, $defendingForceId)){
//                    continue;
//                }
//
//                $gnuHex = Hexagon::getHexPartXY($newHexNum);
//                if (!$gnuHex) {
//                    continue;
//                }
//                if ($this->terrain->terrainIsHexSide($hexNum, $newHexNum, "blocked")) {
//                    continue;
//                }
//
//                if ($this->terrain->terrainIsXY($gnuHex[0], $gnuHex[1], "offmap")) {
//                    continue;
//                }
//                if ($this->terrain->terrainIsXY($gnuHex[0], $gnuHex[1], "blocked")) {
//                    continue;
//                }
//                $newMapHex = $this->mapData->getHex($newHexNum);
//                if ($newMapHex->isOccupied($defendingForceId)) {
//                    continue;
//                }
                /*
                 * TODO order is important in if statement check if doing zoc zoc move first then if just one hex move.
                 * Then check if oneHex and firstHex
                 */
                if($movePoints - 1 < 0){
                    continue;
                }
                $head = false;

                if (isset($this->moves->$newHexNum)) {
                    if($this->moves->$newHexNum->pointsLeft > ($movePoints - 1) ){
                        continue;
                    }
                }
                $newPath = new HexPath();
                $newPath->name = $newHexNum;
                $newPath->pathToHere = $path;
                $newPath->pointsLeft = $movePoints - 1;
                $this->moveQueue[] = $newPath;
            }
        }
        return false;
    }

    function bfsCommunication($goal, $bias, $attackingForceId = false, $maxHex = false)
    {
        $goalArray = array();
        if (is_array($goal)) {
            foreach ($goal as $key => $val) {
                $goalArray[$val] = true;
            }
        } else {
            $goalArray[$goal] = true;
        }
        if ($attackingForceId !== false) {
            $defendingForceId = $this->force->Enemy($attackingForceId);
        } else {
            $attackingForceId = $this->force->attackingForceId;
            $defendingForceId = $this->force->defendingForceId;
        }

        $cnt = 0;
        while (count($this->moveQueue) > 0) {

            $cnt++;
            $hexPath = array_shift($this->moveQueue);
            $hexNum = $hexPath->name;
            if($maxHex !== false){
                $movePoints = $hexPath->pointsLeft;
            }
            if (!$hexNum) {
                continue;
            }
            if (!empty($goalArray[$hexNum])) {
                return true;
            }
            if (!isset($this->moves->$hexNum)) {
                /* first time here */
                $this->moves->$hexNum = $hexPath;

            } else {
                /* invalid hex */
                if ($this->moves->$hexNum->isValid === false) {
                    continue;
                }
                /* been here, done that */
                continue;
            }
            /* @var MapHex $mapHex */
            $mapHex = $this->mapData->getHex($hexNum);

            if ($mapHex->isOccupied($attackingForceId)) {
                $this->moves->$hexNum->isOccupied = true;
            }
            if ($mapHex->isOccupied($defendingForceId)) {
                $this->moves->$hexNum->isValid = false;
                continue;
            }
            if($maxHex !== false){
                $this->moves->$hexNum->pointsLeft = $movePoints;
            }
            $this->moves->$hexNum->pathToHere = $hexPath->pathToHere;

            if ($this->moves->$hexNum->isZoc == NULL) {
                $this->moves->$hexNum->isZoc = $this->force->mapHexIsZOC($mapHex, $defendingForceId);
            }
            if ($this->moves->$hexNum->isZoc) {
                if (!$this->moves->$hexNum->isOccupied) {
                    continue;
                }

            }
            $path = $hexPath->pathToHere;
            $path[] = $hexNum;

            for ($i = 1; $i <= 6; $i++) {
                $newHexNum = $mapHex->neighbors[$i - 1];
                $gnuHex = Hexagon::getHexPartXY($newHexNum);
                if (!$gnuHex) {
                    continue;
                }
                if ($this->terrain->terrainIsHexSide($hexNum, $newHexNum, "blocked")) {
                    continue;
                }

                if ($this->terrain->terrainIsXY($gnuHex[0], $gnuHex[1], "offmap")) {
                    continue;
                }
                if ($this->terrain->terrainIsXY($gnuHex[0], $gnuHex[1], "blocked")) {
                    continue;
                }
                $newMapHex = $this->mapData->getHex($newHexNum);
                if ($newMapHex->isOccupied($defendingForceId)) {
                    continue;
                }
                /*
                 * TODO order is important in if statement check if doing zoc zoc move first then if just one hex move.
                 * Then check if oneHex and firstHex
                 */
                if($maxHex !== false && $movePoints - 1 < 0){
                    continue;
                }
                $head = false;
                if (!empty($bias[$i])) {
                    $head = true;
                }
                if (isset($this->moves->$newHexNum)) {
                        if($maxHex !== false){
                            if($this->moves->$newHexNum->pointsLeft > ($movePoints - 1) ){
                                continue;
                            }
                        }else{
                            continue;
                        }
                }
                $newPath = new HexPath();
                $newPath->name = $newHexNum;
                $newPath->pathToHere = $path;
                if($maxHex !== false){
                    $newPath->pointsLeft = $movePoints - 1;
                }
                if ($head) {
                    array_unshift($this->moveQueue, $newPath);
                } else {
                    $this->moveQueue[] = $newPath;

                }
            }
        }
        return false;
    }

    function bfsRoadTrace($goal, $bias, $attackingForceId = false, $maxHex = false)
    {
        $goalArray = array();
        if (is_array($goal)) {
            foreach ($goal as $key => $val) {
                $goalArray[$val] = true;
            }
        } else {
            $goalArray[$goal] = true;
        }
        if ($attackingForceId !== false) {
            $defendingForceId = $this->force->Enemy($attackingForceId);
        } else {
            $attackingForceId = $this->force->attackingForceId;
            $defendingForceId = $this->force->defendingForceId;
        }

        $cnt = 0;
        while (count($this->moveQueue) > 0) {

            $cnt++;
            $hexPath = array_shift($this->moveQueue);
            $hexNum = $hexPath->name;
            if($maxHex !== false){
                $movePoints = $hexPath->pointsLeft;
            }
            if (!$hexNum) {
                continue;
            }

            if (!isset($this->moves->$hexNum)) {
                /* first time here */
                $this->moves->$hexNum = $hexPath;

            } else {
                /* invalid hex */
                if ($this->moves->$hexNum->isValid === false) {
                    continue;
                }
                /* been here, done that */
                continue;
            }
            /* @var MapHex $mapHex */
            $mapHex = $this->mapData->getHex($hexNum);

            if ($mapHex->isOccupied($attackingForceId)) {
                $this->moves->$hexNum->isOccupied = true;
            }
            if ($mapHex->isOccupied($defendingForceId)) {
                $this->moves->$hexNum->isValid = false;
                continue;
            }
            if($maxHex !== false){
                $this->moves->$hexNum->pointsLeft = $movePoints;
            }
            $this->moves->$hexNum->pathToHere = $hexPath->pathToHere;

            if ($this->moves->$hexNum->isZoc == NULL) {
                $this->moves->$hexNum->isZoc = $this->force->mapHexIsZOC($mapHex, $defendingForceId);
            }
            if ($this->moves->$hexNum->isZoc) {
                if (!$this->moves->$hexNum->isOccupied) {
                    unset($this->moves->$hexNum);
//                    $this->moves->$hexNum->isValid = false;
                    continue;
                }

            }
            $path = $hexPath->pathToHere;
            $path[] = $hexNum;

            for ($i = 1; $i <= 6; $i++) {
                $newHexNum = $mapHex->neighbors[$i - 1];
                $gnuHex = Hexagon::getHexPartXY($newHexNum);
                if (!$gnuHex) {
                    continue;
                }
                if (!($this->terrain->terrainIsHexSide($hexNum, $newHexNum, "road") || $this->terrain->terrainIsHexSide($hexNum, $newHexNum, "trail")
                    || $this->terrain->terrainIsHexSide($hexNum, $newHexNum, "secondaryroad"))) {
                    continue;
                }

                if ($this->terrain->terrainIsXY($gnuHex[0], $gnuHex[1], "offmap")) {
                    continue;
                }
                $newMapHex = $this->mapData->getHex($newHexNum);
                if ($newMapHex->isOccupied($defendingForceId)) {
                    continue;
                }
                /*
                 * TODO order is important in if statement check if doing zoc zoc move first then if just one hex move.
                 * Then check if oneHex and firstHex
                 */
                if($maxHex !== false && $movePoints - 1 < 0){
                    continue;
                }
                $head = false;
                if (!empty($bias[$i])) {
                    $head = true;
                }
                if (isset($this->moves->$newHexNum)) {
                    if($maxHex !== false){
                        if($this->moves->$newHexNum->pointsLeft > ($movePoints - 1) ){
                            continue;
                        }
                    }else{
                        continue;
                    }
                }
                $newPath = new HexPath();
                $newPath->name = $newHexNum;
                $newPath->pathToHere = $path;
                if($maxHex !== false){
                    $newPath->pointsLeft = $movePoints - 1;
                }
                if ($head) {
                    array_unshift($this->moveQueue, $newPath);
                } else {
                    $this->moveQueue[] = $newPath;

                }
            }
        }
        return false;
    }
    function startMoving($id)
    {
        $battle = Battle::getBattle();
        $victory = $battle->victory;
        /* @var Unit $unit */
        $unit = $this->force->getUnit($id);
        $victory->preStartMovingUnit($unit);

        /*
         * Don't think this is important test. Unit will be STATUS_STOPPED if cannot move.
         */
        if (!$this->stickyZoc || $this->force->unitIsZOC($id) == false) {
            if ($unit->setStatus(STATUS_MOVING) == true) {
                $this->anyUnitIsMoving = true;
                $this->movingUnitId = $id;
            }
        }
        $victory->postStartMovingUnit($unit);
    }

    function airMove(MovableUnit $movingUnit, $hexagon)
    {
        if ($movingUnit->unitIsMoving()
            && $this->airMoveIsValid($movingUnit, $hexagon)
        ) {
            $this->updateMoveData($movingUnit, $hexagon);
        }

        return true;
    }

    function move(MovableUnit $movingUnit, $hexagon)
    {
        if ($movingUnit->unitIsMoving()
            && $this->moveIsValid($movingUnit, $hexagon)
        ) {
            $this->updateMoveData($movingUnit, $hexagon);
        }
    }

    function stopMove(MovableUnit $movingUnit, $force = false)
    {
        $battle = Battle::getBattle();
        $victory = $battle->victory;
        $victory->preStopMovingUnit($movingUnit);

        $this->moves = new stdClass();
        if ($movingUnit->unitIsMoving() == true) {
            if ($movingUnit->unitHasNotMoved() && !$force) {
                $movingUnit->setStatus(STATUS_READY);
                $this->anyUnitIsMoving = false;
                $this->movingUnitId = NONE;
            } else {
                if ($movingUnit->setStatus(STATUS_STOPPED) == true) {
                    $this->anyUnitIsMoving = false;
                    $this->movingUnitId = NONE;
                }
            }
        }
        if($movingUnit->unitIsDeploying()){
            $this->stopDeploying($movingUnit);
        }
        $victory->preStopMovingUnit($movingUnit);
    }

    function exitUnit($id)
    {
        /* @var Unit $unit */
        $unit = $this->force->units[$id];
        if ($unit->unitIsMoving() == true) {
            $battle = Battle::getBattle();
            $victory = $battle->victory;
            $ret = $victory->isExit($unit);
            if($ret === false){
                return false;
            }
            if ($unit->setStatus(STATUS_EXITED) == true) {
                /* TODO: awful. probably don't need to set $id for Hexagon name */
                $hexagon = new Hexagon($id);
                $hexagon->parent = 'exitBox';
                $this->force->updateMoveStatus($unit->id, $hexagon, 1);
                $this->anyUnitIsMoving = false;
                $this->movingUnitId = NONE;
                $this->moves = new stdClass();
                return true;
            }

        }
        return false;
    }
    function airMoveIsValid(MovableUnit $movingUnit, $hexagon, $startHex = false, $firstHex = false)
    {
        return true;
    }

    function moveIsValid(MovableUnit $movingUnit, $hexagon, $startHex = false, $firstHex = false)
    {
        // all 4 conditions must be true, so any one that is false
        //    will make the move invalid

        $isValid = true;

        if ($startHex === false) {
            $startHex = $movingUnit->getUnitHexagon()->name;
        }
        if ($firstHex === false) {
            $firstHex = $movingUnit->unitHasNotMoved();
        }
        // condition 1
        // can only move to nearby hexagon
        if ($this->rangeIsOneHexagon($startHex, $hexagon) == false) {
            $isValid = false;
        }
        // condition 2
        // check if unit has enough move points
        $moveAmount = $this->terrain->getTerrainMoveCost($startHex, $hexagon, $movingUnit->forceMarch, $movingUnit);

        // need move points, but can always move at least one hexagon
        //  can always move at least one hexagon if this->oneHex is true
        //  only check move amount if unit has been moving
        if (!($firstHex == true && $this->oneHex)) {
            if ($movingUnit->unitHasMoveAmountAvailable($moveAmount) == false) {
                $isValid = false;
            }
        }

        // condition 3
        // can only move across river hexside if at start of move
//        if (($this->isAlongRail($startHex, $hexagon) == false) && $this->railMove) {
//            $isValid = false;
//        }

        // condition 4
        // can not exit
        if (($this->terrain->isExit($hexagon) == true)) {
            $isValid = false;
        }
        return $isValid;
    }

    function updateMoveData(MovableUnit $movingUnit, $hexagon)
    {
        $battle = Battle::getBattle();
        /* @var MapData $mapData */
        $mapData = $battle->mapData;
        $fromHex = $movingUnit->hexagon;
        $moveAmount = $this->terrain->getTerrainMoveCost($movingUnit->getUnitHexagon()->name, $hexagon, $movingUnit->forceMarch, $movingUnit);
        /* @var MapHex $mapHex */
        $mapHex = $mapData->getHex($hexagon);
        if ($mapHex->isZoc($this->force->defendingForceId) == true) {
            if (is_numeric($this->enterZoc)) {
                $moveAmount += $this->enterZoc;
            }
        }
        $fromMapHex = $mapData->getHex($fromHex->name);
        if ($fromMapHex->isZoc($this->force->defendingForceId) == true) {
            if (is_numeric($this->exitZoc)) {
                $moveAmount += $this->exitZoc;
            }
        }

        $movingUnit->updateMoveStatus(new Hexagon($hexagon), $moveAmount);
//
//        if (($this->storm && !$this->railMove) && !$movingUnit->unitHasNotMoved()) {
//            $this->stopMove($movingUnit);
//        }
        if ($movingUnit->unitHasUsedMoveAmount() == true) {
            $this->stopMove($movingUnit);
        }

        if ($mapHex->isZoc($this->force->defendingForceId) == true) {
            if ($this->enterZoc === "stop") {
                $this->stopMove($movingUnit);
            }
        }

        if ($this->terrain->isExit($hexagon)) {
            $this->eexit($movingUnit->id);
        }
    }

    function rangeIsOneHexagon($startHexagon, $endHexagon)
    {
        $rangeIsOne = false;

        $los = new Los();
        $los->setOrigin($startHexagon);
        $los->setEndPoint($endHexagon);
        if ($los->getRange() == 1) {
            $rangeIsOne = true;
        }

        return $rangeIsOne;
    }

    function startReinforcing($id, $turn)
    {
        /* @var Unit $unit */
        $unit = $this->force->getUnit($id);

        if ($unit->getUnitReinforceTurn($id) <= $turn) {

            $battle = Battle::getBattle();
            $victory = $battle->victory;
            /* @var Unit $unit */
            $victory->preStartMovingUnit($unit);

            if ($unit->setStatus(STATUS_REINFORCING) == true) {
                $movesLeft = $unit->getMaxMove();
                $zoneName = $unit->reinforceZone;
                $zones = $this->terrain->getReinforceZonesByName($zoneName);
                list($zones) = $battle->victory->postReinforceZones($zones, $unit);
                foreach ($zones as $zone) {
                    if ($this->force->hexagonIsOccupied($zone->hexagon, $this->stacking, $unit)) {
                        continue;
                    }
                    $startHex = $zone->hexagon->name;
                    $hexPath = new HexPath();
                    $hexPath->name = $startHex;
                    $hexPath->pointsLeft = $movesLeft;
                    $hexPath->pathToHere = array();
                    $hexPath->firstHex = true;
                    $this->moves->$startHex = $hexPath;
                }
                $this->anyUnitIsMoving = true;
                $this->movingUnitId = $id;
            }
        }
    }

    function startDeploying($id, $turn)
    {
        /* @var Unit $unit */
        $unit = $this->force->getUnit($id);
        if ($unit->getUnitReinforceTurn($id) <= $turn) {

            if ($unit->setStatus(STATUS_DEPLOYING) == true) {
                $battle = Battle::getBattle();
                $victory = $battle->victory;
                $movesLeft = 0;
                $zoneName = $unit->reinforceZone;
                $zones = $this->terrain->getReinforceZonesByName($zoneName);
                list($zones) = $battle->victory->postDeployZones($zones, $unit);
                foreach ($zones as $zone) {
                    $startHex = $zone->hexagon->name;
                    if ($this->force->hexagonIsOccupied($zone->hexagon, $this->stacking, $unit)) {
                        continue;
                    }
                    $hexPath = new HexPath();
                    $hexPath->name = $startHex;
                    $hexPath->pointsLeft = $movesLeft;
                    $hexPath->pathToHere = array();
                    $hexPath->firstHex = true;
                    $this->moves->$startHex = $hexPath;
                }
                $this->anyUnitIsMoving = true;
                $this->movingUnitId = $id;
            }
        }
    }

    function startReplacing($id)
    {
        $battle = Battle::getBattle();
        /* @var Unit $unit */
        $unit = $this->force->getUnit($id);
        if ($unit->setStatus(STATUS_REPLACING) == true) {
            $movesLeft = 0;
            $zones = $this->terrain->getReinforceZonesByName($unit->getUnitReinforceZone($id));
            list($zones) = $battle->victory->postReinforceZones($zones, $unit);
            foreach ($zones as $zone) {
                if ($this->force->hexagonIsOccupied($zone->hexagon, $this->stacking, $unit)) {
                    continue;
                }
                if(!$zone->hexagon || !$zone->hexagon->name){
                    continue;
                }
                $startHex = $zone->hexagon->name;
                $hexPath = new HexPath();
                $hexPath->name = $startHex;
                $hexPath->pointsLeft = $movesLeft;
                $hexPath->pathToHere = array();
                $hexPath->firstHex = true;
                $this->moves->$startHex = $hexPath;
            }
            $this->anyUnitIsMoving = true;
            $this->movingUnitId = $id;
        }
    }

    function stopReplacing()
    {
        $this->moves = new stdClass();

        $this->anyUnitIsMoving = false;
        $this->movingUnitId = false;
        $this->moves = new stdClass();
    }

    function reinforce($movingUnit, Hexagon $hexagon)
    {
        /* @var Unit $movingUnit */
        $battle = Battle::getBattle();
        if ($movingUnit->unitIsReinforcing() == true) {

            list($zones) = $battle->victory->postReinforceZoneNames($this->terrain->getReinforceZoneList($hexagon), $movingUnit);

            if (in_array($movingUnit->getUnitReinforceZone() , $zones)) {
                if ($movingUnit->setStatus(STATUS_MOVING) == true) {
                    $battle = Battle::getBattle();
                    $victory = $battle->victory;
                    $victory->reinforceUnit($movingUnit, $hexagon);
                    $movingUnit->updateMoveStatus($hexagon, 0);
                }

            }
        }
    }

    function deploy($movingUnit, $hexagon)
    {
        /* @var Unit $movingUnit */
        if ($movingUnit->unitIsDeploying() == true) {
            if (in_array($movingUnit->getUnitReinforceZone(), $this->terrain->getReinforceZoneList($hexagon))) {

                if ($movingUnit->setStatus(STATUS_CAN_DEPLOY) == true) {
                    $movingUnit->updateMoveStatus($hexagon, 0);
                    $this->anyUnitIsMoving = false;
                    $this->movingUnitId = NONE;
                    $this->moves = new stdClass();
                }

            }
        }
    }

    function stopReinforcing($unit)
    {
        /* @var Unit $unit */
        if ($unit->unitIsReinforcing() == true) {
            if ($unit->setStatus(STATUS_CAN_REINFORCE) == true) {
                $this->anyUnitIsMoving = false;
                $this->movingUnitId = NONE;
                $this->moves = new stdClass();
            }
        }
    }

    function stopDeploying(MovableUnit $unit)
    {
        /* @var Unit $unit */
        if ($unit->unitIsDeploying() == true) {
            if ($unit->setStatus(STATUS_CAN_DEPLOY) == true) {
                $this->anyUnitIsMoving = false;
                $this->movingUnitId = NONE;
                $this->moves = new stdClass();
            }
        }
    }

// retreat rules

// gameRules has cleared retreat list

    function retreatUnit($eventType, $id, $hexagon)
    {
            // id will be retreating unit id if counter event
            if ($this->anyUnitIsMoving == false) {
                if ($this->force->unitCanRetreat($id) == true) {
                    $this->startRetreating($id);
                    if($this->anyUnitIsMoving){
                        $this->calcRetreat($id);
                    }
                }
            } else {
                    $finalHex = $hexagon;
                    $moves = $this->moves->$finalHex;
                    foreach ($moves->pathToHere as $move){
                        $this->retreat($this->movingUnitId, new Hexagon($move));
                    }
                    $this->retreat($this->movingUnitId, new Hexagon($finalHex));
                    $this->moves = new stdClass();
            }
    }

// retreat rules

// gameRules has cleared retreat list

//    function retreatUnit($eventType, $id, $hexagon)
//    {
//        // id will be map if map event
//        if ($eventType == SELECT_MAP_EVENT) {
//            if ($this->anyUnitIsMoving == true) {
//                $retreatingUnit = $this->force->units[$id];
//                if (true || $retreatingUnit->unitIsretreating() == true) {
//                    $newHex = $hexagon;
//                    $newHexName = $newHex->name;
//
//                    if ($this->moves->{$newHexName}) {
//                        $this->path = $this->moves->$newHexName->pathToHere;
//
//                        foreach ($this->path as $retreatHex) {
//                            $this->retreat($id, new Hexagon($retreatHex));
//
//                        }
////                        $retreatsLeft = $this->moves->$newHex->pointsLeft;
//                        $this->moves = new stdClass();
//
//                        $this->retreat($id, $newHex);
//                        $this->path = array();
//                        $dirty = true;
//                    }
//                }
//                $this->retreat($this->movingUnitId, $hexagon);
//            }
//        } else {
//            // id will be retreating unit id if counter event
//            if ($this->anyUnitIsMoving == false) {
//                if ($this->force->unitCanRetreat($id) == true) {
//                    $this->startRetreating($id);
//                    $this->calcRetreat($id);
//                }
//            } else {
//                $this->retreat($this->movingUnitId, $hexagon);
//            }
//        }
//    }


    function startRetreating($id)
    {
        $battle = Battle::getBattle();
        $victory = $battle->victory;

        /* @var Unit $movingUnit */
        $movingUnit = $this->force->getUnit($id);
        $victory->preStartMovingUnit($movingUnit);
        if ($movingUnit->setStatus(STATUS_RETREATING) == true) {
            $this->anyUnitIsMoving = true;
            $this->movingUnitId = $id;
        }
        $victory->postStartMovingUnit($movingUnit);
    }

    function retreatIsBlocked($id)
    {
        throw new Exception("bad bad call ");
        $isBlocked = true;

        $adjacentHexagonXadjustment = array(0, 2, 2, 0, -2, -2);
        $adjacentHexagonYadjustment = array(-4, -2, 2, 4, 2, -2);

        /* @var Hexagon $hexagon */
        /* @var Unit $unit */
        $unit = $this->force->getUnit($id);
        $hexagon = $unit->getUnitHexagon();
        $hexagonX = $hexagon->getX($id);
        $hexagonY = $hexagon->getY($id);
        for ($eachHexagon = 0; $eachHexagon < 6; $eachHexagon++) {
            $adjacentHexagonX = $hexagonX + $adjacentHexagonXadjustment[$eachHexagon];
            $adjacentHexagonY = $hexagonY + $adjacentHexagonYadjustment[$eachHexagon];
            $adjacentHexagon = new Hexagon($adjacentHexagonX, $adjacentHexagonY);

            if ($this->hexagonBlocksRetreat($id, $adjacentHexagon) == false) {
                $isBlocked = false;
                break;
            }

        }

        return $isBlocked;
    }

    function hexagonBlocksRetreat($id, Hexagon $startHex, Hexagon $hexagon)
    {
        $isBlocked = false;

        if (!$hexagon->name) {
            return true;
            /* off map hexes have no name */
        }


        // make sure hexagon is not ZOC
        $unit = $this->force->units[$id];
        $mapHex = $this->mapData->getHex($hexagon->name);
        $forceId = $unit->forceId;

        if ($this->zocBlocksRetreat === true && $this->force->mapHexIsZOC($mapHex, $this->force->enemy($unit->forceId))){
            $isBlocked = true;
            if($this->friendlyAllowsRetreat && $mapHex->isOccupied($forceId)){
                if(!$mapHex->isOccupied($forceId,$this->stacking, $unit)){
                    $isBlocked = false;
                }
            }
        }
        if ($this->terrain->terrainIsHexSide($startHex->name, $hexagon->name, "blocked")) {
            $isBlocked = true;
        }
        if ($this->terrain->getTerrainMoveCost($startHex->name, $hexagon->name, false, $unit) == "blocked") {
            $isBlocked = true;
        }

//        if ($this->zocBlocksRetreat === true && ($this->force->hexagonIsZOC($id, $hexagon) == true)) {
//            $isBlocked = true;
//        }
        // make sure hexagon is not occupied
        if ($this->mapData->hexagonIsOccupiedEnemy($hexagon, $forceId) == true) {
            $isBlocked = true;
        }

        if ($this->terrain->isExit($hexagon) == true) {
            $isBlocked = true;
        }
        //alert(unitHexagon->getName() + " to " + hexagon->getName() + " zoc: " + $this->force->hexagonIsZOC(id, hexagon) + " occ: " + $this->force->hexagonIsOccupied(hexagon)  + " river: " + $this->terrain->terrainIs(hexpart, "river"));
        return $isBlocked;
    }

    function retreat($id, Hexagon $hexagon)
    {
        /* @var  Unit $movingUnit */
        $movingUnit = $this->force->getUnit($id);
        $battle = Battle::getBattle();
        $mapData = $battle->mapData;
        $startHex = $this->force->units[$id]->hexagon;

        if ($this->rangeIsOneHexagon($movingUnit->getUnitHexagon()->name, $hexagon)
            && $this->hexagonBlocksRetreat($id, $startHex, $hexagon) === false
        ) {
            $this->force->addToRetreatHexagonList($id, $movingUnit->getUnitHexagon());
            // set move amount to 0
            $occupied = $mapData->hexagonIsOccupiedForce($hexagon, $movingUnit->forceId, $this->stacking, $movingUnit);
            $movingUnit->updateMoveStatus($hexagon, 0);

            // check crt retreat count required to how far the unit has retreated
            if ($this->force->unitHasMetRetreatCountRequired($id) && !$occupied) {
                // stop if unit has retreated the required amount
                if ($movingUnit->setStatus(STATUS_STOPPED) == true) {
                    $this->anyUnitIsMoving = false;
                    $this->movingUnitId = NONE;
                    $this->moves = new stdClass();
                }
            }
        }
    }

// advancing rules

    function advanceUnit($eventType, $id, $hexagon)
    {
        if ($eventType == SELECT_MAP_EVENT) {
            if ($this->anyUnitIsMoving == true) {
                $hexagon = new Hexagon($hexagon);
                $this->advance($this->movingUnitId, $hexagon);
            }
        } else {
            if (($this->anyUnitIsMoving == true) && ($id == $this->movingUnitId)) {
                $this->endAdvancing($this->movingUnitId);
            } else {
                if ($this->force->unitCanAdvance($id) == true) {
                    $this->startAdvancing($id);
                }
            }
        }
    }

    function startAdvancing($id)
    {
        /* @var Hexagon $hexagon */
        $hexagon = $this->force->getFirstRetreatHex($id);
        $hexes = $this->force->getAllFirstRetreatHexes($id);
        foreach ($hexes as $hexagon) {
            $startHex = $hexagon->name;
            $hexPath = new HexPath();
            $hexPath->name = $startHex;
            $hexPath->pointsLeft = $this->force->units[$id]->getMaxMove();
            $hexPath->pathToHere = array();
            $hexPath->firstHex = true;
            $this->moves->$startHex = $hexPath;
        }

        /* @var Unit $unit */
        $unit = $this->force->getUnit($id);
        if ($unit->setStatus(STATUS_ADVANCING) == true) {
            $this->anyUnitIsMoving = true;
            $this->movingUnitId = $id;
        }
    }

    function advance($id, $hexagon)
    {
        /* @var Unit $unit */
        $unit = $this->force->getUnit($id);
        if ($this->advanceIsValid($unit, $hexagon) == true) {
            // set move amount to 0

            $unit->updateMoveStatus($hexagon, 0);
            $this->stopAdvance($id);
        }
    }

    function stopAdvance($id)
    {
        /* @var Unit $unit */
        $unit = $this->force->getUnit($id);
        if ($unit->setStatus(STATUS_ADVANCED) == true) {
            $this->moves = new stdClass();
            $this->anyUnitIsMoving = false;
            $this->movingUnitId = NONE;
            $this->force->resetNonFittingAdvancingUnits($unit);
        }
    }

    function endAdvancing($id)
    {
        /* @var Unit $unit */
        $unit = $this->force->getUnit($id);
        if ($unit->setStatus(STATUS_ADVANCED) == true) {
            $this->moves = new stdClass();
            $this->force->resetRemainingAdvancingUnits();
            $this->anyUnitIsMoving = false;
            $this->movingUnitId = NONE;
        }
    }

    function advanceIsValid($unit, $hexagon)
    {
        $isValid = false;


        $startHexagon = $unit->getUnitHexagon();
        if ($this->force->advanceIsOnRetreatList($unit->id, $hexagon) == true && $this->rangeIsOneHexagon($startHexagon, $hexagon) == true) {
            $isValid = true;
        } else {
        }

        return $isValid;
    }
}
