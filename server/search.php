<?php
require_once 'connection.php';

$response = array();
$names = array();
$emails = array();

$sql = "SELECT CONCAT(firstName, ' ', lastName) as name, email FROM user ORDER BY name;"; 
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
  $counter = 1;
  while($row = mysqli_fetch_assoc($result)){
    array_push($names, $row["name"]);
    array_push($emails, $row["email"]);
  }

  $response["names"] = $names;
  $response["emails"] = $emails;
  $response["error"] = false;
  $response["message"] = "Successfully retrieved users.";
}
else{
  $response['error']=true;
  $response['message']='No users found in database.';  
}
echo json_encode($response);
?>