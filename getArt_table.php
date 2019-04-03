<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
  </head>
<body >

<?php
    $q = trim($_GET['q']);

    $con = mysqli_connect('localhost','root','','ics325safedb');
        if (!$con) {
        die('Could not connect: ' . mysqli_error($con));    
        }
?>


	 <table id="table_id" class="table_id" width="100%" style="width: 70%;" >
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
                   WHERE ((c.program_increment = '$q' OR c.total IS null) OR (c.program_increment is null))
                
                   AND t.team_id LIKE 'ART-%%%'
                    ORDER BY t.team_id";

                 $result = $con->query($sql);
                    if($result ->num_rows > 0){
                        while($row = $result -> fetch_assoc()){
                         echo
                        "<tr>
                             <td onclick = getAtTable('".$row["team_id"]."','".$q."') onload = getAtTable('".$row["team_id"]."','".$q."')>".$row["team_id"]. "</td>
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
        <tfoot>
            <tr>
                    <td> Total of Cadence</td>
                <!--     <?php $sum = "SELECT SUM(c.total)
                  FROM capacity c RIGHT OUTER JOIN trains_and_teams t ON (t.team_id = c.team_id)
                   WHERE ((c.program_increment = '$q' OR c.total IS null) OR (c.program_increment is null))
                   AND t.team_id LIKE 'ART-%%%' ORDER BY t.team_id";
                    $result = mysqli_query($connection_mysql,$sql);
                    $row = mysqli_fetch_array($result,MYSQLI_NUM);
                        while($row=mysqli_fetch_array($sum))
                            {
                                $artSum = $row['sum(c.total)'];}
                    ?>
                    <td><?php echo $artSum ?></td> -->
            </tr>
        </tfoot>
     </table >
    <script type="text/javascript">
         $(document).ready( function () {
         $('#table_id').DataTable();
             } );
    </script>
</body>
<footer >
</footer>
</html>