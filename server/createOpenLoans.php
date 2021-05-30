<?php
require_once 'connection.php';
date_default_timezone_set("America/Indianapolis");

$response = array();
if(isset($_POST['ID'], $_POST['borrowOrLend'], $_POST['paymentInterval'], $_POST['STARTDATE'], $_POST['PAYMENTNUM'], $_POST['VALUE'], $_POST['RATE'])){
  $id = $_POST['ID'];
  $borrowOrLend = $_POST['borrowOrLend'];
  $paymentInterval = $_POST['paymentInterval'];
  $date = $_POST['STARTDATE'];
  $paymentnum = $_POST['PAYMENTNUM'];
  $amount = $_POST['VALUE'];
  $rate = $_POST['RATE'];

  //Gets name of user who sent loan to sign signature.
  $stmt = $conn->prepare("SELECT CONCAT(firstName, ' ', lastName)as name FROM user WHERE userID = ?;");
  $stmt->bind_param("i",$id);
  $stmt->execute();
  $stmt->bind_result($name);
  $stmt->fetch();
  $stmt->close();  

  //Gets the end Date.
  if($paymentInterval == "Daily"){
    $string = '+'.$paymentnum.' day';
    $newDate = new DateTime($date);
    $newDate->modify($string); 
    $endDate = $newDate->format('Y-m-d');
  }elseif($paymentInterval == "Monthly"){
    $string = '+'.$paymentnum.' month';
    $newDate = new DateTime($date);
    $newDate->modify($string);
    $endDate = $newDate->format('Y-m-d'); 
  }elseif($paymentInterval == "Weekly"){
    $string = '+'.$paymentnum.' week';
    $newDate = new DateTime($date);
    $newDate->modify($string);
    $endDate = $newDate->format('Y-m-d'); 
  }

  //Checks wether selected lend or borrow
  if($borrowOrLend == "Lend"){

    //inserts the values into loan table.
    $sql = "INSERT INTO loan(loanAmount,interestRate,loanStatus,notificationStatus,loanDateStart,loanDateEnd,paymentSchedule,numberPayments,dateSent,timeSent,debtorSignature, creditorSignature, groupLoanID) VALUES ($amount,$rate,'pending','Sent','$date','$endDate','$paymentInterval',$paymentnum,CURDATE(),CURRENT_TIME(),NULL, '$name', NULL);";
    if(mysqli_query($conn, $sql)){

      //Gets the loanID of the loan just inserted.
      $stmt2 = $conn->prepare("SELECT loanID FROM loan WHERE creditorSignature = ? ORDER BY dateSent DESC, timeSent DESC LIMIT 1;");
      $stmt2->bind_param("s", $name);
      $stmt2->execute();
      $stmt2->bind_result($loanID);
      $stmt2->fetch();
      $stmt2->close();
     
      //Inserts a row into the userloan table.
      $sql2 = "INSERT INTO userLoan(userDebtor, userCreditor, loanID) VALUES (NULL, $id, $loanID);";
      if(mysqli_query($conn, $sql2)){
        $response['error'] = false;
        $response['message'] = "Successfully added the open loan";
      }
    }
  }elseif($borrowOrLend == "Borrow"){
    //inserts the values into loan table.
    $sql = "INSERT INTO loan(loanAmount,interestRate,loanStatus,notificationStatus,loanDateStart,loanDateEnd,paymentSchedule,numberPayments,dateSent,timeSent,debtorSignature, creditorSignature, groupLoanID) VALUES ($amount,$rate,'pending','Sent','$date','$endDate','$paymentInterval',$paymentnum,CURDATE(),CURRENT_TIME(),'$name',NULL, NULL);";
    if(mysqli_query($conn, $sql)){

      //Gets the loanID of the loan just inserted.
      $stmt2 = $conn->prepare("SELECT loanID FROM loan WHERE debtorSignature = ? ORDER BY dateSent DESC, timeSent DESC LIMIT 1;");
      $stmt2->bind_param("s", $name);
      $stmt2->execute();
      $stmt2->bind_result($loanID);
      $stmt2->fetch();
      $stmt2->close();

      //Inserts a row into the userloan table.
      $sql2 = "INSERT INTO userLoan(userDebtor, userCreditor, loanID) VALUES ($id, NULL, $loanID);";
      if(mysqli_query($conn, $sql2)){
        $response['error'] = false;
        $response['message'] = "Successfully added the open loan";
      }
    }
  }
}else{
  $response['error']=true;
  $response['message']='Missing Values.';
}
echo json_encode($response);
?>