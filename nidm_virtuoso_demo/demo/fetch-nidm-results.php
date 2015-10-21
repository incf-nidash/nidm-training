<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';

  $data = get_nidm_result_contrasts();
  $numrows = count($data);

  echo "<form>";
  echo "Please select a contrast: ";
  echo "<select id='contrast_select'>";
  echo "<option value='*'>All contrasts</option>";
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    echo "<option>".$row['contrast']."</option>";
  }
	
  echo '</select>';
  echo '<button onClick="getNIDMResults()" id="get_stats_by_contrast" type="button" class="nihilo">Go!</button>';
  echo "</form>";
?>

