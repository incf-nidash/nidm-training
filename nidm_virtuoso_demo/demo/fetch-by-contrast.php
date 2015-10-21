<?php
function stringInsert($str,$insertstr,$pos)
{
    $str = substr($str, 0, $pos) . $insertstr . substr($str, $pos);
    return $str;
}  

?>
<?php
  require $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/misc.php';
  $nifti_drop_master_string = "nifti-drop-master";

  //the "yes" flag specifies that the term should have hyperlinks to source URL
  $terms = get_conte_terms_hash("no", "nidm");
  $file_baseuri = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/nifti-drop-master/auth/data/downloads/nidm_results/";
  //$nifti_drop_baseuri = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/nifti-drop-master/?file=/auth/data/downloads/nidm_results/";
  //hardcoded in for now
  //$view = $nifti_drop_baseuri . "ds000001/RESULTS/Group/Con1/nidm_001/nidm.ttl";
  //$ref = $file_baseuri . "ds000001/RESULTS/Group/Con1/nidm_001/nidm.ttl";
  //$file = '<a href="'.$view.'">View</a>';
  //$download = '<a href="'.$ref.'" download="'.$ref.'">Download</a>';


  $contrast = $_REQUEST['contrast'];

  $arr = get_stats_by_contrast($contrast);
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
  echo '<th>peak</th>'; 
  echo '<th>coord</th>';
  echo '<th>zstat</th>';
  echo '<th>p-value FWER</th>';
  //echo '<th>statistic</th>';
  echo '<th>View</th>';
  echo '<th>Download</th>';
  //echo '<th>URL</th>';
  echo '<tbody>';
  for($ri = 0; $ri < $numrows; $ri++) {
    $ref = $row['url'];

    //First take $row['url'] and find $nifti_drop_master_string.
    //Next, insert ?File= like this:  http://127.0.0.1:4567/$nifti_drop_master_string/?File=/auth....
    $offset = strpos($row['url'],$nifti_drop_master_string);
    $nifti_drop_url = stringInsert($row['url'], "?file=/", $offset+strlen($nifti_drop_master_string)+1);
    $view = $nifti_drop_url;
    //$file = '<a href="'.$view.'" target="_blank">View</a>';
    $file = '<a href="'.$view.'">View</a>';
    $download = '<a href="'.$ref.'" download="'.$ref.'">Download</a>';
    echo '<tr>';
    $row = $data[$ri];


    echo "<td>".$row['peak']."</td>";
    echo "<td>".$row['x']."</td>";
    echo "<td>".$row['equiv_z']."</td>";
    echo "<td>".$row['pval_fwer']."</td>";
    //echo "<td>".$row['stat']."</td>";
    echo "<td>".$file."</td>";
    echo "<td>".$download."</td>";
    //echo "<td>".$nifti_drop_url."</td>";

    echo '</tr>';
  }
  echo "</tbody>";
  echo "</table>";
  echo "</div>";
?>

