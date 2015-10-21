<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';

  $scantype = $_REQUEST['scantype'];
  $hemi = $_REQUEST['hemi'];
//echo $scantype . " " . $hemi;

  $data = fetch_rat_dti_stats_across_slices($scantype, $hemi);
  $numrows = count($data);
  
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    $avg = $row['avg'];
    $std = $row['std'];
    $max = $row['max'];
    $min = $row['min'];
    echo "<strong>Sample statistics across slices (scan type:" . $scantype . " hemisphere:" . $hemi . ")<br />Average:</strong> " . $avg . " <strong>Standard deviation:</strong> " . $std . " <strong>Max:</strong> " . $max . " <strong>Min:</strong> " . $min . "<br /><br />";
  }

?>

