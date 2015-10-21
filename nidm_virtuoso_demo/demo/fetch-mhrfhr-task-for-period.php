<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';

  $period = $_REQUEST['period'];

  $data = get_mhrfhr_stats_by_period($period);
  $numrows = count($data);
  
  echo "<option value='*'>All</option>";  
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    $avg = $row['heartRateAvg'];
    $std = $row['heartRateStd'];
    $min = $row['heartRateMin'];
    $max = $row['heartRateMax'];
    $priod = $row['gestationPeriod'];
    $label = $row['label'];
    echo "<option value='".$label."'>".$label."</option>";
  }

?>