@include('wargame::global-header')
@include('wargame::TMCW.Airborne.airborneHeader')
<style type="text/css">
    <?php
    include_once "Wargame/TMCW/Airborne/all.css";
?>
</style>
</head>

@section('unitRules')
    @parent
    <li class="exclusive">No units may be receive replacements in this game.
    </li>
@endsection

@section('victoryConditions')
    @include('wargame::TMCW.Amph.victoryConditions')
@endsection

@section('commonRules')
    @include('wargame::TMCW.commonRules')
@endsection

@section('exclusiveRules')
    @include('wargame::TMCW.exclusiveRules')
@endsection

@section('obc')
    @include('wargame::TMCW.Airborne.obc')
@endsection

@include('wargame::stdIncludes.view' )