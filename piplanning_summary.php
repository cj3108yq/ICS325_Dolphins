<?php

  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "SUMMARY";


  include("./nav.php");
  global $db;
  
  $nav_selected = "PIPLANNING";
  $left_buttons = "YES"; 
  $left_selected = "CALCULATE";
  /////////////////////////////////////Jens stuff
  
  //Database results
  date_default_timezone_set('America/Chicago');

  $sql = "SELECT * FROM `trains_and_teams;`";
  $result = $db->query($sql);



	/////////////////////////////////////Jens stuff

  ?>

	 <table id="info" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered datatable-style"
              width="100%" style="width: 100px;">
              <thead>
                <tr id="table-first-row">

                  <th>ART</th>
                  <th>Total</th>
                </tr>
              </thead>

              <tbody>

                <?php

                 $sql2 = "SELECT *
                         FROM trains_and_teams 
						 LEFT JOIN capacity ON 
						 trains_and_teams.team_id = capacity.team_id
						 WHERE total IS NOT NULL;";
				$sql = "SELECT * FROM `capacity`;";
                 $result = $db->query($sql);

                 if($result -> num_rows > 0){
                   while($row = $result -> fetch_assoc()){




                     echo
                     "<tr>

                         <td>" .$row["team_id"] . "</td>
                         <td>" .$row["total"] ."</td>

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

         $(document).ready(function () {

             $('#info').DataTable({

             });

         });

     </script>
  <!--<img src="images/work_in_progress.jpg" height = "100" width = "100"/>
  <h3> Capacity Summary </h3>
  <br> * What is the capacity of each ART in the current PI (PI?)
  <br> * What is the cpacity of each TEAM in the current PI (PI)?
  <br> * What is capacity in each Iteration (I)?
  <br> * What is the capacity of the entire org (all ARTS) in the current PI and each of 6 Is?
  <br>
  <br> A datatable showing these numbers will be presented here.-->
  

<?php include("./footer.php"); ?>
