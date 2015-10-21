<?php
  require './lib/rdf-db.php';
  
  $data = get_anatomical_regions();
  $numrows = count($data);

  $regions = array();
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    list($iri, $region) = split('#', $row['region']);
    array_push($regions,$region);
  }
  sort($regions);

  echo "<form>";
  echo "Please select an anatomical region: ";
  echo "<select id='region_select'>";
  echo "<option value='*'>Select anatomical region</option>";
  echo "<option value='*'>All regions</option>";
  foreach ($regions as $region){
    echo "<option>".$region."</option>";
  }
  echo '</select>';
  echo '<button onClick="getStatData()" id="stat_data" type="button" class="nihilo">Go!</button>';
  echo "</form>";
?>

