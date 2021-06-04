<?php
require_once 'connection.php';

$response = array();
if(isset($_POST['ID'], $_POST['email'], $_POST['message'])){

$id = (int) $_POST['ID'];
$email = $_POST['email'];
$message = $_POST['message'];

$sql = "INSERT INTO message(messageSentTime, messageSentDate, messageReadTime, messageReadDate, messageContent) VALUES (DATE_FORMAT(NOW(),'%H:%i:%s'), CURDATE(), '00:00:00', '0000-00-00', '$message');";

if(mysqli_query($conn, $sql)){

  //Gets name of user who sent loan to sign signature.
  $stmt = $conn->prepare("SELECT userID FROM user WHERE email = ?;");
  $stmt->bind_param("s",$email);
  $stmt->execute();
  $stmt->bind_result($id2);
  $stmt->fetch();
  $stmt->close();

  $stmt3 = $conn->prepare("SELECT messageID FROM message WHERE messageContent = ? ORDER BY messageSentDate DESC, messageSentTime DESC LIMIT 1;");
  $stmt3->bind_param("s", $message);
  $stmt3->execute();
  $stmt3->bind_result($messageID);
  $stmt3->fetch();
  $stmt3->close();

  $sql2 = "INSERT INTO userMessage(userSent, userRecieve, messageID) VALUES ($id, $id2, $messageID);";
  if(mysqli_query($conn, $sql2)){
  $response["error"] = false;
  $response["message"] = "Successfully retrieved all conversations.";
  }else{
   $response['error']=true;
  $response['message']='Error with second database statement.';
  }
}
else{
  $response['error']=true;
  $response['message']='Error with database.';
}
}else{
  $response['error']=true;
  $response['message']='Missing parameters';
}
echo json_encode($response);
?>