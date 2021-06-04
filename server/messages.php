<?php
require_once 'connection.php';

$response = array();
if(isset($_POST['ID'])){

$id = $_POST['ID'];

$names = array();
$emails = array();
$messages = array();
$dates = array();
$times = array();

$sql ="SELECT CONCAT(u.firstName, ' ', u.lastName) AS name, u.email, m.messageContent, m.messageSentTime, m.messageSentDate FROM user AS u, userMessage AS um, message AS m WHERE um.messageID=m.messageID AND ((um.userSent=u.userID AND um.userRecieve=$id) OR (um.userRecieve=u.userID AND um.userSent=$id)) GROUP BY u.email ORDER BY m.messageSentDate ASC, m.messageSentTime ASC;";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

  while($row = mysqli_fetch_assoc($result)){
    array_push($names, $row["name"]);
    array_push($emails, $row["email"]);
    array_push($messages, $row["messageContent"]);
    array_push($dates, $row["messageSentDate"]);
    array_push($times, $row["messageSentTime"]);
  }

  $response["names"] = $names;
  $response["emails"] = $emails;
  $response["messages"] = $messages;
  $response["dates"] = $dates;
  $response["times"] = $times;

  $response["error"] = false;
  $response["message"] = "Successfully retrieved all conversations.";
}
else{
  $response['error']=true;
  $response['message']='There are currently No Open Loans. Create an Open Loan.';
}
}else{
  $response['error']=true;
  $response['message']='Missing parameters';
}
echo json_encode($response);
?>