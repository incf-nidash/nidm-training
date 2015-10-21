function renderFibers(filename){
  //alert(filename);

  var w = window.open('','','width=600, height=600');
  w.document.write('<html><head>');
  w.document.write('<script type="text/javascript" src="http://get.goXTK.com/xtk_edge.js"><\/script>');
  w.document.write('<script>');
  w.document.write('window.onload = function() {');
  w.document.write('var r = new X.renderer3D();');
  w.document.write('r.init();');
  w.document.write('r.camera.position = [0, 0, 150];');
  w.document.write('var fibers = new X.fibers();');
  w.document.write('fibers.file ="'+filename+'";');
  w.document.write('r.add(fibers);');
  w.document.write('r.render();');
  w.document.write('};');
  w.document.write('<\/script>');
  w.document.write('<style>html, body {background-color:#000;margin: 0;padding: 0;height: 100%;overflow: hidden !important;}</style>');
  w.document.write('</head>');
  w.document.write('<body></body></html>');
  w.document.close();
}

function renderVolume(filename){
  //alert(filename);

  var w = window.open('','','width=600, height=600');
  w.document.write('<html><head>');
  w.document.write('<script type="text/javascript" src="http://get.goXTK.com/xtk_edge.js"><\/script>');
  w.document.write('<script>');
  w.document.write('window.onload = function() {');
  w.document.write('var r = new X.renderer3D();');
  w.document.write('r.bgColor = [.62, .62, 1];');
  w.document.write('r.init();');
  w.document.write('var volume = new X.volume();');
  w.document.write('volume.file ="'+filename+'";');
  w.document.write('r.add(volume);');
  w.document.write('r.camera.position = [120, 80, 160];');
  w.document.write('r.render();');
  w.document.write('};');
  w.document.write('<\/script>');
  w.document.write('<style>html, body {background-color:#000;margin: 0;padding: 0;height: 100%;overflow: hidden !important;}</style>');
  w.document.write('</head>');
  w.document.write('<body></body></html>');
  w.document.close();
}
