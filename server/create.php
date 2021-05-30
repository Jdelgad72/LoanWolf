<?php
require '../../../vendor/autoload.php';
require_once 'connection.php';
date_default_timezone_set("America/Indianapolis");

// This is your real test secret API key.
\Stripe\Stripe::setApiKey('sk_test_51IMkh6Kj5NtitlgyqihTCeVwe93Sxa6OviL9kAfDwWDnstyCpew3TdsDHxSqBSSDyDs0I67iO6py0eRQ1A1E2ajl003KTTWWJj');
function calculateOrderAmount($num){
  // Replace this constant with a calculation of the order's amount
  // Calculate the order total on the server to prevent
  // customers from directly manipulating the amount on the client
  $newnum = (float)$num * 100;

  return (int)$newnum;
}
header('Content-Type: application/json');
try {
  // retrieve JSON from POST body
  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);
  $paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => calculateOrderAmount($json_obj->amount),
    'currency' => 'usd',
  ]);

  //Updates amount balance in user table
  $balance = (float)$json_obj->amount + (float)$json_obj->balance;
  $stmt = $conn->prepare("UPDATE user SET amount = ? WHERE userid = ?;");
  $stmt->bind_param("di", $balance, $json_obj->id);
  $stmt->execute();
  $stmt->close();

  //Inserts into the transaction table.
  $stmt2 = $conn->prepare("Insert INTO userTransaction(userID, amount, type, time, date) VALUES(?, ?, 0, CURRENT_TIME() ,CURDATE());");
  $stmt2->bind_param("id", $json_obj->id, $json_obj->amount);
  $stmt2->execute();
  $stmt2->close(); 
 
  $output = [
    'clientSecret' => $paymentIntent->client_secret,
  ];
  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}