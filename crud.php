<?php
	/**
	* Creates a database called scrapping_assignment
	*/
	function createDatabase($host, $user, $password){
		
		try {
			$pdo = new PDO("mysql:host=$host", $user, $password);

			$stmt = "DROP DATABASE IF EXISTS scraping_assignment;
					 CREATE DATABASE scraping_assignment;";

			$pdo -> exec($stmt);
		} catch (PDOException $e) {
			echo $e -> getMessage();	
		} finally{
			unset ($pdo);
		}
	}

	/**
	* Creates the Home table
	*/
	function createHomeTable($host, $user, $password, $dbname){
		try {
			$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

			$stmt = "DROP TABLE IF EXISTS home;
					 CREATE TABLE home (
					 Home_Id int unsigned primary key auto_increment,
					 Price int(10) NOT NULL,
					 Address varchar(50) NOT NULL,
					 Location varchar(50) NOT NULL,
					 Bedroom int(2) NOT NULL,
					 Bathroom int(2) NOT NULL,
					 Type varchar(25) NOT NULL);";

			$pdo -> exec($stmt);
		} catch (PDOException $e) {
			echo $e -> getMessage();	
		} finally{
			unset ($pdo);
		}
	}

	/**
	* Populates the Home table with the given list of homes
	*/
	function addHomesToDB($host, $user, $password, $dbname, $homeList){
		try{
			$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

			$stmt = $pdo -> prepare('INSERT INTO home(price, address, location, bedroom, bathroom, type)
										VALUES(?,?,?,?,?,?);');

			$stmt -> bindParam(1, $price);
			$stmt -> bindParam(2, $address);
			$stmt -> bindParam(3, $location);
			$stmt -> bindParam(4, $bedroom);
			$stmt -> bindParam(5, $bathroom);
			$stmt -> bindParam(6, $type);
			
			foreach ($homeList as $home){
				$price = $home -> getPrice();
				$address = $home -> getAddress();
				$location = $home -> getLocation();
				$bedroom = $home -> getBedroom();
				$bathroom = $home -> getBathroom();
				$type = $home -> getHomeType();

				$stmt -> execute();
			}
		}
		catch(PDOException $e){
			echo $e -> getMessage();
		}
		finally{
			unset($pdo);
		}
	}
	/*
	//TEST remove this later on
	function getHomesFromDB($host, $user, $password, $dbname){
		try{
			$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

			$stmt = $pdo -> prepare('SELECT price, address, location, bedroom, bathroom, type
									FROM home;');
			
			$stmt -> execute();
			$results = $stmt -> fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,'Home');
		}
		catch(PDOException $e){
			echo $e -> getMessage();
		}
		finally{
			unset($pdo);
		}

		if(count($results))
			return $results;
		else
			return;
	}
	*/

	///////////////DO VALIDATION IN THE STICKY FORM//////////////
	/**
	* Returns the average price of houses that match the given descriptions.
	* So, basically to qualify, the house has to be in the price range and have the exact number of bedrooms and bathrooms,
	* and location
	*/
	function getAveragePriceOfHouses($host, $user, $password, $dbname, $minPrice, $maxPrice, $numBedrooms, $numBathrooms, $location){
		try{
			$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

			$stmt = $pdo -> prepare('SELECT AVG(price) FROM home
									 WHERE price >= ?
									 AND price <= ?
									 AND bedroom = ?
									 AND bathroom = ?
									 AND location = ?;');

			$stmt -> bindParam(1, $minPrice);
			$stmt -> bindParam(2, $maxPrice);
			$stmt -> bindParam(3, $numBedrooms);
			$stmt -> bindParam(4, $numBathrooms);
			$stmt -> bindParam(5, $location);

			$stmt -> execute();

			$avgPrice = $stmt -> fetch();
		}
		catch(PDOException $e){
			echo $e -> getMessage();
		}
		finally{
			unset($pdo);
		}

		//supposed to return only value, but returns an array
		return round($avgPrice[0], 2, PHP_ROUND_HALF_EVEN);
	}

	/////////////////DO VALIDATION IN THE STICKY FORM!!!!!!!!////////////////
	/**
	* Returns a count of the number of houses that match the given descriptions.
	* So, basically to qualify, the house has to be in the price range and have the exact number of bedrooms and bathrooms
	*/
	function getNumOfHousesWithDescription($host, $user, $password, $dbname, $minPrice, $maxPrice, $numBedrooms, $numBathrooms, $location){
		try{
			$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

			$stmt = $pdo -> prepare('SELECT count(*) FROM home
									 WHERE price >= ?
									 AND price <= ?
									 AND bedroom = ?
									 AND bathroom = ?
									 AND location = ?;');

			$stmt -> bindParam(1, $minPrice);
			$stmt -> bindParam(2, $maxPrice);
			$stmt -> bindParam(3, $numBedrooms);
			$stmt -> bindParam(4, $numBathrooms);
			$stmt -> bindParam(5, $location);

			$stmt -> execute();

			$count = $stmt -> fetch();
		}
		catch(PDOException $e){
			echo $e -> getMessage();
		}
		finally{
			unset($pdo);
		}

		//supposed to return only one value, but returns an array
		return $count[0];
	}

	/**
	* Returns an array of the locations
	*/
	function getLocationArray($host, $user, $password, $dbname){
		try{
			$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

			$stmt = $pdo -> prepare('SELECT DISTINCT location FROM home;');

			$stmt -> execute();

			$results = $stmt -> fetchAll();
			$locationArr = [];

			foreach($results as $element){
				$locationArr[] = $element[0];
			}
		}
		catch(PDOException $e){
			echo $e -> getMessage();
		}
		finally{
			unset($pdo);
		}

		//supposed to return only one value, but returns an array
		return $locationArr;
	}
?>
