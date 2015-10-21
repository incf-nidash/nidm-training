<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';

  $period = $_REQUEST['period'];

  $data = get_mhrfhr_stats_by_period($period);
  $numrows = count($data);
  
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    $avg = $row['heartRateAvg'];
    $std = $row['heartRateStd'];
    $min = $row['heartRateMin'];
    $max = $row['heartRateMax'];
    $priod = $row['gestationPeriod'];
    $label = $row['label'];
    echo "<strong>Gestation period " . $period . ":</strong> " . $label . "<br /><strong>Maternal average heart rate:</strong> " . round($avg,2) . "<br /><strong>Maternal heart rate standard deviation:</strong> " . round($std,2);
    echo "<br /><strong>Maternal max heart rate:</strong> ".round($max,2)."<br /><strong>Maternal min heart rate:</strong> ".round($min,2);
    echo "<br /><br />";
  }

?>