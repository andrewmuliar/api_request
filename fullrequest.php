<?php
/* header('Access-Control-Allow-Origin: *'); 
 header("Access-Control-Allow-Credentials: true");
 header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
 header('Access-Control-Max-Age: 1000');
 header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');*/

//Recursive func for inception all data from array of object and hashing
 function redo($arrayka)
 {
  $ar = array();
  foreach($arrayka as $key => $value)
  {
   //If key has another level childs, he go recurse himself
   if(gettype($arrayka->$key) == 'object')
   {
	$ar[$key] = redo($arrayka->$key); //recurse
   }
   else // if key simple key no multydimension level
     $ar[$key] = hash('sha256',$value);
  }
  return $ar;
 }

 /*Start read array for hashing*/
function HashRequest($data)
{
 $new_data = array();
  for ($i = 0; $i < count($data); $i++)
  {
   $new_data[] = redo($data[$i]); // User recurse for every value -> key
  } 
 return $new_data;
}

//Transform array to string
function backToString($array)
{
 return json_encode(array_values($array));
}

 /*creating API request to get data from BX8, hashing and put in logs files*/
 function takeData()
 {
  $module = 'Lead';
  $api_username = 'RND@leomarkets.com';
  $api_password = '2Aj484$!2A';
  $recordsToShow = 15; //MAX 500 records
  $recordStart = 0; //PAGES by 500 records START FROM 0
  $url = 'http://affiliates.bx8.me/?MODULE='.$module.'&COMMAND=View&LIMIT[recordsToShow]='.$recordsToShow.'&LIMIT[recordStart]='.$recordStart.'&api_username='.$api_username.'&api_password='.$api_password;
  //MODULE=Customer&COMMAND=view&LIMIT[recordsToShow]=5&LIMIT[recordStart]=0

  $ch = curl_init($url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); //LEADS
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,20);
  curl_setopt($ch,CURLOPT_TIMEOUT,30);
  curl_setopt($ch,CURLOPT_FOLLOWLOCATION, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,["Content-Type:application/x-www-form-urlencoded; charset=utf-8"]);
  curl_setopt($ch,CURLOPT_HEADER, false);
  $exec = curl_exec($ch);
  $response = json_decode($exec);
  print_r($response);
  if($response->status == 'OK')
  {
   $filename = 'super_log_file.txt';
   $hashfile = 'super_hash_data.txt';
   $today = getdate();
   $date = 'Date: '.$today['hours'].':'.$today['wday'].':'.$today['minutes'].' '.$today['month'].' '.$today['wday'];
   $line = '========================================';
   $countArray = count($response->leads);
   $dataString = backToString($response->leads);
   $hashdata = backToString(HashRequest($response->leads));
   $log = $line.PHP_EOL.' Response Status: '.$response->status.PHP_EOL.' Records count: '.$countArray.PHP_EOL.$date.PHP_EOL.$dataString.PHP_EOL.$line;
   $hashlog = $line.PHP_EOL.' Response Status: '.$response->status.PHP_EOL.' Records count: '.$countArray.PHP_EOL.$date.PHP_EOL.$hashdata.PHP_EOL.$line;
   file_put_contents($filename, $log, FILE_APPEND);
   file_put_contents($hashfile, $hashlog, FILE_APPEND);
  }
  else
   echo 'error';

  curl_close($ch);
 }

 takeData();
 /*creating log files*/
 /* $request = 'Request type: '.$_SERVER['REQUEST_METHOD'];
  $post = file_get_contents('php://input',true);
  $json = json_decode($post);
  $filename = 'super_log_file.txt';
  $hasfile = 'super_hash_data.txt';
  $today = getdate();
  $type = "Data type: ".gettype($post);
  $date = 'Date: '.$today['hours'].':'.$today['wday'].':'.$today['minutes'].' '.$today['month'].' '.$today['wday'];
  $line = '==== New transaction ======';
  $hashdata = HashRequest($json);
  $hashdata = backToString($hashdata);
  echo $hashdata;
  $log = $line.PHP_EOL.$type.PHP_EOL.' '.$request.' '.PHP_EOL.$post.PHP_EOL.$date.PHP_EOL;
  $haslog = $line.PHP_EOL.$request.PHP_EOL.' '.$hashdata.' '.PHP_EOL.$date.PHP_EOL;
  file_put_contents($filename, $log, FILE_APPEND);
  file_put_contents($hasfile, $haslog, FILE_APPEND);
  echo ' Status = Ok';*/
?>