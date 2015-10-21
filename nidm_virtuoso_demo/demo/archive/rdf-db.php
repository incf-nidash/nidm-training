<?php
require_once( "sparqllib.php" );
//$db = "http://192.168.33.10:8890/sparql/";
$db = "http://localhost:8890/sparql/";

$dbhandle = sparql_connect( $db );
if( !$dbhandle ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

sparql_ns( "rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
sparql_ns( "rdfs","http://www.w3.org/2000/01/rdf-schema#" );
//sparql_ns("crypto", "http://id.loc.gov/vocabulary/preservation/cryptographicHashFunctions/");
//sparql_ns("dcterms", "http://purl.org/dc/terms/");
//sparql_ns("foaf", "http://xmlns.com/foaf/0.1/");
sparql_ns("fs", "http://www.incf.org/ns/nidash/fs#");
sparql_ns("nidm", "http://www.incf.org/ns/nidash/nidm#");
//sparql_ns("nif", "http://neurolex.org/wiki/");
sparql_ns("niiri", "http://iri.nidash.org/");
//sparql_ns("obo", "http://purl.obolibrary.org/obo/");
sparql_ns("xsd", "http://www.w3.org/2001/XMLSchema#");
sparql_ns("prov", "http://www.w3.org/ns/prov#");
sparql_ns("cml", "http://www.connectomics.org/cff-2/");

function get_connectome_data($dtype, $timepoint = "tp1", $species = "Homo sapiens"){
  $where = <<<EOT
     {  ?s1 cml:species ?species;
            cml:subject_name ?subject;
            cml:timepoint ?timepoint .
        ?s prov:hadMember ?s1 .
        ?s prov:hadMember ?s2 .
EOT;
  $where .= "?s2 cml:dtype '" . $dtype . "' . ";
  if ($timepoint != '*'){
    //$sql .= " AND m.timepoint='$timepoint'";
    $where .= " ?s1 cml:timepoint '" . $timepoint . "' . ";
  }
  $where .= <<<EOT
        ?s2 cml:dtype ?dtype;
            cml:src ?src;
            cml:name ?name;
            cml:fileformat ?format }
EOT;
  //print "<pre>".$where."</pre>";
  $sparql = "SELECT  distinct ?species ?subject ?dtype ?timepoint ?src ?format ?name ";
  $sparql .= "FROM <http://iri.conte.cff.org/> WHERE " . $where;

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
  }

  return $myarray;
}

function get_connectome_dtypes () {
  $sparql = "SELECT distinct ?dtype from <http://iri.conte.cff.org/> WHERE {  ?s <http://www.connectomics.org/cff-2/dtype> ?dtype  }";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    array_push($myarray,$row);
  }

  return $myarray;
}

function get_connectome_timepoints () {
  $sparql = "SELECT distinct ?timepoint from <http://iri.conte.cff.org/> WHERE {  ?s <http://www.connectomics.org/cff-2/timepoint> ?timepoint  }";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
  //print $numrows . "\n";

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    //print $row{'timepoint'}."\n";
  }

  return $myarray;
}

function get_subjects () {
  $sparql ="SELECT ?species ?timepoint ?subject_id  FROM <http://iri.conte.cff.org/>
            WHERE { ?connectome cml:species ?species;
                    cml:timepoint ?timepoint;
                    cml:subject_name ?subject_id . } ";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    //print $row{'subject_id'}."\n";
  }

  return $myarray;
}

function get_anatomical_regions () {
  $sparql = "SELECT DISTINCT ?region FROM <http://iri.conte.fs.org/> WHERE { ?s nidm:anatomicalAnnotation ?region ; fs:NVoxels ?v  .  }";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    //print $row{'subject_id'}."\n";
  }

  return $myarray;
}

function get_stat_by_region($region){
  $sparql = <<<EOT
SELECT DISTINCT ?subjectID ?subject  ?volume ?segID  ?region ?max ?mean ?nvoxel ?min ?range ?stddev
FROM <http://iri.conte.fs.org/>
WHERE {
  ?subjectdir a fs:SubjectDirectory ;
    fs:subjectID ?subjectID ;
    nidm:tag ?subject ;
    prov:hadMember ?dirMember .
  ?statsCollection prov:wasDerivedFrom ?dirMember ;
    prov:hadMember ?statsEntity .
  ?statsEntity a prov:Entity ;
    fs:Volume_mm3 ?volume ;
    fs:SegId ?segID ;
    nidm:anatomicalAnnotation ?region ;
    fs:normMax ?max ;
    fs:normMean ?mean ;
    fs:NVoxels ?nvoxel ;
    fs:normMin ?min ;
    fs:normRange ?range ;
    fs:normStdDev ?stddev .
EOT;
  if ($region !== '*') {
    //$sparql .= "filter(regex(?region,'".$region."','i'))";
    $sparql .= " ?statsEntity ?annotation fs:".$region." . ";
  }
  $sparql .= " } ";
  
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    //print $row{'subject_id'}."\n";
  }

  return $myarray;

}
?>
