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
<button class="text-button" type="button" id="dtypes">Human Connectomes</button>
<br />
<br />
<button class="text-button" type="button" id="regions">Human Brain Segmentations</button>
<br />
<br />
<button class="text-button" type="button" id="NIDMresults">NIDM Results</button>
<br />
<br />
<button class="text-button" type="button" id="mhr">Heart Rates</button>
<br />
<br />
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

