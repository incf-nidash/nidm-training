function getData(){
  document.getElementById('data_table').innerHTML = '';
  var root = location.protocol + '//' + location.host;

  var sel = document.getElementById('dtype_select');
  var dtype = sel.options[sel.selectedIndex].value;
  if (dtype == '*'){
    alert('Please select a data type');
    return false;
  }
  var tp_sel = document.getElementById('tp_select');
  var tp = tp_sel.options[tp_sel.selectedIndex].value;

  var http = new XMLHttpRequest();
  var url = root + "/fetch-by-dtype-tp.php";
  var params = "dtype="+dtype+"&tp="+tp;
  http.open("GET", url+"?"+params, true);
  document.getElementById('data_table').innerHTML = '<code><img src="/images/spiningwheel.gif" width="35px" /></code>';
  http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
//      alert(http.responseText);
      document.getElementById('data_table').innerHTML = '<code>'+http.responseText+'</code>';
    }
  }
  http.send(null);
}

function getFSStatData(){
  document.getElementById('data_table').innerHTML = '';
  var root = location.protocol + '//' + location.host;
  var sel = document.getElementById('region_select');
  var region = sel.options[sel.selectedIndex].value;
//  if (region == '*'){
//    alert('Please select a region');
//    return false;
//  }

  var http = new XMLHttpRequest();
  var url = root + "/fetch-by-anatomical-region.php";
  var params = "region="+region;
  http.open("GET", url+"?"+params, true);
  document.getElementById('data_table').innerHTML = '<code><img src="/images/spiningwheel.gif" width="35px" /></code>';
  http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
//      alert(http.responseText);
      document.getElementById('data_table').innerHTML = '<code>'+http.responseText+'</code>';
    }
  }
  http.send(null);
}

function getNIDMResults() {
  document.getElementById('data_table').innerHTML = '';
  var root = location.protocol + '//' + location.host;
  var sel = document.getElementById('contrast_select');
  var contrast = sel.options[sel.selectedIndex].value;

  var http = new XMLHttpRequest();
  var url = root + "/fetch-by-contrast.php";
  var params = "contrast="+contrast;
  http.open("GET", url+"?"+params, true);
  document.getElementById('data_table').innerHTML = '<code><img src="/images/spiningwheel.gif" width="35px" /></code>';
  http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
//      alert(http.responseText);
      document.getElementById('data_table').innerHTML = '<code>'+http.responseText+'</code>';
    }
  }
  http.send(null);

}

function getMHRData(){
  document.getElementById('data_table').innerHTML = '';
  var root = location.protocol + '//' + location.host;

  var sel1 = document.getElementById('mhr_data_select');
  var data_range = sel1.options[sel1.selectedIndex].value;
  var sel2 = document.getElementById('mhr_period_select');
  var period = sel2.options[sel2.selectedIndex].value;
  var std = document.getElementById('std').value;
  std = std.replace(/(^\s+|\s+$)/g, '');
  if (std == ''){
    alert('Please enter as standard deviation');
    return false;
  }
  if (data_range != '*' && period == '*'){
    alert('You need to select a gestation period to compare to sample statististics.');
    return false;
  }

  var chbox = document.getElementById("download_data");
  var download = 'no';
  if(chbox.checked){
    download = 'yes';
  }

  var http = new XMLHttpRequest();
  var url = root + '/fetch-mhr-data.php';
  var params = "range="+data_range+"&period="+period+"&std="+std+"&dl="+download;
  http.open("GET", url+"?"+params, true);
  document.getElementById('data_table').innerHTML = '<code><img src="/images/spiningwheel.gif" width="35px" /></code>';
  http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
//      alert(http.responseText);
      document.getElementById('data_table').innerHTML = '<code>'+http.responseText+'</code>';
    }
  }
  http.send(null);
}



function getRatStructStats() {
  document.getElementById('data_table').innerHTML = '';
  var root = location.protocol + '//' + location.host;

  var sel1 = document.getElementById('rat_struct_animal_select');
  var animal = sel1.options[sel1.selectedIndex].value;
  var sel2 = document.getElementById('rat_struct_region_select');
  var region = sel2.options[sel2.selectedIndex].value;
  if (animal == '*' || region == '*'){
    return false;
  }

  fields = animal.split("-");
  animal_num = fields[0];
  acq = fields[1];

  var http = new XMLHttpRequest();
  var url = root + '/fetch-rat-struct-region-stats.php';
  var params = "animal="+animal_num+"&acq="+acq+"&region="+region;
  http.open("GET", url+"?"+params, true);
  http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
//      alert(http.responseText);
      document.getElementById('stats').innerHTML = '<code>'+http.responseText+'</code>';
    }
  }
  http.send(null);
}

function getRatStructData() {
  document.getElementById('data_table').innerHTML = '';
  var root = location.protocol + '//' + location.host;

  var sel1 = document.getElementById('rat_struct_animal_select');
  var animal = sel1.options[sel1.selectedIndex].value;
  var sel2 = document.getElementById('rat_struct_region_select');
  var region = sel2.options[sel2.selectedIndex].value;
  var sel3 = document.getElementById('rat_struct_sample_comp_select');
  var sample_comp = sel3.options[sel3.selectedIndex].value;
  var chbox = document.getElementById("download_data");
  var download = 'no';
  if(chbox.checked){
    download = 'yes';
  }

  var std = document.getElementById('std').value;
  std = std.replace(/(^\s+|\s+$)/g, '');
  if (sample_comp != '*' && std == ''){
    alert('Please enter as standard deviation if you want to compare to sample statististics.');
    return false;
  }
  if (sample_comp != '*' && (animal == '*' || region == '*')){
    alert('You need to select an animal and brain region to compare to sample statististics.');
    return false;
  }

  fields = animal.split("-");
  animal_num = fields[0];
  acq = fields[1];

  var http = new XMLHttpRequest();
  var url = root + '/fetch-rat-struct-data.php';
  var params = "animal="+animal_num+"&acq="+acq+"&region="+region+"&comp="+sample_comp+"&std="+std+"&dl="+download;
  http.open("GET", url+"?"+params, true);
  document.getElementById('data_table').innerHTML = '<code><img src="/images/spiningwheel.gif" width="35px" /></code>';
  http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
      document.getElementById('data_table').innerHTML = '<code>'+http.responseText+'</code>';
    }
  }
  http.send(null);

}

function getRatDTIData() {
  document.getElementById('data_table').innerHTML = '';
  var root = location.protocol + '//' + location.host;

  var sel1 = document.getElementById('rat_dti_scantype_select');
  var scantype = sel1.options[sel1.selectedIndex].value;
  var sel2 = document.getElementById('rat_dti_hemi_select');
  var hemi = sel2.options[sel2.selectedIndex].value;
  var sel3 = document.getElementById('rat_dti_slice_select');
  var slice = sel3.options[sel3.selectedIndex].value;
  var sel4 = document.getElementById('rat_dti_sample_comp_select');
  var sample_comp = sel4.options[sel4.selectedIndex].value;

  var chbox = document.getElementById("download_data");
  var download = 'no';
  if(chbox.checked){
    download = 'yes';
  }

  var std = document.getElementById('std').value;
  std = std.replace(/(^\s+|\s+$)/g, '');
  if (sample_comp != '*' && std == ''){
    alert('Please enter as standard deviation if you want to compare to sample statististics.');
    return false;
  }
  if (sample_comp != '*' && (scantype == '*' || hemi == '*')){
    alert('You need to select a scan type and hemisphere to compare to sample statististics.');
    return false;
  }

  var http = new XMLHttpRequest();
  var url = "";
}
function toggleSPARQL () {
  var chbox = document.getElementById("toggle_switch");
  var vis = "none";
  if(chbox.checked){
    vis = "block";
  }
  document.getElementById('sparql_query').style.display = vis;
  return false;
}

function getDTIStats() {
  var sel1 = document.getElementById('rat_dti_scantype_select');
  var scantype = sel1.options[sel1.selectedIndex].value;
  var sel2 = document.getElementById('rat_dti_hemi_select');
  var hemi = sel2.options[sel2.selectedIndex].value;
  var sel3 = document.getElementById('rat_dti_slice_select');
  var slice = sel3.options[sel3.selectedIndex].value;

//alert("scantype="+scantype+"&hemi="+hemi+"&slice="+slice);
  var root = location.protocol + '//' + location.host;
  var http = new XMLHttpRequest();
  var url = '';
  var params = ''

  if (scantype != '*' && hemi != '*' && slice != '*'){
    //alert('3');
    url = root + "/fetch-rat-dti-stats-within-slice.php";
    params = "scantype="+scantype+"&hemi="+hemi+"&slice="+slice;
    //alert(params);

    http.open("GET", url+"?"+params, true);
    http.timeout = 90000;
    http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
      //alert(http.responseText);
      document.getElementById('stats').innerHTML = '<code>'+http.responseText+'</code>';
      } else {
        //alert(http.readyState + " " + http.status);
      }
    }
    http.send(null);
  } else if (scantype != '*' && hemi != '*') {
    //alert('2');
    url = root + "/fetch-rat-dti-stats-across-slices.php";
  }
}
function changeMHRSampleStats () {
  var sel = document.getElementById('mhr_period_select');
  var period = sel.options[sel.selectedIndex].value;

  if (period == '*'){
    return false;
  }

  var root = location.protocol + '//' + location.host;
  var http = new XMLHttpRequest();
  var url = root + "/fetch-mhr-stats-for-period.php";
  var params = "period="+period;
  http.open("GET", url+"?"+params, true);
  http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
//      alert(http.responseText);
      document.getElementById('stats').innerHTML = '<code>'+http.responseText+'</code>';
    }
  }
  http.send(null);

}

function drpPeriodChange(){
	changeMHRFHRSampleStats();
	changeCognitiveTask();
}

// not used (can delete later), changed to changeMHRSampleStats
function changeMHRpopulationStats () {
  document.getElementById("stats").innerHTML = document.getElementById("mhr_period_select").options[document.getElementById("mhr_period_select").selectedIndex].getAttribute("title");
  return false;
}

