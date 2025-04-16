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

// Check if ID is received
if (isset($_GET['ID'])) {
    $ID = mysqli_real_escape_string($conn, $_GET['ID']);

    // Use prepared statement for security
    $stmt = mysqli_prepare($conn, "DELETE FROM stud WHERE ID = ?");
    mysqli_stmt_bind_param($stmt, "i", $ID);
    mysqli_stmt_execute($stmt);

    // Redirect with a success/error message
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: listing.php?status=success");
    } else {
        header("Location: listing.php?status=error");
    }
} else {
    echo "No ID provided for deletion!";
}

mysqli_close($conn);
?>
