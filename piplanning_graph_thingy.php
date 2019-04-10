<?php

// if( !function_exists('apache_request_headers') ) {

//   function apache_request_headers() {
//       $arh = array();
//       $rx_http = '/AHTTP_/';
//       foreach($_SERVER as $key => $val) {
//           if( preg_match($rx_http, $key) ) {
//               $arh_key = preg_replace($rx_http, '', $key);
//               $rx_matches = array();
//
//               $rx_matches = explode('_', $arh_key);
//               if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
//                   foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
//                   $arh_key = implode('-', $rx_matches);
//               }
//               $arh[$arh_key] = $val;
//           }
//       }
//       return( $arh );
//   }
// }
// $headers = apache_request_headers();
//
// if (!isset($headers['AUTHORIZATION']) || substr($headers['AUTHORIZATION'],0,4) !== 'NTLM'){
//       header('HTTP/1.1 401 Unauthorized');
//       header('WWW-Authenticate: NTLM');
//       exit;
// }
//
// $auth = $headers['AUTHORIZATION'];
//
// if (substr($auth,0,5) == 'NTLM ') {
//         $msg = base64_decode(substr($auth, 5));
//         if (substr($msg, 0, 8) != "NTLMSSPx00")
//                 die('error header not recognised');
//
//         if ($msg[8] == "x01") {
//                 $msg2 = "NTLMSSPx00x02"."x00x00x00x00".
//                         "x00x00x00x00".
//                         "x01x02x81x01".
//                         "x00x00x00x00x00x00x00x00".
//                         "x00x00x00x00x00x00x00x00".
//                         "x00x00x00x00x30x00x00x00";
//
//                 header('HTTP/1.1 401 Unauthorized');
//                 header('WWW-Authenticate: NTLM '.trim(base64_encode($msg2)));
//                 exit;
//         }
//         else if ($msg[8] == "x03") {
//                 function get_msg_str($msg, $start, $unicode = true) {
//                         $len = (ord($msg[$start+1]) * 256) + ord($msg[$start]);
//                         $off = (ord($msg[$start+5]) * 256) + ord($msg[$start+4]);
//                         if ($unicode)
//                                 return str_replace("\0", '', substr($msg, $off, $len));
//                         else
//                                 return substr($msg, $off, $len);
//                 }
//                 $user = get_msg_str($msg, 36);
//                 $domain = get_msg_str($msg, 28);
//                 $workstation = get_msg_str($msg, 44);
//                 print "You are $user from $workstation.$domain";
//         }
// }


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

		$sql = "SELECT * FROM `capacity`;";
		$sql = 'SELECT t.team_id, c.total 
FROM capacity c RIGHT OUTER JOIN trains_and_teams t ON (t.team_id = c.team_id)
WHERE ((c.program_increment = "$q" OR c.total IS null) OR (c.program_increment is null))
AND t.team_id LIKE "ART-%%%"
ORDER BY t.team_id';

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
<body onload = "getArtTable('<?php echo $todayCadence ?>')">
<div class="container">
	<section>
		<h1>Dolphins Capacity Trends</h1>
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


  <script type="text/javascript">
         $(document).ready( function () {
         $('#table_id').DataTable();
             } );
    </script>


<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html> 