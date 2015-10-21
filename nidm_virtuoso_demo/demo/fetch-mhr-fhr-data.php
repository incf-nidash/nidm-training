<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';

  //the "yes" flag specifies that the term should have hyperlinks to source URL
  $terms = get_conte_terms_hash("yes", "mhrfhr");


  $comp = $_REQUEST['datatype'];
  $old_comp = $comp;
  $period = $_REQUEST['period'];
  $old_period = $period;  
  $download = $_REQUEST['dl'];

  $arr = get_mhrfhr_data($period,$comp,$std);
  $data = $arr['data'];
  $query = $arr['query'];
  $query = str_replace("<", "&lt", $query);
  $query = str_replace(">", "&gt", $query);
  
  //sort data
  foreach ($data as $key => $row) {
    $task[$key]  = $row['task'];
    $mother[$key] = $row['mother'];
  }
  // Sort the data with task ascending, mother ascending
  // Add $data as the last parameter, to sort by the common key
  array_multisort($task, SORT_ASC, $mother, SORT_ASC, $data);
?>
  <div style="float:right;">Toggle SPARQL Query <input style="vertical-align:middle;" type="checkbox" id="toggle_switch" onclick="toggleSPARQL();" /> </div>
  <br />
  <div id='sparql_query' style='display:none;'><pre><?php echo $query; ?></pre> </div>
<?php
  $numrows = count($data);

  if ($period == '*'){
    $period = 'all';
  }
  if ($comp == '*'){
    $comp = 'all';
  }

  $link = '';
  
  $termLink = '';

  if ($download == 'yes'){
    $time = time();
    $filename = "mhrfhr-data-".lcfirst($period);
    if ($comp != '*' && $comp != 'all'){      
      $filename .= "-".strtolower(substr($comp, 3));
    }
    $filename .= "-".$time.".csv";
	
	//get mhr tp lists data
	$mhrArr = get_mhrtp_data($old_period, $old_comp);
	$mhrData = $mhrArr['data'];
	//sort data
	unset($task);
	unset($mother);
	foreach ($mhrData as $key => $row) {
      $task[$key]  = $row['task'];
      $mother[$key] = $row['mother'];
	}
	// Sort the data with task ascending, mother ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($task, SORT_ASC, $mother, SORT_ASC, $mhrData);
	
	//get fhr tp lists data
    $fhrArr = get_fhr_data($old_period, $old_comp);
    $fhrData = $fhrArr['data'];
	//sort data
	unset($task);
	unset($mother);
	foreach ($fhrdata as $key => $row) {
      $task[$key]  = $row['task'];
      $mother[$key] = $row['mother'];
	}
	// Sort the data with task ascending, mother ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($task, SORT_ASC, $mother, SORT_ASC, $fhrdata);
	
	//get spo2 tp lists data
    $spoArr = get_spo_data($old_period, $old_comp);
    $spoData = $spoArr['data'];
	//sort data
	unset($task);
	unset($mother);
	foreach ($spoData as $key => $row) {
      $task[$key]  = $row['task'];
      $mother[$key] = $row['mother'];
	}
	// Sort the data with task ascending, mother ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($task, SORT_ASC, $mother, SORT_ASC, $spoData);
	
	//get fmi tp lists data
    $fmiArr = get_fmi_data($old_period, $old_comp);
    $fmiData = $fmiArr['data'];
	//sort data
	unset($task);
	unset($mother);
	foreach ($fmiData as $key => $row) {
      $task[$key]  = $row['task'];
      $mother[$key] = $row['mother'];
	}
	// Sort the data with task ascending, mother ascending
	// Add $data as the last parameter, to sort by the common key
	array_multisort($task, SORT_ASC, $mother, SORT_ASC, $fmiData);
	
    $link = save_mhr_fhr_data_csv_file($filename,$data,$mhrData, $fhrData, $spoData, $fmiData);
	
	$termfilename = "mhrfhr-term.csv";
	$termNames = array("mother", "fetus", "heartRate", "heartRateAvg", "heartRateStd", "timepoint", "sec", "species", "FMI", "TOCO", "T0", "T1", "T2", "T3", "REST", "ERT", "SOC", "SPO2", "MHR", "FHR");
	$namespace = "mhrfhr";
	$termLink = save_term_data_csv_file($termfilename, $termNames, $namespace);
  }

  echo "Maternal heart rate data for gestation period:<strong>" . $period . "</strong> ";
  if ($comp != 'all'){
    echo "for cognitive task:<strong>" . $comp . "</strong> ";
  }
  echo "<br />";
  echo "$numrows entries found.";
  if ($download == 'yes'){
    echo "<br />$link";
	echo "<br />$termLink";
  }
  echo '<div style="max-height: 400px; overflow-y: auto; clear: both;">';
  echo '<table class="gridtable">';
  echo '<tr><th title="' . $terms['mother'] . '">Mother</th>';
  echo '<th title="' . $terms['fetus'] . '">Fetus</th>';
  echo '<th title="Gestation Period">Period</th>';
  echo '<th title="Cognitive Task">Task</th>';
  echo '<th title="' . $terms['heartRateAvg'] . '">Maternal avg heart rate</th>';
  echo '<th title="' . $terms['heartRateStd'] . '">Maternal heart rate Std dev</th>';
  echo '<th title="Maternal Heart Rate Max">MHR Max</th>';
  echo '<th title="Maternal Heart Rate Min">MHR Min</th>';
  echo '<th title="Fetal Heart Rate Avg">FHR Avg</th>';
  echo '<th title="Fetal Heart Rate Std">FHR Std</th>';
  echo '<th title="Fetal Heart Rate Max">FHR Max</th>';
  echo '<th title="Fetal Heart Rate Min">FHR Min</th>';
  echo '<th title="Maternal blood oxygen level avg(out of 100%)">SPO2 Avg</th>';
  echo '<th title="Maternal blood oxygen level std">SPO2 Std</th>';
  echo '<th title="Fetal Movement Index avg">FMI Avg</th>';
  echo '<th title="Fetal Movement Index std">FMI Std</th></tr>';
  for($ri = 0; $ri < $numrows; $ri++) {
    echo '<tr>';
    $row = $data[$ri];
    echo "<td>".$row['mother']."</td>";
    echo "<td>".$row['fetus']."</td>";
	echo "<td>".$row['period']."</td>";
	echo "<td>".substr($row['task'],3)."</td>";
    echo "<td>".round($row['MomHRAvg'],2)."</td>";
    echo "<td>".round($row['MomHRStd'],2)."</td>";
    echo "<td>".round($row['MomHRMax'],2)."</td>";
    echo "<td>".round($row['MomHRMin'],2)."</td>";
	echo "<td>".round($row['fetusHRAvg'],2)."</td>";
	echo "<td>".round($row['fetusHRStd'],2)."</td>";
	echo "<td>".round($row['fetusHRMax'],2)."</td>";
	echo "<td>".round($row['fetusHRMin'],2)."</td>";
	echo "<td>".round($row['spoAvg'],2)."</td>";
	echo "<td>".round($row['spoStd'],2)."</td>";
	echo "<td>".round($row['moveAvg'],2)."</td>";
	echo "<td>".round($row['moveStd'],2)."</td>";
    echo '</tr>';
  }
  echo "</table>";
  echo "</div>";
?>

