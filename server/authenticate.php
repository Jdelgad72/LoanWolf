<?php
require_once 'connection.php';
date_default_timezone_set("America/Indianapolis");

$response = array();
if(isset($_POST['ID'], $_POST['openloanid'], $_POST["borrowerLender"], 
$_POST['amount'], $_POST['interestRate'], $_POST["paymentType"], 
$_POST['startDate'], $_POST["numPayments"])){
  $id = $_POST['ID'];
  $openloanid = $_POST['openloanid'];
  $borrowerLender = $_POST['borrowerLender'];
  $amount = (int)$_POST['amount'];
  $interestRate = (float)$_POST['interestRate'];
  $paymentType = $_POST["paymentType"];
  $startDate = $_POST['startDate'];
  $numPayments = (int)$_POST["numPayments"];

  //Gets user who created the wolfpack.
  $stmt = $conn->prepare("select userid from user, userLoan Where  
if('Debtor' = ?, userid = userCreditor, userid = userDebtor) AND loanID = 
?;");
  $stmt->bind_param("si", $borrowerLender, $openloanid);
  $stmt->execute();
  $stmt->bind_result($id2);
  $stmt->fetch();
  $stmt->close();  

  //Gets user name.
  $stmt2 = $conn->prepare("select CONCAT(firstName, ' ', lastName) from 
user Where userid = ?;");
  $stmt2->bind_param("i", $id);
  $stmt2->execute();
  $stmt2->bind_result($name);
  $stmt2->fetch();
  $stmt2->close();
  
  //Updates loan table based on borrower or lender.
  if($borrowerLender == "Debtor"){
    $stmt3 = $conn->prepare("UPDATE loan SET loanStatus = 'Complete', 
notificationStatus = 'Notify', dateAccepted = CURDATE(), timeAccepted = 
CURRENT_TIME(), debtorSignature = ? WHERE loanID = ?;");
  }else{
    $stmt3 = $conn->prepare("UPDATE loan SET loanStatus = 'Complete', 
notificationStatus = 'Notify', dateAccepted = CURDATE(), timeAccepted = 
CURRENT_TIME(), creditorSignature = ? WHERE loanID = ?;");
  }
  $stmt3->bind_param("si", $name, $openloanid);
  $stmt3->execute();
  $stmt3->close();

  //Updates userLoan table making it no longer listed on open loans.
  if($borrowerLender == "Debtor"){
    $stmt4 = $conn->prepare("UPDATE userLoan SET  userDebtor = ? WHERE 
loanID = ?;");
  }else{
    $stmt4 = $conn->prepare("UPDATE userLoan SET  userCreditor = ? WHERE 
loanID = ?;");
  }
  $stmt4->bind_param("ii", $id, $openloanid);
  $stmt4->execute();
  $stmt4->close();


  //Begin the transfer of money from lender to lendee into the payments 
table.
  $stmt5 = $conn->prepare("INSERT INTO payment(loanID, paymentTime, 
paymentDate, paymentAmount, paymentStatus, notificationStatus) VALUES(?, 
'00:00:00', ?, ?, 'In Progress', 'In Progress');");
  $stmt5->bind_param("isi", $openloanid, $startDate, $amount); 
  $stmt5->execute();
  $last_id= mysqli_insert_id($conn);
  $stmt5->close();

  //Inserts entry into userPayment table
  $stmt6 = $conn->prepare("INSERT INTO 
userPayment(userDebtor,userCreditor, paymentID, fromTo) VALUES(?, ?, ?, 
0);");
  if($borrowerLender == "Debtor"){
    $stmt6->bind_param("iii", $id, $id2, $last_id);
  }else{
    $stmt6->bind_param("iii", $id2, $id2, $last_id);
  }
  $stmt6->execute();
  $stmt6->close();
  
  //Adds all the payments to the payments table that will be sent from the 
lendee to the lender.
  //First need to calculate every payment.
  $payment = $amount*(1+$interestRate)/$numPayments;

  //For the number of payments add a row to the payments table.
  for($x=1; $x<=$numPayments; $x++){
    //Modifying date to add days,weeks,months depending on the loan.
    if($paymentType == "Daily"){
      $string = '+'.$x.' day';
    }elseif($paymentType == "Monthly"){
      $string = '+'.$x.' month';
    }elseif($paymentTypel == "Weekly"){
      $string = '+'.$x.' week';
    }
    $newDate = new DateTime($startDate);
    $newDate->modify($string);
    $endDate = $newDate->format('Y-m-d');

    //Creating new row in the payment table.
    $stmt7 = $conn->prepare("INSERT INTO 
payment(loanID,paymentTime,paymentDate,paymentAmount,paymentStatus,notificationStatus) 
VALUES(?,'00:00:00',?,?,'In Progress','In Progress');");
    $stmt7->bind_param("isd", $openloanid, $endDate, $payment);
    $stmt7->execute();
    $last_id= mysqli_insert_id($conn);
    $stmt7->close();

    //Inserts entry into userPayment table
    $stmt8 = $conn->prepare("INSERT INTO 
userPayment(userDebtor,userCreditor, paymentID, fromTo) VALUES(?, ?, ?, 
0);");
    if($borrowerLender == "Debtor"){
      $stmt8->bind_param("iii", $id, $id2, $last_id);
    }else{
      $stmt8->bind_param("iii", $id2, $id2, $last_id);
    }    
    $stmt8->execute();
    $stmt8->close();   
  }

  $response["message"] = "Successfully Accepted the Loan.";
  $response['error'] = false;
}else{
  $response['error']=true;
  $response['message']='Missing Values.';
}
echo json_encode($response);
?>