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
	$q = "PI-1905";
	$sql2 = "SELECT t.team_id, c.total
	FROM capacity c RIGHT OUTER JOIN trains_and_teams t ON (t.team_id = c.team_id)
	WHERE ((c.program_increment = '$q' OR c.total IS null) OR (c.program_increment is null))

	AND t.team_id LIKE 'ART-%%%'
	ORDER BY t.team_id";


	$sql= "SELECT * FROM `capacity`;";
	$currentCadenceQuery ="SELECT * FROM `cadence` where end_date>CURRENT_DATE() order by start_date limit 1";
	$GLOBALS['currentCadenceResults'] = mysqli_query($db, $currentCadenceQuery);
	$todayCadence = $currentCadenceResults->fetch_assoc();
	$todayCadence = $todayCadence['PI_id'];
	//Query cadence table for PI_id drop down
	$cadenceQuery = "SELECT Distinct PI_id FROM cadence";
	$GLOBALS['cadenceResults'] = mysqli_query($db, $cadenceQuery);
	$result = $db->query($sql);
	$dataPoints = array();

	
if($result -> num_rows > 0){
	while($row = $result -> fetch_assoc()){
	
	array_push($dataPoints, array("y" => $row["total"] ,"label" => $row["team_id"] ));
	}
} 

?>
<!DOCTYPE HTML>
<html>
<head>
<script>

    function getArtGraph(str) {
        if (str == "") {
            document.getElementById("artTable").innerHTML = "";
            return;
        } else { 
		}
    }



	window.onload = function() {

	var chart = new CanvasJS.Chart("chartContainer", {
		animationEnabled: true,
		theme: "light2",
		title:{
			text: "Total"
		},
		axisY: {
			title: "Team Names"
		},
		data: [{
			type: "column",
			yValueFormatString: "#,##0.## tonnes",
			dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
		}]
		});
		chart.render();

	}
</script>
</head>
<body>
<div class="container">
	<section>
		<h1>Dolphins Capacity Summary Table</h1>
		<br>
		<form action="" method="post">
			<div class="grid-container">
			

				<label for="iteration_id">Program Increment ID</label>
				<select name="increment_id" id="increment_id" onchange="getArtGraph(this.value)" >
					<?php echo "<option value='". ((empty($incrementId)) ? $todayCadence : $incrementId)."'>".((empty($incrementId)) ? $todayCadence : $incrementId)."</option>";?>
					<?php	if ($cadenceResults->num_rows > 0){
						while ($row = $cadenceResults->fetch_assoc()) {
                 echo '<option value="'.$row['PI_id'].'">'.$row['PI_id'].'</option>';
                
            }
           
					}
					?>
				</select>
				
			
			</div>
		</form>
	</section>


<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html> 