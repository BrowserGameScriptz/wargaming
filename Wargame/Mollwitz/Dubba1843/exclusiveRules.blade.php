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
        <div class="game-rules">
            <H1>
                <?=$name?>
            </H1>
            <h2 class="exclusive"> EXCLUSIVE RULES
            </h2>
            <div class="indent">
                <ol>
                    @section('inner-units')
                        @parent
                        @include('wargame::Mollwitz.india-units',['beluchi'=>'Beluchi'])
                    @show
                    <li>
                <span>Deploy Phase</span>

                <p class="indent">The <?= $deployName[1] ?> player deploys first. The <?= $deployName[2] ?> player deploys
                    Second</p>

                    </li>
                        <li>
                            <span>First Player</span>
                <p class="indent">The <?= $forceName[1] ?> player moves first. The  <?= $forceName[2] ?>  player moves second.
                    After the <?= $forceName[2] ?> player completes their
                    turn, the game turn is incremented.</p>

                    </li>

                </ol>

            </div>
            @section('victoryConditions')
            @show
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