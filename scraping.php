<?php
	/**
	* This PHP script shall be used for the scraping portion of the project. It shall get all the homes available and create home objects
	* to be sent to the database. 
	*
	*
	* @author Christoffer Baur, Brandon Balala, Frank 
	* @date September 24 2015
	* @TODO Still need to go through every page by modifying the url, create the home objects, and modify code accordingly 
	* 			(Make the row loop a function to be called for every page, with a sleep(1))
	*/
	include 'Home.php';
	include 'crud.php';

	$url = 'http://www.royallepage.ca/search/homes/qc/montreal/?min_price=0&max_price=5000000%2B&property_type=8&lat=45.5016889&lng=-73.56725599999999&display_type=gallery-view&tier2=False&tier2_proximity=0&search_str=Montreal%2C+QC%2C+Canada&beds=0&baths=0&sfproperty_type%5b8%5d=8&transactionType=SALE&address=Montreal&method=homes&address_type=city&city_name=Montreal&prov_code=QC&sortby=low_to_high_price';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//default for curl is set to get
	//curl_setopt($ch, CURLOPT_GET, true);
	$page = curl_exec($ch);
	curl_close($ch);
	
	$dom = new DOMDocument();
	@$dom -> loadHTML($page);
	//The class used for condo fields is 'text-holder'
	//em tag, for the price
	//address tag, then "a" tag is used for address
	//ul tag, then li tag contents for city and province
	//ul tag class list, then li tag for TYPE of home, and # of bds and bths
	
	$ulrows = $dom -> getElementsByTagName('ul');
	$divrows = null;
	$rows = null;

	foreach($ulrows as $ul){
		if(($ul -> getAttribute('class')) == 'result-list'){
			$divrows = $ul -> getElementsByTagName('div');
		}
	}
	foreach ($divrows as $div){
		if(($div -> getAttribute('class')) == 'text-holder'){
			$rows[] = $div;
		}
	}

	/*
	$aTag = $dom -> getElementsByTagName('a');
	$numPages = -1;
	foreach ($aTag as $a) {
		if(($a -> getAttribute('href')) == '/search/homes/qc/montreal/46/?min_price=0&max_price=5000000%2B&property_type=8&lat=45.5016889&lng=-73.56725599999999&display_type=gallery-view&tier2=False&tier2_proximity=0&search_str=Montreal%2C+QC%2C+Canada&beds=0&baths=0&sfproperty_type%5B8%5D=8&transactionType=SALE&address=Montreal&method=homes&address_type=city&city_name=Montreal&prov_code=QC&sortby=low_to_high_price'){
			$numPages = $a -> nodeValue;
		}
	}

	echo $numPages;
	*/

	//$url = 'http://www.royallepage.ca/search/homes/qc/montreal/'."$pagenum//".'?min_price=0&max_price=5000000%2B&property_type=8&lat=45.5016889&lng=-73.56725599999999&display_type=gallery-view&tier2=False&tier2_proximity=0&search_str=Montreal%2C+QC%2C+Canada&beds=0&baths=0&sfproperty_type%5b8%5d=8&transactionType=SALE&address=Montreal&method=homes&address_type=city&city_name=Montreal&prov_code=QC&sortby=low_to_high_price';

	
	//String containing all rooms of the home
	$rooms;
	//String containing only bedroom part of the home
	$bdrsection;
	//String containing only bathroom part of the home
	$bthsection;
	//List of homes
	$homeList = [];

	foreach($rows as $row){
		$home = new Home();
		//trim most of the fields because they may contain extra spaces

		//ltrim to remove the '$' at the start of price field
		//str_replace to remove the ','
		$home -> setPrice(str_replace(',', '', ltrim(trim($row -> getElementsByTagName('em') -> item(0) -> nodeValue), '$')));
		$home -> setAddress(trim($row -> getElementsByTagName('a') -> item(0) -> nodeValue));

		$li = $row -> getElementsByTagName('li');

		//The location, type of home, and rooms are all in an li tag
		//using regex that replaces multiple blank spaces into one space
		$home -> setLocation(preg_replace('!\s+!', ' ',$li -> item(0) -> nodeValue));
		$home -> setHomeType(trim($li -> item(1) -> nodeValue));
		$rooms = trim($li -> item(2) -> nodeValue);
		
		//each individual string section for bath room and bed room
		$bdrsection = trim(substr($rooms ,0 ,strpos($rooms, ',')));
		$bthsection = trim(substr($rooms, strpos($rooms, ',') + 1, strlen($rooms) - strlen($bdrsection) - 1));
		
		//actual number of rooms for bed rooms and bath rooms
		$home -> setBedroom(substr($bdrsection, 0, strpos($bdrsection, ' ')));
		$home -> setBathroom(substr($bthsection, 0, strpos($bthsection, ' ')));

		$homeList[] = $home;
	}


	foreach ($homeList as $home) {
		echo $home;
	}

	$host = "localhost";
	$user = "root";
	$password = "";
	$dbname = "scraping_assignment";

	createDatabase($host, $user, $password);
	createHomeTable($host, $user, $password, $dbname);
	addHomesToDB($host, $user, $password, $dbname, $homeList);

	/*
	//test
	$results = getHomesFromDB($host, $user, $password, $dbname);

	//test getting rows from database and displaying them
	foreach ($results as $home) {
		echo $home;
	}
	*/

	//testing getAveragePriceOfHouses
	$avgPrice = getAveragePriceOfHouses($host, $user, $password, $dbname, 0, 1000000, 1, 1, "Ahuntsic-Cartierville ..., QC");
	echo "Average price of houses worth less than 1M with 1 bedroom and bathroom: ".$avgPrice."\r\n";

	//testing getNumOfHousesWithDescription
	$count = getNumOfHousesWithDescription($host, $user, $password, $dbname, 0, 1000000, 1, 1, "Ahuntsic-Cartierville ..., QC");
	echo "Count of houses worth less than 1M with 1 bedroom and bathroom: ".$count."\r\n";

	//test getlocationArray
	$locationArr = getLocationArray($host, $user, $password, $dbname);
	foreach($locationArr as $elements){
		echo $elements."\r\n\r\n";
	}
?>
	