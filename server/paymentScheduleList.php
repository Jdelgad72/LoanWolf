<?php
require_once 'connection.php';

$response = array();
$date = array();
$amount = array();
if(isset($_POST["id"])){
$id=(int)$_POST["id"];

$sql ="select p.paymentDate, if(pm.fromTo=1 AND userDebtor= $id, p.paymentAmount, IF(pm.fromTo=0 AND userCreditor= $id, p.paymentAmount, -p.paymentAmount)) AS amount FROM payment AS p, userPayment AS pm  WHERE (userDebtor = $id OR userCreditor = $id) AND p.paymentID = pm.paymentID ORDER BY p.paymentDate DESC;
";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

  while($row = mysqli_fetch_assoc($result)){
    array_push($date, $row["paymentDate"]);
    array_push($amount, $row["amount"]);
  }

  $response["date"] = $date;
  $response["amount"] = $amount;
 
  $response["error"] = false;
  $response["message"] = "Successfully created a Payment Schedule.";
}
else{
  $response['error']=true;
  $response['message']='You currently have no loans.';
}
}
echo json_encode($response);
?>