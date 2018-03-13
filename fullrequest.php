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
 $temp_array = array();
 $currency = '';
 $timestamp;
 $value_balance;
 $temp_array['email']   = hash('sha256', $data->email);
 $temp_array['phone']   = hash('sha256', $data->phone);
 $temp_array['doby']    = hash('sha256', $data->phone);
 $temp_array['dobm']    = hash('sha256', $data->phone);
 $temp_array['dobd']    = hash('sha256', $data->phone);
 $temp_array['ln']      = hash('sha256', $data->lastName);
 $temp_array['fn']      = hash('sha256', $data->firstName);
 $temp_array['ct']      = hash('sha256', $data->city);
 $temp_array['st']      = hash('sha256', $data->state);
 $temp_array['zip']     = hash('sha256', $data->postCode);
 $temp_array['country'] = hash('sha256', $data->country); //must be two-letter code in DB number??

 //Parsing date birthday
 /*$date = explode('-', $data->birthday);
 $year =  $date[0];
 $month = $date[1];
 $day = substr($date[2],0,2);

 $temp_array['doby'] = hash('sha256',$year);
 $temp_array['dobm'] = hash('sha256',$month);
 $temp_array['dobd'] = hash('sha256',$day);*/

 //Detect gender and make one hashed letter
 if($data->gender == 'Male')
   $temp_array['gen'] = hash('sha256', 'M');
 else //Female
   $temp_array['gen'] = hash('sha256', 'F');

 $new_data[]['match_keys'] = $temp_array;
 $last = count($new_data)-1; //getting last item of array for inception data (must be ZERO)
 //$new_data[$last]['value']      = $value_balance;
 //$new_data[$last]['currency']   = $currency; //adding keys to this item
 $new_data[$last]['event_name'] = 'Purchase'; //Type of transaction
 //$new_data[$last]['event_time'] = $timestamp; //Adding time of transaction
/*				   'accountBalance',
				   'country',
				   'currency');*/
 /*if($key == 'currency') //We need pure uppercase currency, not hashed
 {
  $currency = strtoupper($value);
 }
  else if($key == 'regTime') 
  {
   $timestamp = date_timestamp_get(date_create($value));  // We need timestamp format
  }
  else if($key == 'accountBalance')
  {
   $value_balance = $value; //value = accountBalance ??
  }
 }
   }
  }
 }*/
 return $new_data; //array
}

//Transform array to string
function backToString($array)
{
 return json_encode(array_values($array));
}

//Getting response from CRM request
function getResponse($module, $lastTimeReg, $filter) 
{
 $api_username = 'FB_offline@test.com'; //user
 $api_password = 'HEvk69'; //pass
 //MAX 500 records
 $recordStart = 0; //PAGES by 500 records START FROM 0
 $url = 'http://affiliates.bx8.me/?MODULE='.$module; //module type
 $url .= '&COMMAND=';
 $url .= 'View'; //action type = VIEW
 //$url .= '&LIMIT[recordStart]='.$recordStart; //Can start items from this point
 if($lastTimeReg != '') //If param NOT empty add to request FILTER
 {
  $url .= '&FILTER[date][min]='.$lastTimeReg; //FILTER FOR NEW DATA 
  //$url .= '&FILTER[date][max] = ';
 }

 if($filter != '') //adding filters if exists
  $url .= $filter;
 $url .= '&api_username='.$api_username;
 $url .= '&api_password='.$api_password;
 echo $url;
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
 echo '<pre>' . var_export($response, true) . '</pre>';
 return $response;
}

//Creating Cusomers list from Deposite Response
function createCustomerFilter($depositResponse)
{
 if($depositResponse->status == 'OK') //Request is OK
 {
  $filterString = '';
  $i = 0;
  $bathc = "&BATCH['.$i.']";
  foreach($depositResponse->deposits as $key) //parse array
  {
   foreach( $key as $next => $value) // getting all customerId
   {
    if($next == 'customerId')
	{
	 //Creating request with few BATCH
     $filterString .= '&BATCH['.$i.'][MODULE]=Customer&BATCH['.$i.'][COMMAND]=View&BATCH['.$i.'][FILTER][id][]='.$value; 
	 $i++;
	}
   }
  }
  return $filterString;
 }
 else if($depositResponse->status == 'No results') //If request is empty
  return 'No results from this date';
 else //Error in request
  return 'Error in depositResponse request';
}
 /*creating API request to get data from BX8, hashing and put in logs files*/
 function makeFbConversion($customers)
 {
  $dataString = backToString($customers); //Response to string for log file
  $dataArray = HashRequest($customers); //Hash response
   //this data we should send to FB ---- $hashdata
   // FaceBook Api connect
   //Creating link for cUrl fb
  $TOKEN = 'EAAKU6gI8oi8BANVupRfx4hCR5qinRAs91FSQXn2nY4r8ixC97UeBek6kcFoYBZATq2Jwj8cekrK32O4gMEH4ck01N2Lv9pZAENPIDxczFd9C02ozJ4yvLreZCXGQogZBudwigVC6gWFFia49SasQFQ6RY8eyp9pJ7PBPc4Llbt3Hp5srDh21U6qJ45GAtTcQw0XULOPp8AZDZD';
  $OFFLINE_CONV_ID   = '1971209353202465'; //parameter for this FB CONVERSION
  $facebook_link  = 'https://graph.facebook.com/v2.12/';
  $facebook_link .= $OFFLINE_CONV_ID;
  $facebook_link .= '/events';
  $facebook_link .= '?access_token=';
  $facebook_link .= $TOKEN;
  $facebook_link .= '&data=';
  $facebook_link .= $hashdata; // Our data(hashed)
  $facebook_link .= '&upload_tag='; //Uploads tags
  $facebook_link .= 'daily'; //daily upload_tag for fb cheecking OR 'uploads'
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

 //If error founded
 if ($err) 
 {
  echo "cURL Error #:" . $err;
 } 
 else 
 {
  echo $facebook_response; //response cURL FB API
 } 
 
 curl_close($curlForFacebook); //close connection 
}

 $yesterday = date('Y_m_d', strtotime("-1 days")); //Getting yesteday date for checking
 $depositResponse = getResponse('CustomerDeposits', $yesterday, ''); //Getting response from LAST DATE by Deposites
 $filter = createCustomerFilter($depositResponse); //Creating filter for CustomerRequest
 $response = getResponse('Customer','',$filter);
 $dataForFb = array();
 //$response[0]->Response->customers[0] -- this data need to parse
 for($j = 0; $j<count($response); $j++)
 {
  echo '<pre>'.var_export(HashRequest($response[$j]->Response->customers[0])).'</pre>';
  $dataForFb[] = HashRequest($response[$j]->Response->customers[0]);
 }
 echo '<pre>'.var_export(backToString($dataForFb)).'</pre>';
//takeData();
?>