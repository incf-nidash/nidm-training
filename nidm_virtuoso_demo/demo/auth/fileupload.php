<!DOCTYPE html>
<html>
  <head>
    <title>Resumable.js with PHP Backend </title>
    <meta charset="utf-8" />
    <link rel='stylesheet' id='responsive-style-css'  href='../css/style.css?ver=1.9.3.4' type='text/css' media='all' />
    <link rel='stylesheet' id='responsive-media-queries-css'  href='../css/style.css?ver=1.9.3.4' type='text/css' media='all' />
    <link rel='stylesheet' id=''  href='/css/conte-style.css' type='text/css' media='all' />
    <link rel="stylesheet" type="text/css" href="/css/fileupload.css" />
      <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
      <script src="/js/resumable.js"></script>
      <script src="/js/fileupload.js"></script>
  </head>
  <body style="background-color: #00248f;" class="">
<div id="container">
  <?php include_once($_SERVER['DOCUMENT_ROOT']."/header.php"); ?>
<div id="wrapper" class="clearfix" >
    <div id="frame">
      <div class="resumable-error">
        Your browser, unfortunately, is not supported by Resumable.js. The library requires support for <a href="http://www.w3.org/TR/FileAPI/">the HTML5 File API</a> along with <a href="http://www.w3.org/TR/FileAPI/#normalization-of-params">file slicing</a>.
      </div>
      <div class="resumable-drop" ondragenter="jQuery(this).addClass('resumable-dragover');" ondragend="jQuery(this).removeClass('resumable-dragover');" ondrop="jQuery(this).removeClass('resumable-dragover');">
        Drop files here to upload or <a class="resumable-browse"><u>select from your computer</u></a>
      </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
      <div class="resumable-progress">
        <table>
          <tr>
            <td width="100%"><div class="progress-container"><div class="progress-bar"></div></div></td>
            <td class="progress-text" nowrap="nowrap"></td>
            <td class="progress-pause" nowrap="nowrap">
              <a href="#" onclick="r.upload(); return(false);" class="progress-resume-link"><img src="/images/resume.png" title="Resume upload" /></a>
              <a href="#" onclick="r.pause(); return(false);" class="progress-pause-link"><img src="/images/pause.png" title="Pause upload" /></a>
            </td>
          </tr>
        </table>
      </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
      <ul class="resumable-list"></ul>
    </div>
    </div>
    </div>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/footer.php"); ?>
  </body>
</html>
