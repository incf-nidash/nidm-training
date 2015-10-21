function getData(){
  document.getElementById('data_table').innerHTML = '';
  var sel = document.getElementById('dtype_select');
  var dtype = sel.options[sel.selectedIndex].value;
  if (dtype == '*'){
    alert('Please select a data type');
    return false;
  }
  var tp_sel = document.getElementById('tp_select');
  var tp = tp_sel.options[tp_sel.selectedIndex].value;

  var http = new XMLHttpRequest();
  //var url = "./fetch-by-dtype.php";
  var url = "./fetch-by-dtype-tp.php";
  var params = "dtype="+dtype+"&tp="+tp;
  http.open("GET", url+"?"+params, true);
  http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
//      alert(http.responseText);
      document.getElementById('data_table').innerHTML = '<code>'+http.responseText+'</code>';
    }
  }
  http.send(null);
}

function getStatData(){
  document.getElementById('data_table').innerHTML = '';
  var sel = document.getElementById('region_select');
  var region = sel.options[sel.selectedIndex].value;
//  if (region == '*'){
//    alert('Please select a region');
//    return false;
//  }

  var http = new XMLHttpRequest();
  var url = "./fetch-by-anatomical-region.php";
  var params = "region="+region;
  http.open("GET", url+"?"+params, true);
  http.onreadystatechange = function() {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200) {
//      alert(http.responseText);
      document.getElementById('data_table').innerHTML = '<code>'+http.responseText+'</code>';
    }
  }
  http.send(null);
}

function clearData(){
  document.getElementById('data_table').innerHTML = '';
  document.getElementById('update').innerHTML = '';
}


