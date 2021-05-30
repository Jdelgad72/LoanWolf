<?php
require_once '../../../vendor/autoload.php';
require_once 'connection.php';
date_default_timezone_set("America/Indianapolis");

$CLIENT_ID = "381871578603-kq43dlf32e1boghdcja60rh92umulg2m.apps.googleusercontent.com";

$response = array();
if(isset($_POST['idToken'])){

  // Get $id_token via HTTPS POST.
  $id_token = $_POST['idToken'];
  $client = new Google_Client(['client_id' => $CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend
  $payload = $client->verifyIdToken($id_token);
  if ($payload){
    $userid = $payload['sub'];
    $firstName = $payload['given_name'];
    $lastName = $payload['family_name'];
    $email = $payload['email'];
    // If request specified a G Suite domain:
    // $domain = $payload['hd'];

    $stmt = $conn->prepare("SELECT userId FROM user WHERE accountGoogle = ?;");
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $stmt->store_result();

    // Checks if it is a new user.
    if($stmt->num_rows > 0){ //User has logged in before.
      $response['newUser'] = false;

      //grabs the id, name, email.
      $stmt = $conn->prepare("SELECT userId, firstName, lastName, email FROM user WHERE accountGoogle = ?");
      $stmt->bind_param("s",$userid);
      $stmt->execute();
      $stmt->bind_result($id, $firstname, $lastname, $email);
      $stmt->fetch();

      $user = array(
        'id'=>$id,
        'firstName'=>$firstname,
        'lastName'=>$lastname,
        'email'=>$email
      );

      $stmt->close();

      $response['error'] = false;
      $response['message'] = 'User logged in successfully.';
      $response['user'] = $user;

    }else{//User has not signed up before.

      $stmt = $conn->prepare("INSERT INTO user(firstName, lastName, email, accountGoogle) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $firstName, $lastName, $email, $userid);
      if($stmt->execute()){

        //grabs the id, name, email.
        $stmt = $conn->prepare("SELECT userId, firstName, lastName, email FROM user WHERE accountGoogle = ?");
        $stmt->bind_param("s",$userid);
        $stmt->execute();
        $stmt->bind_result($id, $firstname, $lastname, $email);
        $stmt->fetch();

        $user = array(
          'id'=>$id,
          'firstName'=>$firstname,
          'lastName'=>$lastname,
          'email'=>$email
        );

        $stmt->close();

        $response['error'] = false;
        $response['newUser'] = true;
        $response['message'] = 'User registered successfully';
        $response['user'] = $user;
      }else {
        $response['error'] = true;
        $response['message'] = "Error with adding new record to database.";
      }
    }
  }else{
    // Invalid ID token
    $response['error']=true;
    $response['message']="Id Token invalid.";
  }
}else{
  $response['error']=true;
  $response['message']='Missing Id Token';
}
echo json_encode($response);
?>