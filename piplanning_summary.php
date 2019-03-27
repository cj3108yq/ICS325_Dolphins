<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "SUMMARY";


  include("./nav.php");
  global $db;
  
  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "CALCULATE";
  
  

 

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
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
 
   
<body>


	
 
<div class="container">
	<section>
		<h1>Dolphins Capacity Summary Table</h1>
		<br>
		<form action="" method="post">
			<div class="grid-container">
			

				<label for="iteration_id">Program Increment ID</label>
				<select name="increment_id" id="increment_id"  >
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
	
	<section>
	 <table id="table_id" class="display" 
              width="100%" style="width: 100px;">
              <thead>
                <tr id="table-first-row">
                  <th>ART</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php
                   $sql = "SELECT t.team_id, c.total 
                  FROM capacity c RIGHT OUTER JOIN trains_and_teams t ON (t.team_id = c.team_id)
                   WHERE c.program_increment = 'PI-1905' OR c.total IS null 
                   AND t.team_id LIKE 'ART%'
                    ORDER BY t.team_id";
        
                 $result = $db->query($sql);
                 if($result ->num_rows > 0){
                   while($row = $result -> fetch_assoc()){
                     echo
                     "<tr>
                         <td>" .$row["team_id"] . "</td>
                         <td>" .((empty($row["total"])) ? 0 :$row["total"]) ."</td>
                       </tr>";
                   }
                 }
                  else {
                   echo "0 results";
                 }
                 $result->close();
                ?>
              </tbody>
        </table>
        <script type="text/javascript">
         $(document).ready( function () {
    $('#table_id').DataTable();
} );

        

         
     </script>
  
  <!-- <img src="images/work_in_progress.jpg" height = "100" width = "100"/>
  <h3> Capacity Summary </h3>
  <br> * What is the capacity of each ART in the current PI (PI?)
  <br> * What is the cpacity of each TEAM in the current PI (PI)?
  <br> * What is capacity in each Iteration (I)?
  <br> * What is the capacity of the entire org (all ARTS) in the current PI and each of 6 Is?
  <br>
<< 
  <br> A datatable showing these numbers will be presented here.-->

  

<?php include("./footer.php"); ?>
