<?php
  //require './lib/mysql-db.php';
  require './lib/rdf-db.php';
  $dtype = $_REQUEST['dtype'];
  $tp = $_REQUEST['tp'];
  $file_baseurl = "http://demo.conte-data.org/data/cff/";

  $data = get_connectome_data($dtype,$tp);
  $numrows = count($data);

  echo "$numrows entries found.";
  echo '<div style="max-height: 400px; overflow: auto;">';
  echo "<table class='gridtable'>";
  echo "<thead>";
  echo "<tr><th>Subject</th><th>Time-point</th><th>Species</th><th>Name</th><th>View</th><th>Download</th><th>File format</th></tr>";
  echo "</thead>";
  echo "<tbody>";
  for($ri = 0; $ri < $numrows; $ri++) {
    echo '<tr>';
    $row = $data[$ri];
//var_dump($row);

    $file = $row['src'];
    $ref = $file_baseurl . $row['subject'] . "_" . $row['timepoint'] . "/" . $row['src'];
    $download = '<a href="'.$ref.'" download="'.$ref.'">Download</a>';

    if ($row['format'] == 'Nifti1GZ'){
      $file = '<a href="javascript:void(0)" onClick="renderVolume(\''.$ref.'\');">View</a>';
    }
    if ($row['format'] == 'TrackVis'){
      $file = '<a href="javascript:void(0)" onClick="renderFibers(\''.$ref.'\');">View</a>';
    }
    echo "<td>".$row['subject']."</td>";
    echo "<td>".$row['timepoint']."</td>";
    echo "<td>".$row['species']."</td>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$file."</td>";
    echo "<td>".$download."</td>";
    echo "<td>".$row['format']."</td>";
    echo '</tr>';
  }
  echo "</tbody>";
  echo "</table>";
  echo "</div>";
?>

