<head>
    <style type="text/css">
        body{
            background:#ccc;
            color:#333;
            background: url("<?=base_url("js/Bataille_d'Aliwal_1.jpg")?>") #333 no-repeat;
            background-position: 25% 0;
            background-size:100%;
        }
        .wrapper{
            background:rgba(255,255,255,.8);
            border-radius:15px;
            padding:20px;
            margin:20px;
            border:3px solid gray;
        }
        a.British{
            color:#F00;
        }
        li{
            list-style-type: none;
        }
        div{
            text-align:center;
        }
        .center{
            float:left;
            width:8%;
            font-size:45px;
        }
        .left{
            width:45%;

            float:left;
        }
        .right{
            width:45%;

            float:right;
        }
        .clear{
            clear:both;
        }
        .big{
            font-size: 50px;
            text-align: center;
        }
        .British{
            color:#f00;
        }
        .Prussian{
            color:rgb(255,253,127);
            color:rgb(12,0,162);
            border-color:rgb(255,253,127) !important;
        }
        .Sikh {
            color: #865900;
        }
        .attribution{
            padding: 10px 0;
            background: rgba(255,255,255,.6);
        }
        .attribution a{
            color:red;
            text-shadow: 1px 1px 1px black;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php global $force_name;$playerOne = $force_name[1];
    $playerTwo = $force_name[2];?>
    <div class="left <?= $playerOne;?> big"><?= $playerOne;?></div>
    <div class="right <?= $playerTwo;?> big"><?= $playerTwo;?></div>
    <div class="clear"></div>
    <div class="left big <?= $playerOne;?>">
        YOU
    </div>
    <div class="center">&laquo;&laquo;vs&raquo;&raquo;</div>
    <div class="right">
        <ul>
            {users}
            <li><a class="<?= $playerTwo;?>" href="{path}/{wargame}/{me}/{key}">{key}</a></li>
            {/users}
        </ul>
    </div>
    <div class="clear"></div>
    <div class="big">OR</div>
    <div class="left">
        <ul>
            {others}
            <li><a class="<?= $playerOne;?>" href="{path}/{wargame}/{key}">{key}</a></li>
            {/others}
        </ul>
    </div>
    <div class="center">&laquo;&laquo;vs&raquo;&raquo;</div>
    <div class="right big <?= $playerTwo;?>">YOU</div>
    <div class="clear"></div>
    <div>
        <a href="<?=site_url("wargame/play");?>">Back to lobby</a>
    </div>
</div>
<div class="attribution">
    See page for author [Public domain], <a target="blank" href="http://commons.wikimedia.org/wiki/File%3ABataille_d&#039;Aliwal_1.jpg">via Wikimedia Commons</a>
</div>