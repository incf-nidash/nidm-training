<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';

  //the "yes" flag specifies that the term should have hyperlinks to source URL
  $terms = get_conte_terms_hash("yes", "mhr");

  $comp = $_REQUEST['range'];
  $period = $_REQUEST['period'];
  $std = $_REQUEST['std'];
  $download = $_REQUEST['dl'];

  //$data = get_mhr_data($period,$comp);
  $arr = get_mhr_data($period,$comp,$std);
  $data = $arr['data'];
  $query = $arr['query'];
  $query = str_replace("<", "&lt", $query);
  $query = str_replace(">", "&gt", $query);
  //print $query;
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
    $filename = "mhr-data-".lcfirst($period);
    if ($comp != '*' && $comp != 'all'){
      $filename .= "-".$comp."-".$std;
    }
    $filename .= "-".$time.".csv";
    $link = save_mhr_data_csv_file($filename,$data);
	
	$termfilename = "mhr-term.csv";
	$termNames = array("mother", "fetus", "heartRate", "heartRateAvg", "heartRateStd", "timepoint", "sec", "species", "T3", "T4", "T5");
	$namespace = "mhr";
	$termLink = save_term_data_csv_file($termfilename, $termNames, $namespace);
  }

  echo "Maternal heart rate data for gestation period:<strong>" . $period . "</strong> ";
  if ($comp != 'all'){
    echo "view data:<strong>" . $comp . "</strong> than sample by standard deviation:<strong>" . $std."</strong>";
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
  echo '<th title="' . $terms['heartRateAvg'] . '">Mother\'s avg heart rate</th>';
  echo '<th title="' . $terms['heartRateStd'] . '">Std dev</th>';
  echo '<th title="heartRateMax">Max</th>';
  echo '<th title="heartRateMin">Min</th></tr>';
  for($ri = 0; $ri < $numrows; $ri++) {
    echo '<tr>';
    $row = $data[$ri];
    echo "<td>".$row['mother']."</td>";
    echo "<td>".$row['fetus']."</td>";
    echo "<td>".round($row['heartRate'],2)."</td>";
    echo "<td>".round($row['heartRateStd'],2)."</td>";
    echo "<td>".round($row['heartRateMax'],2)."</td>";
    echo "<td>".round($row['heartRateMin'],2)."</td>";
    echo '</tr>';
  }
  echo "</table>";
  echo "</div>";
?>

