
<?php 

function getCityJobs($city,$days) {
	
	// echo $_SERVER['HTTP_USER_AGENT'];
    $ua = ''.$_SERVER['HTTP_USER_AGENT'];
	$params = array('v' => 1,
					'format' => 'json',
					't.p' => '',
					't.k' => '',
					'userip' => '0.0.0.0',
					'useragent' => $ua,
						'l' => $city,
					'action' => 'jobs-stats',
					'fromAge' => $days,
					'returnCities' => 'true',
					'admLevelRequested' => 2

			  );
	
    // Authentication request
    $url = 'https://api.glassdoor.com/api/api.htm?' . http_build_query($params);
	$response = file_get_contents($url);
	// Native PHP object, please
	$result = json_decode($response);
			 
			//print_r($result->response->attributionURL);
			//print_r($result->response->cities[0]);
			//var_dump($result->response->cities[0]->numJobs);
			//echo "  ";
			$count = $result->response->cities[0]->numJobs;
	return $count;

}
function getCityJobTrends($city,$days) {
	if($days>1){
		$currentdaycount = getCityJobs($city,$days);
		$nextday = $days - 1;
		$nextdaycount = getCityJobs($city,$nextday);
		$actualcount = $currentdaycount - 	$nextdaycount;
		return $actualcount;
	}
	else{
		$actualcount = getCityJobs($city,$days);
		return $actualcount;
		
	}
}
function FillCityJobTrends($state) {
		if(is_null($state)){
		$state = 'NJ';
		
	}
	
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "crawledin";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
	} 
	
	for($i = 7; $i > 0; $i--) {
			$numjobs = getCityJobTrends($state,$i);;

		//$cityName = mysqli_escape_string($conn, $results[$i]->name);
		$sql = "INSERT INTO job_count_trends (LOCATION,JOBCOUNT, UPDATETIME)
		VALUES ('".$state."', '".$numjobs."',  DATE_SUB(CURRENT_DATE, INTERVAL '".$i."' DAY))";

	if ($conn->query($sql) === TRUE) {
	} else {
	}
	}
	$conn->close();
}
function FetchCityJobTrends($state) {
		if(is_null($state)){
		$state = 'NJ';
		
	}
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "";
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	$sql = "SELECT  JOBCOUNT,UPDATETIME 
		FROM job_count_trends
		WHERE LOCATION = '".$state."';"; 
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
	//echo json_encode($data);
	return json_encode($data);
	$conn->close();
}
//FetchCityJobTrends('los angeles');
//FillCityJobTrends('los angeles');
/*getCityJobTrends('santa barbara',7);
getCityJobTrends('santa barbara',6);
getCityJobTrends('santa barbara',5);
getCityJobTrends('santa barbara',4);
getCityJobTrends('santa barbara',3);
getCityJobTrends('santa barbara',2);
getCityJobTrends('santa barbara',1);
//getCityJobs('santa barbara',7);*/
?>