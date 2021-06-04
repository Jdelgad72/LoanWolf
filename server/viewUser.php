<?php
require_once 'connection.php';
date_default_timezone_set("America/Indianapolis");

$response = array();
if(isset($_POST['searchEmail'], $_POST['requesterID'])){
  $searchEmail = $_POST['searchEmail'];
  $requesterID = $_POST['requesterID'];

  //Gathers date joined
  $stmt = $conn->prepare("select dateJoined From user WHERE email=?;");
  $stmt->bind_param("s",$searchEmail);
  $stmt->execute();
  $stmt->bind_result($dateJoined);
  $stmt->fetch(); 
  $stmt->close();
  
  $response['dateJoined'] = date("F Y", strtotime($dateJoined));
  
  //Gathers Average Rating.
  $stmt1 = $conn->prepare("select userID, SUM(starRating)/COUNT(starRating) AS averageRating From user, review WHERE userID = userReviewing AND email=? GROUP BY userID;");
  $stmt1->bind_param("s",$searchEmail);
  $stmt1->execute();
  $stmt1->bind_result($id, $averageRating);
  $stmt1->fetch();
  $stmt1->close();

  $response["rating"] = $averageRating;
 
  //Checks to see if the two users have had a past interaction.
  $sql = "select * FROM user, userLoan WHERE (userDebtor = $requesterID OR userCreditor = $requesterID) AND email = '$searchEmail';";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0){
    $response['pastCurrentInteraction'] = true;
  }else{
    $response['pastCurrentInteraction'] = false;
  }

  //Most recent review Shown
  $stmt2 = $conn->prepare("select Concat(firstName, ' ', lastName) AS name, starRating, comment from user, review WHERE email=? and userID = userReviewing ORDER BY reviewDate DESC LIMIT 1;");
  $stmt2->bind_param("s",$searchEmail);
  $stmt2->execute();
  $stmt2->bind_result($recentReviewer, $recentStarRating, $recentComment);
  $stmt2->fetch();
  $stmt2->close();

  $response["recentReviewer"] = $recentReviewer;
  $response["recentStarRating"] = $recentStarRating;
  $response["recentComment"] = $recentComment;
  
  //Shows number of payments sent out as a debtor.
  $stmt3 = $conn->prepare("Select COUNT(p.paymentID) FROM user AS u, userPayment AS up, payment AS p WHERE email = ? AND u.userID = up.userDebtor AND up.paymentID = p.paymentID AND up.fromTo = 1;");
  $stmt3->bind_param("s",$searchEmail);
  $stmt3->execute();
  $stmt3->bind_result($numOfPayments);
  $stmt3->fetch(); 
  $stmt3->close();
  
  $response["numOfPayments"] = $numOfPayments;
 
  //Shows Default rate on payments. Or payments that they did not pay.
  $stmt4 = $conn->prepare("Select COUNT(p.paymentID)/(Select COUNT(p.paymentID) FROM user AS u, userPayment AS up, payment AS p WHERE email = ? AND u.userID = up.userDebtor AND up.paymentID = p.paymentID AND up.fromTo = 1) AS defaultRate FROM user AS u, userPayment AS up, payment AS p WHERE email = ? AND u.userID = up.userDebtor AND up.paymentID = p.paymentID AND paymentStatus = 'Failed' AND up.fromTo = 1;");
  $stmt4->bind_param("ss",$searchEmail, $searchEmail);
  $stmt4->execute();
  $stmt4->bind_result($defaultRate);
  $stmt4->fetch();
  $stmt4->close();

  $response["defaultRate"] = $defaultRate;

  $response['error'] =false;
  $response['message'] ="Successfully ran both queries"; 
}else{
  $response['error'] = true;
  $response['message'] = "Error with parameters set.";
}
echo json_encode($response);
?>