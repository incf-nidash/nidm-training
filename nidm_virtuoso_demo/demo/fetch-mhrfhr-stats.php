<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';

  $data = get_mhrfhr_stats();
  $numrows = count($data);

//print $data[0]['gestationPeriod'] . "\n";
  //the "no" flag specifies that the term should not have hyperlinks to source URL
  $terms = get_conte_terms_hash("no", "mhrfhr");

  // echo 'Comparison to sample data works only for specific gestation periods. It does not work for "All periods" option from dropdown list.';
  echo "<form>";  
  echo "Gestation period: ";
  //echo "<select id='mhr_period_select' onChange='changeMHRpopulationStats()'>";
  echo "<select id='mhr_period_select' onChange='drpPeriodChange()'>";
  echo "<option value='*'>All periods</option>";
  
  $last_period = '';
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];	  
    $extra = "<strong>Mother's average heart rate:</strong> ".round($row['heartRateAvg'],2)."<br /><strong>Mother's heart rate standard deviation:</strong> ".round($row['heartRateStd'],2);
    $extra .= "<strong>Mother's max heart rate:</strong> ".round($row['heartRateMax'],2)."<br /><strong>Mother's min heart rate:</strong> ".round($row['heartRateMin'],2);
    $period = $row['gestationPeriod'];	
    //echo "<option value='".$period."' title='".$extra."'>".$terms[$period]."</option>";
    //echo "<option value='".$period."'>".$terms[$period]."</option>";
	if($last_period != $period){
		if(strlen($terms[$period])>0){
			echo "<option value='".$period."'>".$terms[$period]."</option>";
		}else{
			echo "<option value='".$period."'>".$period."</option>";
		}
	}
	$last_period = $row['gestationPeriod'];
	
  }
  echo '</select>';  
  echo "<div></div>";
  echo "Cognitive Task: ";
  echo "<select id='mhr_data_select'>";
  echo "<option value='*'>All</option>";  
  echo "</select>";
  //echo " Standard dev: <input type='text' id='std' name='std' value='1' />";  
  echo "<br />";

  echo 'Download data: <input style="vertical-align:middle;" type="checkbox" id="download_data" />';
  echo "<br />";
  echo '<button onClick="getMHRFHRData()" id="get_mhr_fhr_data" type="button" class="nihilo">Go!</button>';
  echo "</form>";
?>

