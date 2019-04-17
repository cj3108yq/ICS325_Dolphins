<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
  </head>
<body>

<?php
    $q = trim($_GET['q']);
    $pi = trim($_GET['p']);
    $atSum = 0;

    $con = mysqli_connect('localhost','root','','ics325safedb');
        if (!$con) {
        die('Could not connect: ' . mysqli_error($con));    
        }
    echo ' Train '.$q.' Agile Teams';
   
?>


	 <table id="table_ids" class="table_id" width="100%" style="width: 70%;">
        <thead>
            <tr id="table-first-row">
                  <th>AT</th>
                  <th>Total</th>
            </tr>
        </thead>
        <tbody>
        
             <?php
                $sql = "SELECT t.team_id, c.total
                FROM capacity c RIGHT OUTER JOIN trains_and_teams t ON (t.team_id = c.team_id)
                 WHERE t.parent_name = '$q' 
                 AND (c.program_increment = '$pi' OR c.program_increment IS null)
                 
                
                  ORDER BY t.team_id";
        
                 $result = $con->query($sql);
                    if($result ->num_rows > 0){
                        while($row = $result -> fetch_assoc()){
                         echo
                        "<tr>
                             <td>" .$row["team_id"] . "</td>
                             <td>" .((empty($row["total"])) ? 0 :$row["total"]) ."</td>";
                                     $atSum += $row["total"];
                         echo "</tr>";
                        }
                    }
                    else {
                         echo "0 results";
                    }
                 $result->close();
            ?>
        </tbody>
        <tfoot>
            <tr>
                    <td> Total of Train</td>
              
                    <td><?php echo $atSum ?></td> 
            </tr>
        </tfoot>
     </table>
    <script type="text/javascript">
         $(document).ready( function () {
         $('#table_ids').DataTable();
             } );
    </script>
</body>
</html>