<?php
  $db = $_SERVER['DOCUMENT_ROOT'] . '/lib/rdf-db.php';
  require_once $db;

  function get_conte_terms_hash($links = 'no', $namespace){
    $data = get_conte_terms($namespace);
    $numrows = count($data);

    $hash = array();
    for($ri = 0; $ri < $numrows; $ri++) {
      $row = $data[$ri];
      $term = $row['term'];

      $link = trim($row['prefTerm']);
      if (trim($row['url']) != '' && $links == 'yes'){
        $link = "<u><a href='".trim($row['url'])."' target='_blank'>".trim($row['prefTerm'])."</a></u>";
      }
      $desc = $link.": ".$row['definition'];
      $hash[$term] = $desc;
      //echo $row['term']."<br />".$desc ."<br /><br />";
    }

    return $hash;
  }

  function save_rat_dti_csv_file ($filename, $data){
    $file = $_SERVER['DOCUMENT_ROOT'] . "/temp/" . $filename;

    $content = "Animal number,Acquisition number,Scan type,Hemisphere,Slice,Average intensity,Standard deviation,Max,Min,Pixel area,Pixel sum,ROI file\n";
    $numrows = sizeof($data);
    for($ri = 0; $ri < $numrows; $ri++) {
      $row = $data[$ri];
      $content .= $row['animal'].",".$row['acq'].",".$row['scanType'].",".$row['hemi'].",".$row['slice'].",".round($row['avg'],4).",";
      $content .= round($row['std'],4).",".round($row['max'],4).",".round($row['min'],4).",".$row['area'].",".$row['sum'].",\"".$row['file']."\"\n";
    }

    file_put_contents($file, $content);
    $link = "Click <a href='/download.php?filename=".$filename."' download='".$filename."'>here</a> to download CSV data.";
    return $link;
  }

  function save_rat_struct_csv_file ($filename, $data){
    $file = $_SERVER['DOCUMENT_ROOT'] . "/temp/" . $filename;

    $content = "Animal number,Acquisition number,Region,Slice,Average intensity,Standard deviation,Max,Min,Pixel area,Pixel sum,ROI file\n";
    $numrows = sizeof($data);
    for($ri = 0; $ri < $numrows; $ri++) {
      $row = $data[$ri];
      $content .= $row['animal'].",".$row['acq'].",".$row['region'].",".$row['slice'].",".round($row['avg'],4).",";
      $content .= round($row['std'],4).",".round($row['max'],4).",".round($row['min'],4).",".$row['area'].",".$row['sum'].",\"".$row['file']."\"\n";
    }

    file_put_contents($file, $content);
    $link = "Click <a href='/download.php?filename=".$filename."'>here</a> to download CSV data.";
    return $link;
  }

  function save_mhr_data_csv_file($filename,$data) {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/temp/" . $filename;

    $content = "Mother,Fetus,Gestation period,Heart rate average,Standard dev,Max,Min";
    $tp_keys = array();
    for ($i=2;$i<=180;$i++) {
      $k = 'A00.mhr.v' . $i;
      $tp_keys[$k] = 1;
      $content .= ",$k";
    }
    $content .= "\n";
//print "tp_keys: " . sizeof($tp_keys)."<br />";
//var_dump($tp_keys);

    $numrows = sizeof($data);
    for($ri = 0; $ri < $numrows; $ri++) {
      $row = $data[$ri];
      $hr_list = $row['hrList'];
      $hr_list = ltrim($hr_list, "[");
      $hr_list = rtrim($hr_list, "]");
//print "Heart rate: " . $hr_list . "<br />";
//print "timepoints: " . $tp_list . "<br />";

      $tp_list = $row['tpList'];
      $tp_list = ltrim($tp_list, "[");
      $tp_list = rtrim($tp_list, "]");
      $hr_array = explode( ',', $hr_list );
      $tp_array = explode( ',', $tp_list );
//var_dump($hr_array);
//var_dump($tp_array);
//print "tp_array: " . sizeof($tp_array)."<br />";
//print "hr_array: " . sizeof($hr_array)."<br />";

      $hash = array();
      foreach ($tp_keys as $tp => $val) {
        $arr = split('v',$tp);
        // mhr file timepoints starts at index 2 so account for that 
        $tp_index = $arr[1]; 
        $hr_index = $tp_index - 2 ; 
//print "tp:$tp  tp_index:$tp_index hr_index:$hr_index tp:$tp_array[$hr_index] hr:$hr_array[$hr_index]";
//print "<br />";

          $hr = '';
          if (array_key_exists($hr_index, $hr_array)){
            $hr = $hr_array[$hr_index];
          }
          $hash[$tp] = trim($hr);
      }
//print "hash: " . sizeof($hash)."<br />";
//var_dump($hash);

      $content .= $row['mother'].",".$row['fetus'].",".$row['period'].",";
      $content .= round($row['heartRate'],2).",".round($row['heartRateStd'],2).",".round($row['heartRateMax'],2).",".round($row['heartRateMin'],2);
      $row = '';
      for ($i=2;$i<=180;$i++) {
        $key = 'A00.mhr.v' . $i;
        if (array_key_exists($key, $hash)){
          $row .= ",".$hash[$key];
        } else {
          $row .= ",";
        }
      }
      $content .= "$row\n";
    }

    file_put_contents($file, $content);
    $link = "Click <a href='/download.php?filename=".$filename."' download='".$filename."'>here</a> to download CSV data.";
    return $link;

  }

  
  function save_mhr_fhr_data_csv_file($filename,$data,$mhr_data,$fhr_data, $spo_data, $fmi_data) {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/temp/" . $filename;

    $content = "Mother,Fetus,Gestation,Task,MHRAvg,MHRStd, MHRMax,MHRMin, FHRAvg, FHRStd, FHRMax, FHRMin, SPO2Avg, SPO2Std, FMIAvg, FMIStd \n";
    $tp_keys = array();

    $numrows = sizeof($data);
    for($ri = 0; $ri < $numrows; $ri++) {
		$row = $data[$ri];
		$content .= $row['mother'].",".$row['fetus'].",".$row['period'].",".substr($row['task'],3).",";
		$content .= round($row['MomHRAvg'],2).",".round($row['MomHRStd'],2).",".round($row['MomHRMax'],2).",".round($row['MomHRMin'],2).",";
		$content .= round($row['fetusHRAvg'],2).",".round($row['fetusHRStd'],2).",".round($row['fetusHRMax'],2).",".round($row['fetusHRMin'],2).",";
		$content .= round($row['spoAvg'],2).",".round($row['spoStd'],2).",".round($row['moveAvg'],2).",".round($row['moveStd'],2);
		$row = '';
		$content .= "$row\n";
    }
	
	$mhrRows = sizeof($mhr_data);
	$content .= "\n";
	$content .= "Mother,Fetus,Gestation and Task,TimePoint(sec), MHR, FHR, FMI, SPO2 \n";
	for ($ri = 0; $ri < $mhrRows; $ri++){
		$row = $mhr_data[$ri];
		$hr_list = $row['hrList'];
		$hr_list = ltrim($hr_list, "[");
		$hr_list = rtrim($hr_list, "]");
//print "Heart rate: " . $hr_list . "<br />";
//print "timepoints: " . $tp_list . "<br />";
		$tp_list = $row['tpList'];
		$tp_list = ltrim($tp_list, "[");
		$tp_list = rtrim($tp_list, "]");

		$hr_array = array();
		$tp_array = array();    
		$hr_array = explode( ',', $hr_list );
		$tp_array = explode( ',', $tp_list );
		
		//get fhr list accordingly		
		$fhr_hash = array();
		$fhr_array = array();
		$fhrtp_array = array();
		for($i=0; $i<sizeof($fhr_data);$i++){
			$fhr_row = $fhr_data[$i];
			if($row['mother']==$fhr_row['mother'] && $row['task']==$fhr_row['task']){
				$fhr_list = $fhr_row['fhrList'];
				$fhr_list = ltrim($fhr_list, "[");
				$fhr_list = rtrim($fhr_list, "]");
				$fhrtp_list = $fhr_row['fhrTpList'];
				$fhrtp_list = ltrim($fhrtp_list, "[");
				$fhrtp_list = rtrim($fhrtp_list, "]");
											
				$fhr_array = explode( ',', $fhr_list );
				$fhrtp_array = explode( ',', $fhrtp_list );
				foreach($fhrtp_array as $t=>$val){
					$fhr = '';
					if(array_key_exists($t,$fhr_array)){
						$fhr=$fhr_array[$t];
					}
					$fhr_hash[$val] = $fhr;
				}
				break;
			}
		}
		
		//get spo list accordingly		
		$spo_hash = array();
		$spo_array = array();
		$spotp_array = array();
		for($i=0; $i<sizeof($spo_data);$i++){
			$spo_row = $spo_data[$i];
			if($row['mother']==$spo_row['mother'] && $row['task']==$spo_row['task']){
				$spo_list = $spo_row['spoList'];
				$spo_list = ltrim($spo_list, "[");
				$spo_list = rtrim($spo_list, "]");
				$spotp_list = $spo_row['spoTpList'];
				$spotp_list = ltrim($spotp_list, "[");
				$spotp_list = rtrim($spotp_list, "]");

				$spo_array = explode( ',', $spo_list );
				$spotp_array = explode( ',', $spotp_list );
				foreach($spotp_array as $t=>$val){
					$spo = '';
					if(array_key_exists($t,$spo_array)){
						$spo=$spo_array[$t];
					}
					$spo_hash[$val] = $spo;
				}
				break;
			}
		}
		
		//get fmi list accordingly		
		$fmi_hash = array();
		$fmi_array = array();
		$fmitp_array = array();
		for($i=0; $i<sizeof($fmi_data);$i++){
			$fmi_row = $fmi_data[$i];
			if($row['mother']==$fmi_row['mother'] && $row['task']==$fmi_row['task']){
				$fmi_list = $fmi_row['fmiList'];
				$fmi_list = ltrim($fmi_list, "[");
				$fmi_list = rtrim($fmi_list, "]");
				$fmitp_list = $fmi_row['fmiTpList'];
				$fmitp_list = ltrim($fmitp_list, "[");
				$fmitp_list = rtrim($fmitp_list, "]");

				$fmi_array = explode( ',', $fmi_list );
				$fmitp_array = explode( ',', $fmitp_list );
				foreach($fmitp_array as $t=>$val){
					$fmi = '';
					if(array_key_exists($t,$fmi_array)){
						$fmi=$fmi_array[$t];
					}
					$fmi_hash[$val] = $fmi;
				}
				break;
			}
		}

// print "MotherID: " .$row['mother']."<br />";
// print "tp_array: " . sizeof($tp_array)."<br />";
// print "hr_array: " . sizeof($hr_array)."<br />";
// print "fhrtp_array: " . sizeof($fhrtp_array)."<br />";
// print "fhr_array: " . sizeof($fhr_array)."<br />";
// print "spotp_array: " . sizeof($spotp_array)."<br />";
// print "spo_array: " . sizeof($spo_array)."<br />";
// print "fmitp_array: " . sizeof($fmitp_array)."<br />";
// print "fmi_array: " . sizeof($fmi_array)."<br />";


		for($idx=0; $idx<count($hr_array); $idx++){
			$content.=$row['mother'].",".$row['fetus'].",".$row['task'].",";
			$content.=$tp_array[$idx].",".$hr_array[$idx].",";
			$content.=$fhr_hash[$tp_array[$idx]].",";			
			$content.=$fmi_hash[$tp_array[$idx]].",";
			$content.=$spo_hash[$tp_array[$idx]]."\n";			
		}	
	} 

    file_put_contents($file, $content);
    $link = "Click <a href='/download.php?filename=".$filename."' download='".$filename."'>here</a> to download CSV data.";
    return $link;

  }


function save_term_data_csv_file($filename, $termNames, $namespace) {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/temp/" . $filename;
    
	$data = get_conte_terms($namespace);
print $data;
    $numrows = count($data);
    
	$content = "Term, PrefTerm, Definition, URL \n";
	
    $numrows = sizeof($data);
//print "\n term number rows:".$numrows."\n";
    for($ri = 0; $ri < $numrows; $ri++) {
		$row = $data[$ri];
//print "\n row:".$row['term']."\n";
//print "\n row:".$row['namespace']."\n";
		
		if(in_array($row['term'], $termNames)){
			if($row['term']=="T3"){
				//print "inside:".$row['term']."\n";
				//print "ns:".$row['namespace']."\n";
				if($row['namespace']!=$namespace){
					continue;
				}
			}
			$strDef = $row['definition'];
			if(strpos($strDef, ",")){
				$strDef = "\"".$strDef."\"";
			}
			$content.=$row['term'].",".$row['prefTerm'].",".str_replace("\n", " ", $strDef).",".$row['url'];
			$row = '';
			$content .= "$row\n";
		}		
    }
	
    file_put_contents($file, $content);
    $link = "Click <a href='/download.php?filename=".$filename."' download='".$filename."'>here</a> to download dictionary data.";
    return $link;

  }
  
?>
