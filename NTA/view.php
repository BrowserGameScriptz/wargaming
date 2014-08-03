<body xmlns="http://www.w3.org/1999/html">
<div id="theDiv">
    <header id="header">
        <div id="headerContent">
            <div id="rightHeader">
                <div id="mouseMove">mouse</div>
                <div id="comlinkWrapper" style="float:right;">
                    <div id="comlink"></div>
                </div>
                <div id="menuWrapper"><h4 class="WrapperLabel" title="Game Menu">Menu</h4>

                    <div id="menu">
                        <div class="close">X</div>
                        <ul>
                            <li><a id="muteButton">mute</a></li>
                            <li><a href="<?= site_url("wargame/leaveGame"); ?>">Go To Lobby</a></li>
                            <li><a href="<?= site_url("users/logout"); ?>">logout</a></li>
                            <li><a href="#" onclick="seeUnits();return false;">See Units</a></li>
                            <li><a href="#" onclick="seeBoth();return false;">See Both</a></li>
                            <li><a href="#" onclick="seeMap();return false;">See Map</a></li>
                            <li class="closer"></li>
                        </ul>
                    </div>
                </div>
                <div id="infoWrapper"><h4 class="WrapperLabel" title="Game Information">Info</h4>

                    <div id="info">
                        <div class="close">X</div>
                        <ul>
                            <li> Welcome {user}</li>
                            <li>you are playing as  <?= $player; ?></li>
                            <li>
                                in <span class="game-name">{gameName}-{arg}</span></li>
                            <li> The file is called {wargame}</li>
                            <li>Game Designer: David Rodal</li>

                            <li class="closer"></li>
                        </ul>
                    </div>
                </div>
                <span id="clock"></span>
                <span id="status"></span>
                <span id="requiredCombats"></span>
                <span id="victory"></span>
            </div>
            <div style="clear:both;"></div>

            <!--            <div id="clickCnt"></div>-->
            <!--        <button id="timeMachine">Time Travel</button>-->
            <!--        <button id="timeSurge">Time Surge</button>-->
            <!--        <button id="timeLive">Live</button>-->
            <!--        <span id="phaseClicks"></span>-->
        </div>
        <?php global $results_name; ?>
        <div id="bottomHeader" style="clear:both;">
            <div id="crtWrapper">
                <h4 class="WrapperLabel" title='Combat Results Table'><span class="goLeft">&laquo;</span>CRT<span
                        class="goRight">&raquo;</span></h4>

                <div id="crt">
                    <div class="close">X</div>
                    <div id="altTable">show cavalry table</div>
                    <div id="mainTable">show normal table</div>
                    <h3>Combat Odds</h3>

                    <div class="tableWrapper main">
                        <div id="odds">
                            <span class="col0">&nbsp;</span>
                            <?php
                            $crt = new CombatResultsTable();

                            $i = 1;
                            foreach ($crt->combatResultsHeader as $odds) {
                                ?>
                                <span class="col<?= $i++ ?>"><?= $odds ?></span>
                            <?php } ?>
                        </div>
                        <?php
                        $rowNum = 1;
                        $odd = ($rowNum & 1) ? "odd" : "even";
                        foreach ($crt->combatResultsTable as $row) {
                            ?>
                            <div class="roll <?= "row$rowNum $odd" ?>">
                                <span class="col0"><?= $rowNum++ ?></span>
                                <?php $col = 1;
                                foreach ($row as $cell) {
                                    ?>
                                    <span class="col<?= $col++ ?>"><?= $results_name[$cell] ?></span>

                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tableWrapper alt">
                        <div id="odds">
                            <span class="col0">&nbsp;</span>
                            <?php
                            $crt = new CombatResultsTable();

                            $i = 1;
                            foreach ($crt->combatResultsHeader as $odds) {
                                ?>
                                <span class="col<?= $i++ ?>"><?= $odds ?></span>
                            <?php } ?>
                        </div>
                        <?php
                        $rowNum = 1;
                        $odd = ($rowNum & 1) ? "odd" : "even";
                        foreach ($crt->combatResultsTableCav as $row) {
                            ?>
                            <div class="roll <?= "row$rowNum $odd" ?>">
                                <span class="col0"><?= $rowNum++ ?></span>
                                <?php $col = 1;
                                foreach ($row as $cell) {
                                    ?>
                                    <span class="col<?= $col++ ?>"><?= $results_name[$cell] ?></span>

                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div id="crtOddsExp"></div>
                </div>
            </div>
            <button id="nextPhaseButton">Next Phase</button>
            <div class="dropDown" id="jumpWrapper">
                <h4 class="WrapperLabel" title="Jump Map">Jump</h4>
            </div>

            <?php include_once "obc.php"; ?>

            <?php include_once "tec.php"; ?>
            <?php include_once "timeTravel.php"; ?>

            <?php include_once "help.php"; ?>
            <span id="hideShow">Units Retired</span>
    </header>
    <div id="content">
        <div id="rightCol">
            <div id="deployWrapper">
                <div style="margin-right:3px;" class="left">deploy on turn one</div>
                <div id="deployBox"></div>
                <div style="clear:both;"></div>
            </div>
            <div style="display:none;" id="deadpile">
                <div style="right:10px;font-size:50px;font-family:sans-serif;bottom:10px;position:absolute;color:#666;">
                    Retired Units
                </div>
            </div>
            <div id="gameViewer">
                <div id="gameContainer">
                    <div id="gameImages" class="ui-widget-content">
                        <img id="map" alt="map" src="<?php echo base_url() . $mapUrl; ?>"
                             style="position: relative;visibility: visible;z-index: 0;">
                        <?php $id = 0; ?>
                        {units}
                        <div class="unit {class}" id="{id}" alt="0">
                            <div class="shadow_mask"></div>
                            <img class="arrow" src="<?php echo base_url(); ?>js/short-red-arrow-md.png" class="counter">
                            <div class="counterWrapper">
                                <img src="<?php echo base_url(); ?>js/{image}" class="counter">
                            </div>
                            <div class="unit-numbers">5 - 4</div>

                        </div>
                        {/units}
                        <div id="floatMessage">
                            <header></header>
                            <p></p></div>
                    </div>
                </div>
            </div>

            <audio class="pop" src="<?= base_url() . 'js/pop.m4a' ?>"></audio>
            <audio class="poop" src="<?= base_url() . 'js/lowpop.m4a' ?>"></audio>
            <audio class="buzz" src="<?= base_url() . 'js/buzz.m4a' ?>"></audio>

            <div style="clear:both;height:20px;"></div>
        </div>
    </div>
    <script type="text/javascript">
    </script>
    <div id="display"></div>
</div>
</body></html>