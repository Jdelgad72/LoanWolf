<?php
require_once 'connection.php';

$response = array();
if(isset($_POST['searchEmail'], $_POST['requesterID'])){
  $searchEmail = $_POST['searchEmail'];
  $requesterID = $_POST['requesterID'];
  
  //Retrieves number of loans Sent.
  $stmt = $conn->prepare("Select COUNT(ul.loanID) FROM user AS u, userLoan AS ul WHERE email = ? AND u.userID = ul.userCreditor;");
  $stmt->bind_param("s",$searchEmail);
  $stmt->execute();
  $stmt->bind_result($loanSent);
  $stmt->fetch();
  $stmt->close();

  $response["numOfLoanSent"] = $loanSent;

  //Retrieves amount sent as a creditor.
  $stmt1 = $conn->prepare("select l.loanAmount from user as u, userLoan as ul, loan as l WHERE u.userID = userCreditor AND ul.loanID = l.loanID AND email = ?;");
  $stmt1->bind_param("s",$searchEmail);
  $stmt1->execute();
  $stmt1->bind_result($amountSent);
  $stmt1->fetch();
  $stmt1->close();

  $response["amountSent"] = $amountSent;

  //Retrieves the number of loans accepted as a percentage.
  $stmt2 = $conn->prepare("select COUNT(l.loanID)/(select COUNT(l.loanID) from loan AS l, userLoan as ul, user as u where u.userID = ul.userCreditor AND l.loanID = ul.loanID AND u.email = ?) AS percentAccepted from loan AS l, userLoan as ul, user as u Where l.loanID = ul.loanID AND u.userID = ul.userCreditor AND u.email = ? AND (loanStatus = 'Complete' OR loanStatus = 'pending');");
  $stmt2->bind_param("ss",$searchEmail, $searchEmail);
  $stmt2->execute();
  $stmt2->bind_result($percentLoanAccepted);
  $stmt2->fetch();
  $stmt2->close();

  $response["percentLoanAccepted"] = $percentLoanAccepted;

  //Retrieves number of payments as a debtor.
  $stmt3 = $conn->prepare("Select COUNT(p.paymentID) FROM user AS u, userPayment AS up, payment AS p WHERE email = ? AND u.userID = up.userDebtor AND up.paymentID = p.paymentID AND up.fromTo = 1;");
  $stmt3->bind_param("s",$searchEmail);
  $stmt3->execute();
  $stmt3->bind_result($numOfPayments);
  $stmt3->fetch(); 
  $stmt3->close();
  
  $response["numOfPayments"] = $numOfPayments;
 
  //Retrieves Default rate of payments as a debtor.
  $stmt4 = $conn->prepare("Select COUNT(p.paymentID)/(Select COUNT(p.paymentID) FROM user AS u, userPayment AS up, payment AS p WHERE email = ? AND u.userID = up.userDebtor AND up.paymentID = p.paymentID AND up.fromTo = 1) AS defaultRate FROM user AS u, userPayment AS up, payment AS p WHERE email = ? AND u.userID = up.userDebtor AND up.paymentID = p.paymentID AND paymentStatus = 'Failed' AND up.fromTo = 1;");
  $stmt4->bind_param("ss",$searchEmail, $searchEmail);
  $stmt4->execute();
  $stmt4->bind_result($defaultRate);
  $stmt4->fetch();
  $stmt4->close();

  $response["defaultRate"] = $defaultRate;

  //Retrieves Amount recieved from creditors.
  $stmt5 = $conn->prepare("select l.loanAmount from user as u, userLoan as ul, loan as l WHERE u.userID = userDebtor AND ul.loanID = l.loanID AND email = ?;");
  $stmt5->bind_param("s",$searchEmail);
  $stmt5->execute();
  $stmt5->bind_result($amountRecieved);
  $stmt5->fetch();
  $stmt5->close();

  $response["amountRecieved"] = $amountRecieved;

  $response['error'] =false;
  $response['message'] ="Successfully ran all queries"; 
}else{
  $response['error'] = true;
  $response['message'] = "Error with parameters set.";
}
echo json_encode($response);
?>