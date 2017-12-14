<?php
include('simple_html_dom.php');

function getStateJobsUrl($state) {
	/* Method to get URL for state information to be crawled */
    $ua = ''.$_SERVER['HTTP_USER_AGENT'];
	$params = array('v' => 1,
					'format' => 'json',
					't.p' => '',
					't.k' => '',
					'userip' => '0.0.0.0',
					'useragent' => $ua,
					'l' => $state,
					'action' => 'jobs-stats',
					'fromAge' => 1,
					'returnCities' => 'true',
					'admLevelRequested' => 2
			  );
    // Authentication request
    $url = 'https://api.glassdoor.com/api/api.htm?' . http_build_query($params);
	$response = file_get_contents($url);
	// Native PHP object, please
	$result = json_decode($response);
	return $result->response->attributionURL;
}

function GetStateJobCount($state){
	/* Method to get total job counts from state */
	$url = getStateJobsUrl($state);
	$options = array(
  		'http'=> array(
    		'method'=>"GET",
			'header'=>"Content-type: application/x-www-form-urlencoded
			Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
            "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
  			)
		);
	$context = stream_context_create($options);
	$file = file_get_contents($url, false, $context);
	$html = new simple_html_dom();
	$html->load($file);
 
	# get an element representing the second paragraph
	$element = $html->find("p");
	$JobCount = explode("Jobs", $element[1]);
	$countvalue = explode(">", $JobCount[0]);
	$value = substr($countvalue[1], 0, -6);
	return $value;
}

function FillStateJobCount($state){
	/* Method to fill the total state abbreviation, job counts, and update time  into 
	state_job_counts table in database */
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	$end = GetStateJobCount($state);
	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	$sql = "INSERT INTO state_job_counts (state_id, jobcount, updatetime)VALUES 
	('".$state."', '".$end."', CURRENT_DATE )";
	$conn->query($sql);
	$conn->close();
}

function FillMapData1(){
	/* Method for filling map in dashboard with total job counts based on state beginning
	with letter A through M */
	$states = array( 
		"AK", "AL", "AR", "AZ", "CA", "CO", "CT", "DC",  
		"DE", "FL", "GA", "HI", "IA", "ID", "IL", "IN", "KS", "KY", "Louisiana",  
		"MA", "MD", "ME", "MI", "MN", "MO", "MS", "MT");
	for ($i = 0; $i < count($states); $i++) {
		FillStateJobCount($states[$i]);
	}
}

function FillMapData2(){
	/* Method for filling map in dashboard with total job counts based on state beginning
	with letter N through W */
	$states = array( 
		"NC", "ND", "NE",  
    	"NH", "NJ", "NM", "NV", "NY", "OH", "OK", "OR", "PA", "RI", "SC",  
    	"SD", "TN", "TX", "UT", "VA", "VT", "WA", "WI", "WV", "WY");
	for ($i = 0; $i < count($states); $i++) {
		FillStateJobCount($states[$i]);
  	}
}

function FillGeoInfo(){
	/* Method to fill the geo_infor table with states and coordinates */
	$statesjson = file_get_contents('states.json');
  	$GeoInfo = json_decode($statesjson);
  	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	for($i = 0; $i < 50; $i++) {
		$sql = "INSERT INTO geo_info (state_id, latitude,longitude)
		VALUES ('".$GeoInfo[$i]->title."', '".$GeoInfo[$i]->latitude."', '".$GeoInfo[$i]->longitude."')";

		if ($conn->query($sql) === TRUE) {
		} else {
		}
	}
}	
	
function getMapData(){
	/* Method to retrieve geo-coordinates and job counts from states to plot on map */
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	$conn = new mysqli($servername, $username, $password, $dbname);
	$sql = "SELECT  '0.5' as 'scale',sd.STATE_ID as 'title', p.LATITUDE as 'latitude', p.LONGITUDE as 'longitude', sd.JOBCOUNT AS 'jobs'
			FROM state_job_counts AS sd
			JOIN geo_info AS p 
			ON sd.STATE_ID = p.STATE_ID
			WHERE sd.UPDATETIME = '2017-11-29';"; 
	// perform the query and store the result
	$result = $conn->query($sql);
	$json = array();
	// if the $result contains at least one row
	if ($result->num_rows > 0) {
	  // output data of each row from $result
	    while($row = $result->fetch_assoc()) {
		    $row['zoomLevel'] = 5;
		    $row['scale'] = 0.5;
			$row['latitude'] = floatval($row['latitude']);
			$row['longitude'] = floatval($row['longitude']);
			$json =$row;
		   	$data[] = $json;
  		}
	} else {
  		echo '0 results';
	}
	return json_encode($data);
	$conn->close();
}

function getTopJobStatResults($state,$days) {
	/* Method to get top job count based on location and days inputed. */
    $ua = ''.$_SERVER['HTTP_USER_AGENT'];
	$params = array('v' => 1,
					'format' => 'json',
					't.p' => '201954',
					't.k' => 'jt63OtDFZI3',
					'userip' => '0.0.0.0',
					'useragent' => $ua,
					'l' => $state,
					'action' => 'jobs-stats',
					'fromAge' => $days,
					'returnCities' => 'true',
					'returnJobTitles' => 'true',
					'admLevelRequested' => 2
			  );
    // Authentication request
    $url = 'https://api.glassdoor.com/api/api.htm?' . http_build_query($params);
	$response = file_get_contents($url);
	// Native PHP object, please
	$result = json_decode($response);
	return $result->response->cities;
}

function FillCityJobStatResults($state) {
	/* Method to fill job counts, city, state, and update time based on location and days inputed
	into TopCitiesByState table in database.  */
	if(is_null($state)){
		$state = 'NJ';
	}
	if(strlen($state)>2){
		$state = 'NJ';
	}
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
	} 
	$results = getTopJobStatResults($state,1);
	
	for($i = 0; $i < 5; $i++) {
		$sql = "INSERT INTO TopCitiesByState (NUMJOBS, CITY, STATE_ID, UPDATETIME)
		VALUES ('".$results[$i]->numJobs."', '".$results[$i]->name."', '".$results[$i]->stateAbbreviation."', CURRENT_DATE )";

		if ($conn->query($sql) === TRUE) {
		} else {
		}
	}
	$conn->close();
}

function getJobStatData($state){
	/* Method to retrieve city and job counts based on location and days inputed
	from TopCitiesByState table in database to display in descending order in pie chart. */
	if(is_null($state)){
		$state = 'NJ';	
	}
	if(strlen($state)>2){
		$state = 'NJ';
	}
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	$sql = "SELECT CITY, NUMJOBS 
		FROM TopCitiesByState
		WHERE STATE_ID = '".$state."' AND UPDATETIME = CURRENT_DATE ORDER BY NUMJOBS DESC LIMIT 5;"; 
	// perform the query and store the result
	$result = $conn->query($sql);
	$json = array();
	// if the $result contains at least one row
	if ($result->num_rows > 0) {
  		// output data of each row from $result
  		while($row = $result->fetch_assoc()) {
			$json =$row;
		   	$data[] = $json;
  		}
	} else {
  		echo '0 results';
	}
	return json_encode($data);
	$conn->close();
}
function getIndustryResults($location, $category) {
	/* Method to get top job titles from the 33 job categories */
    $ua = ''.$_SERVER['HTTP_USER_AGENT'];
	$params = array('v' => 1,
					'format' => 'json',
					't.p' => '',
					't.k' => '',
					'userip' => '0.0.0.0',
					'useragent' => $ua,
					'l' => $location,
					'action' => 'jobs-stats',
					'jc' => $category,		// job categories 1-33
					'returnJobTitles' => 'true',
					'fromAge' => 1
			  );
	
    // Authentication request
    $url = 'https://api.glassdoor.com/api/api.htm?' . http_build_query($params);
	$response = file_get_contents($url);
	// Native PHP object, please
	$result = json_decode($response);
	return $result->response->jobTitles;
}

function fillIndustryResults($location, $category) {
	/* Method to fill top job titles based on location and job category */
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	if(is_null($location)){
		$location='san francisco';
	}  
	if(is_null($category)){
		$category="29";
	}  
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
	} 
	
	$results = getIndustryResults($location, $category);
	
	for($i = 1; $i < count($results); $i++) {
		$sql = "INSERT INTO TopJobTitles (JOB_CATEGORY, JOB_TITLE, NUMJOBS, LOCATION,UPDATETIME)
		VALUES ('".$category."', '".$results[$i]->jobTitle."', '".$results[$i]->numJobs."', '".$location."',CURRENT_DATE)";
	
		if ($conn->query($sql) === TRUE) {
		} else {
		}
	}
	$conn->close();
}

function getIndustryData($location, $category){
	/* Method to get top job titles based on location and job category from TopJobTitles
	table in database for display in pie chart*/
	if(is_null($location)){
		$location='san francisco';
	}  
	if(is_null($category)){
		$category="9";
	}  
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	$sql = "SELECT NUMJOBS, JOB_TITLE
		FROM TopJobTitles
		WHERE LOCATION = '".$location."' AND UPDATETIME = CURRENT_DATE AND JOB_CATEGORY = '".$category."'"; 
	// perform the query and store the result
	$result = $conn->query($sql);
	$json = array();
	// if the $result contains at least one row
	if ($result->num_rows > 0) {
  		// output data of each row from $result
  		while($row = $result->fetch_assoc()) {
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