<?php
$servername = "db.luddy.indiana.edu";
$username = "i494f20_team21";
$password = "my+sql=i494f20_team21";
$database = "i494f20_team21";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>