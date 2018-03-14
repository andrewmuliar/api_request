<?php
//Headers for open CORS 
/* header('Access-Control-Allow-Origin: *'); 
 header("Access-Control-Allow-Credentials: true");
 header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
 header('Access-Control-Max-Age: 1000');
 header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');*/

 /*Start read array for hashing*/
function HashRequest($data, $amount, $time) //return array
{
 $temp_array = array();
 $value_balance;
 $temp_array['email']   = hash('sha256', $data->email);
 $temp_array['phone']   = hash('sha256', $data->phone);
 $temp_array['ln']      = hash('sha256', $data->lastName);
 $temp_array['fn']      = hash('sha256', $data->firstName);
 $temp_array['ct']      = hash('sha256', $data->city);
 $temp_array['st']      = hash('sha256', $data->state);
 $temp_array['zip']     = hash('sha256', $data->postCode);
 $temp_array['country'] = hash('sha256', $data->country); //must be two-letter code in DB number??

 //Parsing date birthday if it not NULL
 if($data->birthday != NULL)
 {
  $date = explode('-', $data->birthday);
  $year =  $date[0];
  $month = $date[1];
  $day = substr($date[2],0,2);
  $temp_array['doby'] = hash('sha256',$year);
  $temp_array['dobm'] = hash('sha256',$month);
  $temp_array['dobd'] = hash('sha256',$day);
 }

 //Detect gender and make one hashed letter
 if($data->gender == 'Male')
   $temp_array['gen'] = hash('sha256', 'M');
 else //Female
   $temp_array['gen'] = hash('sha256', 'F');

 $new_data['match_keys']    = $temp_array; //Passing customer data
 $new_data['value']         = $amount; // Amount passing by param in func
 $new_data['currency']      = strtoupper($data->currency); //adding keys to this item
 $new_data['event_name']    = 'Purchase'; //Type of transaction
 $new_data['event_time']    = strtotime($time); //making timestamp $time - param in func
 
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
 echo '<pre>'.var_export($response, true).'</pre>';
 return $response;
}

//Creating Cusomers list from Deposite Response
function createCustomerFilter($depositResponse)
{
 if($depositResponse->status == 'OK') //Request is OK
 {
  $filterString = '';
  $i = 0;
  foreach($depositResponse->deposits as $key) //parse array
  {
   foreach( $key as $next => $value) // getting all customerId
   {
    if($next == 'customerId') //Find customerId key
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
 /*creating FB CONVERSION*/
 function makeFbConversion($hashData)
 {
  $TOKEN = 'EAAKU6gI8oi8BAPZBNpWZATasqHYfIqqjf7jFMdkzk5ljiTQ4PZCheQ0GVQigaZATqftZAljUeENfpYJNOemnmhN4l31nd2UwDGyRpQeMzeoNCZB7rlq9o5ZBGwbJIcjkdXZBAG4SCFqo8MOqZAaLxKS3UMypwZAL0bZBCgYorwKA4HGJ0jUSNRyGTcRivbKjuTpuNm2EZBDZAkTzKOQZDZD';
  $OFFLINE_CONV_ID   = '1971209353202465'; //parameter for this FB CONVERSION
  $facebook_link  = 'https://graph.facebook.com/v2.12/';
  $facebook_link .= $OFFLINE_CONV_ID;
  $facebook_link .= '/events';
  $facebook_link .= '?access_token=';
  $facebook_link .= $TOKEN;
  $facebook_link .= '&data=';
  $facebook_link .= $hashData; // Our data(hashed)
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

//Scripts starts from here
 $yesterday = date('Y_m_d', strtotime("-2 days")); //Getting yesteday date for checking
 $depositResponse = getResponse('CustomerDeposits', $yesterday, ''); //Getting response from LAST DATE by Deposites
 if(!empty($depositResponse->deposits))
 {
  echo '<span style="font-weight:bold;">Records count: '.count((array)$depositResponse->deposits).'</span><br/>';
  $filter = createCustomerFilter($depositResponse); //Creating filter for CustomerRequest
  $response = getResponse('Customer','',$filter); //Getting response from Customers with filter by ID
  $dataForFb = array();
  if(!empty($response))
  {
   for($j = 0; $j<count($response); $j++)
   {
    $amount = $depositResponse->deposits[$j]->amount; //Getting amount for value
    $time   = $depositResponse->deposits[$j]->confirmTime; //Getting time for event_time
    $dataForFb[] = HashRequest($response[$j]->Response->customers[0], $amount, $time); //hashing record
   }
   echo '<pre>'.var_export(backToString($dataForFb),true).'</pre>';
  // makeFbConversion(backToString($dataForFb)); // Sendind data-string to FB
  }
  else  echo 'Customer response is empty';
 }
 else echo 'Deposit response is empty';
?>