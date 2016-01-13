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
            <ol class="topNumbers">
                <li>
                    <span class="lessBig">Setting Up</span>
                <ol>
                    <li>The <?= $player[2] ?> player sets up first. The <?= $player[1] ?> Setup second.</li>
                    <?php if(!$scenario->redux && !$scenario->hastenbeck2){?>
                    <li>When the <?= $player[1] ?> player starts deploying their units. There is a %50 chance they can
                        deploy in the F2 hexes, and %50 they
                        have to deploy in the F1 Hexes.
                    </li>
                    <?php } ?>
                    <li> <?= $player[1] ?> moves first. <?= $player[2] ?> moves second.</li>

                    </li>
                </ol>
                <li><span class="lessBig">Terrain</span>
                    <ol>
                        <li>
                            Streams are +2 MP's to cross
                        </li>
                        <li>
                            Major Rivers (dark thick blue) are impassable
                        </li>
                        <li>
                            HAMELN Is a Major Fortification Garrisoned by the Allies No French unit may ever enter it.
                        </li>
                    </ol>

                </li>

                    <?php include "victoryConditions.php"?>
            </ol>
            <div id="credits">
                <h2><cite><?=$name?></cite></h2>
                <h4>Design Credits</h4>

                <div class="indent">
                    <h4>Game Design:</h4>
                    <cite>Lance Runolfsson</cite>
                    <h4>Graphics and Rules:</h4>
                    <cite>Lance Runolfsson</cite>
                    <h4>HTML 5 Version:</h4>
                    <cite>David M. Rodal</cite>
                </div>
            </div>
        </div>
    </div>
</div>