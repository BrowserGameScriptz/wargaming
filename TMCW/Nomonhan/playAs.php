<head>
    <meta charset="UTF-8">
</head>
<body>
<style>
    <?php include "playAs.css";?>
    body{
        background:url("<?=base_url("js/KhalkhinGolTank.jpg")?>") #333 no-repeat;
        background-size:100%;
    }
    h1{
        color:#f66;
        text-shadow: 0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,
        0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,
        0 0 5px white,0 0 5px white,0 0 5px white, 0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,
        0 0 5px white,0 0 5px white,0 0 5px white
    }
    h2{
        text-align:center;
        font-size:38px;
        font-family:'Great Vibes';
        color:#f66;
        text-shadow: 0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,
        0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,
        0 0 5px white,0 0 5px white,0 0 5px white, 0 0 5px white,0 0 5px white,0 0 5px white,0 0 5px white,
        0 0 5px white,0 0 5px white,0 0 5px white
    }
    .rebel{
        font-size:40px;
        color:red;
    }
    .loyalist{
      font-size:40px;
        color:blue;

    }
    .link{
        font-size:40px;
        text-decoration: none;
        color:#f66;
        text-shadow: 3px 3px 3px black,3px 3px 3px black,3px 3px 3px black,3px 3px 3px black,3px 3px 3px black
    }
    legend   {
    color:white;
    }
   fieldset{
       background:rgba(0,0,0,.3);
       text-align:center;width:30%;
       margin:60px auto;
       border-radius:15px;
   }
    .clear{
        clear:both;
    }
    a:hover{
        text-decoration: underline;
    }
    @font-face{
        font-family: Zenzai;
        src: url('<?=base_url("js/CHOWFUN.ttf");?>');
    }
    .zenFont{
        font-family:Zenzai;
    }
</style>

<h2 class="zenFont"> Welcome to</h2>
    <h1 class="zenFont" style="text-align:center;font-size:90px;line-height:40px;">The Nomonhan Incident</h1>
<div class="clear">&nbsp;</div>
<fieldset ><Legend>Play As </Legend>
    <a class="link"  href="<?=site_url("wargame/enterHotseat");?>/<?=$wargame?>">Play Hotseat</a><br>
    <a class="link"  href="<?=site_url("wargame/enterMulti");?>/<?=$wargame?>">Play Multi Player </a><br>
    <a class="link" href="<?=site_url("wargame/leaveGame");?>">Go to Lobby</a>
    <div class="attribution">
        By Dōmei Tsushin (Contemporary Military Historian) [Public domain], <a target='blank' href="http://commons.wikimedia.org/wiki/File%3ABattle_of_Khalkhin_Gol-Japanese_Type_89_Chi-Ro_midium_tank.jpg">via Wikimedia Commons</a>
    </div>
</fieldset>
