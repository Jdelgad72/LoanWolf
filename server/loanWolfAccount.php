<?php
require_once 'connection.php';

$response = array();
if(isset($_POST['id'])){
  $id = $_POST['id'];

  //Query to retrieve amount in balance.
  $stmt = $conn->prepare("select amount from user Where userid = ?;");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($amount);
  $stmt->fetch();
  $stmt->close();

  $response['amount']=$amount;
  $response["error"] = false;
  $response["message"] = "Successfully retrieved Balance.";
}else{
  $response['error']=true;
  $response['message']='Missing parameters';
}
echo json_encode($response);
?>