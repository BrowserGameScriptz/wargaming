<?php
// combatRules->js

// copyright (c) 2009-2011 Mark Butler
// This program is free software; you can redistribute it 
// and/or modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation;
// either version 2 of the License, or (at your option) any later version-> 

Class Combat{
    public $attackers;
    public $index;
    public $attackStrength;
    public $defenseStrength;
    public $Die;
    public $combatResult;
    public function __construct(){
        $this->attackers = new StdClass;
    }
}
class CombatRules
{
	// Class references
    /* @var Force */
    public $force;
    /* @var Terrain */
    public $terrain;
    
    // local publiciables
    /* @var CombatResultsTable */
    public $crt;
    public $currentDefender = false;
    public $combats;
    public $combatsToResolve;
    public $attackers;
    public $resolvedCombats;
    public $lastResolvedCombat;

    /*
     * TODO
     * This is how we serialized data in the ancient days...
     */
    function save()
    {
        $data = new StdClass();
        foreach ($this as $k => $v) {
            if ((is_object($v) && $k != "lastResolvedCombat" && $k != "resolvedCombats" && $k != "combats" && $k != "attackers" && $k != "combatsToResolve") || $k == "crt" ) {
                continue;
            }
            $data->$k = $v;
        }
        return $data;
    }
    function __construct($Force, $Terrain, $data = null){
    $this->force = $Force;
    $this->terrain = $Terrain;

        if ($data) {
            foreach ($data as $k => $v) {
                $this->$k = $v;
            }
        } else {
            /*
             * TODO all this sucks and needs to be initialized
             */
            $this->combats = new stdClass();
            $this->attackers = new stdClass();
            $this->currentDefender = false;
        }
        $this->crt = new CombatResultsTable();
    }


    function setupCombat( $id , $shift = false) {

    $cd = $this->currentDefender;

    if ($this->force->unitIsEnemy($id) == true)
    {
        // defender is already in combatRules, so make it currently selected
        if ($this->combats->$id)
        {
//            if(count($this->combats->$this->currnetDefender->attackers) == 0){
//                unset($this->currnetDefender[$id]);
//            }
            if($this->currentDefender === false){
                $this->currentDefender = $id;
            }else{

                if($id === $this->currentDefender){
                    $this->currentDefender = false;
                }else{
                $this->currentDefender = $id;
                }
            }
        }
        else
        {
            $this->currentDefender = $id;
            $this->force->setupDefender($id);
            if(!$this->combats){
                $this->combats = new  stdClass();
            }
            $this->combats->$id = new Combat();
        }
    }
    else
    // attacker
    {

        if ($this->currentDefender !== false && $this->force->units[$id]->status != STATUS_UNAVAIL_THIS_PHASE)
        {
            $los = new Los();
            $los->setOrigin($this->force->getUnitHexagon($id));
            $los->setEndPoint($this->force->getUnitHexagon($this->currentDefender));
            $range = $los->getRange();
            $bearing = $los->getBearing();
            if ($range <= $this->force->getUnitRange($id))
            {
                if ($this->combats->${cd}->attackers->$id !== false && $this->attackers->$id === $cd)
                {
                    $this->force->undoAttackerSetup($id);
                    unset($this->attackers->${id});
                    unset($this->combats->${cd}->attackers->$id);
                    $this->setCombatIndex($cd);
                }
                else
                {
                    $this->force->setupAttacker($id, $range);
                    if(isset($this->attackers->$id) && $this->attackers->$id !== $cd){
                        /* move unit to other attack */
                        $oldCd = $this->attackers->${id};
                        unset($this->combats->${oldCd}->attackers->$id);
                        $this->setCombatIndex($oldCd);
                    }
                    $this->attackers->${id} = $cd;
                    $this->combats->${cd}->attackers->$id = $bearing;
                    $this->setCombatIndex($cd);
                }
            }
        }
    }
        $this->cleanUpAttacklessDefenders();
}

function cleanUpAttacklessDefenders()
{
    if(!$this->combats){
        $this->combats = new stdClass();
    }
    foreach ($this->combats as $id => $combat) {
        if ($id == $this->currentDefender) {
            continue;
        }
        if (count((array)$combat->attackers) == 0) {
            $this->force->setStatus($id, STATUS_READY);
            unset($this->combats->$id);
        }
    }
 }
function setupFireCombat( $id ){
}

function getDefenderTerrainCombatEffect($defenderId)
{
    $terrainCombatEffect = $this->terrain->getDefenderTerrainCombatEffect($this->force->getCombatHexagon($defenderId),$this->force->attackingForceId);
    if ($this->allAreAttackingAcrossRiver($defenderId)) {

        $terrainCombatEffect += $this->terrain->getAllAreAttackingAcrossRiverCombatEffect();

    }
    
	return $terrainCombatEffect;
}


function setCombatIndex($defenderId)
{

    $combats = $this->combats->$defenderId;
    if(count((array)$combats->attackers) == 0){
        $combats->index = null;
        $combats->attackStrength = null;
        $combats->defenseStrength = null;
        $combats->terrainCombatEffect = null;
        return;
    }
    $attackStrength = $this->force->getAttackerStrength($combats->attackers);
    $defenseStrength = $this->force->getDefenderStrength($defenderId);
    $combatIndex = $this->crt->getCombatIndex($attackStrength, $defenseStrength);
    /* Do this before terrain effects */
    if ($combatIndex >= $this->crt->maxCombatIndex) {
        $combatIndex = $this->crt->maxCombatIndex;
    }


    $terrainCombatEffect = $this->getDefenderTerrainCombatEffect($defenderId);

    $combatIndex -= $terrainCombatEffect;

    $combats->attackStrength = $attackStrength;
    $combats->defenseStrength = $defenseStrength;
    $combats->terrainCombatEffect = $terrainCombatEffect;
    $combats->index = $combatIndex;
//    $this->force->storeCombatIndex($defenderId, $combatIndex);
}
function cleanUp(){
    unset($this->combats);
    unset($this->resolvedCombats);
    unset($this->lastResolvedCombat);
    unset($this->combatsToResolve);
    $this->currentDefender = false;
    $this->attackers = new StdClass();
}
function resolveCombat( $id ) {
    global $results_name;
    if($this->force->unitIsEnemy($id) && !isset($this->combatsToResolve->${id})){
        return false;
    }
    if($this->force->unitIsFriendly($id)){
        if(isset($this->attackers->$id)){
            $id = $this->attackers->$id;
        }
        else{return false;}
    }
    $this->currentDefender = $id;
    // Math->random yields number between 0 and 1
    //  6 * Math->random yields number between 0 and 6
    //  Math->floor gives lower integer, which is now 0,1,2,3,4,5

    $Die = floor($this->crt->dieSideCount * (rand()/getrandmax()));
//    $Die = 5;
//    $index = $this->force->getUnitCombatIndex($id);
    $index = $this->combatsToResolve->${id}->index;

    $combatResults = $this->crt->getCombatResults($Die, $index);
    $this->combatsToResolve->${id}->Die = $Die + 1;
    $this->combatsToResolve->${id}->combatResult = $results_name[$combatResults];
    $this->force->applyCRTresults($id, $this->combatsToResolve->{$id}->attackers, $combatResults, $Die);
    $this->lastResolvedCombat = $this->combatsToResolve->$id;
    if(!$this->resolvedCombats){
        $this->resolvedCombats = new stdClass();
    }
        $this->resolvedCombats->$id = $this->combatsToResolve->$id;
        unset($this->combatsToResolve->$id);
    foreach($this->lastResolvedCombat->attackers as $attacker =>$v){
        unset($this->attackers->$attacker);
        return $Die;
    }
}

function resolveFireCombat( $id ) {
}

function allAreAttackingAcrossRiver($defenderId) {

    $allAttackingAcrossRiver = true;

//     $attackerHexagonList = array();
//    $attackerHexagonList = $this->force->getAttackerHexagonList($combatNumber);
    $attackerHexagonList = $this->combats->$defenderId->attackers;
    $defenderHexagon = $this->force->getCombatHexagon($defenderId);
    foreach ($attackerHexagonList as $attackerHexagonId => $val) {
        $attackerHexagon = $this->force->getCombatHexagon($attackerHexagonId);

        $hexsideX = ($defenderHexagon->getX() + $attackerHexagon->getX()) / 2;
        $hexsideY = ($defenderHexagon->getY() + $attackerHexagon->getY()) / 2;
        
        $hexside = new Hexpart($hexsideX, $hexsideY);

        if ($this->terrain->terrainIs($hexside, "river") == false) {

            $allAttackingAcrossRiver = false;
        }
    }
    
    return $allAttackingAcrossRiver;
}

function getCombatOddsList($combatIndex)
{
   return $this->crt->getCombatOddsList($combatIndex);
}
    function undoDefendersWithoutAttackers()
    {
        $this->currentDefender = false;
        if($this->combats){
            foreach ($this->combats as $defenderId => $combat)
            {
                if(count((array)$combat->attackers) == 0){
                    unset($this->combats->$defenderId);
                    $this->force->setStatus($defenderId,STATUS_READY);
                    continue;
                }
                if($combat->index < 0){
                    if($combat->attackers){
                        foreach($combat->attackers as $attackerId => $attacker){
                            unset($this->attackers->$attackerId);
                            $this->force->setStatus($attackerId, STATUS_READY);
                        }
                    }
                    unset($this->combats->$defenderId);
                    $this->force->setStatus($defenderId,STATUS_READY);
                    continue;
                }
            }
        }
        $this->combatsToResolve = $this->combats;
        unset($this->combats);
    }

}