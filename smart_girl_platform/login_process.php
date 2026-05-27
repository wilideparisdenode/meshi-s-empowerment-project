<?php

session_start();

$conn = mysqli_connect(
"localhost:",
"root",
"",
"smart_girl_empowerment_db"
);

$email = $_POST['email'];
$password = $_POST['password'];

$query = "SELECT * FROM users

WHERE email='$email'
AND password='$password'";

$result = mysqli_query($conn,$query);

if(mysqli_num_rows($result)>0){

$_SESSION['email']=$email;

header("Location: dashboard.php");

}else{

echo "Invalid Login Details";

}

?>