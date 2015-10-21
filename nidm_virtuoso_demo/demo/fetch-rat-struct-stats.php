<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';

  $data = get_rat_brain_regions();
  $numrows = count($data);
  $rats = get_rat_list();
  $rat_num = count($rats);

  //the "no" flag specifies that the term should not have hyperlinks to source URL
  //$terms = get_conte_terms_hash("no");

  echo "Select a unique combination of animal ID and brain region to get statistics for the region of that animal. ";
  echo "<form>";

  echo "Select animal: ";
  echo "<select id='rat_struct_animal_select' onchange='getRatStructStats();'>";
  echo "<option value='*'>All animals</option>";
  for($ri = 0; $ri < $rat_num; $ri++) {
    $row = $rats[$ri];
    $animal_num = $row['animal'];
    $acq = $row['acq'];
    $animal = $animal_num . "-" . $acq;
    echo "<option value='".$animal."'>".$animal."</option>";
  }
  echo '</select> ';

  echo "Brain region: ";
  echo "<select id='rat_struct_region_select' onchange='getRatStructStats();'>";
  echo "<option value='*'>All regions</option>";
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    $region = $row['region'];
    echo "<option value='".$region."'>".$region."</option>";
  }
  echo '</select> ';

  echo '<br />';
  echo "View data: ";
  echo "<select id='rat_struct_sample_comp_select'>";
  echo "<option value='*'>All data</option>";
  echo "<option value='higher'>&gt;= sample average intensity</option>";
  echo "<option value='lower'>&lt;= sample average intensity</option>";
  echo "</select>";
  echo " Standard dev: <input type='text' id='std' name='std' value='1' />";
  echo "<br />";

  echo 'Download data: <input style="vertical-align:middle;" type="checkbox" id="download_data" />';
  echo "<br />";
  echo ' <button onClick="getRatStructData()" id="get_struct_data" type="button" class="nihilo">Go!</button>';
  echo "</form>";
?>

