<?php

$conn = mysqli_connect(
"localhost:",
"root",
"",
"smart_girl_empowerment_db"
);
if($conn){
    echo "Database Connected Successfully";
}else{
    echo "Connection Failed"
}

if(isset($_POST['register'])){

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$age = $_POST['age'];
$password = $_POST['password'];

$query = "INSERT INTO users
(full_name,email,phone,age,password)

VALUES(
'$fullname',
'$email',
'$phone',
'$age',
'$password'
)";

mysqli_query($conn,$query);

echo "Registration Successful";

}

?>