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
?><li><span class="lessBig">Victory Conditions</span>
    <ol>

        <li>
        Each side is awarded one victory point for each hostile combat factor destroyed. And
        multiple victory points for locations occupied.
            These locations are marked with numbers in blue for Swedish objectives and red for Saxon Polish Objectives.
            <p class="ruleComment">
                Note: objectives start in the possession of the enemy, so they will have a label of the opposite color
                of their number at the beginning of the game. It will switch back and forth depending upon whoever last occupied the objective.
            </p>
            </li>

        <li>{{$forceName[1]}} wins: At the end of any turn on or before the end of turn 6 that they have scored 30 or more
        points. Or they win if they score 40 points or more on or before the end of turn 8.</li>

        <li>{{$forceName[2]}} Wins: At the end of any turn on or before the end of turn 8 that they have scored 40 or

        more points, Or if the Swedish player fails to win by the end of the game.</li>

        <li>A Draw Occurs: If both sides meet their victory requirements at the end of the same turn.</li>
    </ol>
</li>