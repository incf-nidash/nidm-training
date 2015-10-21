<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';

  $data = get_mhr_stats();
  $numrows = count($data);

  //the "no" flag specifies that the term should not have hyperlinks to source URL
  $terms = get_conte_terms_hash("no", "mhr");

  echo 'Comparison to sample data works only for specific gestation periods. It does not work for "All periods" option from dropdown list.';
  echo "<form>";

  echo "Gestation period: ";
  //echo "<select id='mhr_period_select' onChange='changeMHRpopulationStats()'>";
  echo "<select id='mhr_period_select' onChange='changeMHRSampleStats()'>";
  echo "<option value='*'>All periods</option>";
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    $extra = "<strong>Sample average heart rate:</strong> ".round($row['heartRateAvg'],2)."<br /><strong>Sample heart rate standard deviation:</strong> ".round($row['heartRateStd'],2);
    $extra .= "<strong>Sample max heart rate:</strong> ".round($row['heartRateMax'],2)."<br /><strong>Sample min heart rate:</strong> ".round($row['heartRateMin'],2);
    $period = $row['gestationPeriod'];
    //echo "<option value='".$period."' title='".$extra."'>".$terms[$period]."</option>";
    echo "<option value='".$period."'>".$terms[$period]."</option>";
  }
  echo '</select>';
  echo "<br />";

  echo "View data: ";
  echo "<select id='mhr_data_select'>";
  echo "<option value='*'>All data</option>";
  echo "<option value='higher'>&gt;= sample average heart rate</option>";
  echo "<option value='lower'>&lt;= sample average heart rate</option>";
  echo "</select>";
  echo " Standard dev: <input type='text' id='std' name='std' value='1' />";
  echo "<br />";

  echo 'Download data: <input style="vertical-align:middle;" type="checkbox" id="download_data" />';
  echo "<br />";
  echo '<button onClick="getMHRData()" id="get_mhr_data" type="button" class="nihilo">Go!</button>';
  echo "</form>";
?>

