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
  <script>

    function getArtTable(str) {
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
    }

    function getAtTable(str,cadence) {
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
            xmlhttp.open("GET","getAt_table.php?q="+str,true);
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

  </body>

<!-- <?php include("./footer.php"); ?> -->
