<?php
require_once 'connection.php';

$response = array();
$borrower_lender = array();
$openLoanID = array();
$amount = array();
$interestRates = array();
$paymentTypes = array();
$startDates = array();
$numPayments = array();

$sql ="SELECT IF(ul.userDebtor = 1, 'Debtor', 'Lender') AS 
lenderBorrower, l.loanID, l.loanAmount, l.interestRate, l.loanDateStart, 
l.paymentSchedule, l.numberPayments FROM loan AS l, userLoan as ul, wolfPack AS w WHERE 
l.loanID = ul.loanID AND l.loanID = w.loanID AND loanDateStart >= CURDATE() AND (ul.userDebtor = 
1 OR ul.userCreditor = 1) ORDER BY loanDateStart DESC;"; 
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

  while($row = mysqli_fetch_assoc($result)){
    array_push($borrower_lender, $row["lenderBorrower"]);
    array_push($openLoanID, $row["loanID"]);
    array_push($amount, $row["loanAmount"]);
    array_push($interestRates, $row["interestRate"]);
    array_push($paymentTypes, $row["paymentSchedule"]);
    array_push($startDates, $row["loanDateStart"]);
    array_push($numPayments, $row["numberPayments"]);
  }
  
  $response["borrowerLender"] = $borrower_lender;
  $response["openLoanID"] = $openLoanID;
  $response["amount"] = $amount;
  $response["interestRate"] = $interestRates;
  $response["paymentType"] = $paymentTypes;
  $response["startDate"] = $startDates;
  $response["numPayments"] = $numPayments;

  $response["error"] = false;
  $response["message"] = "Successfully retrieved all currently open 
Loans.";
}
else{
  $response['error']=true;
  $response['message']='There are currently No Open Loans. Create an Open 
Loan.';  
}
echo json_encode($response);
?>