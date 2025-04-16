<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student";

$conn =mysqli_connect($servername, $username, $password, $dbname);

//check the connection
if(!$conn){
    die("Connection failed:".mysqli_connect_error());
}

//create table
$sql = "CREATE TABLE stud(
ID int(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
Name VARCHAR(20),
Email VARCHAR(40),
Contact_Number VARCHAR(10) NOT NULL,
DOB DATE,
Gender VARCHAR(30),
Languages VARCHAR(40),
Qualification VARCHAR(30),
Photo VARCHAR(255),
Action VARCHAR(50)
)";

if(mysqli_query($conn, $sql)){
    echo "Table created successfully";
}else{
    echo "Error creating table".mysqli_error($conn);
}

//close connection
mysqli_close($conn);
?>