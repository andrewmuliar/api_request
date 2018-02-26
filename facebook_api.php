<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://graph.facebook.com/v2.12/170454536348550/?access_token=EAACbBwoW44YBAOP4XZATxDaa1wZB3wFkywZAWMu1AvIWD5hOSjDnptqWWMvgko6Y1uth3hJgJPTxVblXsPDoW49bg0auJ1WQ6cQVVgdltbJRrHZABW6BjwQYKlqxbn29AGxsNyGT9Eq45BDYP6kTUZBJD3IU5FYjZCboZA7DmDkjYIWZCaZBBjDvghoJDZAJqfIdZAN463BDXsnDL0ppfrvm0blBl5InVtA3D38pZABE6i6vjmu0Wrr6weAB",
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

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo 'Facebook Api connected:<br/>'.$response;
}
?>