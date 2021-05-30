<?php
require_once 'connection.php';

$response = array();
$ids = array();
$messages = array();
$type = array();
if(isset($_POST['id'])){
  $id = $_POST['id'];

  $sql ="SELECT l.loanID, l.loanAmount, l.interestRate, l.loanDateStart FROM loan AS l, userLoan AS ul WHERE (ul.userDebtor =$id OR ul.userCreditor=$id) AND l.loanID=ul.loanID AND l.notificationStatus = 'Notify' ORDER BY l.dateAccepted DESC, l.timeAccepted DESC;";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {

    while($row = mysqli_fetch_assoc($result)){
      array_push($ids, $row["loanID"]);
      $dates = date("F d, Y", strtotime($row["loanDateStart"]));
      array_push($messages, "The loan of $".(string)$row["loanAmount"]." with the interest Rate of ".(string)((float)$row["interestRate"]*100)."% has successfully been initiated. The loan will begin on ".$dates.".");
      array_push($type, "Loan Success");
    }

    $response["ids"] = $ids;
    $response["messages"] = $messages;
    $response["type"] = $type;

    $response["error"] = false;
    $response["message"] = "Successfully retrieved notifiations.";
  }
  else{
    $response['error']=true;
    $response['message']='There are currently No rankings to make leaderboards.';
  }
}else{
  $response['error']=true;
  $response['message']='Missing parameters';
}
echo json_encode($response);
?>