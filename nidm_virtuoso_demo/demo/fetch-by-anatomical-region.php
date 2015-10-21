<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';

  //the "yes" flag specifies that the term should have hyperlinks to source URL
  $terms = get_conte_terms_hash("yes", "");

  $region = $_REQUEST['region'];

  //$data = get_stat_by_region($region);
  $arr = get_stat_by_region($region);
  $data = $arr['data'];
  $query = $arr['query'];
  $query = str_replace("<", "&lt", $query);
  $query = str_replace(">", "&gt", $query);
?>
  <div style="float:right;">Toggle SPARQL Query <input style="vertical-align:middle;" type="checkbox" id="toggle_switch" onclick="toggleSPARQL();" /> </div>
  <br />
  <div id='sparql_query' style='display:none;'><pre><?php echo $query; ?></pre> </div>
<?php
  $numrows = count($data);

  echo "$numrows entries found.";
//var_dump($data);
//exit;
  echo '<div style="max-height: 400px; overflow-y: auto; clear: both;">';
  echo '<table class="gridtable">';
  echo '<thead><tr>';
  echo '<th title="' . $terms['subjectID'] . '">Subject ID</th>';
  echo '<th>Region</th>';
  echo '<th>SegID</th>';
  echo '<th>Volume (mm<sup>3</sup>)</th>';
  echo '<th>Voxels</th>';
  echo '<th>normMax</th>';
  echo '<th>normMin</th>';
  echo '<th>normMean</th>';
  echo '<th>Range</th>';
  echo '<th>StdDev</th>';
  echo '</tr></thead>';
  echo '<tbody>';
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

