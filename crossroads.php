<?php
	
	include "config.php";
	
	$date = date('Y-m-d');
	$request_done = false;
	$url = 'http://crossroads.domainactive.com/api/v2/prepare-bulk-data?key=fe3db63a-ec0f-44a1-99c7-6d13005ec79a&date='.$date.'&extra-fields=tg5,tg10,lander_keyword&format=json';
	$c = json_decode(file_get_contents($url), true);
	print_r($c);
	$cntr = 0;
	while(!$request_done) {
		sleep(5);
		$cntr++;
		if ($cntr > 5) $request_done = true;
		$url = 'http://crossroads.domainactive.com/api/v2/get-request-state?key=fe3db63a-ec0f-44a1-99c7-6d13005ec79a&request-id='.$c['request_id'];
		$data = json_decode(file_get_contents($url), true);
		if ($data['status'] == 'SUCCESS') {
			$request_done = true;
			$request_data = json_decode(file_get_contents($data['file_url']), true);
			print_r($request_data);
			$data = array();
			foreach ($request_data as $key => $value) {
				$data[$value['tg5']][$value['tg10']] = array(
				'campaign_name' => $value['campaign__name'],
				'campaign_type' => $value['campaign__type'],
				'campaign_id' => $value['campaign_id'],
				'category' => $value['lander_keyword'],
				'publisher_revenue_amount' => @$data[$value['tg5']][$value['tg10']]['publisher_revenue_amount'] + $value['publisher_revenue_amount'],
				'revenue_clicks' => @$data[$value['tg5']][$value['tg10']]['revenue_clicks'] + $value['revenue_clicks'],
				'date' => date("Y-m-d H:i:s"));
			
			}
			foreach($data as $tg5 => $adset_data) {
				foreach($adset_data as $tg10 => $value) {
					$result = $mysqli->query("SELECT id FROM crossroads WHERE `date` >= CURRENT_DATE() AND `date` < CURRENT_DATE() + INTERVAL 1 DAY AND `tg10` = '".$tg10."'");
					if (mysqli_num_rows($result)) {
							$id_array = $result->fetch_array(MYSQLI_ASSOC);
							$mysqli->query("UPDATE `crossroads` SET 
							`campaign_name`='".$value['campaign_name']."',
							`campaign_type`='".$value['campaign_type']."',
							`campaign_id`='".intval($value['campaign_id'])."',
							`tg5`='".$tg5."',
							`tg10`='".$tg10."',
							`category`='".$value['category']."',
							`publisher_revenue_amount`='".$value['publisher_revenue_amount']."',
							`revenue_clicks`='".$value['revenue_clicks']."',
							`date`='".date("Y-m-d H:i:s")."' WHERE `id`='".$id_array['id']."'");
						}
						else $mysqli->query("INSERT INTO `crossroads`(`campaign_name`, `campaign_type`, `campaign_id`, `category`, `tg5`, `tg10`, `publisher_revenue_amount`, `revenue_clicks`, `date`) VALUES ('".$value['campaign_name']."','".$value['campaign_type']."','".$value['campaign_id']."','".$value['category']."','".$tg5."','".$tg10."','".$value['publisher_revenue_amount']."','".$value['revenue_clicks']."','".date("Y-m-d H:i:s")."')");
						print_r($mysqli->error);
				}
			}
		}
	}
	
	
	/*
				foreach ($request_data as $key => $value) {
				$result = $mysqli->query("SELECT id FROM crossroads WHERE `date` >= CURRENT_DATE() AND `date` < CURRENT_DATE() + INTERVAL 1 DAY AND `tg10` = '".$value['tg10']."'");
				if (mysqli_num_rows($result)) {
					$id_array = $result->fetch_array(MYSQLI_ASSOC);
					$mysqli->query("UPDATE `crossroads` SET 
					`campaign_name`='".$value['campaign__name']."',
					`campaign_type`='".$value['campaign__type']."',
					`campaign_id`='".intval($value['campaign_id'])."',
					`tg5`='".$value['tg5']."',
					`tg10`='".$value['tg10']."',
					`category`='".$value['lander_keyword']."',
					`publisher_revenue_amount`='".$value['publisher_revenue_amount']."',
					`revenue_clicks`='".$value['revenue_clicks']."',
					`date`='".date("Y-m-d H:i:s")."' WHERE `id`='".$id_array['id']."'");
				}
				else $mysqli->query("INSERT INTO `crossroads`(`campaign_name`, `campaign_type`, `campaign_id`, `category`, `tg5`, `tg10`, `publisher_revenue_amount`, `revenue_clicks`, `date`) VALUES ('".$value['campaign__name']."','".$value['campaign__type']."','".$value['campaign_id']."','".$value['category']."','".$value['tg5']."','".$value['tg10']."','".$value['publisher_revenue_amount']."','".$value['revenue_clicks']."','".date("Y-m-d H:i:s")."')");
				print_r($mysqli->error);
			}
	*/
?>

