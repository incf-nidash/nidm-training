<!DOCTYPE HTML>
<html>
<head>
<?php include_once("head.php"); ?>
</head>
<body style="background-color: #00248f;" class="">

<div id="container">
  <?php include_once("header.php"); ?>

<div id="wrapper" class="clearfix" >

        <div class="sidebar">
          <h3>Queries</h3>
          <div class="sidebar_item">
<button class="text-button" type="button" id="subjects" style="display: none">Show all subjects</button>
<button class="text-button" type="button" id="dtypes" style="display: none">Query connectome data</button>
<button class="text-button" type="button" id="regions" style="display: none">Query segmentation data</button>
<button class="text-button" type="button" id="mhr" style="display: none">Query maternal heart rate data</button>
<button class="text-button" type="button" id="rat_dti" style="display: none">Query rat brain DTI data</button>
<button class="text-button" type="button" id="rat_struct" style="display: none">Query rat brain structural data</button>
<button class="text-button" type="button" id="mhrfhr">Query mhr fhr data</button>

          </div>
          <div class="sidebar_base"></div>
        </div>
      <div class="content">
        <h1>Data from query</h1>
        <div class="content_item">
          <div id="stats"></div>
          <div id="update"></div>
          <div id="data_table"></div>
        </div>
      </div>

</div><!-- end of #wrapper -->
</div><!-- end of #container -->

<?php include_once("footer.php"); ?>
</body>
</html>
