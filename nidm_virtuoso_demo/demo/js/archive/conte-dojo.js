require(["dojo/request/xhr", "dojo/dom", "dojo/dom-construct", "dojo/json", "dojo/on", "dojo/domReady!"],
function(xhr, dom, domConst, JSON, on){
  on(dom.byId("subjects"), "click", function(){
    clearData();
    dom.byId('update').innerHTML = "Fetching data...";
    xhr("./fetch-subjects.php",{
      query: {
        project: "4",
        fetch: "subjects"
      },
      handleAs: "html"
    }).then(function(data){
      dom.byId('update').innerHTML = '<code>'+data+'</code>';
    });
  });

  on(dom.byId("dtypes"), "click", function(){
    clearData();
    dom.byId('update').innerHTML = "Fetching data...";
    xhr("./fetch-query-fields.php",{
      query: {
        project: "4",
        fetch: "dtypes"
      },
      handleAs: "html"
    }).then(function(data){
      dom.byId('update').innerHTML = '<code>'+data+'</code>';
    });
  });

  on(dom.byId("regions"), "click", function(){
    clearData();
    dom.byId('update').innerHTML = "Fetching data...";
    xhr("./fetch-anatomical-regions.php",{
      query: {
        project: "4",
        fetch: "anatomical-regions"
      },
      handleAs: "html"
    }).then(function(data){
      dom.byId('update').innerHTML = '<code>'+data+'</code>';
    });
  });
});
