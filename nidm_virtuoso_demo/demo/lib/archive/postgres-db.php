<?php

function get_connection () {
  $link = pg_Connect("host=localhost port=5432 dbname=conte user=postgres password=alm");

  return $link;
}

function get_connectome_data($dtype, $species = "Homo sapiens", $timepoint = "tp1"){
  $link = get_connection();
  $sql = "SELECT * FROM meta m, data d WHERE d.dtype='$dtype' AND m.id=d.meta_id AND m.species='$species' AND m.timepoint='$timepoint'";

  $result = pg_exec($link, $sql);
  $numrows = pg_numrows($result);

  $myarray = array();
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = pg_fetch_array($result, $ri);
    $myarray[] = $row;
  }

  pg_close($link);
  return $myarray;
}

function get_connectome_dtypes () {
  $link = get_connection();
  $sql = "SELECT DISTINCT dtype FROM data";

  $result = pg_exec($link, $sql);
  $numrows = pg_numrows($result);

  $myarray = array();
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = pg_fetch_array($result, $ri);
    $myarray[] = $row;
  }

  pg_close($link);
  return $myarray;
}

function get_subjects () {
  $link = get_connection();
  $sql = "SELECT subject_id, timepoint, species FROM meta";

  $result = pg_exec($link, $sql);
  $numrows = pg_numrows($result);

  $myarray = array();
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = pg_fetch_array($result, $ri);
    $myarray[] = $row;
  }

  pg_close($link);
  return $myarray;
}

?>
