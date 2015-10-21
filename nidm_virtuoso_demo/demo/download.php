<?php
$file = $_REQUEST{'filename'};


//sanitize user parameters:
$path_parts = pathinfo($file);
//var_dump($path_parts);
$file_name  = $path_parts['basename'];

//validate parameter
//can add check for only certain file types/extensions to be downloadable
//only .pdf extensions in this case, but can have an array and check if the requested extension is in array
$file_ext = $path_parts['extension'];
if ($file_ext != 'csv'){
  print "Only CSV files are allowed to download";
  exit;
}

//allow only from "downloads" directories so a hacker cannot get for instance /etc/passwd file
$allowed_download_dir_path = $_SERVER['DOCUMENT_ROOT'] . "/temp/";
$file = $allowed_download_dir_path . $file_name;
//echo $file_name;


if (file_exists($file)) {
	header('Content-Type: application/octet-stream');
	header('Content-Length: ' . filesize($file));
  header('Content-Disposition: attachment; filename="'.$file_name.'"');

  //allow for large files to download, readfile can choke on big files
  //can also code for resumable downloads
  set_time_limit(0);
  $fh = fopen($file,"rb");
  while(!feof($fh)) {
    print(fread($fh, 1024*8));
    ob_flush();
    flush();
  }
  fclose($fh);
}

//original code:
/*
if (file_exists($file)) {
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: ' . filesize($file));
	readfile($file);
}
*/
?>
