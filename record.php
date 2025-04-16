<?php
    // Database Connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "student";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .error { color: red; }
    </style>
</head>
<body>

<?php
    // Initialize variables
    $ID = $Name = $Email = $Contact_Number = $DOB = $Gender = $Languages = $Qualification = $Photo = "";
    $NameErr = $EmailErr = $Contact_NumberErr = $DOBErr = $GenderErr = $LanguagesErr = $QualificationErr = $PhotoErr = "";
    $allowed_languages = ["Hindi", "English", "Marathi", "Marwadi", "Gujarati"];
    $errors = false;

    function test_input($data) {
        $data = trim($data);
        $data = htmlspecialchars($data);
        $data = stripslashes($data);
        return $data;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Name Validation
        if (empty($_POST["Name"])) {
            $NameErr = "Name is required";
            $errors = true;
        } else {
            $Name = test_input($_POST["Name"]);
            if (!preg_match("/^[a-zA-Z-' ]*$/", $Name)) {
                $NameErr = "Only letters, spaces, hyphens, and apostrophes allowed";
                $errors = true;
            }
        }

        // Email Validation
        if (empty($_POST["Email"])) {
            $EmailErr = "Email is required";
            $errors = true;
        } else {
            $Email = test_input($_POST["Email"]);
            if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
                $EmailErr = "Invalid Email format";
                $errors = true;
            }
        }

        // Contact Number Validation
        if (empty($_POST["Contact_Number"])) {
            $Contact_NumberErr = "Contact Number is required";
            $errors = true;
        } else {
            $Contact_Number = test_input($_POST["Contact_Number"]);
            if (!preg_match("/^[0-9]{10}$/", $Contact_Number)) {
                $Contact_NumberErr = "Invalid Contact Number";
                $errors = true;
            }
        }

        // Date of Birth Validation
        if (empty($_POST["DOB"])) {
            $DOBErr = "Date of Birth is required";
            $errors = true;
        } else {
            $DOB = test_input($_POST["DOB"]);
        }

        // Gender Validation
        if (empty($_POST["Gender"])) {
            $GenderErr = "Gender is required";
            $errors = true;
        } else {
            $Gender = test_input($_POST["Gender"]);
        }

        // Languages Validation
        if (empty($_POST["Languages"])) {
            $LanguagesErr = "Please select at least one language";
            $errors = true;
        } else {
            $Languages = $_POST["Languages"];
            foreach ($Languages as $lang) {
                if (!in_array($lang, $allowed_languages)) {
                    $LanguagesErr = "Invalid Language selected";
                    $errors = true;
                    break;
                }
            }
            if (!$errors) {
                $Languages = implode(", ", $Languages);
            }
        }

        // Qualification Validation
        if (empty($_POST["Qualification"])) {
            $QualificationErr = "At least one Qualification must be selected";
            $errors = true;
        } else {
            $Qualification = test_input($_POST["Qualification"]);
        }

        // File Upload Handling
        $target_dir = __DIR__ . "/uploads/"; 

        // Ensure 'uploads/' directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (!isset($_FILES["Photo"]) || $_FILES["Photo"]["error"] != 0) {
            $PhotoErr = "Error: No file uploaded or an error occurred.";
            $errors = true;
        } else {
            $filename = basename($_FILES["Photo"]["name"]);
            $filename = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $filename);
            $target_file = $target_dir . $filename;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Allowed file formats
            $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($imageFileType, $allowed_extensions)) {
                $PhotoErr = "Only JPG, JPEG, PNG & GIF files are allowed.";
                $errors = true;
            }

            // Check if file is actually an image
            $check = getimagesize($_FILES["Photo"]["tmp_name"]);
            if ($check === false) {
                $PhotoErr = "File is not a valid image.";
                $errors = true;
            }

            // Move file to target location
            if (!$errors && move_uploaded_file($_FILES["Photo"]["tmp_name"], $target_file)) {
                $Photo = "uploads/" . $filename;
            } else {
                $PhotoErr = "Error uploading the file.";
                $errors = true;
            }

            if ($_FILES["Photo"]["error"] != 0) {
                die("File upload error: " . $_FILES["Photo"]["error"]);
            }
            
        }

        // Insert into the database only if there are no errors
        if (!$errors) {
            $sql = "INSERT INTO stud (Name, Email, Contact_Number, DOB, Gender, Languages, Qualification, Photo)
                    VALUES ('$Name', '$Email', '$Contact_Number', '$DOB', '$Gender', '$Languages', '$Qualification', '$Photo')";

            if (mysqli_query($conn, $sql)) {
                echo "<p style='color:green'>Student added successfully!</p>";
            } else {
                echo "<p class='error'>Error: " . mysqli_error($conn) . "</p>";
            }
        }
    }

    mysqli_close($conn);
?>

<!-- HTML Form -->
<div class="container my-2">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="Name" class="form-label">Name</label>
                <input type="text" class="form-control" name="Name" value="<?php echo $Name; ?>">
                <span class="error">*<?php echo $NameErr; ?></span>
            </div>

            <div class="form-group">
                <label for="Email" class="form-label">Email</label>
                <input type="email" class="form-control" name="Email" value="<?php echo $Email; ?>">
                <span class="error">*<?php echo $EmailErr; ?></span>
            </div>

            <div class="form-group">
                <label for="Contact_Number" class="form-label">Contact Number</label>
                <input type="tel" class="form-control" name="Contact_Number" value="<?php echo $Contact_Number; ?>">
                <span class="error">*<?php echo $Contact_NumberErr; ?></span>
            </div>

            <div class="form-group">
                <label for="DOB" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="DOB" value="<?php echo $DOB; ?>">
                <span class="error">*<?php echo $DOBErr; ?></span>
            </div>

            <!-- Gender (Radio Buttons) -->
            <div class="form-group">
                <label class="form-label d-block">Gender</label>
                <div class="d-flex flex-column align-items-start">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="Gender" value="Male" id="male"
                            <?php if ($Gender == "Male") echo "checked"; ?>>
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="Gender" value="Female" id="female"
                            <?php if ($Gender == "Female") echo "checked"; ?>>
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="Gender" value="Other" id="other"
                            <?php if ($Gender == "Other") echo "checked"; ?>>
                        <label class="form-check-label" for="other">Other</label>
                    </div>
                </div>
                <span class="error">*<?php echo $GenderErr; ?></span>
            </div>

            <!--Languages-->
            <div class= "form-group">
                <label class="form-label d-block">Languages</label>
                <div class="d-flex flex-column align-items-start">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="Languages[]" value="Hindi" id="hindi">
                        <label class="form-check-label" for="Hindi">Hindi</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="Languages[]" value="English" id="english">
                        <label class="form-check-label" for="English">English</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="Languages[]" value="Marathi" id="marathi">
                        <label class="form-check-label" for="Marathi">Marathi</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="Languages[]" value="Marwadi" id="marwadi">
                        <label class="form-check-label" for="Marwadi">Marwadi</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="Languages[]" value="Gujarati" id="gujarati">
                        <label class="form-check-label" for="Gujarati">Gujarati</label>
                    </div>
                </div>
                <span class="error">*<?php echo $LanguagesErr; ?></span>
            </div>  
            
            <div class="form-group">
                <label class="form-label" for="Qualification">Qualification</label>
                <select name="Qualification" class="form-select form-control" id="qualification">
                    <option value="select" disabled selected>Select</option>
                    <option value="10th">10th</option>
                    <option value="12th">12th</option>
                    <option value="Graduation">Graduation</option>
                    <option value="Post-Graduation">Post-Graduation</option>
                    <option value="P.H.D">P.H.D</option>
                </select>
                <span class="error">*<?php echo $QualificationErr; ?></span>
            </div>
            <div class="form-group">
                <label for="Photo" class="form-label">Upload Your Photo</label>
                <input class="form-control" type="file" name="Photo" id="fileToUpload" required>
                <span class="error">*<?php echo $PhotoErr; ?></span>
            </div>
            <div>
                <button type="submit" class="btn btn-primary my-2">Submit</button>
            </div>
        </form>
    </div>

</body>
</html>
