<?php
require_once( "sparqllib.php" );
$db = "http://localhost:8890/sparql/";

$dbhandle = sparql_connect( $db );
if( !$dbhandle ) {
	print ("error when connecting db");
	print sparql_errno() . ": " . sparql_error(). "\n"; exit; 
}

sparql_ns( "rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
sparql_ns( "rdfs","http://www.w3.org/2000/01/rdf-schema#" );
//sparql_ns("crypto", "http://id.loc.gov/vocabulary/preservation/cryptographicHashFunctions/");
//sparql_ns("dcterms", "http://purl.org/dc/terms/");
//sparql_ns("foaf", "http://xmlns.com/foaf/0.1/");
//sparql_ns("nif", "http://neurolex.org/wiki/");
sparql_ns("fs", "http://www.incf.org/ns/nidash/fs#");
sparql_ns("nidm", "http://www.incf.org/ns/nidash/nidm#");
sparql_ns("niiri", "http://iri.nidash.org/");
sparql_ns("xsd", "http://www.w3.org/2001/XMLSchema#");
sparql_ns("prov", "http://www.w3.org/ns/prov#");
sparql_ns("cff", "http://www.contecenter.org/ns/cff#");
sparql_ns("ncit", "http://ncitt.ncit.nih.gov/");
sparql_ns("mhr", "http://www.contecenter.org/ns/mhr#");
sparql_ns("ccterms", "http://www.contecenter.org/ns/ccterms#");
sparql_ns("cuci", "http://www.contecenter.org/ns/ConteUCI#");
#sparql get gestation periods
sparql_ns("fhr", "http://www.contecenter.org/ns/fhr#");
sparql_ns("obo", "http://purl.obolibrary/obo/");


function get_connectome_data($dataType, $timepoint = "tp1", $species = "Homo sapiens"){
  $where = <<<EOT
{  
  ?s1 ncit:species ?species;
      ncit:subjectID ?subjectID;
      ncit:timepoint ?timepoint .
  ?s  prov:hadMember ?s1 .
  ?s  prov:hadMember ?s2 .
EOT;
  $where .= "\n  ?s2 ncit:dataType '" . $dataType . "' . \n";
  if ($timepoint != '*'){
    //$sql .= " AND m.timepoint='$timepoint'";
    $where .= "  ?s1 ncit:timepoint '" . $timepoint . "' . \n";
  }
  $where .= <<<EOT
  ?s2 ncit:dataType ?dataType;
      ncit:source ?source;
      cff:fileType ?fileType;
      cff:fileFormat ?fileFormat .
}
EOT;
  //print "<pre>".$where."</pre>";
  $sparql = "SELECT  DISTINCT ?species ?subjectID ?dataType ?timepoint ?source ?fileFormat ?fileType \n";
  $sparql .= "FROM <http://iri.conte.cff.org/> \nWHERE " . $where;
  //print "<pre>".$sparql."</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
  }

  //return $myarray;

  $namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces .= "prefix cff: <http://www.contecenter.org/ns/cff#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;
}

function get_connectome_dtypes () {
  $sparql = "SELECT distinct ?dataType from <http://iri.conte.cff.org/> WHERE {  ?s ncit:dataType ?dataType  }";
  //print "<pre>".$sparql."</pre>";

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
  $sparql = "SELECT distinct ?timepoint from <http://iri.conte.cff.org/> WHERE {  ?s ncit:timepoint ?timepoint  }";
  //print "<pre>".$sparql."</pre>";

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
  $sparql ="
  Select * Where {
  	{
SELECT ?species ?timepoint ?subjectID  
FROM <http://iri.conte.cff.org/>
WHERE { ?connectome ncit:species ?species;
                    ncit:timepoint ?timepoint;
                    ncit:subjectID ?subjectID . }
    }
    UNION{
    	SELECT Distinct ?species ?acq as ?timepoint ?subjectID
		FROM <http://iri.conte.rat-frag.org/>
		WHERE { ?entity 
		          cuci:animalNumber ?subjectID ;
		          cuci:acquisitionNumber ?acq ;
		          prov:wasGeneratedBy ?activity .
		
		?activity prov:wasAssociatedWith ?agent .
		
		?agent ncit:species ?species .
		      } order by ?subjectID ?timepoint
    }
    UNION{
    	SELECT ?subjectID ?timepoint ?species
		FROM <http://iri.conte.mhr.org/>
		WHERE {
		?col
		    mhr:gestationPeriod ?timepoint ;
		    prov:hadMember ?entity .
		
		?entity rdfs:label ?label ;
		     prov:wasAttributedTo ?agent1 .
		?activity prov:wasAssociatedWith ?agent1 .
		?agent1 ncit:species ?species ;
		ncit:subjectID ?subjectID .
		
		
		 }
		order by ?subjectID ?timepoint
    } 
	UNION{		
		SELECT distinct ?subjectID ?period as ?timepoint ?species
		FROM <http://iri.conte.mhrnew.org/T0/>
		FROM <http://iri.conte.mhrnew.org/T1/>
		FROM <http://iri.conte.mhrnew.org/T2/>
		FROM <http://iri.conte.mhrnew.org/T3/>
		WHERE { 
		?col mhr:gestationPeriod ?period ;
		     prov:hadMember ?entity . 
		
		?entity prov:wasAttributedTo ?agent1 .
		
		?spo_entity prov:wasAttributedTo ?agent1 .
		
		?activity prov:wasAssociatedWith ?agent1 .
		
		?agent1 ncit:subjectID ?subjectID ;
		        ncit:species ?species .
		
		 } order by ?subjectID ?timepoint
	}
	
                    
}order by ?subjectID ?timepoint";

  //print "<pre>".$sparql."</pre>";
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

  //return $myarray;

  $namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n\n";
  $namespaces .= "prefix cuci: <http://www.contecenter.org/ns/ConteUCI#>\n\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n\n";
  $namespaces .= "prefix fhr: <http://www.contecenter.org/ns/fhr#>\n\n";
  $namespaces .= "prefix mhr: <http://www.contecenter.org/ns/mhr#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;
}

function get_anatomical_regions () {
  $sparql = "SELECT DISTINCT ?region FROM <http://iri.conte.fs.org/> WHERE { ?s nidm:anatomicalAnnotation ?region ; fs:NVoxels ?v  .  } ORDER BY ?region";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  // print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    //print $row{'subject_id'}."\n";
  }

  return $myarray;
}

function get_nidm_result_contrasts() {

  $sparql = "SELECT DISTINCT ?contrast FROM <http://nidm.nidash.org/results/> WHERE { ?s a obo:STATO_0000323 ; rdfs:label ?contrast . }";
  
  $namespaces = "prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>\n\n";
  $namespaces .= "prefix obo: <http://purl.obolibrary.org/obo/>\n\n";

  $query = $namespaces . $sparql;

  $result = sparql_query( $query);
  if( !$result ) { print "ERROR: " . sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
  }

  return $myarray;

}

function get_stats_by_contrast($contrast) {


  $sparql = "


SELECT DISTINCT ?value ?cluster ?peak ?x ?equiv_z ?pval_fwer ?stat ?url
FROM <http://nidm.nidash.org/results/> 
WHERE
{ 
?contrast_estimation a nidm:NIDM_0000001;
      prov:used ?contrast_id .
  ?spm_result a nidm:NIDM_0000027;
      dcat:accessURL ?url .
  niiri:contrast_id rdfs:label ?value .
  ?peak a peak: .
  ?cluster a significant_cluster: .
  ?peak prov:wasDerivedFrom ?cluster .
  ?peak prov:atLocation ?coordinate .
  ?coordinate coordinateVector: ?x .
  ?peak equivalent_zstatistic: ?equiv_z .
  ?peak pvalue_fwer: ?pval_fwer .
  ?cluster prov:wasDerivedFrom/prov:wasGeneratedBy/prov:used ?statmap .

  FILTER regex(?value,'^$contrast','i')
}
ORDER BY ?cluster ?peak ?x";

$namespaces = "prefix prov: <http://www.w3.org/ns/prov#>\n";
$namespaces .= "prefix xsd: <http://www.w3.org/2001/XMLSchema#>\n";
$namespaces .= "prefix nidm: <http://purl.org/nidash/nidm#>\n";
$namespaces .= "prefix niiri: <http://iri.nidash.org/>\n";
$namespaces .= "prefix spm: <http://purl.org/nidash/spm#>\n";
$namespaces .= "prefix neurolex: <http://neurolex.org/wiki/>\n";
$namespaces .= "prefix crypto: <http://id.loc.gov/vocabulary/preservation/cryptographicHashFunctions#>\n";
$namespaces .= "prefix dct: <http://purl.org/dc/terms/>\n";
$namespaces .= "prefix nfo: <http://www.semanticdesktop.org/ontologies/2007/03/22/nfo#>\n";
$namespaces .= "prefix dc: <http://purl.org/dc/elements/1.1/>\n";
$namespaces .= "prefix dctype: <http://purl.org/dc/dcmitype/>\n";
$namespaces .= "prefix obo: <http://purl.obolibrary.org/obo/>\n";
$namespaces .= "prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>\n";
$namespaces .= "prefix dcat: <http://www.w3.org/ns/dcat#>\n";
$namespaces .= "prefix peak: <http://purl.org/nidash/nidm#NIDM_0000062>\n";
$namespaces .= "prefix significant_cluster: <http://purl.org/nidash/nidm#NIDM_0000070>\n";
$namespaces .= "prefix coordinate: <http://purl.org/nidash/nidm#NIDM_0000086>\n";
$namespaces .= "prefix equivalent_zstatistic: <http://purl.org/nidash/nidm#NIDM_0000092>\n";
$namespaces .= "prefix pvalue_fwer: <http://purl.org/nidash/nidm#NIDM_0000115>\n";
$namespaces .= "prefix pvalue_uncorrected: <http://purl.org/nidash/nidm#NIDM_0000116>\n";
$namespaces .= "prefix statistic_map: <http://purl.org/nidash/nidm#NIDM_0000076>\n";
$namespaces .= "prefix statistic_type: <http://purl.org/nidash/nidm#NIDM_0000123>\n";
$namespaces .= "prefix coordinateVector: <http://purl.org/nidash/nidm#NIDM_0000086>\n";

  $query = $namespaces . $sparql;

  $result = sparql_query( $query );
  if( !$result ) { 
	print "ERROR\n"; print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );


  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
  }

  //return $myarray;
  $hash['data'] = $myarray;
  $hash['query'] = $query;

  return $hash;
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
  print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    //print $row{'subject_id'}."\n";
  }

  //return $myarray;

  $namespaces = "prefix fs: <http://www.incf.org/ns/nidash/fs#>\n";
  $namespaces .= "prefix nidm: <http://www.incf.org/ns/nidash/nidm#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;

}

function get_rat_struct_data($animal, $acq, $region, $comp, $std) {
  $sample_avg = 0;
  $sample_std = 0;
  $clause = '';
  if ($comp !== '*') {
    $data = fetch_rat_struct_stats_by_region($animal, $acq, $region);
    $sample_data = $data[0];
    $sample_avg = $sample_data['avg'];
    $sample_std = $sample_data['std'];
  }

  $comp_clause = '';
  if ($comp !== '*') {
    if ($comp == 'higher') {
      $value = $sample_avg + ($sample_std * $std);
      $clause .= " ?avg >= " . $value;
    }
    if ($comp == 'lower') {
      $value = $sample_avg - ($sample_std * $std);
      $clause .= " ?avg <= " . $value;
    }
  }

  $filter = "";
  if (strlen($clause) > 1){
    $filter = " filter ( " . $clause . " ) ";
  }

  $constraints = '';
  if ($region != '*'){
    $constraints .= " cuci:region '" . $region . "' ;\n";
  }
  if ($animal != '*'){
    $constraints .= " cuci:animalNumber " . $animal . " ;\n";
    $constraints .= " cuci:acquisitionNumber " . $acq . " ;\n";
  }

  $sparql = <<<EOT
SELECT DISTINCT ?region ?animal ?acq ?hemi ?slice ?avg ?std ?max ?min ?area ?sum ?file
FROM <http://iri.conte.rat-frag.org/>
WHERE { ?entity
EOT;
  $sparql .= $constraints;
  $sparql .= <<<EOT
    cuci:region ?region ;
    cuci:acquisitionNumber ?acq ;
    cuci:animalNumber ?animal ;
    cuci:hemisphere ?hemi ;
    cuci:sliceNumber ?slice ;
    cuci:intensityAvg ?avg ;
    cuci:intensityStdDev ?std ;
    cuci:intensityMax ?max ;
    cuci:intensityMin ?min ;
    cuci:pixelArea ?area ;
    cuci:pixelSum ?sum ;
    cuci:fileName ?file .
EOT;
  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " }";
  $sparql .= "\nORDER BY ?region ?animal ?acq ?slice ?avg ";
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'scanType'}."\n";
  }

  //$namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces = "prefix cuci: <http://www.contecenter.org/ns/ConteUCI#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;
}

function get_rat_dti_data_across_slices_no_comp($scantype, $hemi) {
  $sparql = <<<EOT
SELECT DISTINCT ?animal ?acq ?slice ?file ?hemi ?avg ?std ?max ?min ?area ?sum ?scanType 
FROM <http://iri.conte.rat-frag.org/>
WHERE { ?entity 
EOT;
  if ($scantype != '*'){
    $sparql .= "\n cuci:scanType '" . $scantype . "' ;\n";
  }
  if ($hemi != '*'){
    $sparql .= "\n cuci:hemisphere '" . $hemi . "' ;\n";
  }
  $sparql .= <<<EOT
          cuci:scanType ?scanType ;
          cuci:animalNumber ?animal ;
          cuci:acquisitionNumber ?acq ;
          cuci:sliceNumber ?slice ;
          cuci:fileName ?file ;
          cuci:hemisphere ?hemi ;
          cuci:intensityAvg ?avg ;
          cuci:intensityStdDev ?std ;
          cuci:intensityMax ?max ;
          cuci:intensityMin ?min ;
          cuci:pixelArea ?area ;
          cuci:pixelSum ?sum .
      }
ORDER BY ASC(?animal) ASC(?acq) ?slice ?hemi ?scanType
EOT;
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'scanType'}."\n";
  }

  //$namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces = "prefix cuci: <http://www.contecenter.org/ns/ConteUCI#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;
}

function get_rat_dti_data_across_slices($scantype, $hemi, $comp, $std) {
  if ($comp == '*') {
    return get_rat_dti_data_across_slices_no_comp($scantype, $hemi);
  }

  $sample_avg = 0;
  $sample_std = 0;
  $clause = '';
  if ($comp !== '*') {
    $data = fetch_rat_dti_stats_across_slices($scantype, $hemi);
    $sample_data = $data[0];
    $sample_avg = $sample_data['avg'];
    $sample_std = $sample_data['std'];
  }

  $comp_clause = '';
  if ($comp !== '*') {
    if ($comp == 'higher') {
      $value = $sample_avg + ($sample_std * $std);
      $clause .= " ?avg >= " . $value;
    }
    if ($comp == 'lower') {
      $value = $sample_avg - ($sample_std * $std);
      $clause .= " ?avg <= " . $value;
    }
  }

  $filter = "";
  if (strlen($clause) > 1){
    $filter = " filter ( " . $clause . " ) ";
  }

  $sparql = <<<EOT
SELECT DISTINCT ?animal ?acq ?slice ?file ?hemi ?avg ?std ?max ?min ?area ?sum ?scanType ?statType
FROM <http://iri.conte.rat-frag.org/>
WHERE {
  ?collection prov:hadMember ?agent ;
              prov:hadMember ?stat .
  ?activity prov:wasAssociatedWith ?agent .
  ?entity prov:wasGeneratedBy ?activity ;
EOT;
  $sparql .= "\n cuci:scanType '" . $scantype . "' ;\n";
  $sparql .= "\n cuci:hemisphere '" . $hemi . "' ;\n";
  $sparql .= <<<EOT
          cuci:animalNumber ?animal ;
          cuci:acquisitionNumber ?acq ;
          cuci:sliceNumber ?slice ;
          cuci:fileName ?file ;
          cuci:hemisphere ?hemi ;
          cuci:intensityAvg ?avg ;
          cuci:intensityStdDev ?std ;
          cuci:intensityMax ?max ;
          cuci:intensityMin ?min ;
          cuci:pixelArea ?area ;
          cuci:pixelSum ?sum .
  ?stat  cuci:scanType ?scanType ;
         cuci:statType 'across slices' ;
         cuci:statType ?statType ;
EOT;
  $sparql .= "\n cuci:scanType '" . $scantype . "' .\n";
  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " }";
  $sparql .= "\nORDER BY ASC(?animal) ASC(?acq) DESC(?avg) ";
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'scanType'}."\n";
  }

  //$namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces = "prefix cuci: <http://www.contecenter.org/ns/ConteUCI#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;
}

function get_rat_dti_data_within_slice_no_comp($scantype, $hemi, $slice) {
  $sparql = <<<EOT
SELECT DISTINCT ?animal ?acq ?slice ?file ?hemi ?avg ?std ?max ?min ?area ?sum ?scanType 
FROM <http://iri.conte.rat-frag.org/>
WHERE { ?entity 
EOT;
  $sparql .= "\n cuci:sliceNumber " . $slice . " ;\n";
  if ($scantype != '*'){
    $sparql .= "\n cuci:scanType '" . $scantype . "' ;\n";
  }
  if ($hemi != '*'){
    $sparql .= "\n cuci:hemisphere '" . $hemi . "' ;\n";
  }
  $sparql .= <<<EOT
          cuci:scanType ?scanType ;
          cuci:animalNumber ?animal ;
          cuci:acquisitionNumber ?acq ;
          cuci:sliceNumber ?slice ;
          cuci:fileName ?file ;
          cuci:hemisphere ?hemi ;
          cuci:intensityAvg ?avg ;
          cuci:intensityStdDev ?std ;
          cuci:intensityMax ?max ;
          cuci:intensityMin ?min ;
          cuci:pixelArea ?area ;
          cuci:pixelSum ?sum .
      }
ORDER BY ASC(?animal) ASC(?acq) ?slice ?hemi ?scanType
EOT;
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'scanType'}."\n";
  }

  //$namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces = "prefix cuci: <http://www.contecenter.org/ns/ConteUCI#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;
}

function get_rat_dti_data_within_slice($scantype, $hemi, $slice, $comp, $std){
  if ($comp == '*') {
    return get_rat_dti_data_within_slice_no_comp($scantype, $hemi, $slice);
  }

  $sample_avg = 0;
  $sample_std = 0;
  $clause = '';
  if ($comp !== '*') {
    $data = fetch_rat_dti_stats_within_slice($scantype, $hemi, $slice);
    //some slices (with scantype and hemisphere combination) don't have within slice statistics, so make sure stat exists
    if (sizeof($data) > 0){
      $sample_data = $data[0];
      $sample_avg = $sample_data['avg'];
      $sample_std = $sample_data['std'];
    }
  }

  $comp_clause = '';
  if ($comp !== '*') {
    //do not add filters to slices that don't have within slice statistics
    if ($comp == 'higher' and $sample_avg > 0) {
      $value = $sample_avg + ($sample_std * $std); 
      $clause .= " ?avg >= " . $value;
    }
    if ($comp == 'lower' and $sample_avg > 0) {
      $value = $sample_avg - ($sample_std * $std); 
      $clause .= " ?avg <= " . $value;
    }
  }

  $filter = "";
  if (strlen($clause) > 1){
    $filter = " filter ( " . $clause . " ) ";
  } 

  $constraints = '';
  if ($scantype != '*'){
    $constraints .= "\n cuci:scanType '" . $scantype . "' ;\n";
  }
  if ($hemi != '*'){
    $constraints .= "\n cuci:hemisphere '" . $hemi . "' ;\n";
  }

  $sparql = <<<EOT
SELECT DISTINCT ?animal ?acq ?slice ?file ?hemi ?avg ?std ?max ?min ?area ?sum ?scanType ?statType
FROM <http://iri.conte.rat-frag.org/>
WHERE {
  ?collection prov:hadMember ?agent ;
              prov:hadMember ?stat .
  ?activity prov:wasAssociatedWith ?agent .
  ?entity prov:wasGeneratedBy ?activity ;
EOT;
  $sparql .= "\n cuci:scanType '" . $scantype . "' ;\n";
  $sparql .= "\n cuci:hemisphere '" . $hemi . "' ;\n";
  $sparql .= " cuci:sliceNumber " . $slice . " ;\n";
  $sparql .= <<<EOT
         cuci:animalNumber ?animal ;
         cuci:acquisitionNumber ?acq ;
         cuci:sliceNumber ?slice ;
         cuci:fileName ?file ;
         cuci:hemisphere ?hemi ;
         cuci:intensityAvg ?avg ;
         cuci:intensityStdDev ?std ;
         cuci:intensityMax ?max ;
         cuci:intensityMin ?min ;
         cuci:pixelArea ?area ;
         cuci:pixelSum ?sum  .
  ?stat  cuci:scanType ?scanType ;
         cuci:statType 'within slice' ;
         cuci:statType ?statType ;
EOT;
  $sparql .= "\n cuci:scanType '" . $scantype . "' .\n";
  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " }";
  $sparql .= "\nORDER BY DESC(?slice) ASC(?animal) ASC(?acq) DESC(?avg) ";
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'scanType'}."\n";
  }

  //$namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces = "prefix cuci: <http://www.contecenter.org/ns/ConteUCI#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;

}

function get_rat_list() {
  $sparql = <<<EOT
SELECT DISTINCT ?animal ?acq
FROM <http://iri.conte.rat-frag.org/>
WHERE {
?col cuci:studyType "Structural" ;
     prov:hadMember ?entity .
?entity cuci:animalNumber ?animal ;
        cuci:acquisitionNumber ?acq .
}
ORDER BY ASC(?animal) ASC(?acq)
EOT;

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
  }

  return $myarray;
}

function get_rat_brain_regions() {
  $sparql = "SELECT DISTINCT ?region FROM <http://iri.conte.rat-frag.org/> WHERE { ?entity cuci:region ?region .  } ORDER BY ASC(?region)";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    //print $row{'region'}."\n";
  }

  return $myarray;
}

function fetch_rat_struct_stats_by_region($animal, $acq, $region) {
  $where = "?stat cuci:acquisitionNumber " . $acq . " ;\n";
  $where .= "     cuci:animalNumber " . $animal . " ;\n";
  $where .= "     cuci:region '" . $region . "' ;\n";
  $where .= <<<EOT
    cuci:region ?region ;
    cuci:sampleAvgOfIntensityMeans ?avg ;
    cuci:sampleMaxOfIntensityMeans ?max ;
    cuci:sampleMinOfIntensityMeans ?min ;
    cuci:sampleStdDevOfIntensityMeans ?std ;
    cuci:sample ?sample .
EOT;
  $sparql = "SELECT DISTINCT ?region ?avg ?std ?max ?min ?sample FROM <http://iri.conte.rat-frag.org/> WHERE { " . $where . " }";
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'period'}."\n";
  }

  return $myarray;

}

function get_mhr_data($period, $data_range, $std){

  $sparql = <<<EOT
SELECT ?mother ?fetus ?period ?label ?heartRate ?heartRateStd ?heartRateMax ?heartRateMin ?heartRateAvg ?overallStd ?overallMax ?overallMin ?hrList ?tpList
FROM <http://iri.conte.mhr.org/>
WHERE {
?col mhr:heartRateAvg ?heartRateAvg ;
    mhr:heartRateStd ?overallStd ;
    mhr:heartRateMax ?overallMax ;
    mhr:heartRateMin ?overallMin ;
    mhr:gestationPeriod ?period ;
    prov:hadMember ?entity .

?entity rdfs:label ?label ;
     ncit:heartRateAvg ?heartRate ;
     mhr:heartRateStd ?heartRateStd ;
     mhr:heartRateMax ?heartRateMax ;
     mhr:heartRateMin ?heartRateMin ;
     ncit:heartRate ?hrList ;
     ncit:timepoint ?tpList ;
     prov:wasAttributedTo ?agent1 .
?activity prov:wasAssociatedWith ?agent1 .
?agent1 ncit:subjectID ?mother .

?activity prov:qualifiedAssociation ?agent2 .
?agent2 prov:agent ?agent2id ;
     prov:role ncit:fetus .

?agent2id ncit:subjectID ?fetus  .

EOT;

  $sample_avg = 0;
  $sample_std = 0;
  $period_clause = '';
  if ($period !== '*') {
    $period_clause = ' ?period = "' . $period . '" ';
    $data = get_mhr_stats_by_period($period);
    $period_data = $data[0];
    $sample_avg = $period_data['heartRateAvg'];
    $sample_std = $period_data['heartRateStd'];
  }

  $range_clause = '';
  if ($data_range !== '*') {
    if ($data_range == 'higher') {
      $value = $sample_avg + ($sample_std * $std); 
      $range_clause .= " ?heartRate >= " . $value;
    }
    if ($data_range == 'lower') {
      $value = $sample_avg - ($sample_std * $std); 
      $range_clause .= " ?heartRate <= " . $value;
    }
  }

  $filter = "";
  if (strlen($period_clause) > 1 && strlen($range_clause) > 1){
    $filter .= " filter ( " . $period_clause . " && " . $range_clause . " ) ";
  } elseif (strlen($period_clause) > 1){
    $filter .= " filter ( " . $period_clause . " ) ";
  } elseif (strlen($range_clause) > 1){
    $filter .= " filter ( " . $range_clause . " ) ";
  }

  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " }";
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'mother'}."\n";
  }

  //return $myarray;
  $namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces .= "prefix mhr: <http://www.contecenter.org/ns/mhr#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;
}

function get_mhrfhr_data($period, $data_type, $std){

	if($period!='*'){
		$strPeriod = "'".$period."' as ?period";
		$strMhr = "mhr:gestationPeriod '".$period."' ;";
	}else{
		$strPeriod = "?period";
		$strMhr = "mhr:gestationPeriod ?period ;";
	}
	if($data_type!='*'){
		$strTask = "'".$data_type."' as ?task";
		$strCol = "?col rdfs:label '".$data_type."' ; ";
	}else{
		$strTask = "?task";
		$strCol = "?col rdfs:label ?task ; ";
	}
  $sparql = "SELECT ?mother ?fetus ".$strPeriod." ".$strTask." ?label ?MomHRAvg ?MomHRStd ?MomHRMax ?MomHRMin  
?fetusHRAvg ?fetusHRStd ?fetusHRMax ?fetusHRMin ?spoAvg ?spoStd ?moveAvg ?moveStd \n";
  
  if($period=='*'){
  	$sparql.="FROM <http://iri.conte.mhrnew.org/T0/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T1/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T2/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T3/>\n";
  }else{
  	$sparql.="FROM <http://iri.conte.mhrnew.org/".ucfirst($period)."/>\n";
  }  
  
  $sparql.="WHERE { \n".$strCol."\n".$strMhr."\n prov:hadMember ?entity . \n";
  	
	$sparql.=<<<EOT
?entity rdfs:label ?label ;
     ncit:heartRateAvg ?MomHRAvg ;
     mhr:heartRateStd ?MomHRStd ;
     mhr:heartRateMax ?MomHRMax ;
     mhr:heartRateMin ?MomHRMin ;
     prov:wasAttributedTo ?agent1 .

?fetus_entity ncit:heartRateAvg ?fetusHRAvg ;
        fhr:heartRateStd ?fetusHRStd ;
        fhr:heartRateMax ?fetusHRMax ;
        fhr:heartRateMin ?fetusHRMin ;
        prov:wasAttributedTo ?agent2 .

?spo_entity ncit:SPOMean ?spoAvg ;
     mhr:SPOStdDev ?spoStd ;
     prov:wasAttributedTo ?agent1 .

?move_entity mhr:movementMean ?moveAvg ;
        mhr:movementStdDev ?moveStd ;
        prov:wasAttributedTo ?agent2 .

?activity prov:wasAssociatedWith ?agent1 .

?agent1 ncit:subjectID ?mother .

?activity prov:wasAssociatedWith ?agent2 .

?agent2 ncit:subjectID ?fetus .

EOT;

//print "<pre>$sparql</pre>";
  $sample_avg = 0;
  $sample_std = 0;
  // $period_clause = '';
  // if ($period !== '*') {
    // $period_clause = ' ?period = "' . $period . '" ';
    // $data = get_mhr_stats_by_period($period);
    // $period_data = $data[0];
    // $sample_avg = $period_data['heartRateAvg'];
    // $sample_std = $period_data['heartRateStd'];
  // }
// 
  // $range_clause = '';
  // if ($data_type !== '*') {
  	// $range_clause .= " ?task = '" . $data_type . "'";
  // }
// 
  // $filter = "";
  // if (strlen($period_clause) > 1 && strlen($range_clause) > 1){
    // $filter .= " filter ( " . $period_clause . " && " . $range_clause . " ) ";
  // } elseif (strlen($period_clause) > 1){
    // $filter .= " filter ( " . $period_clause . " ) ";
  // } elseif (strlen($range_clause) > 1){
    // $filter .= " filter ( " . $range_clause . " ) ";
  // }

  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " }";
//print "<pre>$sparql</pre>";
$temp = str_replace("<", "&lt;", $sparql);
  $temp = str_replace("<", "&lt;", $temp);
//print "<pre>$temp</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'mother'}."\n";
  }

  //return $myarray;
  $namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces .= "prefix mhr: <http://www.contecenter.org/ns/mhr#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n";
  $namespaces .= "prefix fhr: <http://www.contecenter.org/ns/fhr#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;
}

function get_fhr_data($period, $task_type){
  $sparql = "SELECT ?mother ?fetus ?period ?task ?fetus_label ?fhrList ?fhrTpList \n";
  if($period=='*'){
  	$sparql.="FROM <http://iri.conte.mhrnew.org/T0/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T1/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T2/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T3/>\n";
  }else{
  	$sparql.="FROM <http://iri.conte.mhrnew.org/".ucfirst($period)."/>\n";
  }
  $sparql .= <<<EOT

WHERE {
?col rdfs:label ?task ; 
    mhr:gestationPeriod ?period ;
    prov:hadMember ?entity .

?entity  mhr:heartRateStd ?MomHRStd ;          
     prov:wasAttributedTo ?agent1 .

?fetus_entity rdfs:label ?fetus_label ;
        fhr:heartRateStd ?fetusHRStd ;        
		ncit:heartRate ?fhrList;
		ncit:timepoint ?fhrTpList;
        prov:wasAttributedTo ?agent2 .

?activity prov:wasAssociatedWith ?agent1 .

?agent1 ncit:subjectID ?mother .

?activity prov:wasAssociatedWith ?agent2 .

?agent2 ncit:subjectID ?fetus .

EOT;

//print "<pre>$sparql</pre>";
  $period_clause = '';
  if ($period !== '*') {
    $period_clause = ' ?period = "' . $period . '" ';
  }
  $range_clause = '';
  if ($task_type !== '*') {
        $range_clause .= " ?task = '" . $task_type . "'";
  }
  $filter = "";
  if (strlen($period_clause) > 1 && strlen($range_clause) > 1){
    $filter .= " filter ( " . $period_clause . " && " . $range_clause . " ) ";
  } elseif (strlen($period_clause) > 1){
    $filter .= " filter ( " . $period_clause . " ) ";
  } elseif (strlen($range_clause) > 1){
    $filter .= " filter ( " . $range_clause . " ) ";
  }

  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " }";
//print "<pre>$sparql</pre>";
$temp = str_replace("<", "&lt;", $sparql);
$temp = str_replace("<", "&lt;", $temp);
//print "<pre>$temp</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
  }

  $namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces .= "prefix mhr: <http://www.contecenter.org/ns/mhr#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n";
  $namespaces .= "prefix fhr: <http://www.contecenter.org/ns/fhr#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;
}

function get_mhr_stats(){
  $sparql = <<<EOT
SELECT DISTINCT ?heartRateAvg ?heartRateStd ?heartRateMax ?heartRateMin ?gestationPeriod ?label
FROM <http://iri.conte.mhr.org/>
WHERE {
?col mhr:heartRateAvg ?heartRateAvg ;
     mhr:heartRateStd ?heartRateStd ;
     mhr:heartRateMax ?heartRateMax ;
     mhr:heartRateMin ?heartRateMin ;
     mhr:gestationPeriod ?gestationPeriod ;
     rdfs:label ?label .
} order by ?gestationPeriod
EOT;
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  //var_dump($result);
  if( !$result ) {
  	print("error here 1");
  	print sparql_errno() . ": " . sparql_error(). "\n"; exit; 
  }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
//print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'period'}."\n";
  }

  return $myarray;
}

function get_mhrfhr_stats(){
  $sparql = <<<EOT
SELECT DISTINCT ?heartRateAvg ?heartRateStd ?heartRateMax ?heartRateMin ?gestationPeriod ?label
FROM <http://iri.conte.mhrnew.org/T0/>
FROM <http://iri.conte.mhrnew.org/T1/>
FROM <http://iri.conte.mhrnew.org/T2/>
FROM <http://iri.conte.mhrnew.org/T3/>
WHERE {
?col mhr:heartRateAvg ?heartRateAvg ;
     mhr:heartRateStd ?heartRateStd ;
     mhr:heartRateMax ?heartRateMax ;
     mhr:heartRateMin ?heartRateMin ;
     mhr:gestationPeriod ?gestationPeriod ;
     rdfs:label ?label .
}
Order by ?gestationPeriod
EOT;
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
//var_dump($result);
  if( !$result ) {
  	print("error here 1");
  	print sparql_errno() . ": " . sparql_error(). "\n"; exit; 
  }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    //print "Hello \n";
  }
//var_dump($myarray);
  return $myarray;
}

function get_conte_terms($namespace){
  $sparql = <<<EOT
SELECT DISTINCT ?term ?definition ?prefTerm ?url ?namespace
FROM <http://iri.conte.terms.org/>
WHERE {
  ?entity a prov:DataItem ;
          ccterms:term   ?term ;
          rdfs:label ?label ;
          ccterms:definition ?definition ;
          ccterms:namespace ?namespace ;
          ccterms:prefTerm   ?prefTerm ;
          ccterms:url ?url .
EOT;

  $range_clause = '';
  if ($namespace !== '*') {
        $range_clause .= " ?namespace = '" . $namespace . "'";
  }
  $filter = "";
  if (strlen($range_clause) > 1){
    $filter .= " filter ( " . $range_clause . " ) ";
  }
  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " } order by ?term";
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
//print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'term'}."\n";
  }

  return $myarray;
}

function get_scan_types () {
  $sparql = "SELECT DISTINCT ?scan FROM <http://iri.conte.rat-frag.org/> WHERE { ?s cuci:scanType ?scan .  } ORDER BY ?scan";
  //print "<pre>".$sparql."</pre>";

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

function get_rat_dti_slices () {
  $sparql = <<<EOT
SELECT DISTINCT  ?slice
FROM <http://iri.conte.rat-frag.org/>
WHERE 
{
  ?col cuci:studyType "DTI" ;
       prov:hadMember ?stat .
  ?stat cuci:sliceNumber ?slice ;
        cuci:statType 'within slice' .
}
ORDER BY ASC(?slice)
EOT;
  //print "<pre>".$sparql."</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    //print $row['slice']."<br />";
    array_push($myarray,$row);
  }

  return $myarray;
}

function fetch_rat_dti_stats_across_slices($scantype, $hemi){
  $where = "\n?s cuci:scanType '" . $scantype . "' ;\n";
  $where .= " cuci:hemisphere '" . $hemi . "' ;\n";
  $where .= <<<EOT
 rdfs:label ?label ;
 cuci:statType  'across slices' ;
 cuci:sampleAvgOfIntensityMeans ?avg ;
 cuci:sampleMaxOfIntensityMeans ?max ;
 cuci:sampleMinOfIntensityMeans ?min ;
 cuci:sampleStdDevOfIntensityMeans ?std .
EOT;
  $sparql = "SELECT DISTINCT  ?avg ?std ?max ?min ?label FROM <http://iri.conte.rat-frag.org/> WHERE { " . $where . " }";
  //print "<pre>".$sparql."</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    array_push($myarray,$row);
  }

  return $myarray;
}

function fetch_rat_dti_stats_within_slice($scantype, $hemi, $slice) {
  $where = "\n?stat cuci:scanType '" . $scantype . "' ;\n";
  $where .= " cuci:hemisphere '" . $hemi . "' ;\n";
  $where .= " cuci:sliceNumber " . $slice . " ;\n";
  $where .= <<<EOT
    cuci:hemisphere ?hemi ;
    cuci:scanType ?scanType ;        
    cuci:sliceNumber ?slice ;        
    cuci:statType ?stype ;
    cuci:statType "within slice" ;
    cuci:sampleAvgOfIntensityMeans ?avg ;
    cuci:sampleStdDevOfIntensityMeans ?std ;
    cuci:sampleMaxOfIntensityMeans ?max ;
    cuci:sampleMinOfIntensityMeans ?min .
EOT;
  $sparql = "SELECT DISTINCT ?scanType ?hemi ?slice ?stype ?avg ?std ?max ?min FROM <http://iri.conte.rat-frag.org/> WHERE { " . $where . " }";
  //print "<pre>".$sparql."</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    array_push($myarray,$row);
  }

  return $myarray;

}


function get_mhr_stats_by_period($period){
  $where = "?col mhr:gestationPeriod '" . $period . "' ;\n";
  $where .= <<<EOT
    mhr:heartRateAvg ?heartRateAvg ;
    mhr:heartRateStd ?heartRateStd ;
    mhr:heartRateMax ?heartRateMax ;
    mhr:heartRateMin ?heartRateMin ;
    mhr:gestationPeriod ?gestationPeriod ;
    rdfs:label ?label .
EOT;
  $sparql = "SELECT DISTINCT ?heartRateAvg ?heartRateStd ?heartRateMax ?heartRateMin ?gestationPeriod ?label FROM <http://iri.conte.mhr.org/> WHERE { " . $where . " }";
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'period'}."\n";
  }

  return $myarray;
}

function get_mhrfhr_stats_by_period($period){
  $where = "?col mhr:gestationPeriod '" . $period . "' ;\n";
  $where .= <<<EOT
    mhr:heartRateAvg ?heartRateAvg ;
    mhr:heartRateStd ?heartRateStd ;
    mhr:heartRateMax ?heartRateMax ;
    mhr:heartRateMin ?heartRateMin ;
    mhr:gestationPeriod ?gestationPeriod ;
    rdfs:label ?label .
EOT;
  $sparql = "SELECT DISTINCT ?heartRateAvg ?heartRateStd ?heartRateMax ?heartRateMin ?gestationPeriod ?label \n";
  $sparql.="FROM <http://iri.conte.mhrnew.org/T0/>\n";
  $sparql.="FROM <http://iri.conte.mhrnew.org/T1/>\n";
  $sparql.="FROM <http://iri.conte.mhrnew.org/T2/>\n";
  $sparql.="FROM <http://iri.conte.mhrnew.org/T3/>\n";
  $sparql.= "WHERE { " . $where . " }";
//print "<pre>$sparql</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );
  //print $numrows . "\n";

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
    #print $row{'period'}."\n";
  }

  return $myarray;
}

function get_spo_data($period, $task_type){
  $sparql = "SELECT ?mother ?fetus ?period ?task ?label ?spoTpList ?spoList \n";
  if($period=='*'){
  	$sparql.="FROM <http://iri.conte.mhrnew.org/T0/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T1/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T2/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T3/>\n";
  }else{
  	$sparql.="FROM <http://iri.conte.mhrnew.org/".ucfirst($period)."/>\n";
  }
  $sparql .= <<<EOT
  	
	WHERE {
		?col rdfs:label ?task ;
		mhr:heartRateAvg ?overallHRAvg ;		
		mhr:gestationPeriod ?period ;
		prov:hadMember ?entity .

	?entity rdfs:label ?label ;
	     mhr:heartRateStd ?MomHRStd ;	     
	     prov:wasAttributedTo ?agent1 .
	
	?fetus_entity fhr:heartRateStd ?fetusHRStd ;	        
	        prov:wasAttributedTo ?agent2 .
	
	?spo_entity ncit:SPOMean ?spoAvg ;
	     ncit:timepoint ?spoTpList;
	     mhr:SPO ?spoList;
	     prov:wasAttributedTo ?agent1 .	
	
	?activity prov:wasAssociatedWith ?agent1 .
	
	?agent1 ncit:subjectID ?mother .
	
	?activity prov:wasAssociatedWith ?agent2 .
	
	?agent2 ncit:subjectID ?fetus .
	
EOT;

//print "<pre>$sparql</pre>";
  $period_clause = '';
  if ($period !== '*') {
    $period_clause = ' ?period = "' . $period . '" ';
  }
  $range_clause = '';
  if ($task_type !== '*') {
        $range_clause .= " ?task = '" . $task_type . "'";
  }
  $filter = "";
  if (strlen($period_clause) > 1 && strlen($range_clause) > 1){
    $filter .= " filter ( " . $period_clause . " && " . $range_clause . " ) ";
  } elseif (strlen($period_clause) > 1){
    $filter .= " filter ( " . $period_clause . " ) ";
  } elseif (strlen($range_clause) > 1){
    $filter .= " filter ( " . $range_clause . " ) ";
  }

  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " }";
//print "<pre>$sparql</pre>";
$temp = str_replace("<", "&lt;", $sparql);
$temp = str_replace("<", "&lt;", $temp);
//print "<pre>$temp</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
  }

  $namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces .= "prefix mhr: <http://www.contecenter.org/ns/mhr#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n";
  $namespaces .= "prefix fhr: <http://www.contecenter.org/ns/fhr#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;	
}

function get_fmi_data($period, $task_type){
  $sparql = "SELECT ?mother ?fetus ?period ?task ?label ?fmiTpList ?fmiList \n";
  if($period=='*'){
  	$sparql.="FROM <http://iri.conte.mhrnew.org/T0/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T1/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T2/>\n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T3/>\n";
  }else{
  	$sparql.="FROM <http://iri.conte.mhrnew.org/".ucfirst($period)."/>\n";
  }
  $sparql .= <<<EOT
  	
	WHERE {
	?col rdfs:label ?task ; 
		mhr:heartRateAvg ?overallHRAvg ;
	    mhr:gestationPeriod ?period ;
	    prov:hadMember ?entity .
	
	?entity rdfs:label ?label ;
	     mhr:heartRateMax ?MomHRMax ;
	     prov:wasAttributedTo ?agent1 .
	
	?fetus_entity fhr:heartRateStd ?fetusHRStd ;	        
	        prov:wasAttributedTo ?agent2 .
	
	?spo_entity ncit:SPOMean ?spoAvg ;	     
	     prov:wasAttributedTo ?agent1 .
	
	?move_entity mhr:movementMean ?moveAvg ;	        
	        ncit:movement ?fmiList;
	        ncit:timepoint ?fmiTpList;
	        prov:wasAttributedTo ?agent2 .
	
	?activity prov:wasAssociatedWith ?agent1 .
	
	?agent1 ncit:subjectID ?mother .
	
	?activity prov:wasAssociatedWith ?agent2 .
	
	?agent2 ncit:subjectID ?fetus .	
EOT;

//print "<pre>$sparql</pre>";
  $period_clause = '';
  if ($period !== '*') {
    $period_clause = ' ?period = "' . $period . '" ';
  }
  $range_clause = '';
  if ($task_type !== '*') {
        $range_clause .= " ?task = '" . $task_type . "'";
  }
  $filter = "";
  if (strlen($period_clause) > 1 && strlen($range_clause) > 1){
    $filter .= " filter ( " . $period_clause . " && " . $range_clause . " ) ";
  } elseif (strlen($period_clause) > 1){
    $filter .= " filter ( " . $period_clause . " ) ";
  } elseif (strlen($range_clause) > 1){
    $filter .= " filter ( " . $range_clause . " ) ";
  }

  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " }";
//print "<pre>$sparql</pre>";
  $temp = str_replace("<", "&lt;", $sparql);
  $temp = str_replace("<", "&lt;", $temp);
//print "<pre>$temp</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
  }

  $namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces .= "prefix mhr: <http://www.contecenter.org/ns/mhr#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n";
  $namespaces .= "prefix fhr: <http://www.contecenter.org/ns/fhr#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  return $hash;	
}


function get_mhrtp_data($period, $task_type){
  $sparql = "SELECT ?mother ?fetus ?period ?task ?label ?hrList ?tpList \n";
  if($period=='*'){
  	$sparql.="FROM <http://iri.conte.mhrnew.org/T0/> \n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T1/> \n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T2/> \n";
	$sparql.="FROM <http://iri.conte.mhrnew.org/T3/> \n";
  }else{
  	$sparql.="FROM <http://iri.conte.mhrnew.org/".ucfirst($period)."/> \n";
  }  
  $sparql .= <<<EOT
		
	WHERE {
	?col rdfs:label ?task ;
	    mhr:gestationPeriod ?period ;
	    prov:hadMember ?entity .
	
	?entity rdfs:label ?label ;
	     ncit:heartRate ?hrList ;
	     ncit:timepoint ?tpList ;
	     prov:wasAttributedTo ?agent1 .

    ?fetus_entity fhr:heartRateStd ?fetusHRStd ;
	        prov:wasAttributedTo ?agent2 .
	
	?activity prov:wasAssociatedWith ?agent1 .
	
	?agent1 ncit:subjectID ?mother .
	
	?activity prov:wasAssociatedWith ?agent2 .
	
	?agent2 ncit:subjectID ?fetus .
EOT;

  $period_clause = '';
  if ($period !== '*') {
    $period_clause = ' ?period = "' . $period . '" ';
  }
  $range_clause = '';
  if ($task_type !== '*') {
        $range_clause .= " ?task = '" . $task_type . "'";
  }
  $filter = "";
  if (strlen($period_clause) > 1 && strlen($range_clause) > 1){
    $filter .= " filter ( " . $period_clause . " && " . $range_clause . " ) ";
  } elseif (strlen($period_clause) > 1){
    $filter .= " filter ( " . $period_clause . " ) ";
  } elseif (strlen($range_clause) > 1){
    $filter .= " filter ( " . $range_clause . " ) ";
  }

  if (strpos($filter, "?")){
    $sparql .= $filter;
  }
  $sparql .= " }";
  
  $temp = str_replace("<", "&lt;", $sparql);
  $temp = str_replace("<", "&lt;", $temp);
//print "<pre>$temp</pre>";

  $result = sparql_query( $sparql );
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

  $fields = sparql_field_array( $result );
  $numrows = sparql_num_rows( $result );

  $myarray = array();
  while( $row = sparql_fetch_array( $result ) ){
    $myarray[] = $row;
  }

  $namespaces = "prefix ncit: <http://ncitt.ncit.nih.gov/>\n";
  $namespaces .= "prefix mhr: <http://www.contecenter.org/ns/mhr#>\n";
  $namespaces .= "prefix prov: <http://www.w3.org/ns/prov#>\n";
  $namespaces .= "prefix fhr: <http://www.contecenter.org/ns/fhr#>\n\n";
  $query = $namespaces . $sparql;

  $hash['data'] = $myarray;
  $hash['query'] = $query;
  
  return $hash;
  
}

?>
