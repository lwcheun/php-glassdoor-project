
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  
  
  
      <link rel="stylesheet" href="fancystyle.css">

  

<?php
//include('simple_html_dom.php');
//DisplayHotNewJobs("MI");
//CrawlHotNewJobs(getJobsUrl("MI"));
//AdvancedCrawlHotNewJobs("MI");
function getJobsUrl($state) {
	// echo $_SERVER['HTTP_USER_AGENT'];
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
			//print_r($result->response->cities[0]->numJobs);
			//print_r($result->response->attributionURL);
			//print_r($result);
	return $result->response->attributionURL;
}

function AdvancedCrawlHotNewJobs($state){
	$url = getJobsUrl($state);

$baseUrl = explode(".htm", $url);
echo $baseUrl[0] ;

$urlpage2 = $baseUrl[0] . "_IP2.htm";
$urlpage3 = $baseUrl[0] . "_IP3.htm";
$urlpage4 = $baseUrl[0] . "_IP4.htm";
$urlpage5 = $baseUrl[0] . "_IP5.htm";


	$result1= CrawlHotNewJobs($url);
	$result2 = CrawlHotNewJobs($urlpage2);
	$result3= CrawlHotNewJobs($urlpage3);
	$result4= CrawlHotNewJobs($urlpage4);
	$result5= CrawlHotNewJobs($urlpage5);
echo $result1[0];
echo $result2[0];
echo $result3[0];
echo $result4[0];
echo $result5[0];

}
 function CrawlHotNewJobs($url) {

$options = array(
  'http'=>array(
    'method'=>"GET",
	
    'header'=>"Content-type: application/x-www-form-urlencoded
	Accept-language: en\r\n" .
              "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
              "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
  )
);

$context = stream_context_create($options);
$file = file_get_contents($url, false, $context);
//echo $file;
$html = new simple_html_dom();
$html->load($file);
$element = $html->find("section");
return $element ;
 }
 
 
 function DisplayHotNewJobs($state) {
	 if(is_null($state)){
		$state = 'NJ';
		
	}
$url = getJobsUrl($state);

$baseUrl = explode(".htm", $url);

$urlpage2 = $baseUrl[0] . "_IP2.htm";
$urlpage3 = $baseUrl[0] . "_IP3.htm";
$urlpage4 = $baseUrl[0] . "_IP4.htm";
$urlpage5 = $baseUrl[0] . "_IP5.htm";


$options = array(
  'http'=>array(
    'method'=>"GET",
	
    'header'=>"Content-type: application/x-www-form-urlencoded
	Accept-language: en\r\n" .
              "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
              "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
  )
);

$context = stream_context_create($options);
$file = file_get_contents($url, false, $context);
//echo $file;
$html = new simple_html_dom();
$html->load($file);
$element = $html->find("section");
?>
<table class="container">
    <thead>
        <tr>
		            <th>COMPANY</th>

		<th>JOBTITLE</th>
            <th>MEAN SALARY</th>
            <th>STATUS</th>
        </tr>
    </thead>
    <tbody>
       
   
 <?php
for($i = 0; $i < 30; $i++){
	 
	//echo $element[0]->find("li")[$i];
	$jobinfo = count($element[0]->find("li")[$i]->find("div"));
	if($jobinfo == 14){
	$removedLink = explode("</a>", $element[0]->find("li")[$i]->find("div")[2]);
$titleofLink = explode("'>", $removedLink[0]);
		?>
	<tr>          
			 <td><?php echo $element[0]->find("li")[$i]->find("div")[6]; ?></td>

		 <td><?php echo $titleofLink[2]; ?></td>

            <td><?php echo $element[0]->find("li")[$i]->find("div")[10]; ?></td>
            <td><?php echo $element[0]->find("li")[$i]->find("div")[11]; ?></td>
        </tr>
		<?php
		}
	
}
echo "</tbody>";
echo "</table>";
 }
?>  

