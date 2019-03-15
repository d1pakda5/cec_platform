<?php
 include("../config.php");

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
//$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// connect to the mysql database
$link = mysqli_connect('localhost', 'recharge_click', 'ZeU(OiQ+qsJ7', 'recharge_db');
mysqli_set_charset($link,'utf8');
 
// retrieve the table and key from the path
// $table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
// $key = array_shift($request)+0;
 
// escape the columns and values from the input object
//$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
// $values = array_map(function ($value) use ($link) {
//   if ($value===null) return null;
//   return mysqli_real_escape_string($link,(string)$value);},array_values($input));
 
// build the SET part of the SQL command
// $set = '';

// for ($i=0;$i<count($columns);$i++) {
//   $set.=($i>0?',':'').'`'.$columns[$i].'`=';
//   $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
// }
 /*$email1=$_REQUEST["email"];
 $email="$email1";  
  $pass1=$_REQUEST["pass"];
  $pass="$pass1";*/
// create SQL based on HTTP method
switch ($method) {
  case 'GET':
  $email1=mysql_real_escape_string($_REQUEST["username"]);
  $email="$email1";
  
  $pass1=mysql_real_escape_string($_REQUEST["password"]);
  $pass=hashPin($pass1);
  $sql = "select * from apps_admin WHERE username='$email' and pin='$pass'";
  $sql2 = "select * from apps_user WHERE username='$email' and pin='$pass'";


   break;
  case 'PATCH':
    $sql = "update `$table` set $set where weight_user_id=$key"; break;
  case 'POST':
    $sql = "insert into users set $set";
     break;
  case 'DELETE':
    $sql = "delete `$table` where weight_user_id=$key"; break;
}
 
// excecute SQL statement
$result = mysqli_query($link,$sql);

$result2 = mysqli_query($link,$sql2);

if($result->num_rows <=0) {
  
  if($result2->num_rows <= 0)
  {
    //echo "result2 blank";
    http_response_code(404);
  }
  else
  {   
    if ($method == 'GET')
    {
    
      $output2=array();
      header("Access-Control-Allow-Origin: *");
      while ($row2=mysqli_fetch_object($result2))
      {
       // header("Access-Control-Allow-Origin: *");
        $output2[]=$row2;
      }
      if(empty($output2) && empty($output))
      {
        http_response_code(404);
      }
      if(empty($output))
      {
      echo json_encode($output2);
    }
  }
  else {
  echo mysqli_affected_rows($link);
}

}
  
}

 
// print results, insert id or affected row count
if ($method == 'GET') {
  $output=array();
  // if (!$key) echo '[';
   while ($row=mysqli_fetch_object($result))
    {
    header("Access-Control-Allow-Origin: *");
    // echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
  	$output[]=$row;
  	
  }
 if(empty($output)&& empty($output2)){
      http_response_code(404);
    }
  // if (!$key) echo ']';
   if(empty($output2)){
      echo json_encode($output);
    }

} elseif ($method == 'POST') {
  echo mysqli_insert_id($link);
} else {
  echo mysqli_affected_rows($link);
}
 
// close mysql connection
mysqli_close($link);
?>
