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
?>
<script type="text/javascript">
var DR;
    document.addEventListener("DOMContentLoaded",function() {
        console.log("victory registered");
        x.register("vp", function (vp) {
            var bridge = "<span class='playerFrenchFace'>French</span> ";
            if (vp[0]) {
                bridge = "<span class='playerRussianFace'>Russians</span> ";
            }
            console.log("Victory called");
            DR.$("#victory").html(bridge + "Own bridge Hex, <span class='playerFrenchFace' >French Hexes " + vp[2] + "</span> <span class='playerRussianFace'>Russian Hexes " + vp[1] + "</span>");


        });
    });
</script>

