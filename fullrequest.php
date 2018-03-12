<?php
//Headers for open CORS 
/* header('Access-Control-Allow-Origin: *'); 
 header("Access-Control-Allow-Credentials: true");
 header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
 header('Access-Control-Max-Age: 1000');
 header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');*/

 /*Start read array for hashing*/
function HashRequest($data) //return array
{
 $new_data = array();
 $mini_array = array();
 $currency = '';
 $timestamp;
 $value_balance;
 //Array of keys what we need to take
 $key_list = array('email',
				   'phone',	
				   'gender', //FORMAT MUST BE ONE LETTER
				   //'birthday', -- format??
				   'lastName',
				   'firstName',
				   'city',
				   'state',
				   'postCode',
				   'accountBalance',
				   'country',
				   'regTime',
				   'currency');
foreach($data as $mini_data)
{
 foreach($mini_data as $key => $value)
 {
  if(in_array($key, $key_list)) //If our key match with needed keys 
  {
   if($key == 'currency') //We need pure currency not hashed and make uppercase
    {
     $currency = strtoupper($value);
	}
   else if($key == 'regTime') // We need timestamp 
	{
	 $date = date_create($value);
	 $timestamp = date_timestamp_get($date);
	}
   else if($key == 'accountBalance')
   {
    $value_balance = $value; //value = accountBalance ??
   }
   else
   {
    switch ($key) //Change keys name for FB
	{
	 case 'email':
	  $mini_array['email'] = hash('sha256', $value);
	 break;

	 case 'phone':
	  $mini_array['phone'] = hash('sha256', $value);
	 break;

	 case 'gender': //Only one letter must be in
	  if($value == 'Male')
	   $mini_array['gen'] = hash('sha256', 'M');
	  else //Female
 	   $mini_array['gen'] = hash('sha256', 'F');
	 break;

	 case 'lastName':
	  $mini_array['ln'] = hash('sha256', $value);
	 break;

	 case 'firstName':
	  $mini_array['fn'] = hash('sha256', $value);
	 break;

	 case 'city':
	  $mini_array['ct'] = hash('sha256', $value);
	 break;

	 case 'state':
	  $mini_array['st'] = hash('sha256', $value);
	 break;

	 case 'postCode':
	  $mini_array['zip'] = hash('sha256', $value);
	 break;

	 case 'country':
	  $mini_array['country'] = hash('sha256', $value);
	 break;
	}
   }
  }
 }
 //Creating format for FB upload
 $new_data[]['match_keys'] = $mini_array;
 $last = count($new_data)-1; //getting last item of array for inception data
 $new_data[$last]['value']      = $value_balance;
 $new_data[$last]['currency']   = $currency; //adding keys to this item
 $new_data[$last]['event_name'] = 'AddPaymentInfo'; //Type of transaction
 $new_data[$last]['event_time'] = $timestamp; //Adding time of transaction
}
 return $new_data; //array
}

//Transform array to string
function backToString($array)
{
 return json_encode(array_values($array));
}
//Getting date from last array item
function getLastDate($array)
{
 $count = count($array);
 return date('Y_m_d',$array[$count-1]['event_time']);
}

//Record last timestamp to file for next time filtering
function recordDate($date)
{
 $file = fopen("time_records.txt", "w");
 fwrite($file, $date); //reWriteing date ti file
 fclose($file);
}
//Reading last regrTime from file
function read_from_file()
{
 $file = fopen("time_records.txt", "r");
 $date_from_file = fgets($file);
 fclose($file);
 return $date_from_file;
}
 /*creating API request to get data from BX8, hashing and put in logs files*/
 function takeData()
 { 
  $lastTimeReg = read_from_file();
  echo 'Last date from file: '.$lastTimeReg.'<br/>';
  echo 'Making request for data....<br/>';
// Request options
  $module = 'Customer'; //Getting 'Customers' data
  $api_username = 'FB_offline@test.com'; //user
  $api_password = 'HEvk69'; //pass
  //MAX 500 records
  $recordStart = 0; //PAGES by 500 records START FROM 0
  $url = 'http://affiliates.bx8.me/?MODULE='.$module; //module type
  $url .= '&COMMAND=';
  $url .= 'View'; //action type = VIEW
  //$url .= '&LIMIT[recordStart]='.$recordStart; //Can start items from this point
  if($lastTimeReg != '') //If file NOT empty add to request FILTER
  {
   $url .= '&FILTER[regTime][min]='.$lastTimeReg; //FILTER FOR NEW DATA 
  }
  $url .= '&api_username='.$api_username;
  $url .= '&api_password='.$api_password;
//Init curl
  $ch = curl_init($url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); //LEADS
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,20);
  curl_setopt($ch,CURLOPT_TIMEOUT,30);
  curl_setopt($ch,CURLOPT_FOLLOWLOCATION, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,["Content-Type:application/x-www-form-urlencoded; charset=utf-8"]);
  curl_setopt($ch,CURLOPT_HEADER, false);
  $exec = curl_exec($ch);
  $response = json_decode($exec);
  echo var_export($response);
  if($response->status == 'OK') //Success response
  {
   echo 'Request succeed: '.$response->status.'<br/>';
   echo 'Count of records: '. $countArray = count($response->customers).'<br/>'; //Count of records
   $filename = 'super_log_file.txt'; //Logs files
   $hashfile = 'super_hash_data.txt';
   $today = getdate();
   $date = 'Date: '.$today['hours'].':'.$today['wday'].':'.$today['minutes'].' '.$today['month'].' '.$today['wday'];
   $line = '========================================';
   $countArray = count($response->customers); //Count of records
   $dataString = backToString($response->customers); //Response to string for log file
   $dataArray = HashRequest($response->customers); //Hash response
   echo '<pre>' . var_export($dataArray, true) . '</pre>';
   $hashdata = backToString(HashRequest($response->customers)); //hash response and make string for API FB
   recordDate(getLastDate($dataArray));   //record regTime to file
   //Write text for log file
   $log = $line.PHP_EOL.'Response Status: '.$response->status.PHP_EOL.' Records count: '.$countArray.PHP_EOL.$date.PHP_EOL.$dataString.PHP_EOL.$line;
   //Write text for hash log file
   $hashlog = $line.PHP_EOL.'Response Status: '.$response->status.PHP_EOL.' Records count: '.$countArray.PHP_EOL.$date.PHP_EOL.$hashdata.PHP_EOL.$line;
   //Recording to log files
   file_put_contents($filename, $log, FILE_APPEND);
   file_put_contents($hashfile, $hashlog, FILE_APPEND);
  }
  else echo $response;
}
  //this data we should send to FB ---- $hashdata
 // FaceBook Api connect
 //Creating link for cUrl fb
 /*$TOKEN = 'EAAKU6gI8oi8BANVupRfx4hCR5qinRAs91FSQXn2nY4r8ixC97UeBek6kcFoYBZATq2Jwj8cekrK32O4gMEH4ck01N2Lv9pZAENPIDxczFd9C02ozJ4yvLreZCXGQogZBudwigVC6gWFFia49SasQFQ6RY8eyp9pJ7PBPc4Llbt3Hp5srDh21U6qJ45GAtTcQw0XULOPp8AZDZD';
 $OFFLINE_CONV_ID   = '1971209353202465'; //parameter for this FB CONVERSION
 $facebook_link  = 'https://graph.facebook.com/v2.12/';
 $facebook_link .= $OFFLINE_CONV_ID;
 $facebook_link .= '/events';
 $facebook_link .= '?access_token=';
 $facebook_link .= $TOKEN;
 $facebook_link .= '&data=';
 $facebook_link .= $hashdata; // Our data(hashed)
 $facebook_link .= '&upload_tag='; //Uploads tags
 $facebook_link .= 'uploads';
 
 echo 'Making facebook request...<br/>';
 //making cUrl request to FB
 $curlForFacebook = curl_init();
 curl_setopt_array($curlForFacebook, array(
  CURLOPT_URL => $facebook_link,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Cache-Control: no-cache"
  ))
 );

 $facebook_response = curl_exec($curlForFacebook); //Execute
 $err = curl_error($curlForFacebook);

 curl_close($curlForFacebook);

 //If error founded
 if ($err) 
 {
  echo "cURL Error #:" . $err;
 } 
 else 
 {
  echo $facebook_response; //response cURL FB API
 }  
   
// Status request
}
 else
  echo 'Error when making request: '.$response->status;
  curl_close($ch); //close connection 
}*/

//Init all functions to get Bx8 data, log files, API facebook integration
 takeData();

?>