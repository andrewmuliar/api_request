<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://graph.facebook.com/v2.12/170454536348550/?access_token=EAACbBwoW44YBAPcVRrNSQZByAAt3JOfk75yVjpepVgkqZBaoKCKsXRYWKA3S0xV4blpTa5X55PPVhFC0uYSYVndwbB8laM5OICVP9EeoaYWsWeePDt7sQALW7w9NPwZBuhkqw0XjZA2pjEp8x8yQBY45OCQZCIyRr1TGqIIhc7jWrQEv92s2DkIxf5XVoqwBSN7RgR0Y5YdCXA8HDbWAkCixxq46C133ZCEZBWZB85wz4yxntZAXaBNhG",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array("Cache-Control: no-cache")
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) 
{
 echo "cURL Error #:" . $err;
} 
else 
{
 echo 'Facebook Api connected:<br/>'.$response;
}
?>