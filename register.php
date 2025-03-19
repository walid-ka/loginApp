<?php 

include "./components/header.php";


if (isLoggedIn()) {
    redirect("admin.php");
}

$username = $email = $password = $confPassword = $age = $phone = $gender = $terms = "";
$usernameErr = $emailErr = $passwordErr = $confPasswordErr = $ageErr = $phoneErr = $genderErr = $termsErr = "";
$successMessage= "";


if($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = trim($_POST["password"]);
    $confPassword = trim($_POST["confPassword"]);
    $age = mysqli_real_escape_string($conn, $_POST["age"]);
    $phone = filter_var(trim($_POST["phone"]), FILTER_SANITIZE_NUMBER_INT);
    $gender = mysqli_real_escape_string($conn, $_POST["gender"]);
    $terms = isset($_POST["terms"]) ? 1 : 0;
    

    // Validate username
    if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $username)) {
        $usernameErr = "Username must be 5-20 chars (letters, numbers, underscore)";
    }

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    }

    // Validate password
    if (empty($password) || strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $passwordErr = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, and 1 number.";
    }

    // Confirm password match
    if ($password !== $confPassword) {
        $confPasswordErr = "Passwords do not match";
    }

    // Hash the password if valid
    if (empty($passwordErr) && empty($confPasswordErr)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    // Validate age
    if (empty($age) || $age < 18 || $age > 100) {
        $ageErr = "Age must be between 18 and 100";
    }

    // Validate phone number
    if (empty($phone) || !preg_match("/^[0-9]{10,15}$/", $phone)) {
        $phoneErr = "Invalid phone number (10-15 digits)";
    }

    // Ensure terms are accepted
    if (!$terms) {
        $termsErr = "You must agree to the terms and conditions";
    }


    
    // If all validations pass, proceed with registration
    if (!$usernameErr && !$emailErr && !$passwordErr && !$confPasswordErr && !$ageErr && !$phoneErr && !$genderErr && !$termsErr) {

        // Check if username or email already exists, preventing duplicate registrations
        $stmtCheck = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmtCheck->bind_param("ss", $username, $email);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $stmtCheck->close();
    
        if ($result->num_rows > 0) {
            $usernameErr = "Username or email already exists";
        } else {
            // Insert user into database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, age, phone, gender, terms) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissi", $username, $email, $hashedPassword, $age, $phone, $gender, $terms);
    
            if ($stmt->execute()) {
                session_regenerate_id(true); // Prevent session hijacking
                $_SESSION["logged_in"] = true;
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
    
