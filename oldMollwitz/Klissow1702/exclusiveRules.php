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
 * User: david
 * Date: 6/19/13
 * Time: 12:21 PM added this
 * To change this template use File | Settings | File Templates.
 */
?>
<style type="text/css">
    #pontoon-image{
        width:32px;
    }

        /*#GR ol.ExclusiveRules{*/
            /*counter-reset: item 6;*/
       /*}*/
</style>
<div class="dropDown" id="GRWrapper">
    <h4 class="WrapperLabel" title="Game Rules">Exclusive Rules</h4>

    <div id="GR" style="display:none">
        <div class="close">X</div>
        <div id="gameRules">
            <H1>
                <?= $name ?>
            </H1>

            <h2 class="exclusive"> EXCLUSIVE RULES </h2>
            <ol>
                <li><span class="lessBig">Deploy Phase</span>
                    <p class="indent">The <?= $deployName[1]?> player deploys first. The <?= $deployName[2]?> player deploys Second</p>

                </li>
                <li><span class="lessBig">First Player</span>
                    <p class="indent">The <?= $player[1]?> player moves first. The  <?= $player[2]?>  player moves second.</p>
                </li>
                <li><span class="lessBig">Movement</span>
                    <p class="indent">The <?= $player[1]?> units receive a 1 MP bonus on turn 1 only.</p>
                    <p class="indent"> The  <?= $player[2]?>  Units movement rate are halved on turn 1, normal thereafter.</p>
                </li>
                <li><span class="lessBig">Terrain</span>
                    <p class="indent">Marsh: The Swedes experienced very little difficulty attacking across the marshes, therefore marshes are only +2 MP's to enter.
                        In addition, during combat only attacks into a marsh are halved. Not attacks out of them, like the TEC states.</p>
                </li>
                <li><span class="lessBig">Pontoons</span>
                    <p class="indent">
                        <img id="pontoon-image" src="<?=url("js/AngloPontoon.png")?>">
                        Each side during their deployment may place one Pontoon marker on any swamp hex within 3 hexes of a friendly deploy hex.
                        This changes the hex to clear terrain for the remainder of the game. Pontoons may be used by either side. And may not be destroyed.
                    </p>

                </li>
            </ol>
            <ol class="ExclusiveRules topNumbers">
                <?php include "victoryConditions.php" ?>
            </ol>
            <div id="credits">
                <h2><cite><?= $name ?></cite></h2>
                <h4>Design Credits</h4>

                <h4>Game Design:</h4>
                Lance Runolfsson
                <h4>Graphics and Rules:</h4>
                <site>Lance Runolfsson</site>
                <h4>HTML 5 Version:</h4>
                David M. Rodal
            </div>
        </div>
    </div>
</div>