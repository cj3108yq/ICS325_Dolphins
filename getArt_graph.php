<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>-->
  </head>

<?php
    //$q = trim($_GET['q']);
	$q = "PI-1905"; //For testing purposes.

    $con = mysqli_connect('localhost','root','','ics325safedb');
        if (!$con) {
        die('Could not connect: ' . mysqli_error($con));    
        }
?>
<?php
	$sql = "SELECT t.team_id, c.total
	FROM capacity c RIGHT OUTER JOIN trains_and_teams t ON (t.team_id = c.team_id)
	WHERE ((c.program_increment = '$q' OR c.total IS null) OR (c.program_increment is null))

	AND t.team_id LIKE 'ART-%%%'
	ORDER BY t.team_id";

	$result = $con->query($sql);
	$dataPoints = array();

	if($result -> num_rows > 0){
		while($row = $result -> fetch_assoc()){

			array_push($dataPoints, array("y" => $row["total"] ,"label" => $row["team_id"] ));
		}
	} 
?>
<script>
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

<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<body>
<div id="chartContainer" class="chartContainer" style="height: 370px; width: 100%;"></div>
<p>d</p>

    <script type="text/javascript">
         $(document).ready( function () {
         $('#chartContainer').DataTable();
             } );
    </script>
</body>
<footer >
</footer>
</html>