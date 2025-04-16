<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch records safely
$stmt = $conn->prepare("SELECT * FROM stud");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>

<body>
    <div class="container my-5">
        <button class="btn btn-info mb-2">
            <a href="record.php" style="text-decoration: none; color: white;">Add Student</a>
        </button>

        <?php if ($result->num_rows > 0) { ?>
            <table class="table table-bordered table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Languages</th>
                        <th>Qualification</th>
                        <th>Photo</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ID']) ?></td>
                            <td><?= htmlspecialchars($row['Name']) ?></td>
                            <td><?= htmlspecialchars($row['Email']) ?></td>
                            <td><?= htmlspecialchars($row['Contact_Number']) ?></td>
                            <td><?= htmlspecialchars($row['DOB']) ?></td>
                            <td><?= htmlspecialchars($row['Gender']) ?></td>
                            <td><?= htmlspecialchars($row['Languages']) ?></td>
                            <td><?= htmlspecialchars($row['Qualification']) ?></td>
                            <td>
                                <?php if (!empty($row['Photo'])) { ?>
                                    <img src="<?= htmlspecialchars($row['Photo']) ?>" alt="Student Photo" width="100">
                                <?php } else { ?>
                                    <img src="img/student.jpg" alt="No Image" width="100">
                                <?php } ?>
                            </td>
                            <td>
                                <a href="edit.php?ID=<?= $row['ID'] ?>" class="btn btn-primary mb-2">Edit</a>
                                <a href="delete.php?ID=<?= $row['ID'] ?>" onclick="return confirm('Are you sure you want to delete this record?');" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { echo "<p>No records found.</p>"; } ?>

    </div>

    <!-- jQuery & Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>

</body>
</html>

<?php 
$stmt->close();
$conn->close();
?>
