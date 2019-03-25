<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "SUMMARY";


  include("./nav.php");
  global $db;

 

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



  ?>
<body>

<div class="container">
	<section>
		<h1>Dolphins Capacity Summary Table</h1>
		<br>
		<form action="" method="post">
			<div class="grid-container">
				
				<input type="text" name="base_url" id="base_url" hidden value="<?php echo (empty($baseUrl)) ? 'https://metro' : $baseUrl; ?>">

				<label for="iteration_id">Program Increment ID</label>
				<select name="increment_id" id="increment_id">
					<?php echo "<option value='". ((empty($incrementId)) ? $todayCadence : $incrementId)."'>".((empty($incrementId)) ? $todayCadence : $incrementId)."</option>";?>
					<?php	if ($cadenceResults->num_rows > 0){
						while ($row = $cadenceResults->fetch_assoc()) {
   							echo '<option value="'.$row['PI_id'].'">'.$row['PI_id'].'</option>';
						}
					}
					?>
				</select>
				
				<div>
				
					<button>Generate</button>
				</div>
			</div>
		</form>
	</section>
	
	<section>
	
  
  <!-- <img src="images/work_in_progress.jpg" height = "100" width = "100"/>
  <h3> Capacity Summary </h3>
  <br> * What is the capacity of each ART in the current PI (PI?)
  <br> * What is the cpacity of each TEAM in the current PI (PI)?
  <br> * What is capacity in each Iteration (I)?
  <br> * What is the capacity of the entire org (all ARTS) in the current PI and each of 6 Is?
  <br>
  <br> A datatable showing these numbers will be presented here. -->
  

<?php include("./footer.php"); ?>
