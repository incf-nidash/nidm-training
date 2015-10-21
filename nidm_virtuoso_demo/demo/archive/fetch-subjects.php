<?php
  //require './lib/postgres-db.php';
  require './lib/rdf-db.php';

  $data = get_subjects();
  $numrows = count($data);
  //echo 'project: '.$_REQUEST['project'].' '.$_GET['fetch'];
  echo $numrows.' records found.';
  echo "<table class='gridtable'>";
  echo "<tr><th>Subject ID</th><th>Timepoint</th><th>Species</th></tr>";
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    echo "<tr>";
    echo "<td>".$row['subjectID']."</td>";
    echo "<td>".$row['timepoint']."</td>";
    echo "<td>".$row['species']."</td>";
    echo "</tr>";
  }
  echo '</table>';
?>

