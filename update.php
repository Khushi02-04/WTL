<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if form is submitted
if (isset($_POST['ID'])) {
    $ID = mysqli_real_escape_string($conn, $_POST['ID']);
    $Name = mysqli_real_escape_string($conn, $_POST['Name']);
    $Email = mysqli_real_escape_string($conn, $_POST['Email']);
    $Contact_Number = mysqli_real_escape_string($conn, $_POST['Contact_Number']);
    $DOB = mysqli_real_escape_string($conn, $_POST['DOB']);
    $Gender = mysqli_real_escape_string($conn, $_POST['Gender']);
    $Qualification = mysqli_real_escape_string($conn, $_POST['Qualification']);

    // Handling Languages array safely
    $Languages = isset($_POST['Languages']) ? implode(", ", $_POST['Languages']) : '';

    // Handle file upload (Photo)
    if (!empty($_FILES['Photo']['name'])) {
        $target_dir = "uploads/";  // Ensure this directory exists in your project
        $target_file = $target_dir . basename($_FILES["Photo"]["name"]);
        
        // Move uploaded file to destination
        if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $target_file)) {
            $Photo = $target_file;
        } else {
            echo "Error uploading file!";
            exit();
        }
    } else {
        // Keep old photo if new one isn't uploaded
        $sql = "SELECT Photo FROM stud WHERE ID = $ID";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $Photo = $row['Photo'];
    }

    // Update query
    $sql = "UPDATE stud SET 
                Name='$Name', 
                Email='$Email', 
                Contact_Number='$Contact_Number', 
                DOB='$DOB', 
                Gender='$Gender', 
                Languages='$Languages', 
                Qualification='$Qualification', 
                Photo='$Photo' 
            WHERE ID=$ID";

    if (mysqli_query($conn, $sql)) {
        echo "Record updated successfully!";
        header("Location: listing.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    echo "No ID provided for update!";
}

// Close connection
mysqli_close($conn);
?>
