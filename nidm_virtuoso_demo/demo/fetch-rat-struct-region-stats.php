<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';

  $animal = $_REQUEST['animal'];
  $acq = $_REQUEST['acq'];
  $region = $_REQUEST['region'];

  $data = fetch_rat_struct_stats_by_region($animal, $acq, $region);
  $numrows = count($data);
  if ($numrows == 0){
    echo "<strong>There is no data for region '" . $region . "' and animal number " . $animal . " and acquisition number " . $acq . " available in the database.<strong><br /><br />";
  }
  
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    $avg = $row['avg'];
    $std = $row['std'];
    $max = $row['max'];
    $min = $row['min'];
    echo "<strong>Sample statistics for region: '" . $region . "' animal number: " . $animal . " acquisition number: " . $acq . "<br />Average:</strong> " . $avg . " <strong>Standard deviation:</strong> " . $std . " <strong>Max:</strong> " . $max . " <strong>Min:</strong> " . $min . "<br /><br />";
  }

?>

