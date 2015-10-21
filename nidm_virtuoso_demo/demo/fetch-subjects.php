<?php
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';

  //the "yes" flag specifies that the term should have hyperlinks to source URL
  $terms = get_conte_terms_hash("yes", "");

  //$data = get_subjects();
  $arr = get_subjects();
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
  //echo 'project: '.$_REQUEST['project'].' '.$_GET['fetch'];  
  echo $numrows.' records found.';
  echo "<table class='gridtable'>";
  echo '<tr><th title="' . $terms['subjectID'] . '">Subject ID</th>';
  echo '<th title="' . $terms['timepoint'] . '">Timepoint</th>';
  echo '<th title="' . $terms['species'] . '">Species</th></tr>';
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

