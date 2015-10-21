<?php
/*
created by lihuibin
date 2013-6-06
desc server side upload.php provide for resumable.js
*/
$REQUEST_METHOD=$_SERVER['REQUEST_METHOD'];
$uploads_dir="data";
if($REQUEST_METHOD == "GET")
{
    if(count($_GET)>0)
    {
        $chunkNumber = $_GET['resumableChunkNumber'];
        $chunkSize = $_GET['resumableChunkSize'];
        $totalSize = $_GET['resumableTotalSize'];
        $identifier = $_GET['resumableIdentifier'];
        $filename = iconv ( 'UTF-8', 'GB2312', $_GET ['resumableFilename'] );
        if(validateRequest($chunkNumber, $chunkSize, $totalSize, $identifier, $filename)=='valid')
        {
            $chunkFilename = getChunkFilename($chunkNumber, $identifier,$filename);
            {
                if(file_exists($chunkFilename)){
                    echo "found";
                } else {
                    header("HTTP/1.0 404 Not Found");
                    echo "not_found";
                }
            }
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
            echo "not_found";
        }}
}
function getChunkFilename ($chunkNumber, $identifier,$filename){
    global $uploads_dir;
    $temp_dir = $uploads_dir.'/'.$identifier;
    return  $temp_dir.'/'.$filename.'.part'.$chunkNumber;
}
function cleanIdentifier ($identifier){
    return $identifier;
    //return  preg_replace('/^0-9A-Za-z_-/', '', $identifier);
}
//$maxFileSize = 2*1024*1024*1024;
function validateRequest ($chunkNumber, $chunkSize, $totalSize, $identifier, $filename, $fileSize=''){
    // Clean up the identifier
    //$identifier = cleanIdentifier($identifier);
    // Check if the request is sane
    if ($chunkNumber==0 || $chunkSize==0 || $totalSize==0 || $identifier==0 || $filename=="") {
        return 'non_resumable_request';
    }
    $numberOfChunks = max(floor($totalSize/($chunkSize*1.0)), 1);
    if ($chunkNumber>$numberOfChunks) {
        return 'invalid_resumable_request1';
    }
    // Is the file too big?
//      if($maxFileSize && $totalSize>$maxFileSize) {
//          return 'invalid_resumable_request2';
//      }
    if($fileSize!="") {
        if($chunkNumber<$numberOfChunks && $fileSize!=$chunkSize) {
            // The chunk in the POST request isn't the correct size
            return 'invalid_resumable_request3';
        }
        if($numberOfChunks>1 && $chunkNumber==$numberOfChunks && $fileSize!=(($totalSize%$chunkSize)+$chunkSize)) {
            // The chunks in the POST is the last one, and the fil is not the correct size
            return 'invalid_resumable_request4';
        }
        if($numberOfChunks==1 && $fileSize!=$totalSize) {
            // The file is only a single chunk, and the data size does not fit
            return 'invalid_resumable_request5';
        }
    }
    return 'valid';
}
// loop through files and move the chunks to a temporarily created directory
if($REQUEST_METHOD == "POST"){
    if(count($_POST)>0)
    {
        $resumableFilename = iconv ( 'UTF-8', 'GB2312', $_POST ['resumableFilename'] );
        $resumableIdentifier=$_POST['resumableIdentifier'];
        $resumableChunkNumber=$_POST['resumableChunkNumber'];
        $resumableTotalSize=$_POST['resumableTotalSize'];
        $resumableChunkSize=$_POST['resumableChunkSize'];
        if (!empty($_FILES)) foreach ($_FILES as $file) {
            // check the error status
            if ($file['error'] != 0) {
                _log('error '.$file['error'].' in file '.$resumableFilename);
                continue;
            }
            // init the destination file (format <filename.ext>.part<#chunk>
            // the file is stored in a temporary directory
                                                                                                                                                                                                                                                              
            global $uploads_dir;
                                                                                                                                                                                                                                                              
            $temp_dir = $uploads_dir.'/'.$resumableIdentifier;
            $dest_file = $temp_dir.'/'.$resumableFilename.'.part'.$resumableChunkNumber;
            // create the temporary directory
            if (!is_dir($temp_dir)) {
                mkdir($temp_dir, 0777, true);
            }
            // move the temporary file
            if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
                _log('Error saving (move_uploaded_file) chunk '.$resumableChunkNumber.' for file '.$resumableFilename);
            } else {
                // check if all the parts present, and create the final destination file
                createFileFromChunks($temp_dir, $resumableFilename,$resumableChunkSize, $resumableTotalSize);
            }
        }
    }
}
/**
 *
 * Logging operation - to a file (upload_log.txt) and to the stdout
 * @param string $str - the logging string
 */
function _log($str) {
    // log to the output
    $log_str = date('d.m.Y').": {$str}\r\n";
    echo $log_str;
    // log to file
    if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
        fputs($fp, $log_str);
        fclose($fp);
    }
}
/**
 *
 * Delete a directory RECURSIVELY
 * @param string $dir - directory path
 * @link http://php.net/manual/en/function.rmdir.php
 */
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}
/**
 *
 * Check if all the parts exist, and
 * gather all the parts of the file together
 * @param string $dir - the temporary directory holding all the parts of the file
 * @param string $fileName - the original file name
 * @param string $chunkSize - each chunk size (in bytes)
 * @param string $totalSize - original file size (in bytes)
 */
function createFileFromChunks($temp_dir, $fileName, $chunkSize, $totalSize) {
    // count all the parts of this file
    $total_files = 0;
    foreach(scandir($temp_dir) as $file) {
        if (stripos($file, $fileName) !== false) {
            $total_files++;
        }
    }
    // check that all the parts are present
    // the size of the last part is between chunkSize and 2*$chunkSize
    if ($total_files * $chunkSize >=  ($totalSize - $chunkSize + 1)) {
        global $uploads_dir;
        // create the final destination file
        if (($fp = fopen($uploads_dir.'/'.$fileName, 'w')) !== false) {
            for ($i=1; $i<=$total_files; $i++) {
                fwrite($fp, file_get_contents($temp_dir.'/'.$fileName.'.part'.$i));
                //_log('writing chunk '.$i);
            }
            fclose($fp);
        } else {
            _log('cannot create the destination file');
            return false;
        }
        // rename the temporary directory (to avoid access from other
        // concurrent chunks uploads) and than delete it
        if (rename($temp_dir, $temp_dir.'_UNUSED')) {
            rrmdir($temp_dir.'_UNUSED');
        } else {
            rrmdir($temp_dir);
        }
    }
}
?>
