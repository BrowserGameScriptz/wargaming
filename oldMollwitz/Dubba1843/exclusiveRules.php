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
</style>
<div class="dropDown" id="GRWrapper">
    <h4 class="WrapperLabel" title="Game Rules">Exclusive Rules</h4>

    <div id="GR" style="display:none">
        <div class="close">X</div>
        <div id="gameRules">
            <H1>
                <?=$name?>
            </H1>
            <h2 class="exclusive"> EXCLUSIVE RULES
            </h2>

            <h3>Setting Up</h3>

            <ul>
                <li>The <?= $player[2] ?> player sets up first. The <?= $player[1] ?> play sets up second.</li>
                <li> The <?= $player[1] ?> player moves.</li>
            </ul>
            <div class="indent">
                <h3>Units</h3>

                <div class="indent">
                    <p> British units have horse artillery.</p>

                    <div class="unit British horseartillery"
                         style="border-color: rgb(204, 204, 204) rgb(102, 102, 102) rgb(102, 102, 102) rgb(204, 204, 204);   position: relative;"
                         alt="0">
                        <nav class="counterWrapper">
                            <div class="counter">
                        </nav>
                        <p class="range">3</p>

                        <p class="forceMarch">M</p>
                        <section></section>


                        <div class="unit-numbers">3 - 5</div>

                    </div>
                    <p class="ruleComment">It moves faster than regular artillery but is the same
                        otherwise. Note the range may be shorter than regular artillery.</p>
                    <p> The British player has both British and Native units available.</p>

                    <div class="left">
                        British
                        <div class="unit British infantry"
                             style="border-color: rgb(204, 204, 204) rgb(102, 102, 102) rgb(102, 102, 102) rgb(204, 204, 204);   position: relative;"
                             alt="0">
                            <nav class="counterWrapper">
                                <div class="counter">
                            </nav>
                            <p class="range"></p>

                            <p class="forceMarch">M</p>
                            <section></section>


                            <div class="unit-numbers">3 - 5</div>

                        </div>
                    </div>
                    <div class="left">
                        Native
                        <div class="unit Native infantry"
                             style="border-color: rgb(204, 204, 204) rgb(102, 102, 102) rgb(102, 102, 102) rgb(204, 204, 204);   position: relative;"
                             alt="0">
                            <nav class="counterWrapper">
                                <div class="counter">
                            </nav>
                            <p class="range"></p>

                            <p class="forceMarch">M</p>
                            <section></section>


                            <div class="unit-numbers">3 - 5</div>

                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <h3>Deploy Phase</h3>

                <p class="indent">The <?= $deployName[1] ?> player deploys first. The <?= $deployName[2] ?> player deploys
                    Second</p>

                <h3>First Player</h3>

                <p class="indent">The <?= $player[1] ?> player moves first. The  <?= $player[2] ?>  player moves second.
                    After the <?= $player[2] ?> player completes their
                    turn, the game turn is incremented.</p>
            </div>
            <h2>Combat Variations </h2>
            <ul>
                <li> All artillery is double defense in clear</li>
                <li> Beluchi artillery is only ranged 2</li>
                <li> Beluchi Infantry +1 combat point in Jungle/Scrub</li>
                <li> British Infantry and Cavalry MAY retreat in to Beluchi ZOC.</li>
                <li> Beluchi do not get combined arms bonus.</li>
            </ul>
            <?php include "victoryConditions.php"?>
            <div id="credits">
                <h2><cite><?=$name?></cite></h2>
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