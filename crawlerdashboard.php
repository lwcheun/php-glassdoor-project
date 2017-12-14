<!-- Styles -->
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  
  
  
      <link rel="stylesheet" href="fancystyle.css">
<?php include 'companies.php';?>
<?php include 'jobs.php';?>
<?php include 'trends.php';?>
<?php include 'hotjobsandsalaries.php';?>

<style>
body { background-color: #3f3e3b; color: #fff; }


.map-marker {
    /* adjusting for the marker dimensions
    so that it is centered on coordinates */
    margin-left: -8px;
    margin-top: -8px;
}
.map-marker.map-clickable {
    cursor: pointer;
}
.pulse {
    width: 35px;
    height: 30px;
    border: 5px solid #0D8ECF;
    -webkit-border-radius: 30px;
    -moz-border-radius: 30px;
    border-radius: 30px;
    background-color: #0D8ECF;
    z-index: 10;
	text-align: left;
    position: absolute;
  }
.map-marker .dot {
    border: 10px solid #0D8ECF;
    background: transparent;
    -webkit-border-radius: 60px;
    -moz-border-radius: 60px;
    border-radius: 60px;
    height: 50px;
    width: 50px;
    -webkit-animation: pulse 3s ease-out;
    -moz-animation: pulse 3s ease-out;
    animation: pulse 3s ease-out;
    -webkit-animation-iteration-count: infinite;
    -moz-animation-iteration-count: infinite;
    animation-iteration-count: infinite;
    position: absolute;
    top: -20px;
    left: -20px;
    z-index: 1;
    opacity: 0;
  }
  @-moz-keyframes pulse {
   0% {
      -moz-transform: scale(0);
      opacity: 0.0;
   }
   25% {
      -moz-transform: scale(0);
      opacity: 0.1;
   }
   50% {
      -moz-transform: scale(0.1);
      opacity: 0.3;
   }
   75% {
      -moz-transform: scale(0.5);
      opacity: 0.5;
   }
   100% {
      -moz-transform: scale(1);
      opacity: 0.0;
   }
  }
  @-webkit-keyframes "pulse" {
   0% {
      -webkit-transform: scale(0);
      opacity: 0.0;
   }
   25% {
      -webkit-transform: scale(0);
      opacity: 0.1;
   }
   50% {
      -webkit-transform: scale(0.1);
      opacity: 0.3;
   }
   75% {
      -webkit-transform: scale(0.5);
      opacity: 0.5;
   }
   100% {
      -webkit-transform: scale(1);
      opacity: 0.0;
   }
  }
</style>

<!-- Resources -->
<script src="https://www.amcharts.com/lib/3/serial.js"></script>

<script src="https://www.amcharts.com/lib/3/ammap.js"></script>
<script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://www.amcharts.com/lib/3/themes/chalk.js"></script>

<!-- Chart code -->
<script>
/**
 * This example uses pulsating circles CSS by Kevin Urrutia
 * http://kevinurrutia.tumblr.com/post/16411271583/creating-a-css3-pulsating-circle
 */

var map = AmCharts.makeChart( "chartdiv", {
  "type": "map",
  "theme": "chalk",
  "projection": "miller",
  "zoomControl": {
  	"zoomControlEnabled": true,
  	"minZoomLevel": 2,
  },

  "imagesSettings": {
    "rollOverColor": "#089282",
    "rollOverScale": 3,
    "selectedScale": 3,
    "selectedColor": "#089282",
    "color": "#13564e"
  },

  "areasSettings": {
    "unlistedAreasColor": "#15A892"
  },

  "dataProvider": {
    "map": "worldLow",
    "zoomLevel": 5,
    "zoomLatitude": 41.1289,
    "zoomLongitude": -98.2883,    
    "images": <?php echo $myjson = getMapData();?>
  }
} );

// add events to recalculate map position when the map is moved or zoomed
map.addListener( "positionChanged", updateCustomMarkers );

function updateCustomMarkers( event ) {
  // get map object
  var map = event.chart;

  // go through all of the images
  for ( var x in map.dataProvider.images ) {
    // get MapImage object
    var image = map.dataProvider.images[ x ];

    // check if it has corresponding HTML element
    if ( 'undefined' == typeof image.externalElement )
      image.externalElement = createCustomMarker( image );

    // reposition the element according to coordinates
    var xy = map.coordinatesToStageXY( image.longitude, image.latitude );
    image.externalElement.style.top = xy.y + 'px';
    image.externalElement.style.left = xy.x + 'px';
	//image.externalElement.innerHTML = 'DSAHASJ';
  }
}

// this function creates and returns a new marker element
function createCustomMarker( image ) {
  // create holder
  var holder = document.createElement( 'div' );
  holder.className = 'map-marker';
  holder.title = image.title;
  holder.style.position = 'absolute';

  // maybe add a link to it?
  if ( undefined != image.url ) {
    holder.onclick = function() {
      window.location.href = image.url;
    };
    holder.className += ' map-clickable';
  }

  // create dot
  var dot = document.createElement( 'div' );
  dot.className = 'dot';
  holder.appendChild( dot );

  // create pulse
  var pulse = document.createElement( 'div' );
  pulse.className = 'pulse';
  holder.appendChild( pulse );
pulse.innerHTML = image.jobs;
pulse.style.cssText= 'font-size: 14px; cursor: pointer; text-align:center;';
  // append the marker to the map container
  image.chart.chartDiv.appendChild( holder );

  return holder;
}
</script>

<style>
.button {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}

.button2 {background-color: #008CBA; } /* Blue */
.button3 {background-color: #008CBA; } /* Blue */

#chartdiv {
  position:relative;
  float:left;
  width: 100%;
  height: 500px;
  color: #fff;
  text-align: left;
}
#chartdiv2 {
	float: left;
  width: 50%;
  height: 600px;
}
#chartdiv3 {
	float:left;
  width: 50%;
  height: 800px;
}
#trendchartdiv {
	float:left;
	width	: 50%;
	height	: 600px;
	color: #fff;
}#industrychartdiv {
	float:left;
	width	: 50%;
	height	: 800px;
}
.header {
   margin-top: 2cm;
}
.amcharts-export-menu-top-right {
  top: 10px;
  right: 0;
}
</style>

<!-- Resources -->
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />  
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/chalk.js"></script>
<script src="https://www.amcharts.com/lib/3/pie.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>


<form action="" method="post">
City/state: <input type="text" name="name" >
  <input id="submit1" name="submit" type="submit" class="button button2" value="View Highest Rated Companies & Job Trends">
*** Please use state abbreviations such as CA ***
 </form>
<div>
	<html>
		<body>
		  <form method="post" id="jobCat">
		  
		  Location: <input type="text" name="loc" >
			<select name="category" form="jobCat" id="dropdown">                      
				<option value="0" selected disabled>--Select Job Category--</option>
				<option value="1">Accounting / Finance</option>
				<option value="2">Administrative</option>
				<option value="3">Analyst</option>
				<option value="4">Architecture / Drafting</option>
				<option value="5">Art / Design / Entertainment</option>
				<option value="6">Banking / Loan / Insurance</option>
				<option value="7">Beauty / Wellness</option>
				<option value="8">Business Development / Consulting</option>
				<option value="9">Education</option>
				<option value="10">Engineering (Non-software)</option>
				<option value="11">Facilities / General Labor</option>
				<option value="12">Hospitality</option>
				<option value="13">Human Resources</option>
				<option value="14">Installation / Maintenance / Repair</option>
				<option value="15">Legal</option>
				<option value="16">Manufacturing / Production / Construction</option>
				<option value="17">Marketing / Advertising / PR</option>
				<option value="18">Medical / Healthcare</option>
				<option value="19">Non-Profit / Volunteering</option>
				<option value="20">Product / Project Management</option>
				<option value="21">Real Estate</option>
				<option value="22">Restaurant / Food Services</option>
				<option value="23">Retail</option>
				<option value="24">Sales / Customer Care</option>
				<option value="25">Science / Research</option>
				<option value="26">Security / Law Enforcement</option>
				<option value="27">Senior Management</option>
				<option value="28">Skilled Trade</option>
				<option value="29">Software Development / IT</option>
				<option value="30">Sports / Fitness</option>
				<option value="31">Travel / Transportation</option>
				<option value="32">Writing / Editing / Publishing</option>
				<option value="33">Other</option>																																														
			</select>
<input type="submit" class="button button3" value="Find Job Title by Category">			
		  </form>	
		</body>
	</html>
</div>
<script>
function drawTrendChart(){
<?php 
if(!isset($_POST["name"])){
	$_POST["name"] = 'NJ';
	
}
 echo FillCityJobTrends($_POST["name"]);?>
var chart4 = AmCharts.makeChart("trendchartdiv", {
    "type": "serial",
    "theme": "dark",
    "marginRight": 40,
    "marginLeft": 40,
    "autoMarginOffset": 20,
    "mouseWheelZoomEnabled":true,
	 "color": "white",
    "dataDateFormat": "YYYY-MM-DD",
    "valueAxes": [{
        "id": "v1",
        "axisAlpha": 0,
        "position": "left",
        "ignoreAxisWidth":true
    }],
    "balloon": {
        "borderThickness": 1,
        "shadowAlpha": 0
    },
    "graphs": [{
        "id": "g1",
        "balloon":{
          "drop":true,
          "adjustBorderColor":false,
          "color":"#ffffff"
        },
        "bullet": "round",
        "bulletBorderAlpha": 1,
        "bulletColor": "#FFFFFF",
        "bulletSize": 5,
        "hideBulletsCount": 50,
        "lineThickness": 2,
        "title": "red line",
        "useLineColorForBulletBorder": true,
        "valueField": "JOBCOUNT",
        "balloonText": "<span style='font-size:18px;'>[[value]]</span>"
    }],
    "chartScrollbar": {
        "graph": "g1",
        "oppositeAxis":false,
        "offset":30,
        "scrollbarHeight": 80,
        "backgroundAlpha": 0,
        "selectedBackgroundAlpha": 0.1,
        "selectedBackgroundColor": "#888888",
        "graphFillAlpha": 0,
        "graphLineAlpha": 0.5,
        "selectedGraphFillAlpha": 0,
        "selectedGraphLineAlpha": 1,
        "autoGridCount":true,
        "color":"#AAAAAA"
    },
    "chartCursor": {
        "pan": true,
        "valueLineEnabled": true,
        "valueLineBalloonEnabled": true,
        "cursorAlpha":1,
        "cursorColor":"#258cbb",
        "limitToGraph":"g1",
        "valueLineAlpha":0.2,
        "valueZoomable":true
    },
    "valueScrollbar":{
      "oppositeAxis":false,
      "offset":50,
      "scrollbarHeight":10
    },
    "categoryField": "UPDATETIME",
    "categoryAxis": {
        "parseDates": true,
        "dashLength": 1,
        "minorGridEnabled": true
    },
    "export": {
        "enabled": true
    },
    "dataProvider": <?php echo FetchCityJobTrends($_POST["name"]);?>
});
}
</script>
<script>
 var myEl = document.getElementById('submit1');
myEl.addEventListener('click', drawBarChart(), false);
<!--function myFunction() {
<!--   alert("das");
<!--}
/*var element = document.getElementById('submit2');
element.addEventListener('click', CitiesPieChart(), false);*/


function drawBarChart(){
 <?php 
if(!isset($_POST["name"])){
	$_POST["name"] = 'NJ';
	
}
 echo FillCompanyRatingsByCity($_POST["name"],10);?>

var chart = AmCharts.makeChart("chartdiv2", {
  "type": "serial",
  "theme": "light",
  "marginRight": 30,
  "dataProvider": <?php echo SortCompanyRatingsByCity($_POST["name"]);?>,
  "startDuration": 1,
  "fontSize": 15,
  "color": "white",
  "graphs": [{
	  
	"color": "white",
    "balloonText": "<b>[[category]]: [[value]]</b>",
    "fillColorsField": "color",
    "fillAlphas": 0.9,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "overall_rating"
  }],
  "depth3D": 60,
  "angle": 20,
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
   "valueAxes": [{
    "axisAlpha": 0,
    "position": "left",
    "title": "Highest rated companies in  <?php echo $_POST["name"];?>"
  }],
  "categoryField": "name",
  "categoryAxis": {
    "gridPosition": "start",
    "labelRotation": 45
  },
  "export": {
    "enabled": true
  }

});}

drawTrendChart();
CitiesPieChart();
drawIndustryChart();
function CitiesPieChart(){
<?php 
if(!isset($_POST["name"])){
	$_POST["name"] = 'NJ';
	
}
 echo FillCityJobStatResults($_POST["name"],1);?>
var chart3 = AmCharts.makeChart( "chartdiv3", {
  "type": "pie",
  "theme": "light",
  "dataProvider": <?php echo getJobStatData($_POST["name"]);?>,
  "valueField": "NUMJOBS",
  "titleField": "CITY",
  "color": "white",
  "labelText": "[[title]]: [[value]]",
  "outlineAlpha": 0.4,
  "depth3D": 15,
  "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b></span>",
  "angle": 30,
  "export": {
    "enabled": true
  }
} );
}

function drawIndustryChart(){
	<?php if(!isset($_POST["loc"])){
		
		$_POST["loc"]='san francisco';
	}  
	if(!isset($_POST["category"])){
		
		$_POST["category"]="29";
	}  
	?>
<?php echo fillIndustryResults($_POST["loc"], $_POST["category"]);?>
var chart5 = AmCharts.makeChart("industrychartdiv", {
	"type": "pie",
	"theme": "light",
	"dataProvider": <?php echo getIndustryData($_POST["loc"], $_POST["category"]);?>,
	"valueField": "NUMJOBS",
	"titleField": "JOB_TITLE",
	"labelText": "[[title]]: [[value]]",
	"color": "white",
	"outlineAlpha": 0.4,
	"depth3D": 15,
	"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b></span>",
	"angle": 30,
	"export": {
	"enabled": true
	},
     "valueAxes": [{
    "axisAlpha": 0,
    "position": "top",
    "title": "Top Jobs in"
    }]
} );
}

</script>
<div class="header">
<h1>TOTAL # OF JOBS AVAILABLE IN EACH STATE</h1>
</div>
 <div id="chartdiv">
     

</div>
<div class="header">
<?php if(isset($_POST["name"])){?>
<h1>HOT/NEW JOBS IN <?php echo $_POST["name"];?></h1><?php }?>
</div>
<?php DisplayHotNewJobs($_POST["name"]);?>

<div class="header">
<?php if(isset($_POST["name"])){?>
<h1>JOB TREND IN <?php echo $_POST["name"];?></h1><?php }?>
</div>
<div id="trendchartdiv"></div>
<div id="chartdiv2">  </div>
<div class="header">
<?php if(!is_null($_POST["name"])){?>
<h1>TOP 5 CITIES IN <?php if(strlen($_POST["name"])>2){
	$_POST["name"] = 'NJ';
} echo $_POST["name"];?></h1><?php }?>
</div>
<div id="chartdiv3"></div>

<div id="industrychartdiv"></div>


<!-- HTML -->