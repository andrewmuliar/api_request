<?php
header("Access-Control-Allow-Origin: *");
/*Convert to hash data*/
function HashRequest($data)
{
 $new_data = array();
  for ($i = 0; $i < count($data); $i++)
  {
   $new_data[] = redo($data[$i]);
  }
 return $new_data;
}

//Recursive func for inception all data from array of object
 function redo($arrayka)
 {
  $ar = array();
  foreach($arrayka as $key => $value)
  {
   //ho $arrayka->$key;
   if(gettype($arrayka->$key) == 'object')
   {
  //  echo ' object'.PHP_EOL;
	$ar[$key] = redo($arrayka->$key);
   }
   else
     $ar[$key] = hash('sha256',$value);
  // else echo 'not object'.PHP_EOL;
  }
  return $ar;
 }
 /*for($i = 0; $i < count($array_of_object); $i++)
 {
  //$new_data[key($array_of_object[$i])] = $array[$i]['position'];
  $key = key($array_of_object[$i]);
  $value = $array_of_object[$i]->$key;
  if(array_key_exists($value, $array_of_object))
  {
   echo 'exits';
   echo $array_of_object[$i]->$value;
  }
  echo ' type = '.gettype($array_of_object[$i]).' '.key($array_of_object[$i]);
  $new_data = array_values($array_of_object);
 }*/

/*try
{*/
 $request = 'Request type: '.$_SERVER['REQUEST_METHOD'];
 $post = file_get_contents('php://input',true);
 $json = json_decode($post);
 $filename = 'super_log_file.txt';
 $hasfile = 'super_hash_data.txt';
 $today = getdate();
 $type = " Data type: ".gettype($post);
 $date = 'Date: '.$today['hours'].':'.$today['wday'].':'.$today['minutes'].' '.$today['month'].' '.$today['wday'];
 $line = '====================================================';
 print_r(HashRequest($json));
 $hashdata = json_encode(HashRequest($json));
 $log = $line.PHP_EOL.$request.PHP_EOL.$type.PHP_EOL.' '.$post.' '.PHP_EOL.$date.PHP_EOL;
 $haslog = $line.PHP_EOL.$request.PHP_EOL.$type.PHP_EOL.' '.$hashdata.' '.PHP_EOL.$date.PHP_EOL;
 file_put_contents($filename, $log, FILE_APPEND);
 file_put_contents($hasfile, $haslog, FILE_APPEND);
 echo ' Status = Ok';
/*}
catch (Exception $e)
{
 echo 'Error = '.$e->getMessage(),'\n';
}*/
?>