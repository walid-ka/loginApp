<?php 

include "./components/header.php";

if(!isLoggedIn()){
    redirect("login.php");
}



//! Fetch all users
$sql = "SELECT * FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();



if($_SERVER["REQUEST_METHOD"] === "POST") { 

    //! Update user
    if(isset($_POST["editUser"])) {
       $userId = mysqli_real_escape_string($conn, $_POST["userId"]);
       $newUsername = mysqli_real_escape_string($conn, $_POST["username"]);
        $newEmail = mysqli_real_escape_string($conn, $_POST["email"]);


        // var_dump($newEmail, $userId);

        $sql = "UPDATE users SET email = '$newEmail', username = '$newUsername' WHERE id = $userId";
        $result = $conn->prepare($sql); 

        if(checkServer($conn, $result)) {
            $result->execute();
            $_SESSION["toast"] = ["type" => "success", "message" => "User ({$newUsername}) updated successfully!"];
        } else {
            $_SESSION["toast"] = ["type" => "error", "message" => "Failed to update user ({$newUsername})!"];
        }
        redirect("admin.php");

    //! Delete user
    } elseif (isset($_POST["deleteUser"])) {
        $userId = mysqli_real_escape_string($conn, $_POST["userId"]);

        $sql = "DELETE FROM users WHERE id = $userId";
        $result = $conn->prepare($sql);
        if(checkServer($conn, $result)) {
            $result->execute();
            $_SESSION["toast"] = ["type" => "success", "message" => "User with id : {$userId} deleted successfully !"];
        } else {
            $_SESSION["toast"] = ["type" => "error", "message" => "Failed to delete user with id : {$userId}!"];
        }
        redirect("admin.php");
    }

} 


?>

<!-- Accessing Session Data on Another Page  if (isset($_SESSION["username"])) -->

<?php if(isset($_SESSION["toast"])): ?>
    <div class="toast <?php echo $_SESSION["toast"]["type"]; ?>">
        <?php echo $_SESSION["toast"]["message"]; ?>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.toast').style.display = 'none';
        }, 3000);
    </script>
    <?php unset($_SESSION["toast"]); ?>
<?php endif; ?>


<?php if(isAdmin()): ?>
    <h1 style="text-align:center; margin-top:60px;">Welcome <?php echo $_SESSION["username"] ?></h1>
<?php else: ?>
    <?php redirect("app.php"); ?>
<?php endif; ?>

<div class="tableContainer">
    <table class="user-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Age</th>
            <th>Phone</th>
            <th>gender</th>
            <th>Registration Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>


        <?php while ($user = mysqli_fetch_assoc($result)): ?>

            <tr>
                <td><?php echo $user["id"]; ?></td>
                <td><?php echo $user["username"]; ?></td>
                <td><?php echo $user["email"]; ?></td>
                <td><?php echo $user["age"]; ?></td>
                <td><?php echo $user["phone"]; ?></td>
                <td><?php echo $user["gender"]; ?></td>
                <td><?php echo $user["created_at"]; ?></td>
                <td>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="userId" value="<?php echo $user["id"]; ?>">
                        <input type="text" name="username" value="<?php echo $user["username"]; ?>" required>
                        <input type="email" name="email" value="<?php echo $user["email"]; ?>" required>
                        <button class="edit" type="submit" name="editUser">Edit</button>
                    </form>
                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="userId" value="<?php echo $user["id"]; ?>">
                        <button class="delete" type="submit" name="deleteUser">Delete</button>
                    </form>
                </td>
            </tr>

        <?php endwhile; ?>

        </tbody>
    </table>
</div>

<?php include "./components/footer.php"; ?>
