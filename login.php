<?php 
include "./components/header.php";


if (isLoggedIn()) {
    redirect("admin.php");
}


$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST["username"];
    $password = $_POST["password"];
    

    if (empty(trim($username)) || empty(trim($password))) {
        $error = "Username and Password are required";
    } else {
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);
        
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row["password"])) {
                if ($row["admin"] == 1) {
                session_regenerate_id(true); // This is a security measure to prevent session hijacking 
                $_SESSION["logged_in"] = true; // This is how we know the user is logged in
                $_SESSION["username"] = $row["username"]; // 
                $_SESSION["admin"] = true; // This is how we know the user is an admin 
                } else {
                session_regenerate_id(true);
                $_SESSION["logged_in"] = true; 
                $_SESSION["username"] = $row["username"];
                }

                redirect("admin.php");
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "User not found";
        }
    }

}
?>

    
    <div class="container <?php echo pageClass() ?>">
        <div class="form-container">
            <form method="POST" action="">

                <span class="error"><?php echo $error; ?></span><br><br>

                <h2>Login</h2>

                <input value="<?php echo isset($username) ? $username : "" ?>" type="text" name="username" placeholder="username" required><br><br>
                <input type="password" name="password" placeholder="password" required><br><br>

    
                <input type="submit" value="Login">
            </form>
        </div>
    </div>
    
<?php include "./components/footer.php"; ?>
;