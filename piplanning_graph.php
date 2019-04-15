<?php


$nav_selected = "PIPLANNING";
$left_buttons = "YES";
$left_selected = "CADENCE";

include("./nav.php");
global $db;

$hostname = gethostname();

$image = "";

//Amanda: svi6w289
//Jasthi: svi6p274

if($hostname == 'svi6p274' || $hostname == 'ami6p042' || $hostname == 'cii6p660'){
$image = "<img src='images/edit.png' align='right' style='max-height: 15px;'/>";
}
?>

<?php

	echo "<script type='text/javascript'></script>"; //for later use
    
				
		//
	//$sql= "SELECT * FROM `capacity`;";
	$currentCadenceQuery ="SELECT * FROM `cadence` where end_date>CURRENT_DATE() order by start_date limit 1";
	$GLOBALS['currentCadenceResults'] = mysqli_query($db, $currentCadenceQuery);
	$todayCadence = $currentCadenceResults->fetch_assoc();
	$todayCadence = $todayCadence['PI_id'];
	//Query cadence table for PI_id drop down
	$cadenceQuery = "SELECT Distinct PI_id FROM cadence";
	$GLOBALS['cadenceResults'] = mysqli_query($db, $cadenceQuery);
	//Note: If I use cadenceResults in two places, it causes the dropdown to bug out and only show PI-1905.
	//I have NO IDEA why it does this but making a seperate query somehow fixes the issue. 
	$cadenceQuery2 = "SELECT Distinct PI_id FROM cadence"; 
	$GLOBALS['cadenceResults2'] = mysqli_query($db, $cadenceQuery2);
	
	$dataPoints = array();
		
	echo "<script type='text/javascript'>var phpJSArray = []; var primArray = {}; </script>"; //Declare JS array.
	if ($cadenceResults2->num_rows > 0){

		while ($row2 = $cadenceResults2->fetch_assoc()) {
			echo "<script type='text/javascript'>var dps = [];</script>";
			//$q = "PI-1905";
			$q = $row2['PI_id'];
			
			$sql = "SELECT t.team_id, c.total
					FROM capacity c RIGHT OUTER JOIN trains_and_teams t ON (t.team_id = c.team_id)
					WHERE ((c.program_increment = '$q' OR c.total IS null) OR (c.program_increment is null))

					AND t.team_id LIKE 'ART-%%%'
					ORDER BY t.team_id";
					
			$result = $db->query($sql);
			if($result -> num_rows > 0){
				while($row = $result -> fetch_assoc()){
				$total = $row["total"];
				$teamID = $row["team_id"];

				array_push($dataPoints, array("y" => $row["total"] ,"label" => $row["team_id"] ));
				$total = $row['total'];
				$teamID = $row["team_id"];
				echo "<script type='text/javascript'>
				//
				var tots = parseInt('$total', 10);
				var tString = String('$teamID');
				
				dps.push({y: tots, label: tString});
				</script>";
				 
				}
			}
		$piID =$row2['PI_id'];
		echo "<script type='text/javascript'>
		piID = String('$piID');
		//alert('$piID');
		phpJSArray.push(dps);
		primArray[piID] = dps; 
		</script>";
		
		

	}
	}
		
		$win = 201;
		$twin = '20';
		echo "<script type='text/javascript'>
		var w = parseInt('$win', 10);
														for(var x = 0; x < '$win'; x++){}
		//dpss.push({y: w, x: w});
		
		</script>";
		

?>
<!DOCTYPE HTML>
<html>
<head>
<script>






</script>
</head>
<body onload = "getArtGraph('<?php echo $todayCadence ?>')">
<div class="container">
	<section>
		<h1>Dolphins Capacity Summary Table</h1>
		<br>
		<form method="POST" action="/piplanning_graph.php">
			<div class="grid-container">
			

				<label for="iteration_id">Program Increment ID</label>
				<select name="increment_id" id="increment_id" onchange="getArtGraph(this.value)" >
					<?php $cnt = 0; echo "<option value='". $cnt ."'>".((empty($incrementId)) ? $todayCadence : $incrementId)."</option>";?>
					<?php	if ($cadenceResults->num_rows > 0){
						while ($row = $cadenceResults->fetch_assoc()) {
							$cnt++;
							echo '<option value="'.$row['PI_id'].'">'.$row['PI_id'].'</option>';
				 			
                
            }
           
					}
					?>
				</select>
				
			
			</div>
		</form>
	</section>
  <br>
	<div id = "artTable">Art Table<br></div>

  <script type="text/javascript">
         $(document).ready( function () {
         $('#chartContainer').DataTable();
             } );
    </script>
	
	
	
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
<script>

function getArtGraph(str){
	






var dataPointsJS = primArray[str];
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "light1", // "light1", "light2", "dark1", "dark2"
	title:{
		text: "Simple Column Chart with Index Labels"
	},
	data: [{
		type: "column", //change type to bar, line, area, pie, etc
		//indexLabel: "{y}", //Shows y value on all Data Points
		indexLabelFontColor: "#5A5757",
		indexLabelPlacement: "outside",   
		dataPoints: dataPointsJS
	}]
});

    function parseDataPoints () {
				 dataPointsJS;    
     };

   	
   	 parseDataPoints();
     chart.options.data[0].dataPoints = dataPointsJS;
     chart.render();

}
</script>
</html> 