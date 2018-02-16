<?php
//Converting object from IN request to string for bx8 request AND HASHING
function convertToRequest($object)
{
 $result = '';
 foreach($object as $key => $value) 
 {
  $result .= $key."=".hash("sha256",$value)."&"; //hashing
 }
 $result = rtrim($result,"&"); //remove last ampersand;
 return $result;
}

if (isset($_GET['apiModule']))
{
 $module = $_GET['apiModule'];
 $url = 'http://affiliates.bx8.me/?MODULE='.$module.'&COMMAND=View&api_username=RND@leomarkets.com&api_password=2Aj484$!2A';
 $ch = curl_init($url);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); //LEADS
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,20);
 curl_setopt($ch,CURLOPT_TIMEOUT,30);
 curl_setopt($ch,CURLOPT_FOLLOWLOCATION, false);
 curl_setopt($ch,CURLOPT_HTTPHEADER,["Content-Type:application/x-www-form-urlencoded; charset=utf-8"]);
 curl_setopt($ch,CURLOPT_HEADER, false);
 $exec = curl_exec($ch);
 print_r($exec);
}
else 
{
 $module = 'Customer';
 $action = 'Add';
 $api_user = 'RND@leomarkets.com';
 $api_pass = '2Aj484$!2A';
 $companId = "c520f567-4c88-4c3c-a849-a88500dd72e2";

 $firstName = "Test";
 $LastName = "Test";
 $Email = 'test44212312@test.com';
 $Password = 'password';
 $Phone = '132465789';
 $Country = 480;
 $Currency = 'USD';

 $url = 'http://affiliates.bx8.me/';
 $body = array('MODULE'  => $module,
						'COMMAND' => $action,
						'api_username' => $api_user,
						'api_password' => $api_pass,
						'CampaignId' => $companId,
						'FirstName'  => $firstName,
						'LastName'   => $LastName,
						'Email' => $Email,
						'Password' => $Password,
						'Phone' => $Phone,
						'Country' =>  $Country,
						'Currency' => $Currency,
						'IsTestUser' => 'true');
 //var_dump($body);
// echo $body->Email;
 //print_r(convertToRequest($body));
 //var_dump($body);
 
 //A_aid = Test

 //echo $url;


/*$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://affiliates.bx8.me/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $query,
  CURLOPT_HTTPHEADER => array(
    "Cache-Control: no-cache",
    "Content-Type: application/x-www-form-urlencoded"),
));

$response = curl_exec($curl);
echo $response;
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}*/
$post = file_get_contents('php://input',true);
var_dump($post);
//$post = json_decode($post);
/*for($i = 0; $i<count($post); $i++)
{
 //$log = $post[$i]->position.PHP_EOL;
 echo $post[$i]->markerPosition.PHP_EOL;
}*/

//$a =  $post[0];
$filename = 'super_log_file.txt';
$today = getdate();
$date = $today['hours'].':'.$today['wday'].':'.$today['minutes'].' '.$today['month'].' '.$today['wday'];
//print_r($post);
 $log = gettype($post).' '.$post.' '.$date.PHP_EOL;
 file_put_contents($filename, $log, FILE_APPEND);
 echo '';
}
?>