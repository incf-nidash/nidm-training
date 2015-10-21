<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';

  $data = get_scan_types();
  $numrows = count($data);
  $slices = get_rat_dti_slices();
  $slice_num = count($slices);

  //the "no" flag specifies that the term should not have hyperlinks to source URL
  //$terms = get_conte_terms_hash("no");

  echo "Sample statistics are calculated indexed by <br/>#1: scan type + hemisphere (across slices) <br/>#2: scan type + hemisphere + slice number (within slice). <br/>Please select unique combination in #1 to see sample stats across slices or a unique combination of #2 to see sample stats within a slice.";
  echo "<form>";

  echo "Scan type: ";
  echo "<select id='rat_dti_scantype_select' onchange='getDTIStats();'>";
  echo "<option value='*'>All scan types</option>";
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    $scan = $row['scan'];
    echo "<option value='".$scan."'>".$scan."</option>";
  }
  echo '</select> ';

  echo "Hemisphere: ";
  echo "<select id='rat_dti_hemi_select' onchange='getDTIStats();'>";
  echo "<option value='*'>Both hemispheres</option>";
  echo "<option value='Left'>Left hemisphere</option>";
  echo "<option value='Right'>Right hemisphere</option>";
  echo '</select> ';

  echo "Slice number: ";
  echo "<select id='rat_dti_slice_select' onchange='getDTIStats();'>";
  echo "<option value='*'>All slices</option>";
  for($ri = 0; $ri < $slice_num; $ri++) {
    $row = $slices[$ri];
    $slice = $row['slice'];
    echo "<option value='".$slice."'>".$slice."</option>";
  }
  echo '</select> ';
  echo '<br />';

  echo "View data: ";
  echo "<select id='rat_dti_sample_comp_select'>";
  echo "<option value='*'>All data</option>";
  echo "<option value='higher'>&gt;= sample average intensity</option>";
  echo "<option value='lower'>&lt;= sample average intensity</option>";
  echo "</select>";
  echo " Standard dev: <input type='text' id='std' name='std' value='1' />";
  echo "<br />";

  echo 'Download data: <input style="vertical-align:middle;" type="checkbox" id="download_data" />';
  echo "<br />";
  echo '<button onClick="getRatDTIData()" id="get_dti_data" type="button" class="nihilo">Go!</button>';
  echo "</form>";
?>

