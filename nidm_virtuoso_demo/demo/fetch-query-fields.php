<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  
  $ddata = get_connectome_dtypes();
  $dnumrows = count($ddata);

  $tdata = get_connectome_timepoints();
  $tnumrows = count($tdata);

  echo "<form>";
  echo "Please select a data type AND a timepoint: ";
  echo "<select id='dtype_select'>";
  echo "<option value='*'>Select data type</option>";
  for($ri = 0; $ri < $dnumrows; $ri++) {
    $row = $ddata[$ri];
    echo "<option>".$row['dataType']."</option>";
  }
  echo '</select>';
  echo "<select id='tp_select'>";
  echo "<option value='*'>Select time point</option>";
  echo "<option value='*'>All time points</option>";
  for($ri = 0; $ri < $tnumrows; $ri++) {
    $row = $tdata[$ri];
    echo "<option>".$row['timepoint']."</option>";
  }
  echo '</select>';
  echo '<button onClick="getData()" id="query_fields" type="button" class="nihilo">Go!</button>';
  echo "</form>";
?>

