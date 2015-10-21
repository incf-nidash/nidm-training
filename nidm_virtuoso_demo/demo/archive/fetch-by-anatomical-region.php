<?php
  require './lib/rdf-db.php';
  $region = $_REQUEST['region'];

  $data = get_stat_by_region($region);
  $numrows = count($data);

  echo "$numrows entries found.";
//var_dump($data);
//exit;
  echo '<div style="max-height: 400px; overflow-y: auto; clear: both;">';
  echo "<table class='gridtable'>";
  echo "<thead>";
  echo "<tr><th>Subject</th><th>Region</th><th>SegID</th><th>Volume (mm<sup>3</sup>)</th><th>Voxels</th><th>normMax</th><th>normMin</th><th>normMean</th><th>Range</th><th>StdDev</th></tr>";
  echo "</thead>";
  echo "<tbody>";
  for($ri = 0; $ri < $numrows; $ri++) {
    echo '<tr>';
    $row = $data[$ri];
//var_dump($row);
    list($iri, $region) = split('#', $row['region']);
    $mean = round( $row['mean'], 1, PHP_ROUND_HALF_UP);
    $stddev = round( $row['stddev'], 1, PHP_ROUND_HALF_UP);

    echo "<td>".$row['subject']."</td>";
    echo "<td>".$region."</td>";
    echo "<td>".$row['segID']."</td>";
    echo "<td>".$row['volume']."</td>";
    echo "<td>".$row['nvoxel']."</td>";
    echo "<td>".$row['max']."</td>";
    echo "<td>".$row['min']."</td>";
    echo "<td>".$mean."</td>";
    echo "<td>".$row['range']."</td>";
    echo "<td>".$stddev."</td>";
    echo '</tr>';
  }
  echo "</tbody>";
  echo "</table>";
  echo "</div>";
?>

