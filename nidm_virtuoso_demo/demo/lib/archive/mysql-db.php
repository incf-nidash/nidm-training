<?php

function get_connection () {
  $dbhandle = mysql_connect('localhost', 'root', 'faribafana') or die("Unable to connect to MySQL");

  //select a database to work with
  $selected = mysql_select_db("conte",$dbhandle) or die("Could not select examples");

  return $selected;
}

function get_connectome_data($dtype, $timepoint = "tp1", $species = "Homo sapiens"){
  $link = get_connection();
  //$sql = "SELECT * FROM meta m, data d WHERE d.dtype='$dtype' AND m.id=d.meta_id AND m.species='$species' AND m.timepoint='$timepoint'";
  $sql = "SELECT * FROM meta m, data d WHERE d.dtype='$dtype' AND m.id=d.meta_id ";
  if ($timepoint != '*'){
    $sql .= " AND m.timepoint='$timepoint'";
  }

  $result = mysql_query($sql);
  $numrows = mysql_num_rows($result);
  //print $numrows . "\n";

  $myarray = array();
  while ($row = mysql_fetch_array($result)) {
    $myarray[] = $row;
  }

  return $myarray;
}

function get_connectome_dtypes () {
  $link = get_connection();
  $sql = "SELECT DISTINCT dtype FROM data";

  $result = mysql_query($sql);
  $numrows = mysql_num_rows($result);
  //print $numrows . "\n";

  $myarray = array();
  while ($row = mysql_fetch_array($result)) {
    $myarray[] = $row;
    //print $row{'dtype'}."\n";
  }

  return $myarray;
}

function get_connectome_timepoints () {
  $link = get_connection();
  $sql = "SELECT DISTINCT timepoint FROM meta";

  $result = mysql_query($sql);
  $numrows = mysql_num_rows($result);
  //print $numrows . "\n";

  $myarray = array();
  while ($row = mysql_fetch_array($result)) {
    $myarray[] = $row;
    //print $row{'timepoint'}."\n";
  }

  return $myarray;
}

function get_subjects () {
  $link = get_connection();
  $sql = "SELECT subject_id, timepoint, species FROM meta";

  $result = mysql_query($sql);
  $numrows = mysql_num_rows($result);
  //print $numrows . "\n";

  $myarray = array();
  while ($row = mysql_fetch_array($result)) {
    $myarray[] = $row;
    //print $row{'subject_id'}."\n";
  }

  return $myarray;
}


?>
