<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "SUMMARY";


  include("./nav.php");
  global $db;
  
  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "CALCULATE";
  

	//////Set cookie
	$cookie_name = "DEFAULT_ART";
	$cookie_value = $_POST['artSelect'];
	setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); //expires in one day
	/////
	
	/////DB V
	DEFINE('DATABASE_HOST', 'localhost');
	DEFINE('DATABASE_DATABASE', 'ics325safedb');
	DEFINE('DATABASE_USER', 'root');
	DEFINE('DATABASE_PASSWORD', '');
	// connects to database
	$db = new mysqli(DATABASE_HOST, DATABASE_USER, '', DATABASE_DATABASE);
	$db->set_charset("utf8");

	//runs db connection test
	function run_sql($sql_script)
	{
		global $db;
		// check connection
		if ($db->connect_error)
		{
			trigger_error(print_r(debug_backtrace()).'.Database connection failed: '  . $db->connect_error, E_USER_ERROR);
		}
		else
		{
			$result = $db->query($sql_script);
			if($result === false)
			{
				trigger_error('Stack Trace: '.print_r(debug_backtrace()).'Invalid SQL: ' . $sql_script . '; Error: ' . $db->error, E_USER_ERROR);
			}
			else if(strpos($sql_script, "INSERT")!== false)
			{
				return $db->insert_id;
			}
			else
			{
				return $result;
			}
		}
	}

	$currentCadenceQuery ="SELECT * FROM `cadence` where end_date>CURRENT_DATE() order by start_date limit 1";
	$GLOBALS['currentCadenceResults'] = mysqli_query($db, $currentCadenceQuery);
	$todayCadence = $currentCadenceResults->fetch_assoc();
	$todayCadence = $todayCadence['PI_id'];
  
	//Query cadence table for PI_id drop down
	$cadenceQuery = "SELECT Distinct PI_id FROM cadence";
	$GLOBALS['cadenceResults'] = mysqli_query($db, $cadenceQuery);
	
	
		
	
		// Query for team_ids
		$query = "SELECT team_id FROM trains_and_teams WHERE type ='art'";
		$GLOBALS['teamsTable'] = mysqli_query($db, $query);
		
	
	 
		$urlQuery = "SELECT value FROM preferences WHERE name = 'BASE_URL' ";
		$GLOBALS['urlQuery']= mysqli_query($db, $urlQuery);

		$urlRow = $urlQuery->fetch_assoc();
		$baseUrl = $urlRow['value'];
		?>
<script>
function getAtTable(parentName, incrementid, baseUrl) {
        if ($parentName == "") {
            document.getElementById("teamTable").innerHTML = "";
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
                    document.getElementById("teamTable").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET","getTeamTable.php?parentName="+parentName+"&incrementId="+incrementId"&baseUrl="baseUrl, true);
            xmlhttp.send();
        }
    }
</script>
<body onload="getTeamTable('<?php echo $parentName .', '.$incrementId.', '.$baseUrl?>')">

<div class="container">
	<section>
		<h1>Dolphins Capacity team status</h1>
		<br>
		<form action="" method="post">
			<div class="grid-container">
		</br>	

		<label for="iteration_id">Program Increment ID</label>
			<select name="increment_id" id="increment_id" onchange="getTeamTable(this.value,)" >
					<?php echo "<option value='". ((empty($incrementId)) ? $todayCadence : $incrementId)."'>".((empty($incrementId)) ? $todayCadence : $incrementId)."</option>";?>
					<?php	if ($cadenceResults->num_rows > 0){
						while ($row = $cadenceResults->fetch_assoc()) {
                 echo '<option value="'.$row['PI_id'].'">'.$row['PI_id'].'</option>';
						}
					}
					?>
					</select>
	</br>
				
		<label for="art">Agile Release Train (ART):</label>
					
			<select name ='artSelect' id ='artSelect' onchange="cookieChange()" onchange="getTeamTable(this.value)">
				<?php  if ($teamsTable->num_rows > 0) {
					  // output data of each row
					  while($row = $teamsTable->fetch_assoc()) { 
						  if(isset($_COOKIE[$cookie_name])) {
						  if ($_COOKIE[$cookie_name] == $row['team_id']){//Compares cookie value to DB value
								  echo "<option value=" . $row['team_id'] . " selected>" . $row['team_id'] . "</option>"; //Puts cookie val as selected
							  }else{
								echo "<option value=" . $row['team_id'] . ">" . $row['team_id'] . "</option>";
							  }
						      } else{
							    echo "<option value=" . $row['team_id'] . ">" . $row['team_id'] . "</option>"; 
						  }
					  }
				  } 
				?>
			</select>
		</br>
					
					
				<label for="team_name">Names of the Teams</label>
				<input type="text" name="teams" id="teams" readonly 
					value="<?php if ($teams->num_rows>0){
							while($row = $teams->fetch_assoc()){
							echo $row['team_name'].",";
								}
							};?>"
							
			</div>
			<div id="teamTable"></div>
		</form>
	</section>
	
	<section>
	
	
	
	
	

		

		
	<script>
function cookieChange() {

	<?php
		//When option is selected, alters cookie to reflect. 
		$cookie_name = "DEFAULT_ART";
		$cookie_value = $_POST['artSelect'];
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

	?>
	}
function updateTeamValue(team_name){
	 
	
}
</script>
</body>
</html>