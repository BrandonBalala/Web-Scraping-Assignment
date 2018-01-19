<?php
Class Home{
	private $price;
	private $address;
	private $location;
	private $bedroom;
	private $bathroom;
	private $type;

	function __construct($price = 0, $address = "", $location = "", $bedroom = 0, $bathroom = 0, $type = ""){
		$this -> setPrice($price);
		$this -> setAddress($address);
		$this -> setLocation($location);
		$this -> setBedroom($bedroom);
		$this -> setBathroom($bathroom);
		$this -> setHomeType($type);
	}

	function getPrice(){
		return $this -> price;
	}

	function setPrice($price){
		if(is_null($price) || $price < 0 || !is_numeric($price))
			$this -> price = 0;
		else
			$this -> price = round($price, 2, PHP_ROUND_HALF_EVEN);
	}

	function getAddress(){
		return $this -> address;
	}

	function setAddress($address){
		if(is_null($address) || !is_string($address))
			$this -> address = "";
		else
			$this -> address = trim($address);
	}

	function getLocation(){
		return $this -> location;
	}

	function setLocation($location){
		if(is_null($location) || !is_string($location))
			$this -> location = "";
		else
			$this -> location = trim($location);
	}

	function getBedroom(){
		return $this -> bedroom;
	}

	function setBedroom($bedroom){
		if(is_null($bedroom) || $bedroom < 0 || !is_numeric($bedroom))
			$this -> bedroom = 0;
		else
			$this -> bedroom = round($bedroom, 0, PHP_ROUND_HALF_EVEN);
	}

	function getBathroom(){
		return $this -> bathroom;
	}

	function setBathroom($bathroom){
		if(is_null($bathroom) || $bathroom < 0 || !is_numeric($bathroom))
			$this -> bathroom = 0;
		else
			$this -> bathroom = round($bathroom, 0, PHP_ROUND_HALF_EVEN);
	}

	function getHomeType(){
		return $this -> type;
	}

	function setHomeType($type){
		if(is_null($type) || !is_string($type))
			$this -> type = "";
		else
			$this -> type  = trim($type);
	}

	function __toString(){
		return "Price: ". $this -> price
				."\r\nAddress: ". $this -> address
				."\r\nLocation: ". $this -> location
				."\r\nType of Home: ". $this -> type
				."\r\nNumber of bedrooms: ". $this -> bedroom
				."\r\nNumber of bathrooms: ". $this -> bathroom
				."\r\n\r\n";
	}
}