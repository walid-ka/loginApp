

<?php 
include "./components/header.php";


if (isLoggedIn()) {
    redirect("admin.php");
}

$username = $email = $password = $confPassword = $age = $phone = $gender = $terms = "";
$usernameErr = $emailErr = $passwordErr = $confPasswordErr = $ageErr = $phoneErr = $genderErr = $termsErr = "";
$successMessage= "";


if($_SERVER["REQUEST_METHOD"] == "POST") {

    //! Create a new user

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confPassword = $_POST["confPassword"];
    $age = $_POST["age"];
    $phone = $_POST["phone"];
    $gender = $_POST["gender"];
    $terms = $_POST["terms"];
    


    if(empty($username)) {
        $usernameErr = "Username is required";
    } else {
        $username = mysqli_real_escape_string($conn, $username);
        if(!preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
          $usernameErr = "Username must be 5-20 chars (letters, numbers, underscore)";
        }
    }



    if(empty($email)) {
        $usernameErr = "Email is required";
    } else {
        $email = mysqli_real_escape_string($conn, $email);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $emailErr = "Invalid email format)";
        }
    }



     if (empty($password)) {
        $passwordErr = "Password is required";
    } else {
        $password = trim($password);
        
        if (
            strlen($password) < 8 || 
            !preg_match("/[A-Z]/", $password) || 
            !preg_match("/[a-z]/", $password) || 
            !preg_match("/[0-9]/", $password)
        ) {
            $passwordErr = "Password must be at least 8 characters, include 1 uppercase letter, 1 lowercase letter, and 1 number.";
        }
    }

    if (empty($confPassword)) {
        $confPasswordErr = "Confirmation password is required";
    } else {
        $confPassword = trim($confPassword);
        
        if ($password !== $confPassword) {
            $confPasswordErr = "Passwords do not match";
        }
    }

    if (empty($passwordErr) && empty($confPasswordErr)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }



    if (empty($age)) {
        $ageErr = "Age is required";
    } else {
        $age = mysqli_real_escape_string($conn, $age);
        if ($age < 18 || $age > 100) {
            $ageErr = "Age must be between 18 and 100";
        }
    }


    if (empty($phone)) {
        $phoneErr = "Phone number is required";
    } else {
        $phone = filter_var(trim($phone), FILTER_SANITIZE_NUMBER_INT);
        if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
            $phoneErr = "Invalid phone number (10-15 digits)";
        }
    }


    if (empty($gender)) {
        $genderErr = "Gender is required";
    } else {
        $gender = mysqli_real_escape_string($conn, $gender);
    }


    if (!isset($terms)) {
        $termsErr = "You must agree to the terms and conditions";
    } else {
        $terms = 1; 
    }


    

    if (!$usernameErr && !$emailErr && !$passwordErr && !$confPasswordErr && !$ageErr && !$phoneErr && !$genderErr && !$termsErr) {

        // Check if username or email already exists
        $checkUser = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmtCheck = $conn->prepare($checkUser);
        $stmtCheck->bind_param("ss", $username, $email);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $stmtCheck->close();
    
        if ($result->num_rows > 0) {
            $usernameErr = "Username or email already exists";
        } else {
            // Insert user into database
            $sql = "INSERT INTO users (username, email, password, age, phone, gender, terms) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssissi", $username, $email, $hashedPassword, $age, $phone, $gender, $terms);
    
            if ($stmt->execute()) {
                $_SESSION["logged_in"] = true; // This is how we know the user is logged in
                $_SESSION["username"] = $username;
                redirect("admin.php");
            } else {
                $successMessage = "<h3 class='error'> Registration failed (error: " . $stmt->error . ")</h3>";
            }
    
            $stmt->close();
        }
    }
    
}

?>
    
    <div class="container"> 
        <div class="form-container"> 
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

            <h2>Create your Account</h2>

            <input type="text" name="username" placeholder="username" value="<?php echo isset($username) ? $username : "" ?>" required>
            <span class="error"><?php echo $usernameErr; ?></span>

            <input type="email" name="email" placeholder="email" value="<?php echo isset($email) ? $email : "" ?>" required>
            <span class="error"><?php echo $emailErr; ?></span>

            <input type="password" name="password" placeholder="password" required>
            <span class="error"><?php echo $passwordErr; ?></span>

            <input type="password" name="confPassword" placeholder="confirm password" required>
            <span class="error"><?php echo $confPasswordErr; ?></span>


            <input type="number" name="age" placeholder="age"" value="<?php echo isset($age) ? $age : "" ?>" required>
            <span class="error"><?php echo $ageErr; ?></span>

            <input type="text" name="phone" placeholder="phone number" value="<?php echo isset($phone) ? $phone : "" ?>">
            <span class="error"><?php echo $phoneErr; ?></span>

            <div>
            <input type="radio" name="gender" value="Male"  <?php if ($gender == "Male") echo "checked"; ?>> Male
            <input type="radio" name="gender" value="Female"  <?php if ($gender == "Female") echo "checked"; ?>> Female
            <input type="radio" name="gender" value="Other"  <?php if ($gender == "Other") echo "checked"; ?>> Other
            </div>
            <span class="error"><?php echo $genderErr; ?></span>

            <label><input type="checkbox" name="terms" value="agree"> I agree to the terms and conditions</label>
            <span class="error"><?php echo $termsErr; ?></span>

            <input type="submit" value="Register">
        </form>

        </div>
    </div>

<?php include "./components/footer.php"; ?>
    
