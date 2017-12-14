<?php

$results = new stdClass();

function getTotalPageCount($city,$category) {
	/* Method to retrieve total amount of pages from JSON response for that location */
	$params = array(
					't.p' => '',
					't.k' => '',
					'userip' => '0.0.0.0',
					'v' => '1',
					'format' => 'json',
					'action' => $category,
					'countryId' => '1',
					'city' => $city,
					'pn' => 1,
					'ps' => 50
			  );
	
	// Access Token request
	$url = 'http://api.glassdoor.com/api/api.htm?' . http_build_query($params);
	$response = file_get_contents($url);
	$result = json_decode($response);
	return floor($result->response->totalNumberOfPages/10);
}

function getCompanyRatingsByCity($city,$pagenum) {
	/* Method to get overall company ratings by specified location */
	if(strlen($city)==2){
		$params = array(
				't.p' => '',
				't.k' => '',
				'userip' => '0.0.0.0',
				'v' => '1',
				'format' => 'json',
				'action' => 'employers',
				'countryId' => '1',
				'state' => $city,
				'pn' => $pagenum,
				'ps' => 50
		  	);
	} else {
		$params = array(
				't.p' => '',
				't.k' => '',
				'userip' => '0.0.0.0',
				'v' => '1',
				'format' => 'json',
				'action' => 'employers',
				'countryId' => '1',
				'city' => $city,
				'pn' => $pagenum,
				'ps' => 50
			);
	}
	// Access Token request
	$url = 'http://api.glassdoor.com/api/api.htm?' . http_build_query($params);
	$response = file_get_contents($url);
	$result = json_decode($response);
	echo $url;
	return $result->response->employers;
}

function FillCompanyRatingsByCity($city,$page){
	/* Method to fill local database with company rating results from response pages */
	if(is_null($city)){
		$city = 'NJ';
	}
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	
	for($a = 0; $a < $page; $a++) {
		$results = getCompanyRatingsByCity($city,$a);

		for($i = 0; $i < 10; $i++) {
			$nameofCompany = mysqli_escape_string($conn, $results[$i]->name);
			$sql = "INSERT INTO companyratings (Id, Name, Overall_Rating, Industry, location)
			VALUES ('".$results[$i]->id."', '".$nameofCompany."', '".$results[$i]->overallRating."', '".$results[$i]->industry."', '".$city."')";

			if ($conn->query($sql) === TRUE) {
			} else {
			}
		}
	}
	$conn->close();
}

function SortCompanyRatingsByCity($city){
	/* Function to sort company ratings in descending order */
	if(is_null($city)){
		$city = 'NJ';
	}
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	$sql = "SELECT name,overall_rating FROM `companyratings` WHERE LOCATION = '".$city."' ORDER BY `companyratings`.`OVERALL_RATING` DESC LIMIT 20";  
	// perform the query and store the result
	$result = $conn->query($sql);
	$json = array();
	// if the $result contains at least one row
	if ($result->num_rows > 0) {
	  // output data of each row from $result
	    while($row = $result->fetch_assoc()) {
			$row['overall_rating'] = floatval($row['overall_rating']);
			$json =$row;
			$data[] = $json;
	  	}
	} else {
	  	echo '0 results';
	}
	return json_encode($data);
	$conn->close();
}	

?>