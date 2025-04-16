<?php
$servername = "localhost";
$username = "root";
$password = "";

$conn = mysqli_connect($servername, $username, $password);

//check the connection
if(!$conn){
    exit("connection failed:". mysqli_connect_error());
}
//create database
$sql ="CREATE DATABASE student";

if(mysqli_query($conn, $sql)){
    echo "Database created successfully";
}else{
    echo "Error creating Database:".mysqli_error($conn);
}

//close connection
mysqli_close($conn);
?>