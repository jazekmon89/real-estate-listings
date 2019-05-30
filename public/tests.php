<pre>
<?php
	set_time_limit(0);
	// just a flag for debugging purposes on the PDO sql errors/exceptions.
	// Set to true to see exception logs (preferably for development only)
	$is_development = true;
	//$host = "198.71.237.7";
	//$user = "c8_aaron";
	//$pass = "Password1!";
	$host = "localhost";
	$user = "root";
	$pass = "";
	$port = "3306";

	$dbhandle = mysql_connect($host, $user, $pass) or die("Unable to connect");
	$selected = mysql_select_db("c8_aaron",$dbhandle) or die("Unable to connect");

	$file = fopen('test.csv', 'r');
	//$file2 = fopen('newtest.csv','w');
	$counter = 1;
	

	function getNewData($data, $row_num, $isMain = false, $mnd_id = null){
		$temp_data = array();
		foreach(range(0, count($data)-1) as $i){
			if($i == 0 && $isMain)
				$temp_data[$i] = empty(trim($data[$i]))?'0000-00-00 00:00:00':date('Y-m-d H:i:s',strtotime($data[$i]));
			else
				$temp_data[$i] = $data[$i];
			$temp_data[$i] = "'".mysql_real_escape_string(trim(htmlspecialchars_decode($temp_data[$i])))."'";
		}
		if($mnd_id !== null)
			array_unshift($temp_data, "'".$mnd_id."'");
		array_unshift($temp_data, "'".$row_num."'");
		return $temp_data;
	}

	if($file !== FALSE){
		while(($data = fgetcsv($file)) !== FALSE){
			$main_indexes = 	[0,1,2,3,4,5,6,7,8,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,91,92,93,94,95,96,97,98,99];
			$main_arrangement = [3,0,2,30,4,5,6,7,8,32,33,20,21,22,23,24,25,26,27,28,29,38,36,37,39,9,10,11,12,13,14,15,16,17,18,19,45,46,47,48,31,34,35,40,41,42,43,44,1];
			$felony_indexes = range(9,16);//[9,10,11,12,13,14,15,16];
			$felony_arrangement = range(0,7);
			$credit_indexes = range(28,31);//[28,29,30,31];
			$credit_arrangement = range(0,3);
			$misdemeanor_indexes = range(17,24);//[17,18,19,20,21,22,23,24];
			$misdemeanor_arrangement = range(0,7);
			$price_indexes = range(63,81);//[59,60,61,62,63,64,65,66,67];
			$price_arrangement = range(0,18);
			$rental_indexes = range(25,27);//[25,26,27];
			$rental_arrangement = range(0,2);
			$sq_indexes = range(82,90);//[68,69,70,71,72,73,74,75,76];
			$sq_arrangement = range(0,8);
			$main_data = [];
			$felony_data = [];
			$credit_data = [];
			$misdemeanor_data = [];
			$price_data = [];
			$rental_data = [];
			$sq_data = [];
			foreach($main_indexes as $k=>$i){
				$main_data[$main_arrangement[$k]] = $data[$i];
			}
			$main_data = getNewData($main_data, $counter, true);
			
			$felony_data = array();
			foreach($felony_indexes as $k=>$i){
				$felony_data[$felony_arrangement[$k]] = $data[$i];
			}
			
			$credit_data = array();
			foreach($credit_indexes as $k=>$i){
				$credit_data[$credit_arrangement[$k]] = $data[$i];
			}
			
			$misdemeanor_data = array();
			foreach($misdemeanor_indexes as $k=>$i){
				$misdemeanor_data[$misdemeanor_arrangement[$k]] = $data[$i];
			}
			
			$price_data = array();
			foreach($price_indexes as $k=>$i){
				$price_data[$price_arrangement[$k]] = $data[$i];
			}
			
			$rental_data = array();
			foreach($rental_indexes as $k=>$i){
				$rental_data[$rental_arrangement[$k]] = $data[$i];
			}
			
			$sq_data = array();
			foreach($sq_indexes as $k=>$i){
				$sq_data[$sq_arrangement[$k]] = $data[$i];
			}
			
			mysql_query("Insert into MainData values(".implode(",",$main_data).");", $dbhandle);
			$mnd_id = mysql_insert_id();
			
			if($mnd_id !== NULL){
				$felony_data = getNewData($felony_data, $counter, false, $mnd_id);
				$credit_data = getNewData($credit_data, $counter, false, $mnd_id);
				$misdemeanor_data = getNewData($misdemeanor_data, $counter, false, $mnd_id);
				$price_data = getNewData($price_data, $counter, false, $mnd_id);
				$rental_data = getNewData($rental_data, $counter, false, $mnd_id);
				$sq_data = getNewData($sq_data, $counter, false, $mnd_id);
			}
			mysql_query("Insert into Felony values(".implode(",",$felony_data).");", $dbhandle);
			mysql_query("Insert into Credit values(".implode(",",$credit_data).");", $dbhandle);
			mysql_query("Insert into Misdemeanor values(".implode(",",$misdemeanor_data).");", $dbhandle);
			mysql_query("Insert into Price values(".implode(",",$price_data).");", $dbhandle);
			mysql_query("Insert into RentalIssue values(".implode(",",$rental_data).");", $dbhandle);
			mysql_query("Insert into Sq values(".implode(",",$sq_data).");", $dbhandle);
			$counter++;
		}
		fclose($file);
		//fclose($file2);
		//fclose($file4);
	}
?>
</pre>