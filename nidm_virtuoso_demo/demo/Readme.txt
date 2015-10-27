How a query is added to demo:

1. add a button to query.php 
example:
<button type="button" id="mhr">Query maternal heart rate data</button>

2. add ajax query to js/conte-dojo.js
example:
on(dom.byId("mhr"), "click", function(){
  AJAX CODE HERE
  call a php script to get needed data (step 3)
});

3. write a php script to handle the ajax call above.
example: fetch-mhr-stats.php
this has a form and the submit button is handled by a javascript function that calls another php script via AJAX

4. add a method to retrieve data from db to lib/rdf-db.php
example:
function get_mhr_stats()


5. write ajax query called by #3 above in js/conte-js.js
example:
function getMHRData(){
  AJAX CODE HERE
}

6. write a php script to handle the ajax call in #5.
example: fetch-mhr-data.php


7. add a method to retrieve data from db to lib/rdf-db.php
example:
function get_mhr_data($period,$data_range)

