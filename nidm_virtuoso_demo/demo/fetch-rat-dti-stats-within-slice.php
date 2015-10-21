<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';

  $scantype = $_REQUEST['scantype'];
  $hemi = $_REQUEST['hemi'];
  $slice = $_REQUEST['slice'];

//print "(scan type:" . $scantype . " hemisphere:" . $hemi . " slice number:" . $slice . ")";
  $data = fetch_rat_dti_stats_within_slice($scantype, $hemi, $slice);
  $numrows = count($data);
  if ($numrows == 0){
    echo "<strong>There is no data for scan type of '" . $scantype . "' and slice number " . $slice . " available in the database.<strong><br /><br />";
  }
  
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    $avg = $row['avg'];
    $std = $row['std'];
    $max = $row['max'];
    $min = $row['min'];
    echo "<strong>Sample statistics within slice (scan type:" . $scantype . " hemisphere:" . $hemi . " slice number:" . $slice . ")<br />Average:</strong> " . $avg . " <strong>Standard deviation:</strong> " . $std . " <strong>Max:</strong> " . $max . " <strong>Min:</strong> " . $min . "<br /><br />";
  }

?>

