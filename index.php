<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>P2W</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.11.1/css/jquery.dataTables.min.css">
<script src="//cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js"></script>

  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
	$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	$('#datepicker').datepicker('setDate', new Date());
  } );
  
  </script>
</head>
<body>
<?php

	include "config.php";
	
	function showSortButtons($param, $tg, $search_date, $order = null, $column = null) {
		$data = '<div class="row">
						<div class="col-7">
							'.$param.'
						</div>
						<div class="col-5 btn-group btn-group-sm btn-group-vertical" role="group">
						  <input type="radio" class="btn-check" name="btnradio" id="btnradio1 '.$param.'" autocomplete="off" onclick="document.location.href = \'index.php?tg5='.$tg.'&datepicker='.$search_date.'&sort_order=SORT_DESC&sort_column='.$param.'\';" ';
		if($order == 'SORT_DESC' &&  $column == $param) $data .= "checked"; 
		$data .= '>
						  <label class="btn btn-outline-primary" for="btnradio1 '.$param.'">^</label>
						  <input type="radio" class="btn-check" name="btnradio" id="btnradio2 '.$param.'" autocomplete="off" onclick="document.location.href = \'index.php?tg5='.$tg.'&datepicker='.$search_date.'&sort_order=SORT_ASC&sort_column='.$param.'\';" ';
		if($order == 'SORT_ASC' &&  $column == $param) $data .= "checked";
		$data .= '>
						  <label class="btn btn-outline-primary" for="btnradio2 '.$param.'">v</label>
						</div>
					</div>';
		return $data;
	}
	
	
	
	$users = array();
	$result = $mysqli->query("SELECT DISTINCT tg5 FROM crossroads");
	while($row = mysqli_fetch_assoc($result)) {
		if ($row['tg5']) $users[] = $row['tg5'];
	}
	
	$tg = '';
	$search_date = '';
	$order = null;
	$column = null;
	
	if (isset($_GET['tg5'])) $tg = $_GET['tg5'];
	if (isset($_GET['datepicker'])) {
		$search_date = $_GET['datepicker'];
		print '<script>let realDate = new Date("'.$search_date.'");  $( function() {$("#datepicker").datepicker("setDate", realDate);} );</script>';
	}
	$data = array();
	
	if ($tg && $search_date) {
		$crossroads = $mysqli->query("SELECT * FROM crossroads WHERE `date` >= '".$search_date."' AND `date` < '".$search_date."' + INTERVAL 1 DAY AND `tg5` = '".$tg."' AND `tg10` != ''");
		while($row = mysqli_fetch_assoc($crossroads)) {
			$apix = $mysqli->query("SELECT * FROM apix WHERE `date` >= '".$search_date."' AND `date` < '".$search_date."' + INTERVAL 1 DAY AND `name` = '".$row['tg10']."'");
			while($row2 = mysqli_fetch_assoc($apix)) {
				$data[] = array(
				'tg5' => $row['tg5'], 
				'tg10' => $row['tg10'],
				'campaign_name' => $row['campaign_name'],
				'Revenue Events' => $row['revenue_clicks'], 
				'Pub Revenue' => $row['publisher_revenue_amount'], 
				'FB Leads' => $row2['leads'], 
				'FB Spent' => $row2['spend'],
				'apix date' => $row2['date'],
				'cr date' => $row['date'],
				'Difference' => $row['publisher_revenue_amount'] - $row2['spend']
				);
			}
		}
		if (isset($_GET['sort_order']) && isset($_GET['sort_column'])) {
			$order = $_GET['sort_order'];
			$column = $_GET['sort_column'];
			$volume  = array_column($data, $column);
			if ($order == 'SORT_DESC') array_multisort($volume, SORT_DESC, $data); else array_multisort($volume, SORT_ASC, $data);
		}
		
	}
	
	$re_sum = 0;
	$pr_sum = 0;
	$fl_sum = 0;
	$fs_sum = 0;
	$dif_sum = 0;
	
	//$('.datepicker').datepicker('getDate');
	//php print showSortButtons('tg10', $tg, $search_date, $order, $column);
	
?>
<div class="container">
	<form action="" method="get">
		<div class="row mt-2">
			<div class="col-2 col-xs-4">
				<select class="form-select" aria-label="TG5" name="tg5">
				<?php foreach($users as $user) {
					print '<option value="'.$user.'"';
					if ($tg == $user) print 'selected';
					print '>'.$user.'</option>';}
					?>
				</select>
			</div>
			<div class="col-2 col-xs-4">
				<input type="text" id="datepicker" name="datepicker" value="<?php if ($search_date) print $search_date;?>">
			</div>
			<div class="col-8 col-xs-4">
				<input type="submit" value="Показать">
			</div>
		</div>
	</form>
	<div class="row">
		<div class="col">
			<table class="table table-striped" id="p2w">
			  <thead>
				<tr>
				  <th scope="col">tg5</th>
				  <th scope="col">tg10</th>
				  <th scope="col">Campaign Name</th>
				  <th scope="col">Revenue Events</th>
				  <th scope="col">Pub Revenue</th>
				  <th scope="col">FB Leads</th>
				  <th scope="col">FB Spent</th>
				  <th scope="col">Apix Date</th>
				  <th scope="col">CR Date</th>
				  <th scope="col">Difference</th>
				</tr>
			  </thead>
			  <tbody>
				
				  <?php foreach($data as $key => $value) {
					  print '<tr>
					  <td>'.$value['tg5'].'</td>
					  <td>'.$value['tg10'].'</td>
					  <td>'.$value['campaign_name'].'</td>
					  <td>'.$value['Revenue Events'].'</td>
					  <td>'.$value['Pub Revenue'].'</td>
					  <td>'.$value['FB Leads'].'</td>
					  <td>'.$value['FB Spent'].'</td>
					  <td>'.$value['apix date'].'</td>
					  <td>'.$value['cr date'].'</td>
					  <td ';
					  if ($value['Difference'] > 0) print 'class="bg-success"';
					  if ($value['Difference'] < 0) print 'class="bg-danger"';
					  print '>'.$value['Difference'].'</td>
					  </tr>';
					  $re_sum += $value['Revenue Events'];
					  $pr_sum += $value['Pub Revenue'];
					  $fl_sum += $value['FB Leads'];
					  $fs_sum += $value['FB Spent'];
					  $dif_sum += $value['Difference'];
				  }
				  
				  print '<tr>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td class="fw-bold">'.$re_sum.'</td>
					  <td class="fw-bold">'.$pr_sum.'</td>
					  <td class="fw-bold">'.$fl_sum.'</td>
					  <td class="fw-bold">'.$fs_sum.'</td>
					  <td></td>
					  <td></td>
					  <td class="fw-bold ';
					  if ($dif_sum > 0) print 'bg-success"';
					  if ($dif_sum < 0) print 'bg-danger"';
					  if ($dif_sum == 0) print '"';
					  print '>'.$dif_sum.'</td>
					  </tr>';
				  ?>
				
			  </tbody>
			</table>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
  $("#p2w").DataTable({"pageLength": 25});
});
</script>


</body>
</html>
