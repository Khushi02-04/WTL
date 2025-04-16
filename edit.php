<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student";

//create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

//check connection
if (!$conn) {
    die("connection failed:" . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css"
        integrity="sha384-PJsj/BTMqILvmcej7ulplguok8ag4xFTPryRq8xevL7eBYSmpXKcbNVuy+P0RMgq" crossorigin="anonymous">

    <style>
        .error {
            color: red;
        }
    </style>
</head>

<body>
    <?php
    if (isset($_GET['ID'])) {
        $ID = mysqli_real_escape_string($conn, $_GET['ID']);

        //Fetch existing record
        $sql = "SELECT * FROM stud WHERE ID=$ID";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
        } else {
            echo "Record not found!";
            exit();
        }
    } else {
        echo "No ID provided for edit!";
        exit();
    }

    $selected_languages = explode(", ", $row['Languages'] ?? ""); // Convert stored string to array

    //Initialize variables
    $ID = $Name = $Email = $Contact_Number = $DOB = $Gender = $Languages = $Qualification = $Photo = $Action = "";
    $NameErr = $EmailErr = $Contact_NumberErr = $DOBErr = $GenderErr = $LanguagesErr = $QualificationErr = "";

    //Allowed languages
    $allowed_languages = ["Hindi", "English", "Marathi", "Marwadi", "Gujarati"];

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $errors = false;

        //Name Validation
        if (empty($_POST["Name"])) {
            $NameErr = "Name is required";
            $errors = true;
        } else {
            $Name = test_input($_POST["Name"]);
            if (!preg_match("/^[a-zA-Z-' ]*$/", $Name)) {
                $NameErr = "Only letters, spaces, hyphens and apostrophes allowed";
                $errors = true;
            }
        }

        //Email Validation
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

        //Contact Number validation
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

        //Date of Birth Validation
        if (empty($_POST["DOB"])) {
            $DOBErr = "Date of Birth is required";
            $errors = true;
        } else {
            $DOB = test_input($_POST["DOB"]);
        }

        //Gender validation
        if (empty($_POST["Gender"])) {
            $GenderErr = "Gender is required";
            $errors = true;
        } else {
            $Gender = test_input($_POST["Gender"]);
        }

        //Languages validation
        if (empty($_POST["Languages"])) {
            $LanguagesErr = "Please select atleast one language";
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
                $Languages = implode(", ", $Languages); // Convert array to string
            }
        }

        //Qualification Validation
        if (empty($_POST["Qualification"])) {
            $QualificationErr = "Atleast one Qualification must be selected";
            $errors = true;
        } else {
            $Qualification = test_input($_POST["Qualification"]);
        }
    }

    //close connection
    mysqli_close($conn);
    ?>

    <!--Edit HTML Form-->
    <div class="container my-2">
        <form method="post" action="update.php" enctype="multipart/form-data">
            <input type="hidden" name="ID" value="<?php echo $row['ID']; ?>">
            <div class="form-group">
                <label for="Name" class="form-label">Name</label>
                <input type="text" class="form-control" name="Name" value="<?php echo $row['Name']; ?>">
                <span class="error">*<?php echo $NameErr; ?></span>
            </div>

            <div class="form-group">
                <label for="Email" class="form-label">Email</label>
                <input type="email" class="form-control" name="Email" value="<?php echo $row['Email']; ?>">
                <span class="error">*<?php echo $EmailErr; ?></span>
            </div>

            <div class="form-group">
                <label for="Contact_Number" class="form-label">Contact Number</label>
                <input type="tel" class="form-control" name="Contact_Number" value="<?php echo $row['Contact_Number']; ?>">
                <span class="error">*<?php echo $Contact_NumberErr; ?></span>
            </div>

            <div class="form-group">
                <label for="DOB" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="DOB" value="<?php echo $row['DOB']; ?>">
                <span class="error">*<?php echo $DOBErr; ?></span>
            </div>

            <!-- Gender (Radio Buttons) -->
            <div class="form-group">
                <label class="form-label d-block">Gender</label>
                <div class="d-flex flex-column align-items-start">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="Gender" value="Male" id="male"
                            <?= ($row['Gender'] == "Male") ? 'checked' : '' ?>>
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="Gender" value="Female" id="female"
                            <?= ($row['Gender'] == "Female") ? 'checked' : '' ?>>
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="Gender" value="Other" id="other"
                            <?= ($row['Gender'] == "Other") ? 'checked' : '' ?>>
                        <label class="form-check-label" for="other">Other</label>
                    </div>
                </div>
                <span class="error">*<?php echo $GenderErr; ?></span>
            </div>

            <!--Languages-->
            <div class="form-group">
                <label for="Language" class="form-label">Languages</label><br>
                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" name="Languages[]" value="Hindi" id="hindi"
                        <?= in_array("Hindi", $selected_languages) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="hindi">Hindi</label>
                </div><br>

                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" name="Languages[]" value="English" id="english"
                        <?= in_array("English", $selected_languages) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="english">English</label>
                </div><br>

                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" name="Languages[]" value="Marathi" id="marathi"
                        <?= in_array("Marathi", $selected_languages) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="marathi">Marathi</label>
                </div><br>

                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" name="Languages[]" value="Marwadi" id="marwadi"
                        <?= in_array("Marwadi", $selected_languages) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="marwadi">Marwadi</label>
                </div><br>

                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" name="Languages[]" value="Gujarati" id="gujarati"
                        <?= in_array("Gujarati", $selected_languages) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="gujarati">Gujarati</label>
                </div><br>

                <span class="error">*<?php echo $LanguagesErr; ?></span>
            </div>

            <div class="form-group">
                <label class="form-label" for="Qualification">Qualification</label>
                <select name="Qualification" class="form-select form-control" id="qualification">
                    <option value="select" disabled>Select</option>
                    <option value="10th" <?= ($row['Qualification'] == "10th") ? 'selected' : '' ?>>10th</option>
                    <option value="12th" <?= ($row['Qualification'] == "12th") ? 'selected' : '' ?>>12th</option>
                    <option value="Graduation" <?= ($row['Qualification'] == "Graduation") ? 'selected' : '' ?>>Graduation</option>
                    <option value="Post-Graduation" <?= ($row['Qualification'] == "Post-Graduation") ? 'selected' : '' ?>>Post-Graduation</option>
                    <option value="P.H.D" <?= ($row['Qualification'] == "P.H.D") ? 'selected' : '' ?>>P.H.D</option>
                </select>
                <span class="error">*<?php echo $QualificationErr; ?></span>
            </div>
            <div>
                <label for="Photo" class="form-label">Upload Your Photo</label>
                <input class="form-control" type="file" name="Photo" id="fileToUpload">
            </div>
            <div>
                <button type="submit" class="btn btn-primary my-2">Submit</button>
            </div>
        </form>
    </div>
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
        crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
    -->
</body>

</html>