<?php
require_once 'connection.php';
$response = array();
if(isset($_POST['id'])){
  $id = (int)$_POST['id'];
  $first = $_POST['first'];
  $last = $_POST['last'];
  $dob = $_POST['dob'];
  $address = $_POST['address'];
  $zip = (int)$_POST['zip'];
  $state = $_POST['state'];
  $gender = $_POST['gender'];
  $sql="UPDATE user SET firstName = '$first', lastName = '$last', dateOfBirth = '$dob', streetAddress = '$address', zipAddress = $zip, stateAddress = '$state', gender = '$gender' WHERE userID = $id;"; 
if(mysqli_query($conn, $sql)){
  $response['error']=false;
  $response['message']="Record updated successfully";
  }
else{
  $response['error']=true;
  $response['message']="Error with SQL statment";
  }
}
else{
  $response['error']=true;
  $response['message']="Missing User ID";
  }
echo json_encode($response);
?>