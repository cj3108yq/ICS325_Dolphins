<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
  </head>
<body >

<?php
    $parentName = trim($_GET['parentName']);
    $incrementId = trim($_GET['incrementId']);
    $baseUrl = trim($_GET['baseUrl']);

    $con = mysqli_connect('localhost','root','','ics325safedb');
        if (!$con) {
        die('Could not connect: ' . mysqli_error($con));    
        }
	////Query for team_names
	$nameQuery = "SELECT team_name FROM trains_and_teams WHERE parent_name = $parent_name ";
	$GLOBALS['teams']= mysqli_query($db, $nameQuery);
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
		
	
	
	</section>
	
</div>



</body>
</html>