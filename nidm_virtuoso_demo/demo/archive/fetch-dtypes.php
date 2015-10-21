<?php
  require_once( "lib/sparqllib.php" );

  $db = sparql_connect( "http://192.168.33.10:8890/sparql/" );
  if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  sparql_ns("cml", "http://www.connectomics.org/cff-2/");
  $sparql ="SELECT distinct ?o from <http://iri.conte.cff.org/> WHERE {  ?s <http://www.connectomics.org/cff-2/dtype> ?o  }";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  //$data = get_subjects();
  $numrows = sparql_num_rows( $result );

  $data = array();
  echo $numrows.' records found.';
  while( $row = sparql_fetch_array( $result ) )
  {
    array_push($data,$row);
  }

  //$data = get_connectome_dtypes();
  //$numrows = count($data);

  echo "Please select a data type for dropdown: ";
  echo "<select id='dtype_select' onChange='getData()'>";
  echo "<option>Select an option from below</option>";
  for($ri = 0; $ri < $numrows; $ri++) {
    $row = $data[$ri];
    echo "<option>".$row['dtype']."</option>";
  }
  echo '</select>';
?>

