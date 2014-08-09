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

class kolin1757VictoryCore extends victoryCore
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

        list($mapHexName, $forceId) = $args;
        if (in_array($mapHexName, $battle->specialHexA)) {
            if ($forceId == AUSTRIAN_FORCE) {
                $this->victoryPoints[SIKH_FORCE] += 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='austrian'>+10 Austrian vp</span>";
            }
            if ($forceId == PRUSSIAN_FORCE) {
                $this->victoryPoints[SIKH_FORCE] -= 10;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='prussian'>-10 Prussian vp</span>";
            }
        }
        if (in_array($mapHexName, $battle->specialHexB)) {
            if ($forceId == PRUSSIAN_FORCE) {
                $this->victoryPoints[SIKH_FORCE] += 20;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='prussian'>+20 Prussian vp</span>";
            }
            if ($forceId == AUSTRIAN_FORCE) {
                $this->victoryPoints[SIKH_FORCE] -= 20;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='austrian'>-20 Austrian vp</span>";
            }
        }
    }

    protected function checkVictory($attackingId, $battle)
    {
        $gameRules = $battle->gameRules;
        $scenario = $battle->scenario;
        $turn = $gameRules->turn;
        $prussianScore = $austrianWin = $prussianWin = $draw = false;

        if (!$this->gameOver) {
            $specialHexes = $battle->mapData->specialHexes;
            $winScore = 60;
            if (($this->victoryPoints[PRUSSIAN_FORCE] >= $winScore && ($this->victoryPoints[PRUSSIAN_FORCE] - ($this->victoryPoints[AUSTRIAN_FORCE]) >= 10))) {
                if ($turn < 9) {
                    $prussianWin = true;
                }
                if ($turn < 12) {
                    $draw = true;
                }
                $prussianScore = true;
            }
            if ($this->victoryPoints[AUSTRIAN_FORCE] >= $winScore && ($this->victoryPoints[AUSTRIAN_FORCE] - ($this->victoryPoints[PRUSSIAN_FORCE]))) {
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
                $this->winner = PRUSSIAN_FORCE;
                $gameRules->flashMessages[] = "Prussian Win";
            }
            if ($austrianWin) {
                $this->winner = AUSTRIAN_FORCE;
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
