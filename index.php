<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
date_default_timezone_set("Asia/Bangkok");
require_once 'simple_html_dom.php';
header("Content-Type: application/json; charset=UTF-8");

function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

$url = 'http://www.goldtraders.or.th/UpdatePriceList.aspx';
$scrape = file_get_contents_curl($url);
$doms = new simple_html_dom();
$doms->load($scrape);

$rowData = array();

       foreach($doms->find('table[id=DetailPlace_MainGridView]') as $table) {
            foreach($table->find('tr') as $row) {             
              $gdata = array();
              foreach($row->find('td') as $cell) {
                  
                $gdata[] = $cell->plaintext;
              }
              $rowData[] = $gdata;
          }             
       }

       $doms->clear();
       unset($doms);       
       
      // print_r($rowData);

        $myArr = array();
        $myArrx =  array();
        $n= 0;

        $rel = array(); $data = array();$astatus_ok   = array(); $astatus_fail = array();

        $astatus_ok = array("status"=>"success" , "titel"=>"goldupdate");
        $astatus_fail = array("status"=>"fail" , "titel"=>"goldupdate" ); 

        $decs = array(0=>"time",1=>"upd",2=>"blbuy",3=>"blsell",4=>"ombuy",5=>"omsell",6=>"gspot",7=>"usd",8=>"price");
    
        $aix = array();
       
        for ($i = 0; $i < count($rowData); $i++) {
        
          if($i>1){
            $v = 0;
        
             $ai =  array(   $decs[0] =>  $rowData[$i][0],
                             $decs[1] =>  $rowData[$i][1],
                             $decs[2] =>  $rowData[$i][2],
                             $decs[3] =>  $rowData[$i][3],
                             $decs[4] =>  $rowData[$i][4],
                             $decs[5] =>  $rowData[$i][5],
                             $decs[6] =>  $rowData[$i][6],
                             $decs[7] =>  $rowData[$i][7],
                             $decs[8] =>  $rowData[$i][8] );
                
              array_push($data,$ai);
            
             $v++;
            
          }
        }      
   

      if(empty($data)){ 
        array_push($rel,$astatus_fail);            
      }else{
        array_push($rel,$astatus_ok);
        array_push($rel,$data);

        $fp = fopen('gold.json', 'w');
        fwrite($fp, json_encode($rel));
        fclose($fp);
      }      
        echo json_encode($rel);    

?>
