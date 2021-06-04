<?php
require_once 'connection.php';

$response = array();
if(isset($_POST['id'])){
  $userid = $_POST['id'];
  
  $stmt = $conn->prepare("SELECT firstName, lastName, dateOfBirth, streetAddress, zipAddress, stateAddress, gender FROM user WHERE userID = ?");
  $stmt->bind_param("s",$userid);
  $stmt->execute();
  $stmt->bind_result($firstname, $lastname, $dob, $street, $zip, $state, $gender);
  $stmt->fetch();

  $response['first'] = $firstname;
  $response['last'] = $lastname;
  $response['dob'] = $dob;
  $response['address'] = $street;
  $response['zip'] = $zip;
  $response['state']=$state;
  $response['gender'] = $gender;

  $response['error']=false;
  $response['message'] = 'Successfully retrieved users information.';
}else{
  $response['error']=true;
  $response['message']='Missing Id Token';
}
echo json_encode($response);
?>