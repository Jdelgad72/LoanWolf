<?php
require_once 'connection.php';

$response = array();
if(isset($_POST['ID'], $_POST['email'])){

$id = $_POST['ID'];
$email = $_POST['email'];

$statuses = array();
$messages = array();
$dates = array();
$times = array();

$sql ="SELECT * FROM (SELECT 'Sender' AS status, m.messageContent, m.messageSentTime, m.messageSentDate FROM user AS u, userMessage AS um, message AS m WHERE um.messageID=m.messageID AND (um.userRecieve=u.userID AND um.userSent=$id) AND u.email='$email' UNION SELECT 'Reciever' AS status, m.messageContent, m.messageSentTime, m.messageSentDate FROM user AS u, userMessage AS um, message AS m WHERE um.messageID=m.messageID AND (um.userSent=u.userID AND um.userRecieve=$id) AND u.email='$email') AS a ORDER BY a.messageSentDate DESC, a.messageSentTime DESC;";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

  while($row = mysqli_fetch_assoc($result)){
    array_push($statuses, $row["status"]);
    array_push($messages, $row["messageContent"]);
    array_push($dates, $row["messageSentDate"]);
    array_push($times, $row["messageSentTime"]);
  }

  $response["statuses"] = $statuses;
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