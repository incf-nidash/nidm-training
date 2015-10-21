<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/mysql-db.php';
  $dtype = $_REQUEST['dtype'];

  $data = get_connectome_data($dtype);
  $numrows = count($data);

  echo "$numrows entries found.";
  echo "<table class='gridtable'>";
  echo "<tr><th>Subject</th><th>Time-point</th><th>Species</th><th>Name</th><th>View</th><th>Download</th><th>File format</th><th>File size</th></tr>";
  for($ri = 0; $ri < $numrows; $ri++) {
    echo '<tr>';
    $row = $data[$ri];
    $file = $row['source'];
    $ref = $row['url'];
    $download = '<a href="'.$ref.'" download="'.$ref.'">Download</a>';
    if ($row['viewable'] == '1'){
      //$ref = 'http://localhost/conte/cff/1stOutput/'.$row['subject_id'].'_'.$row['timepoint'].'/'.$file;
      if ($row['fileFormat'] == 'Nifti1GZ'){
        $file = '<a href="javascript:void(0)" onClick="renderVolume(\''.$ref.'\');">View</a>';
      }
      if ($row['fileFormat'] == 'TrackVis'){
        $file = '<a href="javascript:void(0)" onClick="renderFibers(\''.$ref.'\');">View</a>';
      }
    }
    echo "<td>".$row['subjectID']."</td>";
    echo "<td>".$row['timepoint']."</td>";
    echo "<td>".$row['species']."</td>";
    echo "<td>".$row['fileType']."</td>";
    echo "<td>".$file."</td>";
    echo "<td>".$download."</td>";
    echo "<td>".$row['fileFormat']."</td>";
    echo "<td>".$row['filesize']."</td>";
    echo '</tr>';
  }
  echo "</table>";
?>

