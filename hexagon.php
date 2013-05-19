<?php
// Hexagon.php
//
// copyright (c) 2009-2011 Mark Butler
// This program is free software; you can redistribute it 
// and/or modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation;
// either version 2 of the License, or (at your option) any later version. 

// Hexagon Constructor

class extHex extends Hexagon implements JsonSerializable{
    public function jsonSerialize(){
        return $this;
    }

}
class HexPath implements JsonSerializable{
    public $name = false;
    public $pointsLeft = false;
    public $isZoc = false;
    public $isValid = true;
    public $isOccupied = false;
    public $pathToHere = array();
    public $depth = false;
    public $firstHex = false;
    public function jsonSerialize(){
        unset($this->isZoc);
        unset($this->isValid);
//        unset($this->isOccupied);
        unset($this->name);
        unset($this->depth);
        unset($this->firstHex);
//        var_dump($this->name);
        return $this;
    }

}
class Hexagon  {

	public $evenColumnShiftDown;
	public $number;
	public $x = false, $y = false;
	public $name;
	public $minX, $minY;
	public $maxX, $maxY;
    public $parent = "gameImages";


    function __construct($a1 = false, $a2 = false){
        $mapData = MapData::getInstance();

        $this->evenColumnShiftDown = true;
        $this->number = 0;
        $this->x = 0;
        $this->y = 0;
        $this->name = "";
        $this->minX = 4;
        $this->minY = 8;
        $this->maxX = $mapData->maxX * 2 + 2;
        $this->maxY = $mapData->maxY * 4 + 4 + 2;

        // Hexagon(name)
        if ( $a1 !== false && $a2 === false ) {
          if(preg_match("/^[a-z]/",$a1)){
              $this->name = "0000";
              $this->parent = $a1;
          }  else{
            $this->name = $a1;
          }
            $this->number = intVal($this->name, 10);
            $this->calculateHexpartXY();
            $this->calculateHexagonName();
        }

        // Hexagon(x, y)
        if ( $a1 !== false && $a2 !== false ) {

                $this->x = $a1;
                $this->y = $a2;
                $this->calculateHexagonNumber();
                $this->calculateHexagonName();
        }
    }

    function getParent(){
        return $this->parent;
    }
function setNumber($number )
{
	$this->number = $number;
	$this->calculateHexpartXY();
	$this->calculateHexagonName();
}

private function setName( $name )
{
	$this->name = $name;
	$this->number = parseInt($this->name, 10);
	$this->calculateHexagonXY();
}

function setXY( $x, $y ) {

	$this->x = $x;
	$this->y = $y;

	$this->calculateHexagonNumber();	
	$this->calculateHexagonName();
}

private function calculateHexagonNumber()
{
  	$x = ( ($this->x - $this->minX ) / 2 ) + 1;

	if($this->evenColumnShiftDown == true)
	{
    		$y = floor((($this->y - $this->minY) / 4) + 1);
  	} else {
    		$y = floor((($this->y - $this->minY + 2) / 4) + 1);
  	}
  	$this->number = $x * 100 + $y;
}

private function calculateHexpartXY() {


 	$x = floor( $this->number / 100 );
	$y = $this->number - ( $x * 100 );

 	$this->y = 4 * ( $y - 1 ) + $this->minY;

	if($this->evenColumnShiftDown == true)
	{
		if ( $x % 2 == 0 ) $this->y += 2;
	} else {
		if ( $x % 2 == 0 ) $this->y -= 2;
	}
	
	$this->x = 2 * ( $x - 1 ) + $this->minX;
}

private function parseX($number) {


	$x = floor($number / 100);
	$x = 2 * ( $x - 1 ) + $this->minX;

	return $x;
}

private function parseY($number) {


	$x = floor(number / 100);
	$y = hexagon_Number % 100;

	$y = 4 * ( $y - 1 ) + $this->miny;

	if ($this->evenColumnsShiftDown == true)
	{
		if ($x % 2 == 0) $y += 2;
	}
	else
	{
		if ($x % 2 == 0) $y -= 2;
	}

	return $y;
}

private function calculateHexagonName() {

    $this->name = "";
    
	if ($this->x >= $this->minX && $this->y >= $this->minY && $this->x <= $this->maxX && $this->y <= $this->maxY)
	{
	    if ($this->number < 1000) {
	        $this->name = "0" + $this->number;
	    }
	    else {
	        $this->name = $this->number;
	    }
    }
}

function equals(Hexagon $hexagon) {

	$isEqual = false;
	
	if ( $this->x == $hexagon->getX() && $this->y == $hexagon->getY())
	{
		$isEqual = true;
	}
	
	return $isEqual;
}

 function getAdjacentHexagon( $direction ) {

	// direction 1=N, 2=NE, 3=SE, 4=S, 5=SW, 6=NW
	$adjX = array( 0,  0,  2,  2,  0, -2, -2 );
	$adjY = array( 0, -4, -2,  2,  4,  2, -2 );
	$this->x += $adjX[$direction];
	$this->y += $adjY[$direction];

    $this->calculateHexagonNumber();
	$this->calculateHexagonName();
}

function getX() {

	return $this->x;
}

function getY() {

    return $this->y;
}

function getNumber() {

	return $this->number;
}

function getName()
{
    $name = $this->name;

    if (preg_match("/^[a-z]/", $name)) {
        return $name;
    }
    $name = "0000".strval($this->number);
    $name = substr($name,-4);
    return $name;

}
}
