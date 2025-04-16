<?php
$servername = "localhost";
$username = "root"; 
$password = "";

$conn = mysqli_connect($servername, $username, $password);

//Die if connection was not successful

if(!$conn){
    die("Connection failed:". mysqli_connect_error());
}else{
    echo "connection was successful";
}
?>