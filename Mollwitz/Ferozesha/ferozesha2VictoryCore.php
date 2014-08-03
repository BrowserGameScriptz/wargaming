<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 5/7/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */
include "victoryCore.php";
include "indiaVictoryCore.php";

class ferozesha2VictoryCore extends indiaVictoryCore
{

    function __construct($data)
    {
        if ($data) {
            $this->movementCache = $data->victory->movementCache;
            $this->victoryPoints = $data->victory->victoryPoints;
            $this->gameOver = $data->victory->gameOver;
        } else {
            $this->victoryPoints = array(0, 0, 0);
            $this->movementCache = new stdClass();
            $this->gameOver = false;
        }
    }

    public function reduceUnit($args)
    {
        $unit = $args[0];
        $mult = 1;
        if ($unit->nationality == "British") {
            $mult = 2;
        }
        if ($unit->forceId == 1) {
            $victorId = 2;
            $this->victoryPoints[$victorId] += $unit->strength * $mult;
        } else {
            $victorId = 1;
            $this->victoryPoints[$victorId] += $unit->strength * $mult;
        }
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();
        if ($battle->scenario->dayTwo) {
            list($mapHexName, $forceId) = $args;

            if ($forceId == SIKH_FORCE) {
                $this->victoryPoints[SIKH_FORCE] += 5;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='beluchi'>+5 Sikh  vp</span>";
            }
            if ($forceId == BRITISH_FORCE) {
                $this->victoryPoints[SIKH_FORCE] -= 5;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='british'>-5 Sikh  vp</span>";
            }
        } else {

            list($mapHexName, $forceId) = $args;
            if ($mapHexName == $battle->moodkee) {
                if ($forceId == SIKH_FORCE) {
                    $this->victoryPoints[SIKH_FORCE] += 20;
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='beluchi'>+5 Sikh  vp</span>";
                }
                if ($forceId == BRITISH_FORCE) {
                    $this->victoryPoints[SIKH_FORCE] -= 20;
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='british'>-5 Sikh  vp</span>";
                }

            } else {
                if ($forceId == BRITISH_FORCE) {
                    $this->victoryPoints[BRITISH_FORCE] += 5;
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='british'>+5 British  vp</span>";
                }
                if ($forceId == SIKH_FORCE) {
                    $this->victoryPoints[BRITISH_FORCE] -= 5;
                    $battle->mapData->specialHexesVictory->$mapHexName = "<span class='beluchi'>-5 British  vp</span>";
                }
            }
        }
    }


    protected function checkVictory($attackingId, $battle)
    {
        $gameRules = $battle->gameRules;
        $scenario = $battle->scenario;
        $turn = $gameRules->turn;
        $britishWin = $sikhWin = false;

        if (!$this->gameOver) {
            $specialHexes = $battle->mapData->specialHexes;
            $sikhVic = 35;
            $britLead = 10;
            if ($this->victoryPoints[SIKH_FORCE] >= $sikhVic ) {
                $sikhWin = true;
            }
            if (($this->victoryPoints[BRITISH_FORCE] >= 40) && (($this->victoryPoints[BRITISH_FORCE] - $this->victoryPoints[SIKH_FORCE]) >= $britLead)) {
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
                $this->winner = SIKH_FORCE;
                $gameRules->flashMessages[] = "Sikh Win";
            }
            if ($britishWin) {
                $this->winner = BRITISH_FORCE;
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