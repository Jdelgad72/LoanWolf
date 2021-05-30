<?php

require_once 'connection.php';


$response = array();
if(isset($_POST['RADIO'])){
$radio = $_POST['RADIO'];
$radio2 = $_POST['RADIO2'];
$username = $_POST['USERNAME'];
$email = $_POST['EMAIL'];
$date = $_POST['DATE'];
$num = $_POST['PAYMENTNUM'];
$value = $_POST['VALUE'];
$rate = $_POST['RATE'];



      //inserts the id, name, value and rate.
     $stmt = $conn->prepare("INSERT INTO loan(loanAmount, interestRate, loanStatus, notificationStatus, loanDateStart, loanDateEnd, paymentSchedule, numberPayments, dateSent, timeSent, creditorSignature) VALUES (?, ?, 'pending' , 'Sent' , ?, '2021-08-01', ?, ?, CURDATE(), CURRENT_TIME(), ?);";
      $stmt->bind_param("iissis", $value, $rate, $date, $radio2, $num, $username );

      if($stmt->execute()){
	$response['error'] = false;
        $response['newUser'] = true;
        $response['message'] = 'User registered successfully';
        $response['user'] = $user;

      );

      

}else{
  $response['error']=true;
  $response['message']='Missing Id Token';
}  
echo json_encode($response);
?>