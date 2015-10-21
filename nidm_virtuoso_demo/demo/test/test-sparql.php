<?php
require_once( "lib/sparqllib.php" );
 
$db = sparql_connect( "http://192.168.33.10:8890/sparql/" );
//$db = sparql_connect( "http://localhost:8080/sparql/" );
if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
sparql_ns( "rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
sparql_ns( "rdfs","http://www.w3.org/2000/01/rdf-schema#" );
sparql_ns("crypto", "http://id.loc.gov/vocabulary/preservation/cryptographicHashFunctions/");
sparql_ns("dcterms", "http://purl.org/dc/terms/");
sparql_ns("foaf", "http://xmlns.com/foaf/0.1/");
sparql_ns("fs", "http://www.incf.org/ns/nidash/fs#");
sparql_ns("nidm", "http://www.incf.org/ns/nidash/nidm#");
sparql_ns("nif", "http://neurolex.org/wiki/");
sparql_ns("niiri", "http://iri.nidash.org/");
sparql_ns("obo", "http://purl.obolibrary.org/obo/");
sparql_ns("prov", "http://www.w3.org/ns/prov#");
sparql_ns("xsd", "http://www.w3.org/2001/XMLSchema#");
sparql_ns("prov", "http://www.w3.org/ns/prov#");
sparql_ns("cml", "http://www.connectomics.org/cff-2/");
//sparql_ns("prov-1", "http://www.w3.org/ns/prov-o/");
//sparql_ns("xml", "http://www.w3.org/XML/1998/namespace");
/*
prefix crypto: <http://id.loc.gov/vocabulary/preservation/cryptographicHashFunctions/>
prefix dcterms: <http://purl.org/dc/terms/>
prefix foaf: <http://xmlns.com/foaf/0.1/>
prefix fs: <http://www.incf.org/ns/nidash/fs#>
prefix nidm: <http://www.incf.org/ns/nidash/nidm#>
prefix nif: <http://neurolex.org/wiki/>
prefix niiri: <http://iri.nidash.org/>
prefix obo: <http://purl.obolibrary.org/obo/>
prefix prov: <http://www.w3.org/ns/prov#> 

SELECT * WHERE
{
 ?id a prov:Entity;
 fs:ThickAvg ?thickness;
 nidm:anatomicalAnnotation ?annot.
}
*/
 
$filter = "FILTER (?thickness > 2.5)";
//FILTER (?thickness > \"2.5\"^^xsd:float)
$sparql = "SELECT * WHERE { ?id a prov:Entity; nidm:anatomicalAnnotation ?annot; fs:ThickAvg ?thickness.".$filter." }";
#$sparql = "SELECT * WHERE { ?id a prov:Entity; nidm:anatomicalAnnotation ?annot; fs:ThickAvg ?thickness . }";
//$sparql = "SELECT * WHERE {  ?s ?p ?o }";
//$sparql = "SELECT distinct ?o from <http://iri.conte.cff.org/> WHERE {  ?s cml:dtype ?o }";
print "SPARQL Query:".$sparql."<br />";
$result = sparql_query( $sparql ); 
if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
 
$fields = sparql_field_array( $result );
 
print "<p>Number of rows: ".sparql_num_rows( $result )." results.</p>";
print "<table border='1px'>";
print "<tr>";
foreach( $fields as $field )
{
  print "<th>$field</th>";
}
print "</tr>";
while( $row = sparql_fetch_array( $result ) )
{
  print "<tr>";
  foreach( $fields as $field )
  {
    print "<td>$row[$field]</td>";
  }
  print "</tr>";
}
print "</table>";

?>
