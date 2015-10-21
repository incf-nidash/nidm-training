<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';

  //the "yes" flag specifies that the term should have hyperlinks to source URL
  $terms = get_conte_terms_hash("yes", "");

  $dataType = $_REQUEST['dtype'];
  $tp = $_REQUEST['tp'];
  $file_baseurl = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/data/cff/";

  //$data = get_connectome_data($dataType,$tp);
  $arr = get_connectome_data($dataType,$tp);
  $data = $arr['data'];
  $query = $arr['query'];
  $query = str_replace("<", "&lt", $query);
  $query = str_replace(">", "&gt", $query);
//print "<pre>".$query."</pre>";
?>
  <div style="float:right;">Toggle SPARQL Query <input style="vertical-align:middle;" type="checkbox" id="toggle_switch" onclick="toggleSPARQL();" /> </div>
  <br />
  <div id='sparql_query' style='display:none;'><pre><?php echo $query; ?></pre> </div>
<?php
  $numrows = count($data);

  echo "$numrows entries found.";
  echo '<div style="max-height: 400px; overflow: auto;">';
  echo '<table class="gridtable">';
  echo '<thead>';
  echo '<tr><th title="' . $terms['subjectID'] . '">Subject ID</th>';
  echo '<th title="' . $terms['timepoint'] . '">Timepoint</th>';
  echo '<th title="' . $terms['species'] . '">Species</th>';
  echo '<th title="' . $terms['fileType'] . '">File type</th>';
  echo '<th title="' . $terms['fileFormat'] . '">File format</th>';
  echo '<th>View</th>';
  echo '<th>Download</th>';
  echo '</tr></thead>';
  echo '<tbody>';
  for($ri = 0; $ri < $numrows; $ri++) {
    echo '<tr>';
    $row = $data[$ri];
//var_dump($row);

    $file = $row['source'];
    $ref = $file_baseurl . $row['subjectID'] . "_" . $row['timepoint'] . "/" . $row['source'];
    $download = '<a href="'.$ref.'" download="'.$ref.'">Download</a>';

    if ($row['fileFormat'] == 'Nifti1GZ'){
      $file = '<a href="javascript:void(0)" onClick="renderVolume(\''.$ref.'\');">View</a>';
    }
    if ($row['fileFormat'] == 'TrackVis'){
      $file = '<a href="javascript:void(0)" onClick="renderFibers(\''.$ref.'\');">View</a>';
    }
    echo "<td>".$row['subjectID']."</td>";
    echo "<td>".$row['timepoint']."</td>";
    echo "<td>".$row['species']."</td>";
    echo "<td>".$row['fileType']."</td>";
    echo "<td>".$row['fileFormat']."</td>";
    echo "<td>".$file."</td>";
    echo "<td>".$download."</td>";
    echo '</tr>';
  }
  echo "</tbody>";
  echo "</table>";
  echo "</div>";
?>

