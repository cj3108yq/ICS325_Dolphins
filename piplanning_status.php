<?php
  $nav_selected = "PIPLANNING";
  $left_buttons = "YES";
  $left_selected = "Status";
  include("./nav.php");
  global $db;
  date_default_timezone_set('America/Chicago');
?>

<?php
	// Sanitize POST data
	$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	
	
	
	
	// Get input values from user

	
		
	$art_selected = trim($_POST['artSelect']);
	$incrementId = trim($_POST['increment_id']);
	$teams = trim(strtoupper($_POST['teams']));
	
	// Get individual teams names
	$teamNames = explode(",", $teams);
	$length    = count($teamNames);
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
	
		// Query for parent_names
		$query = "SELECT DISTINCT parent_name FROM trains_and_teams WHERE type ='at'";
		$GLOBALS['teamsTable'] = mysqli_query($db, $query);
		////Query for team_names
		$nameQuery = "SELECT team_name FROM trains_and_teams WHERE parent_name = '$art_selected'";
		$GLOBALS['teams']= mysqli_query($db, $nameQuery);
		 
		//Query for BASE_URL database preference
		$urlQuery = "SELECT value FROM preferences WHERE name = 'BASE_URL' ";
		$GLOBALS['urlQuery']= mysqli_query($db, $urlQuery);
		$urlRow = $urlQuery->fetch_assoc();
		$baseUrl = $urlRow['value'];
		?>


<body>

<div class="container">
	<section>
		<h1>Dolphins Program Increment (PI) Summary Table</h1>
		<br>
		<form action="" method="post">
			<div class="grid-container">
				
				<input type="text" name="base_url" id="base_url" hidden value="<?php echo (empty($baseUrl)) ? 'https://metro' : $baseUrl; ?>">

				<label for="iteration_id">Program Increment ID</label>
				<select name="increment_id" id="increment_id" onchange = 'form.submit();'>
					<?php echo "<option value='". ((empty($incrementId)) ? $todayCadence : $incrementId)."'>".((empty($incrementId)) ? $todayCadence : $incrementId)."</option>";?>
					<?php	if ($cadenceResults->num_rows > 0){
						while ($row = $cadenceResults->fetch_assoc()) {
   							echo '<option value="'.$row['PI_id'].'">'.$row['PI_id'].'</option>';
						}
					}
					?>
				</select>

				
				<label for="art">Agile Release Train (ART):</label>
					
				<select name ='artSelect' id ='artSelect' onchange='cookieChange();form.submit();'>
				<?php
				  if ($teamsTable->num_rows > 0) {
					  // output data of each row
					  while($row = $teamsTable->fetch_assoc()) { 
						  if(isset($_COOKIE[$cookie_name])) {
						  if ($_COOKIE[$cookie_name] == $row['parent_name']){//Compares cookie value to DB value
								  echo "<option value=" . $row['parent_name'] . " selected>" . $row['parent_name'] . "</option>"; //Puts cookie val as selected
							  }else{
								echo "<option value=" . $row['parent_name'] . ">" . $row['parent_name'] . "</option>";
							  }
						  } else{
							 echo "<option value=" . $row['parent_name'] . ">" . $row['parent_name'] . "</option>"; 
						  }
					  }
				  }
				echo "</select>";
					?>
					
				<label for="team_name">Names of the Teams</label>
				<input type="text" name="teams" id="teams" readonly 
					value="<?php if ($teams->num_rows>0){
							while($row = $teams->fetch_assoc()){
							echo $row['team_name'].",";
								}
							};?>
							"
				
				
				<div></div>
				
				<div>
				
				</div>
			</div>
		</form>
	</section>
	
	<section>
	
	<?php
	
	
	
		
		
	////
		// Table generated using JS or PHP - table auto loads JS - JS set to default
		
		
		if($_SERVER['REQUEST_METHOD'] == 'POST'): // Form submitted using Generate PHP btn
	?>
		
		<table id="php-table">
			<tr>
				<th>No</th>
				<th>Team Name</th>
			<?php for($i = 1; $i <= 6; $i++): // Increment id ?>
				<th><?php echo $incrementId . '-' . $i; ?></th>
			<?php endfor; ?>
				<th><?php echo $incrementId . '-IP'; ?></th>
			</tr>
			
		<?php for($j = 0; $j < $length; $j++): //Loop through teamNames ?>
			<tr>
				<td><?php echo $j + 1 ?></td>
				<td><?php echo $teamNames[$j] ?></td>
			<?php for($i = 1; $i <= 6; $i++): ?>
			
			<?php $a = $baseUrl . '?id=' . $incrementId . '-' . $i . '_' . $teamNames[$j]; // define default link ?>
			
				<td><a href="<?php echo $a ?>" title="<?php echo $a ?>" target="_blank"><?php echo $incrementId . '-' . $i; ?></a></td>
			<?php endfor; ?>
			
			<?php $a = $baseUrl . '?id=' . $incrementId . '-IP' . '_' . $teamNames[$j]; // define IP link ?>
			
				<td><a href="<?php echo $a ?>" title="<?php echo $a; ?>" target="_blank"><?php echo $incrementId . '-IP'; ?></a></td>
			</tr>
				
		<?php endfor; ?>
				
		</table>
		
		<?php endif; ?>
	
	</section>
	
</div>


<script>
function cookieChange() {
	<?php
		//When option is selected, alters cookie to reflect. 
		$cookie_name = "DEFAULT_ART";
		$cookie_value = $_POST['artSelect'];
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
	?>
}
</script>
</body>
</html>

<?php include("./footer.php"); ?>