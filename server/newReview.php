<?php
require_once 'connection.php';
$response = array();
if(isset($_POST['id'])){
	$id = (int)$_POST['id'];
	$review = $_POST['review'];
	$rating = (int)$_POST['rating'];
	$email = $_POST['otherEmail'];
	$stmt1 = $conn->prepare("select userID from user WHERE email = ?");
	$stmt1->bind_param("s", $email);
	$stmt1->execute();
	$stmt1->bind_result($otherID);
	$stmt1->fetch();
	$stmt1->close();

	$sql = "INSERT into review (comment,starRating,reviewTime,reviewDate,userReviewing,userReviewer) VALUES('$review',$rating,Current_Time(),CurDate(),$otherID,$id);";

if (mysqli_query($conn, $sql)){
	$response['error'] = 'false';
	$response['message']="Review Submitted";
	}
/*else{
	$response['error'] = 'true';
	$response['message']="Error with SQL statment";
	}
}*/
else{
	$response['error'] = 'true';
	$response['message']="Missing user ID";
	}
echo json_encode($response);
?>