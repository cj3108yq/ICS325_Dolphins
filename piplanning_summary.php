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
	
	
		
	//primArray is actually a JS object that holds the array objArray, which is an array that holds objects.
	echo "<script type='text/javascript'>var primArray = {}; var primAT = {} </script>"; //Declare JS array.
	if ($cadenceResults2->num_rows > 0){

		while ($row2 = $cadenceResults2->fetch_assoc()) {
			$piID =$row2['PI_id'];
			//objArray is an array that holds objects that are then converted into graph bars
			echo "<script type='text/javascript'>var objArray = [];piID = String('$piID');</script>"; 
			$q = $row2['PI_id']; //For every PI_ID, a new query is created.			
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

					echo "<script type='text/javascript'>
					//Total & Team ID needs to be forcibly converted to INT & String before being converted into JS objects.
					var totalJS = parseInt('$total', 10);
					var teamIDJS =  String('$teamID');
					objArray.push({y: totalJS, label: teamIDJS});
					var objATArray = []; //objATArray is like objArray. 
					</script>";
					
					//Feeling cute. May reconsider naming conventions later.
					$sqlAT = "SELECT t.team_id, c.total
								FROM capacity c RIGHT OUTER JOIN trains_and_teams t ON (t.team_id = c.team_id)
								WHERE t.parent_name = '$teamID' 
								AND (c.program_increment = '$q' OR c.program_increment IS null)


								ORDER BY t.team_id";
					$resultAT = $db->query($sqlAT);
					if($resultAT -> num_rows > 0){
						while($rowAT = $resultAT -> fetch_assoc()){
							$totalAT = $rowAT["total"];
							$teamIDAT = $rowAT["team_id"];
							echo "<script type='text/javascript'>
							var totalJSAT = parseInt('$totalAT', 10);
							var teamIDJSAT =  String('$teamIDAT');
							//alert(totalJSAT);
							objATArray.push({y: totalJSAT, label: teamIDJSAT});
							</script>"; 
							}
					}//This is where the AT ends
					
					echo "<script type='text/javascript'>
					//teamID & PIID are concatenated to form a unique ID per ART.
					var piTeamID = piID + teamIDJS
					primAT[piTeamID] = objATArray;
					</script>";
				}
			}
			
			echo "<script type='text/javascript'>
			
			primArray[piID] = objArray; 
			</script>";
		


			}
	}
		

		

 
  ?>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
  <script>
	var pidNum; //Step 1: var for holding PIID is declared here
    function getArtTable(str) {
		pidNum = str; //Step 2: var snags PIID
		
        if (str == "") {
            document.getElementById("artTable").innerHTML = "";
            return;
        } else { 
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("artTable").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET","getArt_table.php?q="+str,true);
            xmlhttp.send();
        }
		
		//dataPointsJS is used to transfer one object from primArray based on what the user selects in the dropdown.
		var dataPointsJS = primArray[str];
		//chart is how the graph is generated. I am interacting with this as minimially as possible.
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
				click: onClick,
				indexLabelPlacement: "outside",   
				dataPoints: dataPointsJS
			}]
		});
		chart.render();
		
		///////////// Determines the total for the main graph and prints it out below.
		var primeSum = 0;
		for (var i = 0; i < primArray[str].length; i++){
			primeSum += primArray[str][i]['y'];
		}
		document.getElementById('chartTotal').innerHTML = "Total: " + primeSum;
		
		///////////// Creates secondard graph with the first AT as default. 
		var labelAT = pidNum+primArray[str][0]['label'];
		var dataPointsJSAT = primAT[labelAT];
		
		var chartAT = new CanvasJS.Chart("chartContainerAT", {
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
			dataPoints: dataPointsJSAT
			}]
		});
		chartAT.render();
		getAtTable(primArray[str][0]['label'], str);
		
		///////////// Determines the total for the secondard graph and prints it out below.
		var primeSumAT = 0;
		for (var i = 0; i < primAT[labelAT].length; i++){
			primeSumAT += primAT[labelAT][i]['y'];
		}
		document.getElementById('chartTotalAT').innerHTML = "Total: " + primeSumAT;

		
    }
	
	//Function for when a user clicks a graph bar. 
	function onClick(e) {
		//Step 3: pidNum is then concatenated here with teamID to form unique ID
		var label = pidNum+e.dataPoint.label;
		var dataPointsJSAT = primAT[label]; //Step 4: Unique ID is not used to call data.
		
		var chartAT = new CanvasJS.Chart("chartContainerAT", {
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
				dataPoints: dataPointsJSAT
			}]
		});
		//function parseDataPoints2 () {dataPointsJSAT;};
		//parseDataPoints2();
		//chartAT.options.data[0].dataPoints = dataPointsJSAT;
		chartAT.render();
		
		
	}
	
	
    function getAtTable(str, cadence) {
		
		
        if (str == "") {
            document.getElementById("atTable").innerHTML = "";
            return;
        } else { 
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("atTable").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET","getAt_table.php?q="+str+"&p="+cadence,true);
            xmlhttp.send();
        }
    }
    </script>
   
<body onload = "getArtTable('<?php echo $todayCadence ?>')">
 
<div class="container">
	<section>
		<h1>Dolphins Capacity Summary Table</h1>
		<br>
		<form action="" method="post">
			<div class="grid-container">
			

				<label for="iteration_id">Program Increment ID</label>
				<select name="increment_id" id="increment_id" onchange="getArtTable(this.value)" >
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
  <br>
	<div id = "artTable">Art Table<br></div>
  <br>
  <div id = "atTable">Team Table<br></div>

  <script type="text/javascript">
         $(document).ready( function () {
         $('#div_table').DataTable();
        
             } );
    </script>
        

<!--This is where the chart container appears.-->       
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<div id="chartTotal"></div>
<div id="chartContainerAT" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<div id="chartTotalAT"></div>
  </body>

<!-- <?php include("./footer.php"); ?> -->