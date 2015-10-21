function renderFibers(filename){
  //alert(filename);
  //w.document.write('<script type="text/javascript" src="xtk/xtk_edge.js"><\/script>');

  var w = window.open('','','width=600, height=600');
  w.document.write('<html><head>');
  w.document.write('<script type="text/javascript" src="http://get.goXTK.com/xtk_edge.js"><\/script>');
  //w.document.write('<script type="text/javascript" src="https://cdn.rawgit.com/xtk/get/gh-pages/xtk_edge.js"><\/script>');
  
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

  var w = window.open('','','width=900, height=800');
  w.document.write('<html><head>');
  w.document.write('<script type="text/javascript" src="http://get.goXTK.com/xtk_edge.js"><\/script>');
  w.document.write('<script type="text/javascript" src="http://get.goXTK.com/xtk_xdat.gui.js"></script>');
  //w.document.write('<script type="text/javascript" src="https://cdn.rawgit.com/xtk/get/gh-pages/xtk_edge.js"><\/script>');
  //w.document.write('<script type="text/javascript" src="https://cdn.rawgit.com/xtk/get/gh-pages/xtk_xdat.gui.js"></script>');
  w.document.write('<script>');
  w.document.write('window.onload = function() {');
  w.document.write('var r = new X.renderer3D();');
  w.document.write('r.bgColor = [.62, .62, 1];');
  w.document.write('r.init();');
  w.document.write('var volume = new X.volume();');
  w.document.write('volume.file ="'+filename+'";');
  w.document.write('r.add(volume);');
  w.document.write('r.onShowtime = function() {');
  w.document.write('var gui = new dat.GUI();');
  w.document.write('var volumegui = gui.addFolder("Volume");');
  w.document.write('var vrController = volumegui.add(volume, "volumeRendering");');
  w.document.write('var minColorController = volumegui.addColor(volume, "minColor");');
  w.document.write('var maxColorController = volumegui.addColor(volume, "maxColor");');
  w.document.write('var opacityController = volumegui.add(volume, "opacity", 0, 1).listen();');
  w.document.write('var lowerThresholdController = volumegui.add(volume, "lowerThreshold", volume.min, volume.max);');
  w.document.write('var upperThresholdController = volumegui.add(volume, "upperThreshold", volume.min, volume.max);');
  w.document.write('var lowerWindowController = volumegui.add(volume, "windowLow", volume.min, volume.max);');
  w.document.write('var upperWindowController = volumegui.add(volume, "windowHigh", volume.min, volume.max);');
  w.document.write('var sliceXController = volumegui.add(volume, "indexX", 0, volume.range[0] - 1);');
  w.document.write('var sliceYController = volumegui.add(volume, "indexY", 0, volume.range[1] - 1);');
  w.document.write('var sliceZController = volumegui.add(volume, "indexZ", 0, volume.range[2] - 1);');
  w.document.write('volumegui.open();');
  w.document.write('};');
  w.document.write('r.camera.position = [120, 80, 160];');
  w.document.write('r.render();');
  w.document.write('};');
  w.document.write('<\/script>');
  w.document.write('<style>html, body {background-color:#000;margin: 0;padding: 0;height: 100%;overflow: hidden !important;}</style>');
  w.document.write('</head>');
  w.document.write('<body></body></html>');
  w.document.close();
}
